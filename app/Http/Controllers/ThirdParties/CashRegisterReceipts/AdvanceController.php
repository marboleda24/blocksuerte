<?php

namespace App\Http\Controllers\ThirdParties\CashRegisterReceipts;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdvanceController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.advances', [
            'only' => [
                'index',
            ],
        ]);

        $this->middleware('permission:application.third-parties.advances.create', [
            'only' => [
                'index', 'create', 'cancel', 'send_wallet', 'search_customer', 'store',
            ],
        ]);
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        if (Auth::user()->hasRole('super-admin')) {
            $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                ->where('created_by', '=', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return Inertia::render('Applications/ThirdParties/Advances/Index', [
            'advances' => $advances,
        ]);
    }

    /**
     * cancel
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $advance = Advance::find($request->id);

            $advance->update([
                'state' => '0',
                'cancel_justify' => $request->justify,
            ]);

            $advance->log()->create([
                'user_id' => Auth::id(),
                'description' => 'Anticipo anulado',
            ]);

            DB::commit();
            if (Auth::user()->hasRole('super-admin')) {
                $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                    ->where('created_by', '=', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->get();
            }

            return response()->json($advances, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Applications/ThirdParties/Advances/Create');
    }

    /**
     * send_wallet
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function send_wallet(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $advance = Advance::find($request->id);

            $advance->update([
                'state' => '2',
            ]);

            $advance->log()->create([
                'user_id' => Auth::id(),
                'description' => 'Anticipo enviado a cartera',
            ]);

            DB::commit();
            if (Auth::user()->hasRole('super-admin')) {
                $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                    ->where('created_by', '=', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->get();
            }

            return response()->json($advances, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * search_customer
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function search_customer(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query');
            $results = [];

            $queries = DB::connection('DMS')
                ->table('V_CIEV_Clientes')
                ->where('nombres', 'LIKE', '%'.$query.'%')
                ->orWhere('nit', 'LIKE', '%'.$query.'%')
                ->take(20)
                ->get();

            foreach ($queries as $q) {
                $results[] = [
                    'value' => trim($q->nombres),
                    'nit' => trim($q->nit),
                ];
            }

            return response()->json($results, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * store
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'customer_code' => 'required|string',
            'total_paid' => 'required|numeric',
            'payment_date' => 'required|date',
            'bank_account' => 'required|string',
        ])->validate();

        DB::beginTransaction();
        try {
            $advance = new Advance($request->except('payment_date'));
            $advance->state = '1';
            $advance->created_by = Auth::id();
            $advance->payment_date = Carbon::parse($request->payment_date)->format('Y-m-d');
            $advance->save();

            $advance->log()->create([
                'user_id' => Auth::id(),
                'description' => 'Anticipo enviado a cartera',
            ]);

            DB::commit();

            return response()->json('advance saved correctly', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e, 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function view(Request $request): JsonResponse
    {
        try {
            $data = Advance::with('customer', 'createdby', 'log')
                ->find($request->id);

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function report_advance(Request $request): JsonResponse
    {
        $date = explode(' - ', $request->date);

        $data = auth()->user()->hasRole('super-admin')
            ? Advance::where('state', '=', '4')
                ->whereBetween('created_at', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->sum('total_paid')
            : Advance::where('created_by', '=', Auth::id())
                ->where('state', '=', '4')
                ->whereBetween('created_at', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->sum('total_paid');

        return response()->json($data, 200);
    }
}

<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CustomerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:queries.customers');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = DB::connection('MAX')
            ->table('Customer_Master')
            ->orderBy("NAME_23")
            ->get();

        return Inertia::render('Applications/Queries/Customers', [
            'customers' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function active_customer(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master')
                ->where('CUSTID_23', '=', trim($request->code))
                ->update([
                    'STATUS_23' => 'R',
                ]);

            $data = DB::connection('MAX')
                ->table('Customer_Master')
                ->get();

            DB::connection('MAX')->commit();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function inactive_customer(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master')
                ->where('CUSTID_23', '=', trim($request->code))
                ->update([
                    'STATUS_23' => 'H',
                ]);

            $data = DB::connection('MAX')
                ->table('Customer_Master')
                ->get();

            DB::connection('MAX')->commit();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}

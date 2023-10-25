<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderDecorativeOption;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DecorativeOptionController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.decorative-options');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderDecorativeOption::with('createdBy')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/DecorativeOptions', [
            'data' => $data,
        ]);
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
            'code' => 'required|min:2|max:2|unique:encoder_decorative_options,code',
            'name' => 'required|min:4|max:255|unique:encoder_decorative_options,name',
        ])->validate();

        EncoderDecorativeOption::create([
            'code' => $request->code,
            'abbreviation' => $request->abbreviation,
            'name' => $request->name,
            'comments' => $request->comments,
            'created_by' => auth()->user()->id,
            'state' => 1,
        ]);

        $data = EncoderDecorativeOption::with('createdBy')
            ->where('state', '=', 1)
            ->get();

        return response()->json($data, 200);
    }

    /**
     * update
     *
     * @param  mixed  $request
     * @param  mixed  $code
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, $code): JsonResponse
    {
        Validator::make($request->all(), [
            'code' => 'required|min:2|max:2|unique:encoder_decorative_options,code,'.$code.',code',
            'name' => 'required|min:4|max:255|unique:encoder_decorative_options,name,'.$request->name.',name',
        ])->validate();

        if ($code) {
            EncoderDecorativeOption::find($code)
                ->update($request->except('code', 'created_by'));

            $data = EncoderDecorativeOption::with('createdBy')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        }
    }

    /**
     * destroy
     *
     * @param $code
     * @return JsonResponse
     */
    public function destroy($code): JsonResponse
    {
        try {
            EncoderDecorativeOption::find($code)->update(['state' => 0]);

            $data = EncoderDecorativeOption::with('createdBy')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * GetLatestCode
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_latest_code(Request $request): JsonResponse
    {
        try {
            $data = EncoderDecorativeOption::where('state', '=', 1)
                ->pluck('code');

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $name
     * @return JsonResponse
     */
    public function validate_name($name): JsonResponse
    {
        $value = EncoderDecorativeOption::where('name', $name)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

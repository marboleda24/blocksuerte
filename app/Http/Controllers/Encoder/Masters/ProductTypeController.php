<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderProductType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProductTypeController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.products-types');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderProductType::with('createdBy')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/ProductTypes', [
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
            'code' => 'required|min:1|max:1|unique:encoder_product_types,code',
            'name' => 'required|min:4|max:255|unique:encoder_product_types,name',
        ])->validate();

        try {
            EncoderProductType::create([
                'code' => strtoupper($request->code),
                'name' => strtoupper($request->name),
                'comments' => $request->comments,
                'created_by' => auth()->user()->id,
                'state' => 1,
            ]);

            $data = EncoderProductType::with('createdBy')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * destroy
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            EncoderProductType::find($id)->update(['state' => 0]);

            $data = EncoderProductType::with('createdBy')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * update
     *
     * @param  mixed  $request
     * @param $code
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, $code): JsonResponse
    {
        Validator::make($request->all(), [
            'code' => 'required|min:1|max:1|unique:encoder_product_types,code,'.$code.',code',
            'name' => 'required|min:4|max:255|unique:encoder_product_types,name,'.$request->name.',name',
        ])->validate();

        try {
            EncoderProductType::find($code)
                ->update($request->except('code', 'created_by'));

            $data = EncoderProductType::with('createdBy')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $code
     * @return JsonResponse
     */
    public function validate_code($code): JsonResponse
    {
        $value = EncoderProductType::where('code', $code)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }

    /**
     * @param $name
     * @return JsonResponse
     */
    public function validate_name($name): JsonResponse
    {
        $value = EncoderProductType::where('name', $name)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

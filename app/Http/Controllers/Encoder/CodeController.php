<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Models\Art;
use App\Models\Brand;
use App\Models\EncoderCode;
use App\Models\EncoderDecorativeOption;
use App\Models\EncoderFeature;
use App\Models\EncoderGalvanicFinish;
use App\Models\EncoderLine;
use App\Models\EncoderMaterial;
use App\Models\EncoderMeasurement;
use App\Models\EncoderProduct;
use App\Models\EncoderProductType;
use App\Models\EncoderSubline;
use App\Models\Views\V_ENCODER_CODES;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CodeController extends Controller
{
    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        set_time_limit(0);

        $data = V_ENCODER_CODES::with('measurement.detail.unit')
            ->where('state', '=', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $product_types = EncoderProductType::orderBy('name')
            ->where('state', '=', 1)
            ->get();

        $galvanic_finishes = EncoderGalvanicFinish::orderBy('name')
            ->where('state', '=', 1)
            ->get();

        $decorative_options = EncoderDecorativeOption::orderBy('name')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Codes', [
            'data' => $data,
            'product_types' => $product_types,
            'galvanic_finishes' => $galvanic_finishes,
            'decorative_options' => $decorative_options,
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
            'code' => 'required|min:10|max:10|unique:encoder_codes,code',
            'description' => 'required|min:4|max:120|unique:encoder_codes,description',
            'product_type' => 'required',
            'line' => 'required',
            'subline' => 'required',
            'feature' => 'required',
            'material' => 'required',
            'measurement' => 'required',
        ])->validate();

        DB::beginTransaction();
        try {
            EncoderCode::create([
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'product_type_code' => $request->product_type,
                'line_code' => $request->line,
                'subline_code' => $request->subline,
                'feature_code' => $request->feature,
                'material_id' => $request->material,
                'measurement_id' => $request->measurement,
                'galvanic_finish_code' => $request->galvanic_finish,
                'decorative_option_code' => $request->decorative_option,
                'comments' => $request->comments,
                'art_code' => $request->art,
                'created_by' => auth()->user()->id,
                'state' => 1,
            ]);

            $data = V_ENCODER_CODES::with('measurement.detail.unit')
                ->where('state', '=', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            DB::commit();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * update
     *
     * @param  mixed  $request
     * @param $code
     * @return JsonResponse
     */
    public function update(Request $request, $code): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'code' => 'required|min:10|max:10|unique:encoder_codes,code,'.$request->code.',code',
                'description' => 'required|min:4|max:120|unique:encoder_codes,description,'.$request->description.',description',
                'product_type' => 'required',
                'line' => 'required',
                'subline' => 'required',
                'feature' => 'required',
                'material' => 'required',
                'measurement' => 'required',
            ])->validate();

            if ($code) {
                EncoderCode::where('code', '=', $code)
                    ->update([
                        'description' => $request->description,
                        'product_type_code' => $request->product_type,
                        'line_code' => $request->line,
                        'subline_code' => $request->subline,
                        'feature_code' => $request->feature,
                        'material_id' => $request->material,
                        'measurement_id' => $request->measurement,
                        'galvanic_finish_code' => $request->galvanic_finish,
                        'decorative_option_code' => $request->decorative_option,
                        'art_code' => $request->art,
                        'comments' => $request->comments,
                    ]);

                $data = V_ENCODER_CODES::with('measurement.detail.unit')
                    ->where('state', '=', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();

                return response()->json($data, 200);
            }
        } catch (Exception $e) {
            return response()->json("[{$e->getLine()}]: {$e->getMessage()}", 500);
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
            $exist = DB::connection('MAX')
                ->table('Part_Master')
                ->where('PRTNUM_01', '=', $code)
                ->count();

            if ($exist > 0) {
                throw new Exception('Este producto ya se encuentra en MAX y no puede ser eliminado');
            }

            EncoderCode::find($code)->delete();

            $data = V_ENCODER_CODES::with('measurement.detail.unit')
                ->where('state', '=', 1)
                ->orderBy('created_at', 'desc')
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
    public function verify_product($code)
    {
        $exist = DB::connection('MAX')
            ->table('Part_Master')
            ->where('PRTNUM_01', '=', $code)
            ->count();

        return response()->json(! ($exist > 0), 200);
    }

    /**
     * get_lines
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_lines(Request $request): JsonResponse
    {
        try {
            $data = EncoderLine::whereHas('product_types', function ($q) use ($request) {
                $q->where('product_type_code', '=', $request->product_type)
                    ->where('state', '=', 1);
            })->orderBy('name', 'asc')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get_lines
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_sublines(Request $request): JsonResponse
    {
        try {
            $data = EncoderSubline::orderBy('name')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get_other_inputs
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_other_inputs(Request $request): JsonResponse
    {
        try {
            $materials = EncoderMaterial::with('material')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->where('subline_code', $request->subline_code)
                ->get();

            $features = EncoderFeature::orderBy('name')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->where('subline_code', $request->subline_code)
                ->get();

            $measurements = EncoderMeasurement::with('detail', 'detail.characteristic', 'detail.unit')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->where('subline_code', $request->subline_code)
                ->get();

            return response()->json([
                'materials' => $materials,
                'features' => $features,
                'measurements' => $measurements,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_measurements(Request $request): JsonResponse
    {
        try {
            $measurements = EncoderMeasurement::with('detail', 'detail.characteristic', 'detail.unit')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->where('subline_code', $request->subline_code)
                ->get();

            return response()->json($measurements, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get_list_codes
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_list_codes(Request $request): JsonResponse
    {
        try {
            $code_list = EncoderCode::where('product_type_code', $request->product_type ?? '')
                ->where('state', '=', 1)
                ->where('line_code', $request->line ?? '')
                ->where('subline_code', $request->subline ?? '')
                ->where('material_id', $request->material ?? '')
                ->pluck('code')
                ->toArray();

            return response()->json($code_list, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_arts(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            $queries = DB::connection('EVPIUM')
                ->table('V_Artes')
                ->where('CodigoArte', 'LIKE', '%'.$query.'%')
                ->take(10)
                ->get();

            return response()->json($queries, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function products(): Response
    {
        $products = EncoderProduct::with('user')->get();

        return Inertia::render('Applications/Encoder/Product', [
            'products' => $products,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function validate_description(Request $request)
    {
        $value = EncoderCode::where('description', '=', $request->description)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

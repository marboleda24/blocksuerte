<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Models\EncoderCode;
use App\Models\EncoderDecorativeOption;
use App\Models\EncoderFeature;
use App\Models\EncoderGalvanicFinish;
use App\Models\EncoderMeasurement;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DescriptionEditionController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $decorative_options = EncoderDecorativeOption::orderBy('name')->get();
        $galvanic_finish = EncoderGalvanicFinish::orderBy('name')->get();

        return Inertia::render('Applications/Encoder/DescriptionEdition', [
            'decorative_options' => $decorative_options,
            'galvanic_finish' => $galvanic_finish
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function store(Request $request)
    {
        try {

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_product(Request $request)
    {
        $data = EncoderCode::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement', 'galvanic_finish', 'decorative_option')
            ->where('state', '=', 1)
            ->where(function ($query) use ($request) {
                $query->where('code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->q . '%');
            })->take(50)
            ->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_measurements_features(Request $request)
    {
        $measurements = EncoderMeasurement::with('detail')
            ->where('line_code', '=', $request->line_code)
            ->where('subline_code', '=', $request->subline_code)
            ->get();

        $features = EncoderFeature::where('line_code', '=', $request->line_code)
            ->where('subline_code', '=', $request->subline_code)
            ->orderBy('name')
            ->get();

        return response()->json([
            'measurements' => $measurements,
            'features' => $features
        ], 200);
    }
}

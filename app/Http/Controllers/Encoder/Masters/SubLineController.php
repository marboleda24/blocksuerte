<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderLine;
use App\Models\EncoderMeasurementCharacteristic;
use App\Models\EncoderSubline;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SubLineController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.sublines');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderSubline::with('line', 'createdBy', 'measurement_characteristic')
            ->where('state', '=', 1)
            ->get();

        $lines = EncoderLine::where('state', '=', 1)
            ->get();

        $features = EncoderMeasurementCharacteristic::where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/SubLines', [
            'data' => $data,
            'lines' => $lines,
            'features' => $features,
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
            'line' => 'required',
            'code' => ['required', 'min:2', 'max:4',
                Rule::unique('encoder_sublines')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line);
                }), ],
            'name' => ['required', 'min:2', 'max:255',
                Rule::unique('encoder_sublines')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line);
                }), ],
            'abbreviation' => 'max:10',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string|distinct|min:1',
        ])->validate();

        try {
            $subline = new EncoderSubline();
            $subline->line_code = $request->line;
            $subline->code = $request->code;
            $subline->name = $request->name;
            $subline->abbreviation = $request->abbreviation;
            $subline->comments = $request->comments;
            $subline->created_by = auth()->user()->id;
            $subline->state = 1;
            $subline->save();
            $subline->measurement_characteristic()->sync($request->features);

            $data = EncoderSubline::with('line', 'createdBy', 'measurement_characteristic')
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
     * @param $code
     * @return JsonResponse
     */
    public function destroy($code): JsonResponse
    {
        try {
            $line = EncoderSubline::find($code);
            //$line->measurement_characteristic()->detach();
            //$line->delete();
            $line->state = 0;
            $line->save();

            $data = EncoderSubline::with('line', 'createdBy', 'measurement_characteristic')
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
            'line' => 'required',
            'code' => 'required|min:2|max:4|unique:encoder_sublines,code,'.$request->code.',code',
            'name' => 'required|min:2|max:255|unique:encoder_sublines,name,'.$request->name.',name',
            'abbreviation' => 'max:10',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string|distinct|min:1',
        ])->validate();

        if ($code) {
            EncoderSubline::find($code)
                ->update($request->except('created_by', 'code'));

            EncoderSubline::find($code)
                ->measurement_characteristic()->sync($request->features);

            $data = EncoderSubline::with('line', 'createdBy', 'measurement_characteristic')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        }
    }

    /**
     * GetLatestCode
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function GetLatestCode(Request $request): JsonResponse
    {
        try {
            $data = EncoderSubline::where('line_code', $request->line)
                ->where('state', '=', 1)
                ->orderBy('code', 'desc')
                ->pluck('code');

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $line
     * @param $name
     * @return JsonResponse
     */
    public function validate_name($line, $name): JsonResponse
    {
        $value = EncoderSubline::where('line_code', $line)
            ->where('state', '=', 1)
            ->where('name', $name)
            ->count();

        return response()->json(! $value > 0, 200);
    }

    /**
     * @param $line
     * @param $abbreviation
     * @return JsonResponse
     */
    public function validate_abbreviation($line, $abbreviation): JsonResponse
    {
        $value = EncoderSubline::where('line_code', $line)
            ->where('state', '=', 1)
            ->where('abbreviation', $abbreviation)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

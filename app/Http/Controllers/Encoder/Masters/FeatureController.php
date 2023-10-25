<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderFeature;
use App\Models\EncoderLine;
use App\Models\EncoderSubline;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class FeatureController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.features');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderFeature::with('createdBy', 'line', 'subline')
            ->where('state', '=', 1)
            ->orderBy('name', 'asc')
            ->get();

        $lines = EncoderLine::orderBy('name', 'asc')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/Features', [
            'data' => $data,
            'lines' => $lines,
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
            'subline' => 'required',
            'code' => ['required', 'min:6', 'max:6',
                Rule::unique('encoder_features')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                }),
            ],
            'name' => ['required', 'min:2', 'max:255',
                Rule::unique('encoder_features')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                }),
            ],
        ])->validate();

        try {
            EncoderFeature::create([
                'line_code' => $request->line,
                'subline_code' => $request->subline,
                'code' => $request->code,
                'name' => $request->name,
                'abbreviation' => $request->abbreviation,
                'comments' => $request->comments,
                'created_by' => auth()->user()->id,
                'state' => 1,
            ]);

            $data = EncoderFeature::with('createdBy', 'line', 'subline')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * GetSublines
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function GetSublines(Request $request): JsonResponse
    {
        try {
            $data = EncoderSubline::with('measurement_characteristic')
                ->where('state', '=', 1)
                ->where('line_code', $request->line_code)
                ->orderBy('name', 'asc')
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
            'code' => ['required', 'min:6', 'max:6',
                Rule::unique('encoder_features')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                })->ignore($request->code, 'code'),
            ],
            'name' => ['required', 'min:2', 'max:255',
                Rule::unique('encoder_features')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                })->ignore($request->name, 'name'),
            ],
            'line' => 'required',
            'subline' => 'required',
            'abbreviation' => 'required|string|min:2|max:10',
        ])->validate();

        if ($code) {
            EncoderFeature::find($code)
                ->update($request->except('code', 'created_by'));

            $data = EncoderFeature::with('createdBy', 'line', 'subline')
                ->where('state', '=', 1)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($data, 200);
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
            EncoderFeature::find($id)->update(['state' => 0]);

            $data = EncoderFeature::with('createdBy', 'line', 'subline')
                ->where('state', '=', 1)
                ->orderBy('name', 'asc')
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
    public function GetLatestCode(Request $request): JsonResponse
    {
        try {
            $data = EncoderFeature::where('line_code', $request->line)
                ->where('state', '=', 1)
                ->where('subline_code', $request->subline)
                ->orderBy('code', 'desc')
                ->pluck('code');

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $line
     * @param $subline
     * @param $name
     * @return JsonResponse
     */
    public function validate_name($line, $subline, $name): JsonResponse
    {
        $value = EncoderFeature::where('line_code', $line)
            ->where('state', '=', 1)
            ->where('subline_code', $subline)
            ->where('name', $name)
            ->count();

        return response()->json(! $value > 0, 200);
    }

    /**
     * @param $line
     * @param $subline
     * @param $abbreviation
     * @return JsonResponse
     */
    public function validate_abbreviation($line, $subline, $abbreviation): JsonResponse
    {
        $value = EncoderFeature::where('line_code', $line)
            ->where('state', '=', 1)
            ->where('subline_code', $subline)
            ->where('abbreviation', $abbreviation)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

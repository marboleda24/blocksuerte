<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderLine;
use App\Models\EncoderMaterial;
use App\Models\EncoderMaterialExt;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.materials');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderMaterial::with('createdBy', 'line', 'subline', 'material')
            ->where('state', '=', 1)
            ->orderBy('material_code', 'asc')
            ->get();

        $lines = EncoderLine::orderBy('name', 'asc')
            ->where('state', '=', 1)
            ->get();

        $materials = EncoderMaterialExt::orderBy('name', 'asc')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/Materials', [
            'data' => $data,
            'lines' => $lines,
            'materials' => $materials,
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
            'material_code' => ['required',
                Rule::unique('encoder_materials')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                }),
            ],
        ])->validate();

        try {
            EncoderMaterial::create([
                'line_code' => $request->line,
                'subline_code' => $request->subline,
                'material_code' => $request->material_code,
                'comments' => $request->comments,
                'created_by' => auth()->user()->id,
                'state' => 1,
            ]);

            $data = EncoderMaterial::with('createdBy', 'line', 'subline', 'material')
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
     * @param $id
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        Validator::make($request->all(), [
            'line' => 'required',
            'subline' => 'required',
            'material_code' => ['required',
                Rule::unique('encoder_materials')->where(function ($query) use ($request) {
                    return $query->where('line_code', $request->line)
                        ->where('subline_code', $request->subline);
                })->ignore($request->material_code, 'material_code'),
            ],
        ])->validate();

        if ($id) {
            EncoderMaterial::find($id)
                ->update($request->except('created_by'));

            $data = EncoderMaterial::with('createdBy', 'line', 'subline', 'material')
                ->where('state', '=', 1)
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
            EncoderMaterial::find($id)->update(['state' => 0]);

            $data = EncoderMaterial::with('createdBy', 'line', 'subline', 'material')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function validate_material(Request $request)
    {
        $value = EncoderMaterial::where('line_code', '=', $request->line)
            ->where('state', '=', 1)
            ->where('subline_code', '=', $request->subline)
            ->where('material_code', '=', $request->material_code)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

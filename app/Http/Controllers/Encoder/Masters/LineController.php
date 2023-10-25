<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderLine;
use App\Models\EncoderProductType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LineController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.lines');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderLine::with('createdBy', 'product_types')
            ->where('state', '=', 1)
            ->get();

        $product_types = EncoderProductType::where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/Lines', [
            'data' => $data,
            'product_types' => $product_types,
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
            'product_types' => 'required|array|min:1',
            'product_types.*' => 'required|string|distinct|min:1',
            'code' => 'required|min:2|max:2|unique:encoder_lines,code',
            'name' => 'required|min:4|max:255|unique:encoder_lines,name',
            'abbreviation' => 'required|string|min:3|max:10',
        ])->validate();

        try {
            $line = new EncoderLine();
            $line->code = strtoupper($request->code);
            $line->name = strtoupper($request->name);
            $line->abbreviation = strtoupper($request->abbreviation);
            $line->comments = $request->comments;
            $line->created_by = auth()->user()->id;
            $line->state = 1;

            $line->save();
            $line->product_types()->sync($request->product_types);

            $data = EncoderLine::with('createdBy', 'product_types')
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
            $line = EncoderLine::find($code);
            $line->state = 0;
            //$line->product_types()->detach();
            //$line->delete();
            $line->save();

            $data = EncoderLine::with('createdBy', 'product_types')
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
     * @param  mixed  $code
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, $code): JsonResponse
    {
        Validator::make($request->all(), [
            'product_types' => 'required|array|min:1',
            'product_types.*' => 'required|string|distinct|min:1',
            'code' => 'required|min:2|max:2|unique:encoder_lines,code,'.$code.',code',
            'name' => 'required|min:4|max:255|unique:encoder_lines,name,'.$request->name.',name',
            'abbreviation' => 'required|string|min:3|max:10',
        ])->validate();

        if ($code) {
            EncoderLine::find($code)->update($request->except('created_by', 'code'));
            EncoderLine::find($code)->product_types()->sync($request->product_types);
            $data = EncoderLine::with('createdBy', 'product_types')
                ->where('state', '=', 1)
                ->get();

            return response()->json($data, 200);
        }
    }

    /**
     * @param $code
     * @return JsonResponse
     */
    public function validate_code($code): JsonResponse
    {
        $value = EncoderLine::where('code', $code)
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
        $value = EncoderLine::where('name', $name)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }

    /**
     * @param $abbreviation
     * @return JsonResponse
     */
    public function validate_abbreviation($abbreviation): JsonResponse
    {
        $value = EncoderLine::where('abbreviation', $abbreviation)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

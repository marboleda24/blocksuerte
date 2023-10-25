<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderGalvanicFinish;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class GalvanicFinishController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.galvanic-finishes');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderGalvanicFinish::with('createdBy')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/GalvanicFinishes', [
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
            'code' => 'required|min:2|max:2|unique:encoder_galvanic_finishes,code',
            'name' => 'required|min:4|max:255|unique:encoder_galvanic_finishes,name',
            'abbreviation' => 'required|min:2|max:255|unique:encoder_galvanic_finishes,abbreviation',
        ])->validate();

        EncoderGalvanicFinish::create([
            'code' => $request->code,
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'comments' => $request->comments,
            'created_by' => auth()->user()->id,
            'state' => 1,
        ]);

        $data = EncoderGalvanicFinish::with('createdBy')
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
            'code' => 'required|min:2|max:2|unique:encoder_galvanic_finishes,code,'.$code.',code',
            'name' => 'required|min:4|max:255|unique:encoder_galvanic_finishes,name,'.$request->name.',name',
            'abbreviation' => 'required|min:2|max:255|unique:encoder_galvanic_finishes,abbreviation,'.$request->abbreviation.',abbreviation',
        ])->validate();

        if ($code) {
            EncoderGalvanicFinish::find($code)
                ->update($request->except('created_by', 'code'));

            $data = EncoderGalvanicFinish::with('createdBy')
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
            EncoderGalvanicFinish::destroy($id);

            $data = EncoderGalvanicFinish::with('createdBy')
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
            $data = EncoderGalvanicFinish::where('state', '=', 1)
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
        $value = EncoderGalvanicFinish::where('name', $name)
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
        $value = EncoderGalvanicFinish::where('abbreviation', $abbreviation)
            ->where('state', '=', 1)
            ->count();

        return response()->json(! $value > 0, 200);
    }
}

<?php

namespace App\Http\Controllers\Encoder\Masters;

use App\Http\Controllers\Controller;
use App\Models\EncoderLine;
use App\Models\EncoderMeasurement;
use App\Models\EncoderMeasurementDetail;
use App\Models\EncoderUnitMeasurement;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class MeasurementController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.master.measurements');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = EncoderMeasurement::with('line', 'subline', 'createdBy')
            ->where('state', '=', 1)
            ->get();

        $lines = EncoderLine::orderby('name')
            ->where('state', '=', 1)
            ->get();

        $units = EncoderUnitMeasurement::where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/Masters/Measurements', [
            'data' => $data,
            'lines' => $lines,
            'units' => $units,
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
        ])->validate();

        DB::beginTransaction();
        try {
            $measurement = new EncoderMeasurement();
            $measurement->line_code = $request->line;
            $measurement->subline_code = $request->subline;
            $measurement->comments = $request->comments;
            $measurement->created_by = auth()->user()->id;
            $measurement->state = 1;
            $measurement->save();

            foreach ($request->subline_props as $detail) {
                if ($detail['value'] && $detail['value'] > 0) {
                    EncoderMeasurementDetail::create([
                        'measurement_id' => $measurement->id,
                        'unit_code' => $detail['unit'],
                        'characteristic_code' => $detail['code'],
                        'value' => preg_match("/(\d+(?:\.\d+)?)(\/\d+(?:\.\d+)?)*$/", $detail['value']) ? $detail['value'] : floatval($detail['value']),
                    ]);
                }
            }
            DB::commit();
            $data = EncoderMeasurement::with('line', 'subline', 'createdBy', 'detail.characteristic', 'detail.unit')
                ->where('state', '=', 1)
                ->get();

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
        ])->validate();

        if ($id) {
            $measurement = EncoderMeasurement::with('detail')
                ->find($id);

            $measurement->update($request->except('created_by'));

            $measurement->detail()->delete();

            foreach ($request->subline_props as $detail) {
                $measurement->detail()
                    ->create([
                        'unit_code' => $detail['unit'],
                        'characteristic_code' => $detail['code'],
                        'value' => preg_match("/(\d+(?:\.\d+)?)(\/\d+(?:\.\d+)?)*$/", $detail['value']) ? $detail['value'] : floatval($detail['value']),
                    ]);
            }

            $data = EncoderMeasurement::with('line', 'subline', 'createdBy', 'detail.characteristic', 'detail.unit')
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
            //EncoderMeasurementDetail::where('measurement_id', $id)->delete();
            EncoderMeasurement::find($id)->update(['state' => 0]);

            $data = EncoderMeasurement::with('line', 'subline', 'createdBy', 'detail.characteristic', 'detail.unit')
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
    public function validate_denomination(Request $request): JsonResponse
    {
        $records = EncoderMeasurement::with('detail.characteristic', 'detail.unit')
            ->where('state', '=', 1)
            ->where('line_code', '=', $request->line)
            ->where('subline_code', '=', $request->subline)
            ->get();

        if (count($records) > 0) {
            foreach ($records as $record) {
                $denomination = denominationCreator($record->detail);
                if ($request->denomination === $denomination) {
                    $result = false;
                    break;
                }
            }
        } else {
            return response()->json(true, 200);
        }

        return response()->json($result ?? true, 200);
    }
}

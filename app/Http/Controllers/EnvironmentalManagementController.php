<?php

namespace App\Http\Controllers;

use App\Models\Hl1Binnacle;
use App\Models\MachineBinnacle;
use App\Models\NotificationSystem;
use App\Models\Px00Binnacle;
use App\Models\Sensor;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EnvironmentalManagementController extends Controller
{
    /**
     * EnvironmentalManagementController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:application.environmental-management.chimney-gas', [
            'only' => [
                'index', 'disable_notify', 'chimney_new_data',
            ],
        ]);

        $this->middleware('permission:application.environmental-management.machines', [
            'only' => [
                'machines', 'store_machine', 'update_machine', 'delete_machine',
            ],
        ]);

        $this->middleware('permission:application.environmental-management.binnacle-omff.registry.p0xx', [
            'only' => [
                'p0xx', 'p0xx_store',
            ],
        ]);

        $this->middleware('permission:application.environmental-management.binnacle-omff.registry.hl1', [
            'only' => [
                'hl1', 'hl1_store',
            ],
        ]);

        $this->middleware('permission:application.environmental-management.binnacle-omff.management', [
            'only' => [
                'management', 'p0xx_details', 'hl1_details',
            ],
        ]);
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $chimey1 = Sensor::where('machine', 'CHIMEY_1')
            ->where('created_at', '>', Carbon::now()->subHours(12)->toDateTimeString())
            ->pluck('record')
            ->toArray();

        $chimey2 = Sensor::where('machine', 'CHIMEY_2')
            ->where('created_at', '>', Carbon::now()->subHours(12)->toDateTimeString())
            ->pluck('record')
            ->toArray();

        $chimey_labels = Sensor::where('machine', 'CHIMEY_1')
            ->where('created_at', '>', Carbon::now()->subHours(12)->toDateTimeString())
            ->pluck('created_at')
            ->toArray();

        $gas = Sensor::where('machine', 'GAS')
            ->where('created_at', '>', Carbon::now()->subHours(12)->toDateTimeString())
            ->pluck('record')
            ->toArray();

        $gas_labels = Sensor::where('machine', 'GAS')
            ->where('created_at', '>', Carbon::now()->subHours(12)->toDateTimeString())
            ->pluck('created_at')
            ->toArray();

        $notify = NotificationSystem::where('application', 'collect-sensors')->pluck('state')->first();

        $gasArray = [];
        foreach ($gas as $index => $row) {
            if ($index == 0) {
                $gasArray[] = round(0);
            } else {
                $gasArray[] = round($row - $gas[$index - 1], 2);
            }
        }

        return Inertia::render('Applications/EnvironmentalManagement/Index', [
            'chimey1' => $chimey1,
            'chimey2' => $chimey2,
            'chimey_labels' => $chimey_labels,
            'gas' => $gasArray,
            'gas_labels' => $gas_labels,
            'notify' => boolval($notify),
        ]);
    }

    /**
     * show machines list of binnacle
     *
     * @return Response
     */
    public function machines(): Response
    {
        $machines = MachineBinnacle::all();

        return Inertia::render('Applications/EnvironmentalManagement/Machines', [
            'machines' => $machines,
        ]);
    }

    /**
     * store new machine
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store_machine(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'reference' => 'required|string',
            'brand' => 'required|string',
            'btu_tc' => 'required|numeric',
            'kcal_tc' => 'required|numeric',
            'type' => 'required|string',
        ])->validate();

        try {
            MachineBinnacle::create($request->all());

            return response()->json(MachineBinnacle::all(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * update
     *
     * @param  mixed  $request
     * @param  mixed  $id
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update_machine(Request $request, $id): JsonResponse
    {
        Validator::make($request->all(), [
            'reference' => 'required|string',
            'brand' => 'required|string',
            'btu_tc' => 'required|numeric',
            'kcal_tc' => 'required|numeric',
            'type' => 'required|string',
        ])->validate();

        try {
            MachineBinnacle::find($id)->update($request->all());
            $data = MachineBinnacle::all();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete_machine($id): JsonResponse
    {
        try {
            MachineBinnacle::destroy($id);

            $data = MachineBinnacle::all();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * p0xx form
     *
     * @return Response
     */
    public function p0xx(): Response
    {
        $machines = MachineBinnacle::where('type', 'P')->orderBy('reference', 'asc')->get();
        $operators = User::where('occupation', 'operario')->orderBy('name', 'asc')->get();

        return Inertia::render('Applications/EnvironmentalManagement/Binnacle/Registry/P0XX', [
            'machines' => $machines,
            'operators' => $operators,
        ]);
    }

    /**
     * hl1 form
     *
     * @return Response
     */
    public function hl1(): Response
    {
        $machines = MachineBinnacle::where('type', 'H')->orderBy('reference', 'asc')->get();
        $operators = User::where('occupation', 'operario')->orderBy('name', 'asc')->get();

        return Inertia::render('Applications/EnvironmentalManagement/Binnacle/Registry/HL1', [
            'machines' => $machines,
            'operators' => $operators,
        ]);
    }

    /**
     * machine px00 store data
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function p0xx_store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'machine_id' => 'required',
            'date' => 'required|date',
            'workshift' => 'required',
            'tb' => 'required|min:0|numeric',
            'rz' => 'required|min:0|numeric',
            'vz' => 'required|min:0|numeric',
            'z' => 'required|min:0|numeric',
            'operator_id' => 'required',
        ])->validate();
        try {
            Px00Binnacle::create([
                'machine_id' => $request->machine_id,
                'date' => $request->date,
                'workshift' => $request->workshift,
                'tb' => $request->tb,
                'rz' => $request->rz,
                'vz' => $request->vz,
                'z' => $request->z,
                'operator_id' => $request->operator_id,
                'maintenance' => $request->maintenance,
                'type_maintenance' => $request->type_maintenance,
                'maintenance_operator_id' => $request->maintenance_operator_id,
                'observations' => $request->observations,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json('record created successfully', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * machine px00 store data
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function hl1_store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'machine_id' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'operator_id' => 'required',
            'ingots' => 'required|min:0|numeric',
            'filter_pressure' => 'required|min:0|numeric',

        ])->validate();
        try {
            Hl1Binnacle::create([
                'machine_id' => $request->machine_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'operator_id' => $request->operator_id,
                'ingots' => $request->ingots,
                'filter_pressure' => $request->filter_pressure,
                'observations' => $request->observations,
                'created_by' => Auth::id(),
            ]);

            return response()->json('record created successfully', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * management dashboard
     *
     * @return Response
     */
    public function management(): Response
    {
        $hl1_datatable = Hl1Binnacle::select(
            DB::raw('CONVERT(date, date) as date'),
            DB::raw('count(*) as quantity')
        )->groupBy('date')
            ->get();

        $p0xx_datatable = Px00Binnacle::select(
            DB::raw('CONVERT(date, date) as date'),
            DB::raw('count(*) as quantity ')
        )->groupBy('date')
            ->get();

        $operational_load_p0xx = Px00Binnacle::with('machine')
            ->where('date', '>', Carbon::now()->subDays(15)->toDateTimeString())
            ->orderBy('date')
            ->select('machine_id',
                DB::raw('CONVERT(date, date) as date'),
                DB::raw('sum(tb) as tb'),
                DB::raw('sum(rz) as rz'),
                DB::raw('sum(vz) as vz'),
                DB::raw('sum(z) as z'),
            )->groupBy('date', 'machine_id')->get();

        $operational_load_hl1 = Hl1Binnacle::with('machine')
            ->where('date', '>', Carbon::now()->subDays(15)->toDateTimeString())
            ->orderBy('date')
            ->select('machine_id',
                DB::raw('CONVERT(date, date) as date'),
                DB::raw('sum(ingots) as ingots'),
            )
            ->groupBy('date', 'machine_id')
            ->get();

        $filters_hl1 = Hl1Binnacle::with('machine')
            ->where('date', '>', Carbon::now()->subDays(15)->toDateTimeString())
            ->whereNotNull('filter_pressure')
            ->orderBy('date')
            ->select('machine_id',
                DB::raw('CONVERT(date, date) as date'),
                DB::raw('sum(filter_pressure) as filters'),
                DB::raw('count(*) as quantity'),
            )
            ->groupBy('date', 'machine_id')
            ->get();

        $opr_load_p0xx = [];
        $opr_load_hl1 = [];
        $fil_hl1 = [];

        foreach ($operational_load_p0xx as $value) {
            $opr_load_p0xx[$value->machine->reference]['labels'][] = $value->date;
            $opr_load_p0xx[$value->machine->reference]['values'][] = round(round((($value->tb * 25) / 17) + (($value->rz * 50) / 8.3) + (($value->vz * 15) / 14) + (($value->z * 10) / 3), 2) / 3, 2);
        }

        foreach ($operational_load_hl1 as $value) {
            $opr_load_hl1[$value->machine->reference]['labels'][] = $value->date;
            $opr_load_hl1[$value->machine->reference]['values'][] = round((($value->ingots * 100) / 202), 2);
        }

        foreach ($filters_hl1 as $value) {
            $fil_hl1[$value->machine->reference]['labels'][] = $value->date;
            $fil_hl1[$value->machine->reference]['values'][] = round(($value->filters / $value->quantity), 2);
        }

        $p0xx_datasets = [];
        $hl1_datasets = [];
        $hl1_filters_datasets = [];

        $p0xx_labels = [];
        $hl1_labels = [];
        $hl1_filters_labels = [];

        $colors = [
            '#3160D8',
            '#D7263D',
            '#0197F6',
            '#372772',
            '#EE6352',
            '#9AD4D6',
            '#B8F3FF',
            '#58508D',
            '#003F5C',
        ];

        $ixd_col = 0;
        foreach ($opr_load_p0xx as $key => $value) {
            $result = [
                'label' => $key,
                'data' => $value['values'],
                'borderWidth' => 2,
                'borderColor' => $colors[$ixd_col],
                'backgroundColor' => $colors[$ixd_col],
                'pointBorderColor' => $colors[$ixd_col],
                'fill' => false,
            ];
            $p0xx_datasets[] = $result;

            foreach ($value['labels'] as $label) {
                $p0xx_labels[] = $label;
            }
            $ixd_col++;
        }
        $p0xx_labels = array_values(array_unique($p0xx_labels));

        $ixd_col2 = 0;
        foreach ($opr_load_hl1 as $key => $value) {
            $result = [
                'label' => $key,
                'data' => $value['values'],
                'borderWidth' => 2,
                'borderColor' => $colors[$ixd_col2],
                'backgroundColor' => $colors[$ixd_col2],
                'pointBorderColor' => $colors[$ixd_col2],
                'fill' => false,
            ];
            $hl1_datasets[] = $result;

            foreach ($value['labels'] as $label) {
                $hl1_labels[] = $label;
            }
            $ixd_col2++;
        }
        $hl1_unique_labels = array_unique($hl1_labels);

        $ixd_col3 = 0;
        foreach ($fil_hl1 as $key => $value) {
            $result = [
                'label' => $key,
                'data' => $value['values'],
                'borderWidth' => 2,
                'borderColor' => $colors[$ixd_col3],
                'backgroundColor' => $colors[$ixd_col3],
                'pointBorderColor' => $colors[$ixd_col3],
                'fill' => false,
            ];
            $hl1_filters_datasets[] = $result;

            foreach ($value['labels'] as $label) {
                $hl1_filters_labels[] = $label;
            }
            $ixd_col3++;
        }
        $filters_hl1_unique_labels = array_unique($hl1_filters_labels);

        return Inertia::render('Applications/EnvironmentalManagement/Binnacle/Management', [
            'hl1_datatable' => $hl1_datatable,
            'p0xx_datatable' => $p0xx_datatable,
            'operational_load_p0xx' => $opr_load_p0xx,
            'p0xx_datasets' => $p0xx_datasets,
            'p0xx_labels' => $p0xx_labels,
            'hl1_datasets' => $hl1_datasets,
            'hl1_labels' => $hl1_unique_labels,
            'filters_hl1_datasets' => $hl1_filters_datasets,
            'filters_hl1_labels' => $filters_hl1_unique_labels,
        ]);
    }

    /**
     * p0xx_details
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function p0xx_details(Request $request): JsonResponse
    {
        try {
            $data = Px00Binnacle::where('date', $request->date)
                ->with('machine')
                ->select('machine_id', 'workshift', 'date',
                    DB::raw('sum(tb) as tb'),
                    DB::raw('sum(rz) as rz'),
                    DB::raw('sum(vz) as vz'),
                    DB::raw('sum(z) as z')
                )->groupBy('machine_id', 'workshift', 'date')
                ->get();

            $data_workshift = [
                'workshift_1' => [],
                'workshift_2' => [],
                'workshift_3' => [],
                'date' => $request->date,
            ];

            foreach ($data as $value) {
                $data_workshift['workshift_'.$value->workshift][] = $value;
            }

            return response()->json($data_workshift, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * hl1_details
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function hl1_details(Request $request): JsonResponse
    {
        try {
            $data = Hl1Binnacle::where('date', $request->date)
                ->with('machine')
                ->select('machine_id', 'date',
                    DB::raw('sum(ingots) as ingots')
                )->groupBy('machine_id', 'date')
                ->first();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function disable_notify(Request $request): JsonResponse
    {
        NotificationSystem::where('application', 'collect-sensors')->update([
            'state' => ! $request->state,
            'modified_by' => auth()->user()->id,
        ]);

        return response()->json([
            'msg' => ! $request->state == true ? 'activado' : 'desactivado',
            'state' => ! $request->state,
        ], 200);
    }

    /**
     * get new chimey data by date range picker
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function chimney_new_data(Request $request): JsonResponse
    {
        try {
            if ($request->type === 'chimney') {
                $chimey1 = Sensor::where('machine', 'CHIMEY_1')
                    ->whereBetween('created_at', [Carbon::parse($request->startDate), Carbon::parse($request->endDate)])
                    ->pluck('record')
                    ->toArray();

                $chimey2 = Sensor::where('machine', 'CHIMEY_2')
                    ->whereBetween('created_at', [Carbon::parse($request->startDate), Carbon::parse($request->endDate)])
                    ->pluck('record')
                    ->toArray();

                $labels = Sensor::where('machine', 'CHIMEY_1')
                    ->whereBetween('created_at', [Carbon::parse($request->startDate), Carbon::parse($request->endDate)])
                    ->pluck('created_at')
                    ->toArray();

                return response()->json([
                    'chimey1' => $chimey1,
                    'chimey2' => $chimey2,
                    'labels' => $labels,
                ], 200);
            } else {
                $gas = Sensor::where('machine', 'GAS')
                    ->whereBetween('created_at', [Carbon::parse($request->startDate), Carbon::parse($request->endDate)])
                    ->pluck('record')
                    ->toArray();

                $labels = Sensor::where('machine', 'GAS')
                    ->whereBetween('created_at', [Carbon::parse($request->startDate), Carbon::parse($request->endDate)])
                    ->pluck('created_at')
                    ->toArray();

                $gasArray = [];
                foreach ($gas as $index => $row) {
                    if ($index == 0) {
                        array_push($gasArray, round(0));
                    } else {
                        array_push($gasArray, round($row - $gas[$index - 1], 2));
                    }
                }

                return response()->json([
                    'gas' => $gasArray,
                    'labels' => $labels,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}

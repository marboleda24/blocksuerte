<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceRequestExport;
use App\Mail\MaintenanceMail;
use App\Models\MaintenanceActivity;
use App\Models\MaintenanceAsset;
use App\Models\MaintenanceAssetClassification;
use App\Models\MaintenanceAssetFile;
use App\Models\MaintenanceAssetResume;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestLog;
use App\Models\MaintenanceWorkCenter;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceWorkType;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MaintenanceController extends Controller
{
    /**
     * MaintenanceController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:application.maintenance.create', [
            'only' => [
                'create', 'store',
            ],
        ]);

        $this->middleware('permission:application.maintenance.my-requests', [
            'only' => [
                'my_requests', 'view', 'add_comment',
            ],
        ]);

        $this->middleware('permission:application.maintenance.work-orders', [
            'only' => [
                'store_work_order', 'view_work_order', 'store_activity', 'store_conclusion_activity', 'finalize_work_order',
                'finalize_maintenance',
            ],
        ]);

        $this->middleware('permission:application.maintenance.masters.assets', [
            'only' => [
                'assets', 'assets_store', 'assets_update', 'assets_destroy',
            ],
        ]);

        $this->middleware('permission:application.maintenance.masters.asset-classifications', [
            'only' => [
                'assets_classifications', 'assets_classifications_store', 'assets_classifications_update', 'assets_classifications_destroy',
            ],
        ]);

        $this->middleware('permission:application.maintenance.masters.work-centers', [
            'only' => [
                'work_centers', 'work_centers_store', 'work_centers_update', 'work_centers_destroy',
            ],
        ]);
    }

    /**
     * @return Response
     */
    public function assets(): Response
    {
        $assets = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'files', 'resume')
            ->orderBy('name')
            ->get();

        $work_centers = MaintenanceWorkCenter::all();

        $asset_classifications = MaintenanceAssetClassification::all();

        $users = User::orderBy('name')
            ->whereNotIn('occupation', ['vendedor', 'mantenimiento', 'inspeccion', 'operario'])
            ->get();

        return Inertia::render('Applications/Maintenance/Assets', [
            'assets' => $assets,
            'work_centers' => $work_centers,
            'asset_classifications' => $asset_classifications,
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function assets_store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $asset = new MaintenanceAsset($request->all());
            $asset->created_by = Auth::id();
            $asset->save();

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "maintenance/assets/{$asset->code}/files";

                    $full_path = storage_path() . "/app/maintenance/assets/{$asset->code}/files/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::putFileAs("maintenance/assets/{$asset->code}/files/", $file, $filename);

                    if (Storage::exists($storagePath)) {
                        $asset->files()->create([
                            'path' => $storagePath,
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            $assets = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'files')->get();

            DB::commit();

            return response()->json($assets, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $assets = MaintenanceAsset::all();

        return Inertia::render('Applications/Maintenance/Create', [
            'assets' => $assets,
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function assets_destroy($id): JsonResponse
    {
        MaintenanceAsset::destroy($id);
        $assets = MaintenanceAsset::with('classification', 'work_center', 'createdBy')->get();

        return response()->json($assets, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function assets_update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $asset = MaintenanceAsset::find((int)$id);
            $asset->update($request->except('created_by', 'classification', 'work_center', 'files', 'created_at', 'updated_at', 'last_revision'));

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "maintenance/assets/{$asset->code}/files";

                    $full_path = storage_path() . "/app/maintenance/assets/{$asset->code}/files/$filename";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::putFileAs("maintenance/assets/{$asset->code}/files", $file, $filename);

                    if (Storage::exists($storagePath)) {
                        $asset->files()->create([
                            'path' => $storagePath,
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }
            DB::commit();

            $assets = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'files')->get();

            return response()->json($assets, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $value
     * @return JsonResponse
     */
    public function assets_validate_code($value): JsonResponse
    {
        $value = MaintenanceAsset::where('code', $value)->count();

        return response()->json(!$value > 0, 200);
    }

    /**
     * @param $value
     * @return JsonResponse
     */
    public function assets_validate_name($value): JsonResponse
    {
        $value = MaintenanceAsset::where('name', $value)->count();

        return response()->json(!$value > 0, 200);
    }

    /**
     * @return Response
     */
    public function asset_classifications(): Response
    {
        $asset_classifications = MaintenanceAssetClassification::with('created_by')->get();

        return Inertia::render('Applications/Maintenance/AssetClassification', [
            'asset_classifications' => $asset_classifications,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function asset_classification_store(Request $request): JsonResponse
    {
        try {
            $asset_classification = new MaintenanceAssetClassification($request->all());
            $asset_classification->created_by = Auth::id();
            $asset_classification->save();

            $asset_classifications = MaintenanceAssetClassification::with('created_by')->get();

            return response()->json($asset_classifications, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function asset_classifications_destroy($id): JsonResponse
    {
        MaintenanceAssetClassification::destroy($id);
        $asset_classifications = MaintenanceAssetClassification::with('created_by')->get();

        return response()->json($asset_classifications, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function asset_classifications_update(Request $request, $id): JsonResponse
    {
        MaintenanceAssetClassification::find($id)
            ->update([
                'name' => $request->name,
                'comments' => $request->comments,
            ]);

        $asset_classifications = MaintenanceAssetClassification::with('created_by')->get();

        return response()->json($asset_classifications, 200);
    }

    /**
     * @param $value
     * @return JsonResponse
     */
    public function asset_classifications_validate_name($value): JsonResponse
    {
        $value = MaintenanceAssetClassification::where('code', $value)->count();

        return response()->json(!$value > 0, 200);
    }

    /**
     * @return Response
     */
    public function work_centers(): Response
    {
        $work_centers = MaintenanceWorkCenter::with('created_by')->get();

        return Inertia::render('Applications/Maintenance/WorkCenter', [
            'work_centers' => $work_centers,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function work_center_store(Request $request): JsonResponse
    {
        try {
            $work_center = new MaintenanceWorkCenter($request->all());
            $work_center->created_by = Auth::id();
            $work_center->save();

            $work_centers = MaintenanceWorkCenter::with('created_by')->get();

            return response()->json($work_centers, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function work_center_destroy($id): JsonResponse
    {
        MaintenanceWorkCenter::destroy($id);
        $work_centers = MaintenanceWorkCenter::with('created_by')->get();

        return response()->json($work_centers, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function work_center_update(Request $request, $id): JsonResponse
    {
        MaintenanceWorkCenter::find($id)
            ->update([
                'name' => $request->name,
                'comments' => $request->comments,
            ]);

        $work_centers = MaintenanceWorkCenter::with('created_by')->get();

        return response()->json($work_centers, 200);
    }

    /**
     * @param $value
     * @return JsonResponse
     */
    public function work_center_validate_name($value): JsonResponse
    {
        $value = MaintenanceWorkCenter::where('name', $value)->count();

        return response()->json(!$value > 0, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $new = MaintenanceRequest::create([
                'applicant_id' => auth()->user()->id,
                'asset_id' => $request->asset,
                'planning_date' => Carbon::parse($request->date),
                'description' => $request->description,
                'type' => $request->type,
                'area_coordinator_id' => $request->area_coordinator_id,
                'state' => '1',
            ]);

            $coordinator = User::find($request->area_coordinator_id);

            if ($coordinator && $coordinator->mail) {
                Mail::to($coordinator->mail)
                    ->send(new MaintenanceMail($new->applicant->name, $new->asset->name));
            }
            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * list the requirements by user
     *
     * @return Response
     */
    public function my_requests(): Response
    {
        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.maintenance.show-all')) {
            $requests = MaintenanceRequest::with('applicant', 'asset')
                ->get();
        } else {
            $requests = MaintenanceRequest::with('applicant', 'asset')
                ->where('applicant_id', '=', auth()->user()->id)
                ->get();
        }

        return Inertia::render('Applications/Maintenance/MyRequests', [
            'requests' => $requests,
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function view($id): Response
    {
        $data = MaintenanceRequest::with('applicant', 'asset', 'log.user', 'work_orders.updatedby', 'work_orders.createdby')->find($id);
        $technicians = User::orderBy('name', 'asc')->where('occupation', 'mantenimiento')->get();

        return Inertia::render('Applications/Maintenance/Show', [
            'data' => $data,
            'technicians' => $technicians,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_comment(Request $request): JsonResponse
    {
        try {
            MaintenanceRequestLog::create([
                'request_id' => $request->id,
                'description' => $request->comment,
                'type' => 'comment',
                'created_by' => auth()->user()->id,
            ]);

            $data = MaintenanceRequest::with('work_orders', 'log.user', 'asset', 'applicant')
                ->find($request->id);

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_work_order(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $maintenance_request = MaintenanceRequest::find($request->request_id);
            $maintenance_request->update(['state' => '2']);

            $maintenance_request->work_orders()
                ->create([
                    'request_id' => $request->request_id,
                    'description' => $request->description,
                    'state' => '1',
                    'cost' => 0,
                    'type' => $request->type,
                    'assigned_to' => intval($request->assigned_to),
                    'created_by' => auth()->user()->id,
                ]);

            $maintenance_request->log()->create([
                'description' => 'creo una orden de trabajo',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $data = MaintenanceRequest::with('work_orders', 'log.user', 'asset', 'applicant')
                ->find($request->request_id);

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_work_order(Request $request)
    {
        DB::beginTransaction();
        try {
            $work_order = MaintenanceWorkOrder::find($request->id);
            $work_order->update([
                'type' => $request->form['type'],
                'assigned_to' => $request->form['assigned_to'],
                'description' => $request->form['description'],
                'updated_by' => Auth::id()
            ]);

            $maintenance = MaintenanceRequest::find($request->request_id);
            $maintenance->log()->create([
                'description' => "Actualizo la orden de trabajo #{$work_order->consecutive}, Justificación: {$request->justify}",
                'type' => 'log',
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function view_work_order($id): Response
    {
        $data = MaintenanceWorkOrder::with('updatedby', 'createdby', 'assignedto', 'activities',
            'activities.work_type', 'activities.assignedto', 'request', 'request.applicant', 'request.asset', 'request.applicant')
            ->find($id);

        $technicians = User::orderBy('name')
            ->where('occupation', 'mantenimiento')
            ->get();

        $work_types = MaintenanceWorkType::all();

        return Inertia::render('Applications/Maintenance/WorkOrder', [
            'data' => $data,
            'technicians' => $technicians,
            'work_types' => $work_types,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_activity(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            MaintenanceActivity::create([
                'work_order_id' => $request->work_order_id,
                'work_type_id' => $request->work_type_id,
                'assigned_to' => $request->assigned_to,
                'state' => '1',
                'cost' => 0,
                'description' => $request->description,
                'created_by' => auth()->user()->id,
            ]);

            $maintenance_request = MaintenanceRequest::find($request->request_id);
            $maintenance_request->log()->create([
                'description' => 'asigno una actividad',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $data = MaintenanceActivity::with('work_type', 'assignedto')
                ->where('work_order_id', '=', $request->work_order_id)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_conclusion_activity(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            MaintenanceActivity::find($request->id)
                ->update([
                    'state' => '3',
                    'cost' => $request->cost,
                    'conclusion' => $request->conclusion,
                    'finish_date' => Carbon::now(),
                ]);

            $maintenance_request = MaintenanceRequest::find($request->request_id);
            $maintenance_request->log()->create([
                'description' => 'concluyo una actividad',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $data = MaintenanceActivity::with('work_type', 'assignedto')
                ->where('work_order_id', '=', $request->work_order_id)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function finalize_work_order($id): JsonResponse
    {
        try {
            MaintenanceWorkOrder::find($id)
                ->update([
                    'state' => '3',
                    'closing_date' => Carbon::now(),
                ]);

            return response()->json('3', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function finalize_maintenance($id): JsonResponse
    {
        try {
            MaintenanceRequest::find($id)
                ->update([
                    'state' => '4',
                    'closing_date' => Carbon::now(),
                ]);

            $data = MaintenanceRequest::with('work_orders', 'log.user', 'asset', 'applicant')
                ->find($id);

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_state(Request $request): JsonResponse
    {
        MaintenanceRequest::find($request->id)
            ->update([
                'state' => '3',
                'closing_date' => Carbon::now(),
            ]);

        $data = MaintenanceRequest::with('work_orders', 'log.user', 'asset', 'applicant')
            ->find($request->id);

        return response()->json($data, 200);
    }

    /**
     * @return Response
     */
    public function work_order(): Response
    {
        $work_orders = MaintenanceWorkOrder::with('request.asset', 'updatedby', 'createdby', 'assignedto', 'activities')->get();

        return Inertia::render('Applications/Maintenance/WorkOrders', [
            'work_orders' => $work_orders,
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function asset_report($id): Response
    {
        $maintenances = MaintenanceRequest::where('asset_id', '=', $id)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        $asset = MaintenanceAsset::with('classification', 'work_center', 'maintenances.applicant', 'maintenances.work_orders')
            ->find($id);

        return Inertia::render('Applications/Maintenance/AssetReport', [
            'maintenances' => $maintenances,
            'asset' => $asset,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $maintenance_request = MaintenanceRequest::find($request->id);
            $maintenance_request->state = '0';
            $maintenance_request->save();

            $maintenance_request->log()->create([
                'description' => "anulo la solicitud, Justificación: {$request->justify}",
                'type' => 'log',
                'created_by' => auth()->user()->id,
            ]);

            foreach ($maintenance_request->work_orders as $work_order) {
                $work_order->state = 0;
                $work_order->save();
            }

            DB::commit();

            if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.maintenance.show-all')) {
                $requests = MaintenanceRequest::with('applicant', 'asset')
                    ->get();
            } else {
                $requests = MaintenanceRequest::with('applicant', 'asset')
                    ->where('applicant_id', '=', auth()->user()->id)
                    ->get();
            }

            return response()->json($requests, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function report()
    {
        $assets_more_maintenance = MaintenanceAsset::withCount('maintenances')
            ->orderby('maintenances_count', 'desc')
            ->take(7)
            ->get();

        $assets_more_maintenance = $assets_more_maintenance->where('maintenances_count', '>', 0)
            ->toArray();

        $assets_more_maintenance = [
            'labels' => array_column((array)$assets_more_maintenance, 'name'),
            'values' => array_column((array)$assets_more_maintenance, 'maintenances_count'),
        ];

        $maintenance_types = MaintenanceRequest::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get()->toArray();

        $maintenance_types = [
            'labels' => array_column($maintenance_types, 'type'),
            'values' => array_column($maintenance_types, 'total'),
        ];

        $time_close = MaintenanceRequest::whereIn('state', ['3', '4'])
            ->whereNotNull('closing_date')
            ->get()
            ->map(function ($row) {
                return $row->closing_date->diffInHours($row->created_at);
            });

        $time_close = count($time_close) > 0 ? round($time_close->sum() / count($time_close)) : 0;

        return Inertia::render('Applications/Maintenance/Report', [
            'assets_more_maintenance' => $assets_more_maintenance,
            'maintenance_types' => $maintenance_types,
            'time_close' => $time_close,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refuse(Request $request)
    {
        $maintenance = MaintenanceRequest::find($request->id);
        $maintenance->update([
            'state' => '5',
            'closing_date' => Carbon::now(),
        ]);

        $maintenance->log()->create([
            'description' => "Rechazo la solicitud, Justificación: {$request->justify}",
            'type' => 'log',
            'created_by' => auth()->user()->id,
        ]);

        $maintenance->save();

        $data = MaintenanceRequest::with('work_orders', 'log.user', 'asset', 'applicant')
            ->find($request->id);

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel_work_order(Request $request)
    {
        DB::beginTransaction();
        try {
            $work_order = MaintenanceWorkOrder::find($request->id);
            $work_order->update([
                'state' => '0',
                'closing_date' => Carbon::now(),
            ]);

            $work_order->cancel_activities();

            $maintenance = MaintenanceRequest::find($request->request_id);
            $maintenance->log()->create([
                'description' => "Anulo la orden de trabajo #{$work_order->consecutive}, Justificación: {$request->justify}",
                'type' => 'log',
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel_activity(Request $request)
    {
        DB::beginTransaction();
        try {
            $activity = MaintenanceActivity::with('work_order')->find($request->id);
            $activity->update([
                'state' => '0',
                'finish_date' => Carbon::now(),
                'conclusion' => "actividad anulada, Justificación: {$request->justify}",
            ]);

            $maintenance = MaintenanceRequest::find($activity->work_order->request_id);
            $maintenance->log()->create([
                'description' => "Anulo la actividad #{$activity->consecutive} de la orden de trabajo {$activity->work_order->consecutive}, Justificación: {$request->justify}",
                'type' => 'log',
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return BinaryFileResponse
     */
    public function download_report()
    {
        return Excel::download(new MaintenanceRequestExport, 'maintenance-requests.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_file(Request $request)
    {
        $file = MaintenanceAssetFile::find($request->id);

        Storage::delete($file->path);

        if (!Storage::exists($file->path)) {
            $file->delete();
            DB::commit();

            $asset = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'files')
                ->find($file->asset_id);

            return response()->json($asset, 200);
        } else {
            DB::rollBack();

            return response()->json('delete file error', 500);
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        $file = MaintenanceAssetFile::find($request->id);

        return response()->download(storage_path('app/' . $file->path), $file->name);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resume_update(Request $request): JsonResponse
    {
        try {
            MaintenanceAssetResume::updateOrCreate([
                'asset_id' => $request->asset_id
            ], [
                'model' => $request->model,
                'brand' => $request->brand,
                'power' => $request->power,
                'amperage' => $request->amperage,
                'voltage' => $request->voltage,
                'frequency' => $request->frequency,
                'watts' => $request->watts,
                'rpm' => $request->rpm,
                'maintenance_frequency' => $request->maintenance_frequency,
                'dimension' => $request->dimension,
                'preventive_maintenance_description' => $request->preventive_maintenance_description,
                'precautions' => $request->precautions,
                'created_id' => Auth::id(),
                'updated_id' => Auth::id()
            ]);

            $assets = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'files', 'resume')
                ->orderBy('name')
                ->get();

            return response()->json($assets, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $asset_id
     * @return Mpdf
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function resume_pdf($asset_id)
    {
        ini_set("pcre.backtrack_limit", "5000000000");

        $asset = MaintenanceAsset::with('classification', 'work_center', 'createdBy', 'resume', 'files', 'maintenances')->find($asset_id);

        $pdf = $this->initMPDF();
        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI Estrada Velasquez & CIA SAS - {$asset->name}");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->SetWatermarkText($asset->name);
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;
        $pdf->SetHTMLHeader(View::make('pdfs.asset.header', compact('asset')));
        $pdf->SetHTMLFooter(View::make('pdfs.asset.footer'));
        $pdf->WriteHTML(View::make('pdfs.asset.template', compact('asset')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    private function initMPDF()
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-P',
            'fontdata' => $fontData + [
                    'Roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ],
                ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 30,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/asset/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @return Response
     */
    public function maintenance_schedule_preventive()
    {
        return inertia::render('Applications/Maintenance/SchedulePreventive');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function maintenance_schedule_search(Request $request)
    {
        try {
            $date = explode(' - ', $request->date);

            $query = DB::table('V_MAINTENANCE_SCHEDULE')
                ->whereBetween('next', [
                    Carbon::parse($date[0])->format('Y-m-d'),
                    Carbon::parse($date[1])->format('Y-m-d'),
                ])
                ->get()
                ->groupby('week');

            return response()->json($query, 200);
        } catch (exception $e) {
            return response()->json($e, 500);

        }
    }

    /**
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function pdf_preventive($date_range)
    {
        $date = explode(' - ', $date_range);

        $query = DB::table('V_MAINTENANCE_SCHEDULE')
            ->whereBetween('next', [
                Carbon::parse($date[0])->format('Y-m-d'),
                Carbon::parse($date[1])->format('Y-m-d'),
            ])
            ->orderBy('next', 'asc')
            ->get()
            ->groupby('week');

        $pdf = $this->maintenance_schedule_preventivePDF();
        $pdf->SetHTMLHeader(View::make('pdfs.maintenance.preventive.header', compact('date')));
        $pdf->SetHTMLFooter(View::make('pdfs.maintenance.preventive.footer'));
        $pdf->WriteHTML(View::make('pdfs.maintenance.preventive.template', compact('query')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    public function maintenance_schedule_preventivePDF()
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-L',
            'fontdata' => $fontData + [
                    'Roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ],
                ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 33,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/maintenance/preventive/styles.css')), HTMLParserMode::HEADER_CSS);
        $pdf->SetProtection(array('print'));
        $pdf->SetTitle("CI ESTRADA VELASQUEZ & CIA SAS ");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->showWatermarkText = false;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }
}

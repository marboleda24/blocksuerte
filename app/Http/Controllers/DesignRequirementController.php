<?php

namespace App\Http\Controllers;

use App\Models\Blueprint;
use App\Models\Brand;
use App\Models\DesignRequirementArt;
use App\Models\DesignRequirementFile;
use App\Models\DesignRequirementHeader;
use App\Models\DesignRequirementLog;
use App\Models\DesignRequirementProduct;
use App\Models\DesignRequirementProposal;
use App\Models\DesignRequirementProposalLog;
use App\Models\DesignRequirementProposalVersion;
use App\Models\EncoderCode;
use App\Models\EncoderMeasurement;
use App\Models\EncoderProductType;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DesignRequirementController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $requirements = auth()->user()->hasRole('super-admin') || \auth()->user()->hasAnyPermission(['design-requirement.designer','design-requirement.3d-gestion', 'design-requirement.coordinator'])
            ? DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->get()
            : DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->where('seller_id', '=', Auth::id())
                ->get();

        return Inertia::render('Applications/DesignRequirements/Requirements', [
            'requirements' => $requirements,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header = new DesignRequirementHeader($request->except('files'));
            $header->created_by = Auth::id();
            $header->save();

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "design_requirements/{$header->id}";

                    $full_path = storage_path() . "/app/design_requirements/{$header->id}/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::put("design_requirements/{$header->id}", $file);

                    if (Storage::exists($storagePath)) {
                        $header->files()->create([
                            'name' => $filename,
                            'path' => $storagePath,
                            'type' => 'support',
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            $header->logs()->create([
                'description' => 'creo un nuevo requerimiento',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $sellers = User::where('occupation', '=', 'vendedor')
            ->orderBy('name', 'asc')
            ->get();

        $product_types = EncoderProductType::whereIn('code', ['T', 'C', 'S'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Applications/DesignRequirements/Create', [
            'sellers' => $sellers,
            'product_types' => $product_types,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_product(Request $request)
    {
        try {
            $design_requirement = DesignRequirementHeader::find($request->id);
            $design_requirement->update([
                'product_type_code' => $request->product_type_code,
                'line_code' => $request->line_code,
                'subline_code' => $request->subline_code,
                'feature_code' => $request->feature_code,
                'material_id' => $request->material_id,
                'measurement_id' => $request->measurement_id,
            ]);

            $design_requirement->save();

            $requirement = DesignRequirementHeader::with(
                'customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer', 'logs.created_user',
                'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit', 'files', 'proposals.logs',
                'proposals.logs.created_user', 'proposals.material.material', 'proposals.blueprint'
            )->find($request->id);

            return response()->json($requirement, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        $header = DesignRequirementHeader::find($request->id);

        $header->update([
            'state' => '0',
        ]);

        $header->logs()->create([
            'description' => "anulo el requerimiento, JUSTIFICACIÓN: $request->justify",
            'created_by' => Auth::id(),
            'type' => 'log',
        ]);

        $requirements = auth()->user()->hasRole('super-admin')
            ? DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->get()
            : DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->where('seller_id', '=', Auth::id())
                ->get();

        return response()->json($requirements, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_brands(Request $request): JsonResponse
    {
        try {
            $brands = $request->customer_code
                ? Brand::whereIn('type', ['G', 'MP'])
                    ->where('customer_code', '=', $request->customer_code)
                    ->orWhereIn('customer_code', [null, ''])
                    ->orderBy('name', 'asc')
                    ->get()
                : Brand::where('type', 'G')
                    ->orWhereIn('customer_code', [null, ''])
                    ->orderBy('name', 'asc')->get();

            return response()->json($brands, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**f
     * @param Request $request
     * @return JsonResponse
     */
    public function get_measurements(Request $request): JsonResponse
    {
        try {
            $measurements = EncoderMeasurement::with('detail', 'detail.characteristic', 'detail.unit')
                ->where('line_code', '=', $request->line_code)
                ->where('subline_code', '=', $request->subline_code)
                ->get();

            return response()->json($measurements, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $name
     * @return JsonResponse
     */
    public function verify_brand($name): JsonResponse
    {
        $value = Brand::where('name', '=', $name)->count();

        return response()->json(!$value > 0, 200);
    }

    /**
     * @param $code
     * @return JsonResponse
     */
    public function product_info($code): JsonResponse
    {
        $code = EncoderCode::find($code);

        return response()->json($code, 200);
    }

    /**
     * @param $id
     * @return Response
     */
    public function design_requirement($id): Response
    {
        $requirement = DesignRequirementHeader::with(
            'customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer', 'logs.created_user',
            'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit', 'files', 'proposals.logs',
            'proposals.logs.created_user', 'proposals.material.material', 'proposals.blueprint', 'proposals.art.product'
        )->find($id);

        $designers = User::where('occupation', '=', 'diseñador')->get();

        $product_types = EncoderProductType::whereIn('code', ['T', 'P', 'C'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Applications/DesignRequirements/Requirement', [
            'requirement' => $requirement,
            'designers' => $designers,
            'product_types' => $product_types,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_comment(Request $request): JsonResponse
    {
        DesignRequirementLog::create([
            'design_requirement_id' => $request->id,
            'description' => $request->comment,
            'created_by' => Auth::id(),
            'type' => 'comment',
        ]);

        $logs = DesignRequirementLog::with('created_user')
            ->where('design_requirement_id', '=', $request->id)
            ->get();

        return response()->json($logs, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function proposal_add_comment(Request $request): JsonResponse
    {
        DesignRequirementProposalLog::create([
            'proposal_id' => $request->id,
            'description' => $request->comment,
            'created_by' => Auth::id(),
            'type' => 'comment',
        ]);

        $logs = DesignRequirementProposalLog::with('created_user')
            ->where('proposal_id', '=', $request->id)
            ->get();

        return response()->json($logs, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_brand(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header_old = DesignRequirementHeader::find($request->id);
            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);
            $header->brand_id = intval($request->brand);
            $header->save();

            $header->logs()->create([
                'description' => "Cambio la marca {$header_old->brand->name} por la marca {$header->brand->name}, JUSTIFICACIÓN: $request->justify",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();
            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);

            return response()->json([
                'brand' => $header->brand,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_designer(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header_old = DesignRequirementHeader::find($request->id);
            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);
            $header->assigned_designer_id = intval($request->designer);
            $header->save();

            $header->logs()->create([
                'description' => "Cambio la diseñadora {$header_old->assigned_designer->name} por {$header->assigned_designer->name}, JUSTIFICACIÓN: $request->justify",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);

            return response()->json([
                'designer' => $header->assigned_designer,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_state(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header_old = DesignRequirementHeader::find($request->id);
            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);
            $header->state = $request->state;
            $header->save();

            $header->logs()->create([
                'description' => "Cambio el estado de {$this->getState($header_old->state)} a {$this->getState($header->state)}, JUSTIFICACIÓN: $request->justify",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);

            return response()->json([
                'state' => $header->state,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $state
     * @return string|void
     */
    protected function getState($state)
    {
        switch ($state) {
            case '0':
                return 'Anulado';
            case '1':
                return 'Pendiente Revision';
            case '2':
                return 'Rechazado';
            case '3':
                return 'Asignado';
            case '4':
                return 'Iniciado';
            case '5':
                return 'Pendiente Planos';
            case '6':
                return 'Pendiente Render';
            case '7':
                return 'Finalizado';
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_measurement(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header_old = DesignRequirementHeader::with('measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit')->find($request->id);
            $header = DesignRequirementHeader::with('logs.created_user', 'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit')->find($request->id);
            $header->measurement_id = $request->measurement;
            $header->save();

            $old_measurement = denominationCreator($header_old->measurement->detail);
            $new_measurement = denominationCreator($header->measurement->detail);

            $header->logs()->create([
                'description' => "Cambio la medida de {$old_measurement} a {$new_measurement}, JUSTIFICACIÓN: $request->justify",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $header = DesignRequirementHeader::with('logs.created_user', 'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit')->find($request->id);

            return response()->json([
                'measurement' => $header->measurement,
                'logs' => $header->logs,
                'measure' => $new_measurement,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $req
     * @return JsonResponse
     */
    public function search_product(Request $req): JsonResponse
    {
        try {
            $queries = EncoderCode::with('measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit')
                ->where('description', 'LIKE', '%' . $req->q . '%')
                ->orWhere('code', 'LIKE', '%' . $req->q . '%')
                ->take(50)
                ->get();

            return response()->json($queries, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send_design(Request $request): JsonResponse
    {
        DesignRequirementHeader::find($request->id)->update([
            'state' => '1',
        ]);

        $requirements = auth()->user()->hasRole('super-admin')
            ? DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->get()
            : DesignRequirementHeader::with('customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer')
                ->where('seller_id', '=', Auth::id())
                ->get();

        return response()->json($requirements, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function assign_designer(Request $request): JsonResponse
    {
        $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);

        $header->update([
            'state' => '2',
            'assigned_designer_id' => $request->designer,
        ]);

        $header->save();

        $header->logs()->create([
            'description' => 'Asigno el requerimiento',
            'created_by' => Auth::id(),
            'type' => 'log',
        ]);

        return response()->json([
            'designer' => $header->assigned_designer,
            'logs' => $header->logs,
            'state' => $header->state,
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header = DesignRequirementHeader::find($request->id);

            $header->update([
                'state' => '6',
            ]);

            $header->logs()->create([
                'description' => "Rechazo el requerimiento, JUSTIFICACIÓN: {$request->justify}",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $header = DesignRequirementHeader::with('logs.created_user')->find($request->id);

            return response()->json([
                'state' => $header->state,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload_file(Request $request): JsonResponse
    {
        $design_requirement = DesignRequirementHeader::with('logs.created_user', 'files')->find($request->id);

        $filename = $request->file('file')->getClientOriginalName();
        $filename = str_replace(' ', '', $filename);

        $path = "design_requirements/{$request->id}";

        $full_path = storage_path() . "/app/design_requirement/{$request->id}/{$filename}";

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $storagePath = Storage::putFileAs("design_requirements", $request->file('file'), $filename);

        if (Storage::exists($storagePath)) {
            $design_requirement->files()
                ->create([
                    'path' => $storagePath,
                    'name' => $filename,
                    'type' => 'support',
                ]);

            $design_requirement->logs()->create([
                'description' => "subió el archivo: $filename",
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);
        } else {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'msg' => "error saving files: {$full_path}",
            ], 500);
        }

        $design_requirement = DesignRequirementHeader::with('logs.created_user', 'files')->find($request->id);

        return response()->json([
            'files' => $design_requirement->files,
            'logs' => $design_requirement->logs,
        ], 200);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        $file = $request->path;
        $name = substr($file, strrpos($file, '/') + 1);

        return response()->download(storage_path('app/' . $file), $name);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_file(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            Storage::delete($request->path);

            if (!Storage::exists($request->path)) {
                $file = DesignRequirementFile::find($request->file_id);
                $file->delete();

                $design_requirement = DesignRequirementHeader::find($request->id);
                $design_requirement->logs()->create([
                    'description' => "Elimino el archivo $file->name, JUSTIFICACIÓN: $request->justify",
                    'created_by' => Auth::id(),
                    'type' => 'log',
                ]);

                DB::commit();

                $design_requirement = DesignRequirementHeader::with('logs.created_user')->find($request->id);

                return response()->json([
                    'logs' => $design_requirement->logs,
                    'files' => $design_requirement->files,
                ], 200);
            } else {
                DB::rollBack();

                return response()->json('delete file error', 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function proposal_store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $header = DesignRequirementHeader::find($request->id);

            $proposal_id = $header->proposals()->create([
                'product_type_code' => $request->product_type_code,
                'line_code' => $request->line_code,
                'subline_code' => $request->subline_code,
                'feature_code' => $request->feature_code,
                'material_id' => $request->material_id,
                'measurement_id' => $request->measurement_id,
                'details' => $request->details,
                'created_by' => Auth::id(),
            ])->id;

            $proposal = DesignRequirementProposal::find($proposal_id);

            $file = $request->file('file_2d');
            $extension = $file->getClientOriginalExtension();
            $filename = "2D.$extension";

            $path = "design_requirements/{$header->id}/proposals/{$proposal_id}";

            $full_path = storage_path() . "/app/design_requirements/{$header->id}/proposals/{$proposal_id}/{$filename}";

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            $storagePath = Storage::putFileAs($path, $file, $filename);

            if (Storage::exists($storagePath)) {
                $proposal->path2D = $storagePath;
                $proposal->save();
            } else {
                DB::rollBack();

                return response()->json("error saving file: {$full_path}", 500);
            }

            $header->logs()->create([
                'description' => 'Creo una propuesta de diseño',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            if ($header->proposals->count() < 1) {
                $header->state = '3';
                $header->save();
            }

            $header = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.blueprint')
                ->find($request->id);

            DB::commit();

            return response()->json([
                'proposals' => $header->proposals,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function clone_proposal(Request $request){
        DB::beginTransaction();
        try {
            $header = DesignRequirementHeader::find($request->header_id);
            $proposal = DesignRequirementProposal::find($request->id);

            $new_proposal = $proposal->replicate()->fill([
                'created_by' => Auth::id(),
                'path2D' => null,
                'path3D' => null,
                'state' => '1',
                'blueprint_id' => null,
                'features_detail' => null,
            ]);

            $header->logs()->create([
                'description' => 'Creo una propuesta de diseño',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            $new_proposal->save();

            $header = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.blueprint')
                ->find($request->header_id);

            DB::commit();

            return response()->json([
                'proposals' => $header->proposals,
                'logs' => $header->logs,
            ], 200);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function proposal_update(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->type === 'version') {
                $current_proposal = DesignRequirementProposal::with('versions')->find($request->id);

                $old_version = $current_proposal->versions()->create([
                    'line_code' => $current_proposal->line_code,
                    'subline_code' => $current_proposal->subline_code,
                    'feature_code' => $current_proposal->feature_code,
                    'material_id' => $current_proposal->material_id,
                    'measurement_id' => $current_proposal->measurement_id,
                    'features_detail' => $current_proposal->features_detail,
                    'details' => $current_proposal->details,
                    'created_by' => $current_proposal->created_by,
                    'version' => $current_proposal->version,
                    'weight' => $current_proposal->weight,
                ])->id;

                $old_version_path = "design_requirements/{$request->requirement_id}/proposals/{$current_proposal->id}/versions/v{$current_proposal->version}";

                if (!Storage::exists($old_version_path)) {
                    Storage::makeDirectory($old_version_path);
                }

                $array = explode('/', $current_proposal->path2D);
                $filename = end($array);
                $file2d = File::move(storage_path("app/$current_proposal->path2D"), storage_path("app/{$old_version_path}/{$filename}"));

                $version = DesignRequirementProposalVersion::find($old_version);

                if ($file2d) {
                    $version->path2D = "{$old_version_path}/{$filename}";
                    $version->save();
                }

                if ($current_proposal->path3D) {
                    $array = explode('/', $current_proposal->path3D);
                    $filename = end($array);
                    $file3d = File::move(storage_path("app/$current_proposal->path3D"), "app/{$old_version_path}/{$filename}");

                    if ($file3d) {
                        $version->path3D = "{$old_version_path}/{$filename}";
                        $version->save();
                    }
                }

                $new_version = DesignRequirementProposal::find($request->id);

                $new_version->update([
                    'line_code' => $request->line_code,
                    'subline_code' => $request->subline_code,
                    'feature_code' => $request->feature_code,
                    'material_id' => $request->material_id,
                    'measurement_id' => $request->measurement_id,
                    'features_detail' => $request->features_detail,
                    'details' => $request->details,
                ]);

                $file = $request->file('file_2d');
                $extension = $file->getClientOriginalExtension();
                $filename = "2D.$extension";

                $path = "design_requirements/{$request->requirement_id}/proposals/{$new_version->id}";

                $full_path = storage_path() . "/app/design_requirements/{$request->requirement_id}/proposals/{$new_version->id}/{$filename}";

                if (!Storage::exists($path)) {
                    Storage::makeDirectory($path);
                }

                $storagePath = Storage::putFileAs($path, $file, $filename);

                if (Storage::exists($storagePath)) {
                    $new_version->path2D = $storagePath;
                    $new_version->save();
                } else {
                    DB::rollBack();

                    return response()->json("error saving file: {$full_path}", 500);
                }

                $new_version->logs()->create([
                    'description' => 'Creo una nueva version de la propuesta',
                    'created_by' => Auth::id(),
                    'type' => 'log',
                ]);

            } else {
                $new_version = DesignRequirementProposal::find($request->id);

                $new_version->update([
                    'line_code' => $request->line_code,
                    'subline_code' => $request->subline_code,
                    'feature_code' => $request->feature_code,
                    'material_id' => $request->material_id,
                    'measurement_id' => $request->measurement_id,
                    'features_detail' => $request->features_detail,
                    'details' => $request->details,
                ]);

                if ($request->file('file_2d')) {
                    File::delete(storage_path("app/{$new_version->path2D}"));

                    $file = $request->file('file_2d');
                    $extension = $file->getClientOriginalExtension();
                    $filename = "2D.$extension";

                    $path = "design_requirements/{$request->requirement_id}/proposals/{$new_version->id}";

                    $full_path = storage_path() . "/app/design_requirements/{$request->requirement_id}/proposals/{$new_version->id}/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::putFileAs($path, $file, $filename);

                    if (Storage::exists($storagePath)) {
                        $new_version->path2D = $storagePath;
                        $new_version->save();
                    } else {
                        DB::rollBack();

                        return response()->json("error saving file: {$full_path}", 500);
                    }
                }

                $new_version->logs()->create([
                    'description' => 'Actualizo la información de la propuesta',
                    'created_by' => Auth::id(),
                    'type' => 'log',
                ]);

            }
            DB::commit();

            $header = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.versions', 'proposals.blueprint')
                ->find($request->requirement_id);

            return response()->json([
                'logs' => $header->logs,
                'proposals' => $header->proposals,
                'proposal' => $header->proposals->find($request->id),
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $q
     * @return JsonResponse
     */
    public function search_brands($q): JsonResponse
    {
        $brands = Brand::where('name', 'like', "%$q%")
            ->orWhere('customer_code', 'like', "%$q%")
            ->get();

        return response()->json($brands, 200);
    }

    /**
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function proposal_print($id)
    {
        ini_set("pcre.backtrack_limit", "5000000");
        $proposal = DesignRequirementProposal::with('header', 'created_user', 'approved_user', 'blueprint')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.design_requirement.header', compact('proposal')));
        $pdf->SetHTMLFooter(View::make('pdfs.design_requirement.footer'));
        $pdf->WriteHTML(View::make('pdfs.design_requirement.template', compact('proposal')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
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

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/design_requirement/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @param $id
     * @return Model|Collection|Builder|array|null
     *
     * @throws MpdfException
     */
    public function old_proposal_print($id): Model|Collection|Builder|array|null
    {
        $proposal = DesignRequirementProposalVersion::with('proposal.header', 'created_user')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.design_requirement.header'));
        $pdf->SetHTMLFooter(View::make('pdfs.design_requirement.footer'));
        $pdf->WriteHTML(View::make('pdfs.design_requirement.template_old', compact('proposal')), HTMLParserMode::HTML_BODY);

        return \Illuminate\Http\Response::make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Response
     */
    public function gestion_3d(): Response
    {
        $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
            'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
            ->where('state', '=', '2')
            ->get();

        $proposals2 = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
            'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
            ->where('state', '=', '7')
            ->where('weight', '=', '0')
            ->get();

        return Inertia::render('Applications/DesignRequirements/3DManagement', [
            'proposals' => $proposals,
            'pending' => $proposals2
        ]);
    }

    /**
     * @return Response
     */
    public function gestion_blueprint(): Response
    {
        $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
            'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
            ->where('state', '=', '3')
            ->get();

        return Inertia::render('Applications/DesignRequirements/BlueprintManagement', [
            'proposals' => $proposals,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function load_blueprints(Request $request): JsonResponse
    {
        $blueprints = Blueprint::where('line_code', '=', $request->line_code)->get();

        return response()->json($blueprints, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function identify_blueprint($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $proposal = DesignRequirementProposal::find($id);

            $blueprint = Blueprint::where('line_code', '=', $proposal->line->code)
                ->where('subline_code', '=', $proposal->subline->code)
                ->where('feature_code', '=', $proposal->feature->code)
                ->where('material_id', '=', $proposal->material->id)
                ->where('measurement_id', '=', $proposal->measurement->id)
                ->first();

            if ($blueprint) {
                $state = 'blueprint-exist';
                $proposal->blueprint_id = $blueprint->id;
                $proposal->state = '1';
                $proposal->save();

                $proposal->logs()->create([
                    'description' => 'Asigno un plano',
                    'created_by' => Auth::id(),
                    'type' => 'log',
                ]);
            } else {
                $state = 'none';
            }

            DB::commit();

            $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
                'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
                ->where('state', '=', '3')
                ->get();

            return response()->json([
                'proposals' => $proposals,
                'state' => $state,
                'msg' => $state === 'blueprint-exist'
                    ? "Se le ha asignado a la propuesta el plano con ID $blueprint->id"
                    : 'Plano no encontrado',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json("[{$e->getLine()}]: {$e->getMessage()}", 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_3D(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $proposal = DesignRequirementProposal::with('header')->find($request->id);

            $file = $request->file('file_3d');
            $extension = $file->getClientOriginalExtension();
            $filename = "3D.$extension";

            $path = "design_requirements/{$proposal->header->id}/proposals/{$proposal->id}";

            $full_path = storage_path() . "/app/design_requirements/{$proposal->header->id}/proposals/{$proposal->id}/{$filename}";

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            $storagePath = Storage::putFileAs($path, $file, $filename);

            if (Storage::exists($storagePath)) {
                $proposal->path3D = $storagePath;
                $proposal->weight = $request->weight;
                $proposal->state = '1';
                $proposal->save();
            } else {
                DB::rollBack();

                return response()->json("error saving file: {$full_path}", 500);
            }

            $proposal->logs()->create([
                'description' => 'Agrego un archivo 3D',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
                'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
                ->where('state', '=', '2')
                ->get();

            return response()->json($proposals, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function update_proposal_3d(Request $request){
        DB::beginTransaction();
        try {
            $proposal = DesignRequirementProposal::with('header')
                ->find($request->id);
            $proposal->weight = $request->weight;
            $proposal->area = $request->area;
            $proposal->save();

            $proposal->logs()->create([
                'description' => 'Actualizo el area y el peso de la propuesta',
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
                'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
                ->where('state', '=', '7')
                ->where('weight', '=', '0')
                ->get();

            return response()->json($proposals, 200);
        }catch (Exception $e){
            DB::rollBack();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function proposal_change_state(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $msg = '';
            $proposal = DesignRequirementProposal::find($request->id);

            if ($request->state === 4 && $proposal->path2D === null){
                throw new Exception('No se puede solicitar aprobación sin un archivo 2D cargado', 500);
            }else if ($request->state === 3 && $proposal->path2D === null){
                throw new Exception('No se puede solicitar planos sin un archivo 2D cargado', 500);
            }else if ($request->state === 2 && $proposal->path2D === null){
                throw new Exception('No se puede solicitar 3D sin un archivo 2D cargado', 500);
            }


            $proposal->state = $request->state;
            $proposal->save();

            switch ($request->state) {
                case '0':
                    $msg = "Anulo la propuesta, Justificación: {$request->justify}";
                    break;
                case '2':
                    $msg = 'Envío la propuesta para solicitud de 3D';
                    break;
                case '3':
                    $msg = 'Envío la propuesta para solicitud de planos';
                    break;
                case '4':
                    $msg = 'Propuesta enviada para aprobación';
                    break;
                case '5':
                    $msg = 'Envío la propuesta para corrección';
                    break;
                case '6':
                    $msg = 'Aprobó la propuesta';
                    break;
            }

            $proposal->logs()->create([
                'description' => $msg,
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            DB::commit();

            $header = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.versions', 'proposals.blueprint')
                ->find($request->requirement_id);

            return response()->json([
                'proposal' => $header->proposals->find($request->id),
                'proposals' => $header->proposals,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function proposal_validate_blueprint($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $proposal = DesignRequirementProposal::find($id);

            $blueprint = Blueprint::where('line_code', '=', $proposal->line->code)
                ->where('subline_code', '=', $proposal->subline->code)
                ->where('feature_code', '=', $proposal->feature->code)
                ->where('material_id', '=', $proposal->material->id)
                ->where('measurement_id', '=', $proposal->measurement->id)
                ->first();

            if ($blueprint) {
                $state = 'blueprint-exist';
                $proposal->blueprint_id = $blueprint->id;
                $proposal->save();
            } else {
                $state = 'none';
            }

            DB::commit();

            $header = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.versions', 'proposals.blueprint')
                ->find($proposal->header->id);

            return response()->json([
                'state' => $state,
                'proposal' => $header->proposals->find($id),
                'proposals' => $header->proposals,
                'logs' => $header->logs,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function verify_requirement($id): JsonResponse
    {
        $requirement = DesignRequirementHeader::with('proposals')->find($id);

        $completed = $requirement->proposals->where('state', '=', '6')->count();

        $canceled = $requirement->proposals->where('state', '=', '0')->count();

        if ($completed > 0 && $completed + $canceled === $requirement->proposals->count()) {
            return response()->json(true, 200);
        }

        return response()->json(false, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function finalize(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requirement = DesignRequirementHeader::with('proposals', 'proposals.logs', 'proposals.logs.created_user',
                'proposals.line', 'proposals.subline', 'proposals.feature', 'proposals.material', 'proposals.material.material',
                'proposals.measurement', 'logs.created_user', 'proposals.versions', 'proposals.blueprint', 'brand')
                ->find($request->id);

            $completed = $requirement->proposals->where('state', '=', '6');

            $results = [];

            $exist_code = DesignRequirementProduct::where('product_type_code', '=', $requirement->product_type->code)
                ->where('line_code', '=', $requirement->line->code)
                ->where('subline_code', '=', $requirement->subline->code)
                ->where('feature_code', '=', $requirement->feature->code)
                ->where('material_id', '=', $requirement->material->id)
                ->where('measurement_id', '=', $requirement->measurement->id)
                ->where('galvanic_finish_code', '=', '00')
                ->where('decorative_option_code', '=', '00')
                ->whereNull('art_code')
                ->first();

            $father_product = "";
            $new_father_product = null;

            if (!$exist_code) {
                $father_product = generate_code_description($requirement->product_type->code, $requirement->line, $requirement->subline, $requirement->feature->abbreviation, $requirement->material, $requirement->measurement->detail, null);

                $new_father_product = DesignRequirementProduct::create([
                    'code' => substr_replace($father_product['code'], '****', -4),
                    'description' => $father_product['description'],
                    'product_type_code' => $requirement->product_type->code,
                    'line_code' => $requirement->line->code,
                    'subline_code' => $requirement->subline->code,
                    'feature_code' => $requirement->feature->code,
                    'material_id' => $requirement->material->id,
                    'measurement_id' => $requirement->measurement->id,
                    'galvanic_finish_code' => '00',
                    'decorative_option_code' => '00',
                    'art_code' => NULL,
                    'brand_id' => $requirement->brand->id,
                    'type' => 'father',
                    'state' => 'NA'
                ]);

            }else {
                $father_product = $exist_code->toArray();
            }
            $results[] = [
                'proposal' => 'PADRE',
                'art' => 'N/A', //substr_replace($father_product['code'], '******', -4)
                'product' => "*********** – {$father_product['description']}",
            ];

            foreach ($completed as $key => $proposal) {
                $art = artCode($requirement->brand->name[0]);
                $custom_code = NULL;

                $new_art = new DesignRequirementArt([
                    'code' => $art,
                    'design_requirement_id' => $requirement->id,
                    'proposal_id' => $proposal->id,
                ]);
                $new_art->save();

                $new_art->versions()->create([
                    'line_code' => $proposal->line->code,
                    'subline_code' => $proposal->subline->code,
                    'feature_code' => $proposal->feature->code,
                    'material_id' => $proposal->material->id,
                    'measurement_id' => $proposal->measurement->id,
                    'blueprint_id' => $proposal->blueprint->id ?? null,
                    'brand_id' => $requirement->brand->id,
                    'weight' => $proposal->weight,
                    'designer_id' => Auth::id(),
                    'seller_id' => $requirement->seller->id,
                    'comments' => $proposal->comments,
                    'features_detail' => $proposal->features_detail,
                    'version' => 1,
                    'enabled' => true,
                    'state' => '3',
                ]);

                $product = generate_code_description($proposal->product_type->code, $proposal->line, $proposal->subline, $proposal->feature->abbreviation, $proposal->material, $proposal->measurement->detail, $new_art->code);

                $exist_product = DesignRequirementProduct::where('product_type_code', '=', $requirement->product_type->code)
                    ->where('line_code', '=', $requirement->line->code)
                    ->where('subline_code', '=', $requirement->subline->code)
                    ->where('feature_code', '=', $requirement->feature->code)
                    ->where('material_id', '=', $requirement->material->id)
                    ->where('measurement_id', '=', $requirement->measurement->id)
                    ->where('galvanic_finish_code', '=', '00')
                    ->where('decorative_option_code', '=', '00')
                    ->where('art_code', '=', $new_art->code)
                    ->first();

                if (!$exist_product){
                    $father_id =  $new_father_product ? $new_father_product->consecutive : $father_product['id'];

                    $custom_code = "R{$father_id}P{$proposal->id}A{$new_art->code}{$key}";

                    DesignRequirementProduct::create([
                        'father_id' => $new_father_product ? $new_father_product->id : ($father_product['id'] ?? null),
                        'code' => $custom_code,
                        'description' => $product['description'],
                        'product_type_code' => $proposal->product_type->code,
                        'line_code' => $proposal->line->code,
                        'subline_code' => $proposal->subline->code,
                        'feature_code' => $proposal->feature->code,
                        'material_id' => $proposal->material->id,
                        'measurement_id' => $proposal->measurement->id,
                        'galvanic_finish_code' => '00',
                        'decorative_option_code' => '00',
                        'art_code' => $new_art->code,
                        'brand_id' => $requirement->brand->id,
                        'type' => 'child',
                        'state' => 'pending'
                    ]);
                }else {
                    $custom_code = $exist_product->code;
                }

                $file_path = "design_requirements_arts/{$new_art->code}/versions/1";

                if (!Storage::exists($file_path)) {
                    Storage::makeDirectory($file_path);
                }

                $new_art = DesignRequirementArt::find($new_art->id);

                if ($proposal->path2D) {
                    $array = explode('/', $proposal->path2D);
                    $filename = end($array);
                    $file2d = Storage::put("{$file_path}/{$filename}", Storage::get($proposal->path2D));

                    if ($file2d) {
                        $new_art->current->path2D = "{$file_path}/{$filename}";
                        $new_art->current->save();
                    }
                }

                if ($proposal->path3D) {
                    $array = explode('/', $proposal->path3D);
                    $filename = end($array);
                    $file3d = Storage::put("{$file_path}/{$filename}", Storage::get($proposal->path3D));

                    if ($file3d) {
                        $new_art->current->path3D = "{$file_path}/{$filename}";
                        $new_art->current->save();
                    }
                }

                $proposal->logs()->create([
                    'description' => "Genero el arte $art para la propuesta $proposal->id",
                    'created_by' => Auth::id(),
                    'type' => 'log',
                ]);

                $proposal->state = '7';
                $proposal->save();

                $results[] = [
                    'proposal' => $proposal->id,
                    'art' => $art,
                    'product' => $custom_code." – {$product['description']}",
                ];
            }

            $requirement->logs()->create([
                'description' => 'Finalizo el requerimiento, artes creados: ' . count($completed),
                'created_by' => Auth::id(),
                'type' => 'log',
            ]);

            $requirement->state = '5';
            $requirement->save();

            DB::commit();

            $requirement = DesignRequirementHeader::with(
                'customer', 'seller', 'brand', 'seller', 'material.material', 'assigned_designer', 'logs.created_user',
                'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit', 'files', 'proposals.logs',
                'proposals.logs.created_user', 'proposals.material.material', 'proposals.blueprint'
            )->find($request->id);

            return response()->json([
                'html' => view('requirement_result', compact('results'))->render(),
                'requirement' => $requirement,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("[{$e->getLine()}]: {$e->getMessage()}, {$e->getFile()}", 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $designRequirementHeader = DesignRequirementHeader::with('proposals')->find($id);

            if ($designRequirementHeader->proposals()->count() > 0) {
                throw new Exception('No se puede eliminar un requerimiento con propuestas creadas', 422);
            } else {
                $designRequirementHeader->delete();

                return response()->json('success', 200);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function return_without_gestion(Request $request){
        DB::beginTransaction();
        try {
            $proposal =  DesignRequirementProposal::with('logs')->find($request->id);
            $proposal->state = '1';
            $proposal->save();

            $proposal->logs->create([
                'description' => "Se devuelve propuesta desde {$request->origin}, Justificación: {$request->justify}",
                'created_by'  => Auth::id(),
                'type' => 'comment'
            ]);

            DB::commit();

            $proposals = DesignRequirementProposal::with('logs.created_user', 'versions', 'line', 'created_user',
                'subline', 'feature', 'material', 'material.material', 'measurement', 'header.customer', 'header.seller', 'header.brand')
                ->where('state', '=', '2')
                ->get();

            return response()->json($proposals, 200);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}

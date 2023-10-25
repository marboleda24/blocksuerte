<?php

namespace App\Http\Controllers;

use App\Exports\QualityControlReviewExport;
use App\Models\QualityControlInspectionUser;
use App\Models\QualityControlReview;
use App\Models\QualityControlReviewCause;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QualityControlController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $operators = User::where('occupation', '=', 'operario')
            ->orderby('name')
            ->get();

        $quality_users = User::where('occupation', '=', 'calidad')
            ->orderby('name')
            ->get();

        $causes = QualityControlReviewCause::orderby('name')
            ->get();

        $inspection_users = QualityControlInspectionUser::all();

        $plants = DB::connection('MAX')->table('CIEV_Procesos')->get();

        return Inertia::render('Applications/QualityControl/Index', [
            'operators' => $operators,
            'quality_users' => $quality_users,
            'causes' => $causes,
            'inspection_users' => $inspection_users,
            'plants' => $plants,
        ]);
    }

    /**
     * @param $production_order
     * @return JsonResponse
     */
    public function get_production_order($production_order): JsonResponse
    {
        try {
            $production_order_full = "{$production_order}0000";

            $state = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $production_order)
                ->first();

            $registries = QualityControlReview::with('inspector', 'operator', 'cause', 'files')
                ->where('production_order', '=', $production_order_full)
                ->get();

            $registries = $this->group_data('work_center', $registries);

            $work_centers = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $production_order)
                ->orderBy('OPRSEQ_14', 'asc')
                ->pluck('WRKCTR_14')
                ->toArray();

            $work_centers = array_unique($work_centers, SORT_STRING);
            $work_centers = array_merge($work_centers, []);
            $work_centers = array_map(function ($row) use ($registries) {
                return [
                    'code' => $row,
                    'description' => $this->work_center_description($row),
                    'registries' => $registries[$row] ?? [],
                ];
            }, $work_centers);

            $current_work_center = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $production_order)
                ->where('CTActual', '=', 'Y')
                ->orderBy('OPRSEQ_14', 'asc')
                ->pluck('WRKCTR_14')
                ->toArray();

            $current_work_center = array_unique($current_work_center, SORT_STRING);
            $current_work_center = array_merge($current_work_center, []);

            return response()->json([
                'production_order' => $production_order,
                'state' => $state,
                'work_centers' => $work_centers,
                'current_work_center' => $current_work_center,
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getTrace(), 500);
        }
    }

    /**
     * Agrupa los elementos de un array por una
     * clave dada en este caso el centro de trabajo
     *
     * @param $key
     * @param $data
     * @return array
     */
    protected function group_data($key, $data): array
    {
        $result = [];

        foreach ($data as $val) {
            if (isset($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[''][] = $val;
            }
        }

        return $result;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $new_review = new QualityControlReview;
            $new_review->fill($request->except('production_order', 'files'));

            $new_review->fill([
                'inspector_id' => Auth::id(),
                'register_by' => Auth::id(),
                'production_order' => $request->production_order.'0000',
            ]);

            $new_review->save();

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "quality_control_review/{$new_review->id}";

                    $full_path = storage_path() . "/app/quality_control_review/{$new_review->id}/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::put("quality_control_review/{$new_review->id}", $file);

                    if (Storage::exists($storagePath)) {
                        $new_review->files()->create([
                            'name' => $filename,
                            'path' => $storagePath,
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            $production_order_full = "{$request->production_order}0000";

            $state = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $request->production_order)
                ->first();

            $registries = QualityControlReview::with('inspector', 'operator', 'cause')
                ->where('production_order', '=', $production_order_full)
                ->get();

            $registries = $this->group_data('work_center', $registries);

            $work_centers = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $request->production_order)
                ->orderBy('OPRSEQ_14', 'asc')
                ->pluck('WRKCTR_14')
                ->toArray();

            $work_centers = array_unique($work_centers, SORT_STRING);
            $work_centers = array_merge($work_centers, []);
            $work_centers = array_map(function ($row) use ($registries) {
                return [
                    'code' => $row,
                    'description' => $this->work_center_description($row),
                    'registries' => $registries[$row] ?? [],
                ];
            }, $work_centers);

            $current_work_center = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=', $request->production_order)
                ->where('CTActual', '=', 'Y')
                ->orderBy('OPRSEQ_14', 'asc')
                ->pluck('WRKCTR_14')
                ->toArray();

            $current_work_center = array_unique($current_work_center, SORT_STRING);
            $current_work_center = array_merge($current_work_center, []);

            DB::commit();

            return response()->json([
                'production_order' => $request->production_order,
                'state' => $state,
                'work_centers' => $work_centers,
                'current_work_center' => $current_work_center,
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function review($id): JsonResponse
    {
        $review = QualityControlReview::with('inspector', 'operator', 'cause')->find($id);

        return response()->json($review, 200);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function work_center_description($string)
    {
        return DB::connection('MAX')
            ->table('SFC_Work_Center')
            ->where('WRKCTR_13', '=', $string)
            ->pluck('DESC_13')
            ->first();
    }

    /**
     * @return BinaryFileResponse
     */
    public function downloadReview(): BinaryFileResponse
    {
        return Excel::download(new QualityControlReviewExport, 'quality-control-review.xlsx');
    }

    /**
     * @param  Request  $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        return response()->download(storage_path("app/$request->path"), $request->name);
    }
}

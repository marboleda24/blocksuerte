<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use PHPUnit\Exception;

class ReplaceDataController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:queries.replace-data');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/Queries/ReplaceData');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_tables(Request $request): JsonResponse
    {
        try {
            $tables = DB::connection($request->db)->getDoctrineSchemaManager()->listTableNames();

            return response()->json($tables, 200);
        } catch (\Exception $e) {
            return \response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_columns(Request $request): JsonResponse
    {
        try {
            $columns = DB::connection($request->db)->getDoctrineSchemaManager()->listTableColumns($request->table);

            return response()->json($columns, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function count_data(Request $request): JsonResponse
    {
        try {
            $query = $request->q;

            $data = DB::connection($request->db)
                ->table($request->table)
                ->where($request->column, 'like', '%'.$query.'%')
                ->count();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function replace(Request $request): JsonResponse
    {
        DB::connection($request->database)->beginTransaction();
        try {
            $data = DB::connection($request->database)
                ->table($request->table)
                ->where($request->column, 'like', '%'.$request->quer.'%')
                ->get();

            foreach ($data as $row) {
                $old_des = $row->$request->column_replace;
                $row->$request->column_replace = str_replace($request->replace_data, $request->to_data, $old_des);
                $row->save();
            }

            DB::connection($request->database)->commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::connection($request->database)->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}

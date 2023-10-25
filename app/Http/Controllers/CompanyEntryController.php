<?php

namespace App\Http\Controllers;

use App\Models\CompanyEntryInvited;
use App\Models\CompanyEntryStaff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CompanyEntryController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/CompanyEntry/Index');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $employee = DB::connection('DMS')
                ->table('V_CIEV_EmpleadosCentroCostos')
                ->where('estado', '=', 'A')
                ->where('nit', '=', $request->document)->first();

            if ($employee && $request->document) {
                $registry = CompanyEntryStaff::where('document', '=', $request->document)
                    ->where('exit', '=', null)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($registry) {
                    $registry->timestamps = false;
                    $registry->update([
                        'exit' => Carbon::now(),
                    ]);

                    DB::commit();

                    return response()->json([
                        'type' => 'exit',
                        'name' => $employee->nombres_apellidos,
                        'time' => Carbon::now()->format('g:i A'),
                    ], 200);
                } else {
                    CompanyEntryStaff::create([
                        'document' => $employee->nit,
                        'entry' => Carbon::now(),
                    ]);

                    DB::commit();

                    return response()->json([
                        'type' => 'entry',
                        'name' => $employee->nombres_apellidos,
                        'time' => Carbon::now()->format('g:i A'),
                    ], 200);
                }
            } else {
                $invited = CompanyEntryInvited::with('registry')
                    ->where('document', '=', $request->document)
                    ->first();

                if ($invited) {
                    $registries = $invited->registry->orderBy('created_at', 'desc')->first();

                    $registries->exit = Carbon::now();
                    $registries->save();

                    DB::commit();

                    return response()->json([
                        'type' => 'exit',
                        'name' => $invited->name,
                        'time' => Carbon::now()->format('g:i A'),
                    ], 200);
                } elseif ($request->document && $request->name) {
                    $invited = CompanyEntryInvited::create([
                        'document' => $request->document,
                        'name' => "{$request->name } {$request->second_name} {$request->surname} {$request->second_surname}",
                        'sex' => $request->sex,
                        'birth' => Carbon::parse($request->birth),
                        'blood_type' => $request->blood_type,
                    ]);

                    $invited->registry()->create([
                        'entry' => Carbon::now(),
                    ]);

                    DB::commit();

                    return response()->json([
                        'type' => 'entry',
                        'name' => $invited->name,
                        'time' => Carbon::now()->format('g:i A'),
                    ], 200);
                } else {
                    return response()->json('[500]: Invitado no registrado', 500);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json("[{$e->getCode()}]: {$e->getMessage()}", 500);
        }
    }
}

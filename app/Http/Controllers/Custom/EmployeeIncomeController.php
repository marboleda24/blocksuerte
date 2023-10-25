<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use App\Models\EmployeeIncome;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Rats\Zkteco\Lib\ZKTeco;
use TADPHP\TADFactory;

class EmployeeIncomeController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/EmployeeIncome');
    }

    /**
     * @param $document
     * @return JsonResponse
     */
    public function check_guest($document)
    {
        $employee = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $document)
            ->where('estado', '=', 'A')
            ->count();

        $registry = EmployeeIncome::where('document', '=', $document)
            ->whereNull('exit_datetime')
            ->first();

        if ($employee > 0 && $registry) {
            $registry->exit_datetime = Carbon::now();
            $registry->save();

            return response()->json([
                'msg' => 'Salida exitosa',
                'state' => true,
            ], 200);
        } elseif ($employee > 0 && ! $registry) {
            EmployeeIncome::create([
                'document' => $document,
                'entry_datetime' => Carbon::now(),
                'type' => 'employee',
            ]);

            return response()->json([
                'msg' => 'Entrada exitosa',
                'state' => true,
            ], 200);
        } else {
            return response()->json([
                'msg' => 'empleado no existe',
                'state' => false,
            ], 200);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register_guest(Request $request)
    {
        try {
            $original_string = explode(';', $request->original_string);

            $find_guest = EmployeeIncome::where('document', '=', $original_string[0])
                ->where('type', '=', 'guest')
                ->last();

            if ($find_guest && $find_guest->exit_datetime) {
                $new = $find_guest->replicate();
                $new->entry_datetime = Carbon::now();
                $new->exit_datetime = null;
                $new->save();

                return response()->json([
                    'msg' => '¡Bienvenido de nuevo!',
                    'state' => true,
                ], 200);
            } elseif ($find_guest && ! $find_guest->exit_datetime) {
                $find_guest->exit_datetime = Carbon::now();
                $find_guest->save();

                return response()->json([
                    'msg' => '¡Gracias por visitarnos!',
                    'state' => true,
                ], 200);
            } else {
                EmployeeIncome::create([
                    'document' => $original_string[0],
                    'fist_name' => $original_string[3],
                    'second_name' => $original_string[4],
                    'first_lastname' => $original_string[1],
                    'second_lastname' => $original_string[2],
                    'gender' => $original_string[5],
                    'birthday' => $original_string[6],
                    'blood_type' => $original_string[7],
                    'entry_datetime' => Carbon::now(),
                    'type' => 'guest',
                ]);

                return response()->json([
                    'msg' => 'Invitado registrado con exito',
                    'state' => true,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    public function zkteco(){
        $tad = (new TADFactory(['ip'=>'192.168.1.14', 'com_key'=>0]))->get_instance();

        // Get attendance logs for all users.
        $att_logs = $tad->get_att_logs(); // $att_logs is an TADResponse object.

        $array_att_logs = $att_logs->to_array();

        return $array_att_logs;
    }
}

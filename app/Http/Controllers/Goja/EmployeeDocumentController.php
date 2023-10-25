<?php

namespace App\Http\Controllers\Goja;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Luecano\NumeroALetras\NumeroALetras;

class EmployeeDocumentController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/Goja/EmployeeDocuments');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function generate_document(Request $request)
    {
        try {
            if ($request->type === 'working-letter'){
                $pdf = $this->working_letter($request->document);
            }else {
                $pdf = $this->peace_safe($request->document);
            }
            return $pdf->download('file.pdf');
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $employee_document
     * @return \Barryvdh\DomPDF\PDF
     * @throws Exception
     */
    protected function working_letter($employee_document)
    {
        $contract = DB::connection('GOJA')
            ->table('v_NomContratos')
            ->where('nit', '=', $employee_document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->first();

        if (!$contract){
            throw new Exception("Este empleado no existe o no tiene un contrato activo", 500);
        }

        $employee_info = DB::connection('GOJA')
            ->table('v_EmpleadosCentroCostos')
            ->where('nit', '=', $employee_document)
            ->where('estado', '=', 'A')
            ->first();

        if (!$employee_info){
            throw new Exception("No se encontro informacion de cargo para este empleado", 500);
        }

        $salary = new NumeroALetras();
        $salary = $salary->toMoney($contract->basico_mes, 2, 'PESOS', 'CENTAVOS');

        $pdf = PDF::loadView('pdfs.goja.WorkingLetter.template', compact('contract', 'employee_info', 'salary'));
        $pdf->setWarnings(false);
        $pdf->setPaper('letter', 'portrait');

        return $pdf;
    }

    /**
     * @param $employee_document
     * @return \Barryvdh\DomPDF\PDF
     * @throws Exception
     */
    protected function peace_safe($employee_document)
    {
        $contract = DB::connection('GOJA')
            ->table('v_NomContratos')
            ->where('nit', '=', $employee_document)
            ->where('ESTADO', '=', 'RETIRADO')
            ->orderBy('fecha_ingreso', 'desc')
            ->first();

        if (!$contract){
            throw new Exception("Este empleado no encontrado o no se encuentra retirado", 500);
        }

        $employee_info = DB::connection('GOJA')
            ->table('v_EmpleadosCentroCostos')
            ->where('nit', '=', $employee_document)
            ->where('estado', '=', 'R')
            ->first();


        if (!$employee_info){
            throw new Exception("No se encontro informacion de cargo para este empleado", 500);
        }

        /* $imgBase64 = base64_encode(file_get_contents(storage_path('')));
        $signature_image = 'data:image/png;base64, '.$imgBase64; */

        $pdf = PDF::loadView('pdfs.goja.WorkingLetter.peace-safe',
            compact('contract', 'employee_info')
        );

        $pdf->setWarnings(false);
        $pdf->setPaper('letter', 'portrait');

        return $pdf;
    }
}

<?php

namespace App\Http\Controllers\Max\Reports\Production;

use App\Exports\Max\Reports\Production\PendingExport;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
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

class PendingController extends Controller
{
    /**
     * @param $view
     * @param $type
     * @return \Illuminate\Http\Response|Response|mixed|BinaryFileResponse
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function report($view, $type = null)
    {
        $data = DB::connection('MAX')
            ->table($this->tableToReport($view))
            ->select($this->selectToReport($view))
            ->get();

        $data = (object)[
            "data" => $data,
            "title" => $this->titleToReport($view),
            "headings" => $this->headingsToReport($view),
            "report" => $view
        ];

        if ($type === 'xlsx') {
            return Excel::download(new PendingExport($data), 'file.xlsx');
        } else if ($type === 'pdf') {
            $pdf = $this->generatePDF($data->data, $data->headings, $data->title);

            return response()->make($pdf->Output(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } else {
            return Inertia::render('Applications/MAX/Reports/Production', [
                'data' => $data
            ]);
        }
    }

    /**
     * @param $view
     * @return string
     */
    protected function tableToReport($view)
    {
        return match ($view) {
            "electro" => "CIEV_V_PendientesElectro",
            "deformadora-alambre" => "CIEV_V_PendientesDeformadoraAlambre",
            "troquelados-ojaletes" => "CIEV_V_PendientesTroqueladosOjaletes",
            "encabezados" => "CIEV_V_PendientesEncabezados",
            "grabo-laser" => "CIEV_V_PendientesGrabolaser",
            "piedra" => "CIEV_V_PendientesPiedra",
            "pinturas" => "CIEV_V_PendientesPinturas",
            "pintura-uv" => "CIEV_V_PendientesPinturaUV",
            "troquelados-engargolar" => "CIEV_V_PendientesTroqueladosEngargolar",
            "troquelados-broches" => "CIEV_V_PendientesTroqueladosBroches",
            "troquelados-remaches" => "CIEV_V_PendientesTroqueladosRemaches",
            "troquelado-botones" => "CIEV_V_PendientesTroqueladosBotones",
            "inspeccion-empaque" => "CIEV_V_PendientesInspeccionyEmpaque"
        };
    }

    /**
     * @param $view
     * @return array
     */
    protected function selectToReport($view)
    {
        return match ($view) {
            "grabo-laser", "pintura-uv", "troquelados-engargolar", "troquelados-remaches", "inspeccion-empaque" => [
                'OP',
                'OV',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(LOTE) AS LOTE"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                'MARCA',
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'DIAS_OV',
                'DIAS_OPERACION'

            ],

            "pinturas" => [

            DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
            DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
            DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
            DB::raw("RTRIM(ACABADO) AS ACABADO"),
            DB::raw("RTRIM(LOTE) AS LOTE"),
            DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
            'MARCA',
            DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
            DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
            DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
            'DIAS_OV'

            ],

            "deformadora-alambre", "piedra" => [
                'OP',
                'OV',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(LOTE) AS LOTE"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'FECHA_MOV',
                'FECHA_INI',
            ],

            "troquelados-ojaletes", "ojaletes" => [
                'OP',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(LOTE) AS LOTE"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'DIAS_CT',
                'DIAS_OV',
            ],

            "electro" => [
                'OP',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(LOTE) AS LOTE"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'DIAS_OV',
                'DIAS_PLANTA'
            ],

            "encabezados" => [
                'OP',
                'OV',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'FECHA_MOV',
                'FECHA_INI',
            ],

            "troquelados-broches" => [
                'OP',
                'OV',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(LOTE) AS LOTE"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'DIAS_OPERACION',
            ],

            "troquelado-botones" => [
                'OP',
                'OV',
                DB::raw("RTRIM(COD_PROD) AS COD_PROD"),
                DB::raw("RTRIM(PRODUCTO) AS PRODUCTO"),
                DB::raw("RTRIM(REFERENCIA) AS REFERENCIA"),
                DB::raw("RTRIM(ACABADO) AS ACABADO"),
                DB::raw("RTRIM(ARTE_OV) AS ARTE_OV"),
                'MARCA',
                DB::raw("CAST(CANT_COMPLETADA as int) as CANT_COMPLETADA"),
                DB::raw("CAST(CANT_PENDIENTE as int) as CANT_PENDIENTE"),
                DB::raw("FORMAT(FECHA_LIBERACION, 'yyyy-MM-dd') AS FECHA_LIBERACION"),
                'DIAS_OPERACION',
            ]
        };
    }

    /**
     * @param $view
     * @return string
     */
    protected function titleToReport($view)
    {
        return "Pendientes " . match ($view) {
                "electro" => "electro",
                "deformadora-alambre" => "deformadora alambre",
                "troquelados-ojaletes" => "troquelados ojaletes",
                "encabezados" => "encabezados",
                "grabo-laser" => "grabo laser",
                "piedra" => "piedra",
                "pinturas" => "pinturas",
                "pintura-uv" => "pintura uv",
                "troquelados-engargolar" => "troquelados engargolar",
                "troquelados-broches" => "troquelados broches",
                "ojaletes" => "ojaletes",
                "troquelados-remaches" => "troquelados remaches",
                "troquelado-botones" => "troquelado botones",
                "inspeccion-empaque" => "inspeccion y empaque"
            };
    }

    /**
     * @param $view
     * @return string[]
     */
    protected function headingsToReport($view)
    {
        return match ($view) {
            "grabo-laser", "pintura-uv", "troquelados-engargolar", "troquelados-remaches", "inspeccion-empaque" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "MARCA" => "MARCA",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                'DIAS_OPERACION'=>"DIAS OP",
                'DIAS_OV'=>'DIAS_OV'



            ],

            "pinturas" =>  [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "MARCA" => "MARCA",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "DIAS_OV" => "DIAS OPERACION",

            ],

            "deformadora-alambre" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "FECHA_MOV" => "DIAS PLANTA",
                "FECHA_INI" => "DIAS OV",
            ],

            "troquelados-ojaletes", "ojaletes" => [
                "OP" => "OP",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "DIAS_CT" => "DIAS PLANTA",
                "DIAS_OV" => "DIAS OV",
            ],

            "piedra" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "COMPLETADA",
                "CANT_PENDIENTE" => "PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "FECHA_MOV" => "PLANTA",
                "FECHA_INI" => "OV",
            ],

            "electro" => [
                "OP" => "OP",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "DIAS_OV" => "DIAS OV",
                "DIAS_PLANTA" => "DIAS PLANTA"
            ],

            "encabezados" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ACABADO" => "ACABADO",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "FECHA_MOV" => "DIAS PLANTA",
                "FECHA_INI" => "DIAS OV",
            ],

            "troquelados-broches" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "LOTE" => "LOTE",
                "ARTE_OV" => "ARTE",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "DIAS_OPERACION" => "DIAS OPERACION",
            ],

            "troquelado-botones" => [
                "OP" => "OP",
                "OV" => "OV",
                "COD_PROD" => "CODIGO",
                "PRODUCTO" => "DESCRIPCION",
                "REFERENCIA" => "REFERENCIA",
                "ARTE_OV" => "ARTE",
                "MARCA"=>"MARCA",
                "CANT_COMPLETADA" => "CANT COMPLETADA",
                "CANT_PENDIENTE" => "CANT PENDIENTE",
                "FECHA_LIBERACION" => "FECHA LIBERACION",
                "DIAS_OPERACION" => "DIAS OPERACION",
            ]
        };
    }


    /**
     * @param $data
     * @param $headings
     * @param $title
     * @return Mpdf
     * @throws MpdfException
     */
    protected function generatePDF($data, $headings, $title)
    {
        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.MAX.Reports.Production.header', compact('title')));
        $pdf->SetHTMLFooter(View::make('pdfs.MAX.Reports.Production.footer'));
        $pdf->WriteHTML(View::make('pdfs.MAX.Reports.Production.template', compact('data', 'headings')), HTMLParserMode::HTML_BODY);
        $pdf->SetTitle("{$title} | EVPIU");
        return $pdf;
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
            'format' => 'Letter-L',
            'orientation' => 'L',
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
            'margin_top' => 20,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/MAX/Reports/Production/styles.css')), HTMLParserMode::HEADER_CSS);
        return $pdf;
    }

}

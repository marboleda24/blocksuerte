<?php

namespace App\Http\Controllers;

use App\Models\Art;
use App\Models\DesignRequirementArt;
use App\Models\DesignRequirementArtVersion;
use App\Models\EncoderCode;
use Illuminate\Contracts\Container\BindingResolutionException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class ArtsController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.arts');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = Art::orderBy('requerimiento', 'desc')
            ->get();

        return Inertia::render('Applications/Arts', [
            'arts' => $data,
        ]);
    }

    /**
     * @return Response
     */
    public function design_requirement_arts(): Response
    {
        $arts = DesignRequirementArt::with('versions.designer', 'versions.seller', 'versions.blueprint',
            'proposal', 'design_requirement')->whereNotNull(['design_requirement_id', 'proposal_id'])
            ->get();

        return Inertia::render('Applications/DesignRequirements/Art', [
            'arts' => $arts,
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function check_versions($id): JsonResponse
    {
        $versions = DesignRequirementArtVersion::where('art_id', '=', $id)
            ->get()
            ->map(function ($row) {
                return [
                    'version' => "v{$row->version}.0",
                    'product' => $row->product,
                    'designer' => $row->designer->name,
                    'url2D' => $row->url2D,
                    'url3D' => $row->url3D,
                    'urlBlueprint' => $row->blueprint->miniature,
                    'selected' => (bool)$row->enabled,
                ];
            });

        return response()->json($versions, 200);
    }

    /**
     * @param $param
     * @param string $column
     * @return \Illuminate\Http\Response|mixed
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function print($param, string $column = 'id')
    {
        $art = DesignRequirementArt::with('versions.designer', 'versions.seller', 'proposal', 'design_requirement')
            ->where($column, '=', $param)
            ->first();

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.art.header', compact('art')));
        $pdf->SetHTMLFooter(View::make('pdfs.art.footer'));
        $pdf->WriteHTML(View::make('pdfs.art.template', compact('art')), HTMLParserMode::HTML_BODY);

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

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/art/styles.css')),
            HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}

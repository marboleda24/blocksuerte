<?php

namespace App\Http\Controllers;

use App\Models\OtherUser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LabelController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $other_users = OtherUser::all();

        return Inertia::render('Applications/Labels/Index', [
            'packers' => $other_users->where('packer', '=', true),
            'reviewers' => $other_users->where('reviewer', '=', true),
        ]);
    }

    /**
     * @param $op
     * @return JsonResponse
     */
    public function search_production_order($op)
    {
        $query = DB::connection('MAX')
            ->table('CIEV_V_OP')
            ->where('ORDNUM_10', '=', $op)
            ->first();

        $query = [
            'product_code' => trim($query->PRTNUM_10),
            'product_description' => trim($query->PMDES1_01),
            'op' => trim($query->ORDNUM_10),
            'brand' => trim($query->UDFREF_10),
            'lot' => trim($query->LOTNUM_10),
            'order' => trim($query->ORDREF_10),
            'date' => Carbon::now()->format('d M Y'),
            'art' => trim($query->UDFREF_10),
            'reviewer' => '',
            'packer' => '',
            'quantity' => intval($query->CURQTY_10),
        ];

        return response()->json($query, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function generate_label(Request $request)
    {
        $file = public_path().'/labels_files/label_estrada.txt';
        $file = fopen($file, 'w');

        $values = $request->all();

        // Ciclo dependiente de la Cantidad de Etiquetas a Imprimir
        for ($i = 0; $i < $values['labels_quantity']; $i++) {
            $ZPL =
                "^XA
^FO45,65
^BCN,40,N,Y,N
^FR^FD{$values['product_code']}^FS
^CF0,25
^FO45,40^FD{$values['product_description']}^FS
^FO45,110^FD{$values['product_code']}^FS
^FO45,135^FDR: {$values['reviewer']}^FS
^FO45,160^FDE: {$values['packer']}^FS
^FO450,65^FDORDEN PROD: {$values['op']}^FS
^FO450,90^FDMARCA: {$values['brand']}^FS
^FO450,115^FDCANTIDAD: {$values['package_quantity']} unidades^FS
^FO450,140^FDPEDIDO: {$values['order']}^FS
^FO450,165^FDFECHA: {$values['date']}^FS
^FO450,190^FDESTRADA VELASQUEZ^FS
^XZ\n";

            fwrite($file, $ZPL);
        }
        fclose($file);

        return response()->json('success', 200);
    }
}

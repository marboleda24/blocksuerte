<?php

namespace App\Console\Commands;

use App\Mail\InvoiceAuditMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $documents = DB::connection('MAX')
            ->table('CIEV_V_AUDITORIA_FACTURACION')
            ->where('fecha', '=', Carbon::now()->subDay()->format('Y-m-d'))
            ->get();

        $errors = [];
        foreach ($documents as $document) {
            $result = $this->check_documents($document);

            if (count($result) > 0) {
                $errors[$document->numero] = $result;
            }
        }

        if (count($errors) > 0) {
            Mail::to(['dcorrea@estradavelasquez.com', 'sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->send(new InvoiceAuditMail(false, $errors));
        } else {
            Mail::to(['dcorrea@estradavelasquez.com', 'sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->send(new InvoiceAuditMail(true));
        }
    }


    /**
     * @param $document
     * @return array
     */
    protected function check_documents($document): array
    {
        $doc = DB::connection('DMS')
            ->table('Documentos')
            ->select(DB::raw("numero, tipo, FORMAT(fecha, 'yyyy-MM-dd') AS fecha, ROUND(valor_total, 2) AS valor_total,
                ROUND(iva, 2) AS iva, ROUND(retencion, 2) AS retencion, ROUND(retencion_iva, 2) AS retencion_iva,
                ROUND(retencion_ica, 2) AS retencion_ica, ROUND(descuento_pie, 2) AS descuento_pie, ROUND(fletes, 2) AS fletes,
                moneda, tasa, ROUND(valor_mercancia, 2) AS valor_mercancia"))
            ->where('tipo', '=', $document->tipo)
            ->where('numero', '=', $document->numero)
            ->first();

        $result = [];

        if ($doc) {
            if ($doc->tipo !== $document->tipo) {
                $result[] = (object)[
                    'field' => "Tipo Documento",
                    'MAX' => $document->tipo,
                    'DMS' => $doc->tipo,
                    'state' => false,
                    'msg' => "Tipo de documento no coincide"
                ];
            }

            if ($doc->fecha !== $document->fecha) {
                $result[] = (object)[
                    'field' => "Fecha",
                    'MAX' => $document->fecha,
                    'DMS' => $doc->fecha,
                    'state' => false,
                    'msg' => "Fecha no coincide"
                ];
            }

            if ($doc->valor_total == !$document->valor_total && $document->valor_total - $doc->valor_total !== 1) {
                $result[] = (object)[
                    'field' => "Valor Total",
                    'MAX' => $document->valor_total,
                    'DMS' => $doc->valor_total,
                    'state' => false,
                    'msg' => "Valor total con una diferencia superior a 1 peso"
                ];
            }

            if (intval($doc->iva) !== intval($document->iva)) {
                $result[] = (object)[
                    'field' => "IVA",
                    'MAX' => $document->iva,
                    'DMS' => $doc->iva,
                    'state' => false,
                    'msg' => "IVA no coincide"
                ];
            }

            if (intval($doc->retencion) !== intval($document->retencion)) {
                $result[] = (object)[
                    'field' => "RTEFTE",
                    'MAX' => $document->retencion,
                    'DMS' => $doc->retencion,
                    'state' => false,
                    'msg' => "RTEFTE no coincide"
                ];
            }

            if (intval($doc->retencion_iva) !== intval($document->retencion_iva)) {
                $result[] = (object)[
                    'field' => "RTEIVA",
                    'MAX' => $document->retencion_iva,
                    'DMS' => $doc->retencion_iva,
                    'state' => false,
                    'msg' => "RTEIVA no coincide"
                ];
            }

            if (intval($doc->retencion_ica) !== intval($document->retencion_ica)) {
                $result[] = (object)[
                    'field' => "RTEICA",
                    'MAX' => $document->retencion_ica,
                    'DMS' => $doc->retencion_ica,
                    'state' => false,
                    'msg' => "RTEICA no coincide"
                ];
            }

            if (intval($doc->descuento_pie) !== intval($document->descuento_pie)) {
                $result[] = (object)[
                    'field' => "Descuento",
                    'MAX' => $document->descuento_pie,
                    'DMS' => $doc->descuento_pie,
                    'state' => false,
                    'msg' => "Descuento no coincide"
                ];
            }

            if (intval($doc->fletes) !== intval($document->fletes)) {
                $result[] = (object)[
                    'field' => "Fletes",
                    'MAX' => $document->fletes,
                    'DMS' => $doc->fletes,
                    'state' => false,
                    'msg' => "Fletes no coinciden"
                ];
            }

            if ($doc->moneda !== $document->moneda) {
                $result[] = (object)[
                    'field' => "Moneda",
                    'MAX' => $document->moneda,
                    'DMS' => $doc->moneda,
                    'state' => false,
                    'msg' => "Moneda no coincide"
                ];
            }

            if (intval($doc->tasa) !== intval($document->tasa)) {
                $result[] = (object)[
                    'field' => "Tasa de cambio",
                    'MAX' => $document->tasa,
                    'DMS' => $doc->tasa,
                    'state' => false,
                    'msg' => "Tasa de cambio no coincide"
                ];
            }

            if (intval($doc->valor_mercancia) !== intval($document->valor_mercancia)) {
                $result[] = (object)[
                    'field' => "Valor Mercancia",
                    'MAX' => $document->valor_mercancia,
                    'DMS' => $doc->valor_mercancia,
                    'state' => false,
                    'msg' => "Valor de la mercancia no coincide"
                ];
            }
        }
        return $result;
    }
}

<?php

use App\Models\DesignRequirementArt;
use App\Models\EncoderCode;
use App\Models\HeaderOrder;
use App\Models\SupportDocumentHeader;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

if (! function_exists('moneyFormat')) {
    function moneyFormat($money): string
    {
        $amount = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);

        return $amount->formatCurrency(floatval($money), 'COP');
    }
}

if (! function_exists('calculateDelivery')) {
    function calculateDelivery($days): object
    {
        $business_days = DB::connection('MAX')
            ->table('Shop_Calendar')
            ->where('ShopDay', '=', 1)
            ->whereDate('DateValue', '>=', Carbon::now())
            ->get();

        if ($days > 0) {
            return $business_days[$days - 1];
        } else {
            $date = Carbon::now()->format('Y-m-d h:m:i');

            return (object) [
                'DateValue' => $date,
            ];
        }
    }
}

if (! function_exists('denominationCreator')) {
    function denominationCreator($array): string
    {
        $group = $array->reduce(function ($carry, $item) {
            $carry[$item->unit->code][] = $item;

            return $carry;
        }, []);
        $measurement = [];

        foreach ($group as $groupKey => $value) {
            $regx = collect($value)->map(function ($x) {
                if ($x->value && $x->value > 0 || preg_match("/^(?:[1-9]\d*|0(?!(?:\.0+)?$))?(?:\.\d+)?$/", $x->value)) {
                    return "{$x->characteristic->code}:".(fmod($x->value, 1) > 0 ? floatval($x->value) : intval($x->value));
                }
            })->toArray();

            $regx = array_filter($regx);

            if (count($regx) > 0) {
                $measurement[] = count($regx) > 1 ? implode(' ', $regx)."$groupKey" : implode('', $regx)."$groupKey";
            }
        }
        return count($measurement) > 1 ? implode(' ', $measurement) : implode('', $measurement);
    }
}

if (! function_exists('artCode')) {
    function artCode($letter): string
    {
        $arts = DesignRequirementArt::where('code', 'like', "$letter%")
            ->orderBy('code', 'asc')
            ->get()->map(function ($row) use ($letter) {
                return intval(ltrim($row->code, $letter));
            })->max();

        if (is_numeric($arts)) {
            return $letter.str_pad($arts + 1, 5, '0', STR_PAD_LEFT);
        } else {
            return "{$letter}00000";
        }
    }
}

if (! function_exists('last_support_document_consecutive')) {
    function last_support_document_consecutive($entity, $type): int
    {
        if ($entity === 'CIEV') {
            return SupportDocumentHeader::where('entity', '=', 'CIEV')
                ->where('type', '=', $type)
                ->max('consecutive')
                ? SupportDocumentHeader::where('entity', '=', 'CIEV')
                    ->where('type', '=', $type)
                    ->max('consecutive') + 1
                : 1;
        } else {
            return SupportDocumentHeader::where('entity', '=', 'GOJA')
                ->where('type', '=', $type)
                ->max('consecutive')
                ? SupportDocumentHeader::where('entity', '=', 'GOJA')
                    ->where('type', '=', $type)
                    ->max('consecutive') + 1
                : 1001;
        }
    }
}

if (! function_exists('calculateDV')) {
    function calculateDV($nit): float|bool|int
    {
        if (is_numeric(trim($nit))) {
            $secuencia = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
            $d = str_split(trim($nit));
            krsort($d);
            $cont = 0;
            unset($val);
            foreach ($d as $key => $value) {
                $val[$cont] = $value * $secuencia[$cont];
                $cont++;
            }
            $suma = array_sum($val);
            $div = intval($suma / 11);
            $num = $div * 11;
            $resta = $suma - $num;
            if ($resta == 1) {
                return $resta;
            } elseif ($resta != 0) {
                return 11 - $resta;
            } else {
                return $resta;
            }
        } else {
            return false;
        }
    }
}

if (! function_exists('current_retention_rates')) {
    function current_retention_rates($year): Collection
    {
        return DB::connection('MAX')
            ->table('CIEV_ParametrosReteFuente')
            ->where('Ano', '=', $year)
            ->get();
    }
}

if (! function_exists('current_retention_rates_v2')) {
    function current_retention_rates_v2($year): array
    {
        $crr = DB::connection('MAX')
            ->table('CIEV_ParametrosReteFuente')
            ->where('Ano', '=', $year)
            ->get();

        return [
            'services' => [
                'rate' => $crr->where('Tipo', '=', 'SERVICIOS')->first()->Tasa,
                'base' => $crr->where('Tipo', '=', 'SERVICIOS')->first()->Base,
            ],
            'sell' => [
                'rate' => $crr->where('Tipo', '=', 'VENTAS')->first()->Tasa,
                'base' => $crr->where('Tipo', '=', 'VENTAS')->first()->Base,
            ],
        ];
    }
}

if (! function_exists('getLastConsecutive')) {
    function getLastConsecutive($model): int
    {
        return $model::max('consecutive') + 1;
    }
}

if (! function_exists('getLastConsecutiveOrder')) {
    function getLastConsecutiveOrder(): int
    {
        return HeaderOrder::max('consecutive') + 1;
    }
}

if (! function_exists('generate_code_description')) {
    function generate_code_description($product_type, $line, $subline, $feature, $material, $measurement, $art_code = null, $galvanic_finish = null, $decorative_option = null): array
    {
        $code = "{$product_type}{$line->code}".substr($subline->code, 2, 4)."{$material->material_code}";
        $description = "{$product_type}-{$line->abbreviation} {$subline->abbreviation} {$feature} {$material->material_code} ".denominationCreator($measurement)." {$galvanic_finish} {$decorative_option} {$art_code}";

        return [
            'code' => alphaNumericIncrementWithCode($product_type, $line['code'], $subline['code'], $material->id, $code, 4),
            'description' => preg_replace('/\s+/', ' ', $description),
        ];
    }
}

if (! function_exists('alphaNumericIncrementWithCode')) {
    function alphaNumericIncrementWithCode($product_type_code, $line, $subline, $material_id, $code, $length): string
    {
        $code_list = EncoderCode::where('product_type_code', $product_type_code ?? '')
            ->where('state', '=', 1)
            ->where('line_code', $line ?? '')
            ->where('subline_code', $subline ?? '')
            ->where('material_id', $material_id ?? '')
            ->orderBy('code', 'desc')
            ->pluck('code')
            ->first();

        $code_list = is_null($code_list) ? [] : [$code_list];

        $incremental = 0;
        $chart_string_range = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $vector = [];
        $t = 0;
        $numberf = 0;

        for ($i = 0; $i < count($code_list); $i++) {
            if ($code === substr($code_list[$i], 0, 6) && strlen($code_list[$i]) === 10) {
                $string = substr($code_list[$i], 6);
                $text = trim(implode('', array_reverse(str_split($string))));
                $text = str_split($text);

                for ($k = 0; $k < $length; $k++) {
                    for ($j = 0; $j < 36; $j++) {
                        if (array_key_exists($k, $text) && $text[$k] === $chart_string_range[$j]) {
                            break;
                        }
                    }
                    $numberf += $j * pow(36, $k);
                }
                $vector[$t] = $numberf;
                $t++;
                $numberf = 0;
            }
        }

        $maxvector = count($vector) > 0 ? max($vector) : -1;
        if ($maxvector >= 0) {
            $incremental = $maxvector + 1;
        }
        $text2 = '';
        $incretemp = $incremental;
        for ($i = 0; $i < $length; $i++) {
            $incretemp = floor($incretemp) / 36;
            $text2 .= $chart_string_range[intval(round(($incretemp - floor($incretemp)) * 36))];
        }
        $text2 = trim(implode('', array_reverse(str_split($text2))));

        return "{$code}{$text2}";
    }
}

if (function_exists('UpdateTariffPosition')){
    /**
     * @return bool
     * @throws Throwable
     */
    function UpdateTariffPosition(): bool
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTILL 76.16.10.00.00' WHERE PRTNUM_29 LIKE '301%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.L 83.08.20.00.00' WHERE PRTNUM_29 LIKE '302__L%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.H 83.08.10.11.00' WHERE PRTNUM_29 LIKE '302__H%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.A 83.08.10.12.00' WHERE PRTNUM_29 LIKE '302__A%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'ARANDE 83.08.10.19.00' WHERE PRTNUM_29 LIKE '303%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '304%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '305%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '307%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '807%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '807%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '310%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '311%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '312%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '314%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '316%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '318%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '339%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '342%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '344%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE 'T04%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTERA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '347%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '319%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '326%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE 'T06%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '330%'");
            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE 'T09%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'DIJE 83.08.90.00.00' WHERE PRTNUM_29 LIKE '332%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'GANCHO 83.08.90.00.00' WHERE PRTNUM_29 LIKE '806%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.P 39.26.90.90.90' WHERE PRTNUM_29 IN('80643','80647') OR PRTNUM_29 LIKE '802%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '819%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'TROQUEL 82.07.90.00.00' WHERE PRTNUM_29 LIKE '820%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'MANUAL 84.63.90.10.00' WHERE PRTNUM_29 LIKE '821%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTERA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '847%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '306%'");

            DB::connection('MAX')->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 = '30201A080000000'");

            DB::connection('MAX')->commit();
            return true;
        }catch (Exception $e){
            DB::connection('MAX')->rollBack();
            return false;
        }
    }
}

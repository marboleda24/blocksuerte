<?php

namespace App\Exports\SecurityHealthWork;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsenteeismExport implements FromCollection, WithHeadings
{
    public string $start_date, $end_date;

    /**
     * @param $start_date
     * @param $end_date
     */
    public function __construct($start_date, $end_date){
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return DB::connection('DMS')
            ->table('V_CIEV_Incapacidades as I')
            ->join('V_CIEV_Sociodemografia as S', 'I.nit', '=', 'S.IDENTIFICACION')
            ->select('I.nit',
                'S.NOMBRES_APELLIDOS',
                'S.GENERO',
                'S.EDAD',
                'S.ANTIGUEDAD',
                'S.AREA',
                'S.CONTRATO',
                'I.EsProrroga',
                'I.dias_incap',
                'I.ConceptoIncapacidad',
                DB::raw("FORMAT (I.fecha_inicial, 'dd-MM-yyyy') as fecha_inicial"),
                DB::raw("FORMAT (I.fecha_final, 'dd-MM-yyyy') as fecha_final"),
                'I.SVE',
                'I.DescripcionDiagnostico',
                'I.SVE_INTERES',
                'S.EPS')
            ->whereBetween('fecha_inicial', [$this->start_date, $this->end_date])
            ->get();
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'IDENTIFICACIÓN',
            'NOMBRES Y APELLIDOS',
            'GENERO',
            'EDAD',
            'ANTIGÜEDAD',
            'ÁREA',
            'VINCULACIÓN',
            'INDICADOR PRORROGA',
            'DÍAS DE INCAPACIDAD',
            'TIPO INCAPACIDAD',
            'FECHA DESDE',
            'FECHA HASTA',
            'CÓDIGO CIE 10',
            'DESCRIPCIÓN PATOLOGIA',
            //'GRUPO RELACIONADOS DE DIAGNOSTICOS (GRD)',
            'SVE DE INTERES',
            'NOMBRE ENTIDAD (EPS)'
        ];
    }
}

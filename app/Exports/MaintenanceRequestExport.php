<?php

namespace App\Exports;

use App\Models\MaintenanceRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MaintenanceRequestExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return MaintenanceRequest::with('applicant', 'asset', 'work_orders')
            ->get()
            ->map(function ($row) {
                $work_orders = $row->work_orders->count() > 0 ? $row->work_orders->where('state', '=', '3')->last() : null;

                return [
                    $row->consecutive,
                    $row->applicant->name,
                    $row->asset->code,
                    $row->asset->name,
                    $row->planning_date->format('Y-m-d'),
                    $row->description,
                    $this->type_name($row->type),
                    $this->state_name($row->state),
                    $row->closing_date ? $row->closing_date->format('Y-m-d H:i A') : 'N/A',
                    $work_orders ? $work_orders->closing_date : null,
                    $row->created_at->format('Y-m-d H:i A'),
                    $row->updated_at->format('Y-m-d H:i A'),
                    $row->work_orders->count() > 0 ? $row->work_orders->count() : 0,
                ];
            });
    }

    /**
     * @param $type
     * @return string
     */
    protected function type_name($type): string
    {
        return match ($type) {
            'preventive' => 'PREVENTIVO',
            'corrective' => 'CORRECTIVO',
            'locative' => 'LOCATIVO',
            default => 'MEJORATIVO',
        };
    }

    /**
     * @param $state
     * @return string|void
     */
    protected function state_name($state)
    {
        switch ($state) {
            case '0':
                return 'ANULADO';
            case '1':
                return 'EN REVISION';
            case '2':
                return 'APROBADO';
            case '3':
                return 'EN PROCESO';
            case '4':
                return 'FINALIZADO';
            case '5':
                return 'RECHAZADO';
        }
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'CONSECUTIVO',
            'SOLICITANTE',
            'CÓDIGO ACTIVO',
            'NOMBRE ACTIVO',
            'FECHA DE PLANEACIÓN',
            'DESCRIPCIÓN',
            'TIPO',
            'ESTADO',
            'FECHA DE CIERRE',
            'FECHA DE CIERRE ORDER DE TRABAJO',
            'CREADO EL',
            'ACTUALIZADO EL',
            'ORDENES DE TRABAJO',
        ];
    }
}

<!DOCTYPE html>
<html lang="es">

<body>
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-left">EMPLEADO</th>
                <td class="text-left">{{ $data->employee->nombres }} -  <b>CC: </b> {{ $data->employee->nit }}</td>

            </tr>
            <tr>
                <th class="text-left">DESDE EL</th>
                <td class="text-left">{{ \Carbon\Carbon::parse($data->start_date)->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <th class="text-left">HASTA EL</th>
                <td class="text-left">{{ \Carbon\Carbon::parse($data->end_date)->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <th class="text-left">JUSTIFICACION</th>
                <td class="text-left">{{ $data->justify }}</td>
            </tr>
            <tr>
                <th class="text-left">JEFE APROBO</th>
                <td class="text-left">{{ $data->boss->nombres }}</td>
            </tr>
            <tr>
                <th class="text-left">RRHH APROBO</th>
                <td class="text-left">{{ $data->approved_rrhh->name }}</td>
            </tr>
        </thead>

    </table>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<style>
    table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        max-width: 100%;
        margin-bottom: 0px;
        background-color: transparent;
    }

    table thead tr th {
        padding: 0.20rem;
        background-color: rgb(25, 25, 26);
        color: #fff;
        text-align: center;
        font-weight: bold;
    }

    table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tbody td {
        padding: 0.20rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
        text-align: left;
    }

    table tbody td:first-child {
        border-left: none;
    }

    table tbody td:last-child {
        border-right: none;
    }

    table tbody td {
        border-top: none;
    }

    table {
        border-radius: 10px;
        overflow: hidden;
    }

    img {
        width: 100%;
        text-align: center;
    }

    .page_break { page-break-before: always; }

</style>
<body>
<table style="margin-bottom: 20px">
    <tbody>
        <tr>
            <td>
                <table style="font-size: 10px;  border: 1px solid rgb(25,25,26); border-radius: 5px;">
                    <thead>
                    <tr>
                        <th style="text-align: left">SVE</th>
                        <th>CANT</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 0; $i < count($reports->inability->labels); $i++)
                        <tr>
                            <td>{{ $reports->inability->labels[$i] }}</td>
                            <td style="text-align: center !important;">{{ $reports->inability->values[$i] }}</td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </td>
            <td>
                <h3 style="text-align: center">Causas {{ $years->start }} – {{ $years->end }}</h3>
                <img src="{{$charts->inability}}" alt="">
            </td>
        </tr>
    </tbody>
</table>

<div class="page_break"></div>

<table style="margin-bottom: 20px">
    <tbody>
    <tr>
        <td>
            <table style="font-size: 10px;  border: 1px solid rgb(25,25,26); border-radius: 5px;">
                <thead>
                <tr>
                    <th style="text-align: left">CENTRO</th>
                    <th>CANT</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0; $i < count($reports->work_center->labels); $i++)
                    <tr>
                        <td>{{ $reports->work_center->labels[$i] }}</td>
                        <td style="text-align: center !important;">{{ $reports->work_center->values[$i] }}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </td>
        <td>
            <h3 style="text-align: center">Centros de trabajo {{ $years->start }} – {{ $years->end }}</h3>
            <img src="{{$charts->work_center}}" alt="">
        </td>
    </tr>
    </tbody>
</table>

<div class="page_break"></div>

<table style="margin-bottom: 20px">
    <tbody>
    <tr>
        <td>
            <table style="font-size: 10px;  border: 1px solid rgb(25,25,26); border-radius: 5px;">
                <thead>
                <tr>
                    <th style="text-align: left">DIAGNOSTICO</th>
                    <th>CANT</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0; $i < count($reports->diagnostic->labels); $i++)
                    <tr>
                        <td>{{ $reports->diagnostic->labels[$i] }}</td>
                        <td style="text-align: center !important;">{{ $reports->diagnostic->values[$i] }}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </td>
        <td>
            <h3 style="text-align: center">Diagnostico {{ $years->start }} – {{ $years->end }}</h3>
            <img src="{{$charts->diagnostic}}" alt="">
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
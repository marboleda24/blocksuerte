<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {font-family: sans-serif;
            font-size: 10pt;
        }
        p {	margin: 0pt; }
        table.items {
            border: 0.1mm solid #000000;
        }
        td { vertical-align: center; }
        .items td {
            border: 0.1mm solid #000000;
            padding: 5px;
        }
        table thead td { background-color: #EEEEEE;
            text-align: center;
            border: 0.1mm solid #000000;
            font-variant: small-caps;
        }
        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #000000;
            background-color: #FFFFFF;
            border: 0mm none #000000;
            border-top: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }


        .center {
            vertical-align: middle;
            text-align: center;
            font-weight: bold;
            justify-content: center;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #000000;
        }
        .items td.cost {
            text-align: "." right;
        }
    </style>
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    <tr>
        <td colspan="2">PRODUCTO</td>
        <td colspan="3">TIPO DE PRODUCTO</td>
        <td colspan="3">LINEA</td>
        <td colspan="3">SUBLINEA</td>
        <td colspan="3">MATERIAL</td>
        <td colspan="3">MEDIDA</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>CODIGO</td>
        <td>DESCRIPCION</td>
        <td>FORANEA</td>
        <td>DESCRIPCION</td>
        <td>COINCIDE</td>
        <td>FORANEA</td>
        <td>DESCRIPCION</td>
        <td>COINCIDE</td>
        <td>FORANEA</td>
        <td>DESCRIPCION</td>
        <td>COINCIDE</td>
        <td>FORANEA</td>
        <td>DESCRIPCION</td>
        <td>COINCIDE</td>
        <td>FORANEA</td>
        <td>DESCRIPCION</td>
        <td>COINCIDE</td>
        <td>ESTADO</td>
        <td>FECHA CREACION</td>
    </tr>
    </thead>
    <tbody>
        @foreach($results as $result)
            <tr>
                <td>{{ $result->code }}</td>
                <td>{{ $result->description }}</td>

                <td>{{ $result->product_type->foreign }}</td>
                <td>{{ $result->product_type->description }}</td>
                <td align="center" style="color: {{ $result->product_type->match ? 'green' : 'red'}}; font-weight: bold">{{ $result->product_type->match ? 'SI' : 'NO' }}</td>

                <td>{{ $result->line->foreign }}</td>
                <td>{{ $result->line->description }}</td>
                <td align="center" style="color: {{ $result->line->match ? 'green' : 'red'}}; font-weight: bold">{{ $result->line->match ? 'SI' : 'NO' }}</td>

                <td>{{ $result->subline->foreign }}</td>
                <td>{{ $result->subline->description }}</td>
                <td align="center" style="color: {{ $result->subline->match ? 'green' : 'red'}}; font-weight: bold">{{ $result->subline->match ? 'SI' : 'NO' }}</td>

                <td>{{ $result->material->foreign }}</td>
                <td>{{ $result->material->description }}</td>
                <td align="center" style="color: {{ $result->material->match ? 'green' : 'red'}}; font-weight: bold">{{ $result->material->match ? 'SI' : 'NO' }}</td>

                <td>{{ $result->measurement->foreign }}</td>
                <td>{{ $result->measurement->description }}</td>
                <td align="center" style="color: {{ $result->measurement->match ? 'green' : 'red'}}; font-weight: bold">{{ $result->measurement->match ? 'SI' : 'NO' }}</td>

                <td align="center" style="color: {{ $result->all_match === 'FAIL' ? 'red' : 'green' }}; font-weight: bold">{{ $result->all_match }}</td>
                <td>{{ $result->created_at }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            /* font-family: "sans-serif"; */
            font-family: Roboto, sans-serif;
        }

        html,
        body {
            vertical-align: baseline;
        }

        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section {
            display: block;
        }

        body {
            line-height: 1;
        }

        ol,
        ul {
            list-style: none;
        }

        blockquote,
        q {
            quotes: none;
        }

        blockquote:before,
        blockquote:after,
        q:before,
        q:after {
            content: "";
            content: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        body {
            font-family: Roboto, sans-serif;
            font-size: 14px;
            font-weight: 400;
        }

        strong {
            font-weight: 700;
        }

        .row {
            font-size: 11px;
        }

        #container {
            position: relative;
            padding: 4%;
            width: 750px;
            margin: 0 auto;
        }

        header {
            height: 15cm;
            position: fixed;
            top: -60px;
            left: 0.5cm;
            right: 0.5cm;
            bottom: 1cm;
        }

        .page-break {
            page-break-after: always;
        }

        #header > #reference {
            position: absolute;
            margin-top: 20px;
        }

        #reference {
            font-size: 10px;
        }

        #header > #reference h3 {
            margin: 0;
        }

        #header > #reference h4 {
            margin: 0;
            font-size: 85%;
            font-weight: 600;
        }

        #header > #reference p {
            margin: 0;
            margin-top: 2%;
            font-size: 11px;
            text-align: center;
        }

        #fromto {
            height: 160px;
        }

        #fromto > #from,
        #fromto > #to {
            width: 33%;
            min-height: 90px;
            margin-top: 30px;
            font-size: 85%;
            padding: 1.5%;
            line-height: 120%;
        }

        #fromto > #from {
            float: left;
            width: 33%;
            background: #efefef;
            margin-top: 30px;
            font-size: 85%;
            padding: 1.5%;
        }

        #fromto > #to {
            float: left;
            margin-left: 12px;
            width: 31%;
            border: solid grey 1px;
        }

        .subheader {
            margin-top: 10px;
        }

        .subheader > p {
            font-weight: 700;
            text-align: right;
            margin-bottom: 1%;
            font-size: 65%;
        }

        .subheader > table {
            width: 100%;
            font-size: 85%;
            border: solid grey 1px;
        }

        .subheader > table th:first-child {
            text-align: left;
        }

        .subheader > table th {
            font-weight: 400;
            padding: 1px 4px;
        }

        .subheader > table td {
            padding: 1px 4px;
        }

        .subheader > table th:nth-child(2),
        .subheader > table th:nth-child(4) {
            width: 45px;
        }

        .subheader > table th:nth-child(3) {
            width: 60px;
        }

        .subheader > table th:nth-child(5) {
            width: 80px;
        }

        .subheader > table tr td:not(:first-child) {
            text-align: right;
            padding-right: 1%;
        }

        .subheader table td {
            border-right: solid grey 1px;
        }

        .subheader table tr td {
            padding-top: 3px;
            padding-bottom: 3px;
            height: 10px;
        }

        .subheader table tr:nth-child(1) {
            border: solid grey 1px;
        }

        .subheader table tr th {
            border-right: solid grey 1px;
            padding: 3px;
        }

        .subheader table tr:nth-child(2) > td {
            padding-top: 8px;
        }

        .items {
            margin-top: 10px;
        }

        .items > p {
            font-weight: 700;
            text-align: right;
            margin-bottom: 1%;
            font-size: 65%;
        }

        .items > table {
            width: 100%;
            font-size: 85%;
            border: solid grey 1px;
        }

        .items > table th:first-child {
            text-align: left;
        }

        .items > table th {
            font-weight: 400;
            padding: 1px 4px;
        }

        .items > table td {
            padding: 1px 4px;
        }

        .items > table th:nth-child(2),
        .items > table th:nth-child(4) {
            width: 45px;
        }

        .items > table th:nth-child(3) {
            width: 60px;
        }

        .items > table th:nth-child(5) {
            width: 80px;
        }

        .items > table tr td:not(:first-child) {
            text-align: right;
            padding-right: 1%;
        }

        .items table td {
            border-right: solid grey 1px;
        }

        .items table tr td {
            padding-top: 3px;
            padding-bottom: 3px;
            height: 10px;
        }

        .items table tr:nth-child(1) {
            border: solid grey 1px;
        }

        .items table tr th {
            border-right: solid grey 1px;
            padding: 3px;
        }

        .items table tr:nth-child(2) > td {
            padding-top: 8px;
        }

        .summary {
            height: 120px;
            margin-top: 20px;
        }

        .summary #note {
            float: left;
        }

        .summary #note h4 {
            font-size: 10px;
            font-weight: 600;
            font-style: italic;
            margin-bottom: 3px;
        }

        .summary #note p {
            font-size: 10px;
            font-style: italic;
        }

        .summary #total table {
            font-size: 85%;
            width: 260px;
            float: right;
            margin-top: 40px;
        }

        .summary #total table td {
            padding: 3px 4px;
        }

        .summary #total table tr td:last-child {
            text-align: right;
        }

        .summary #total table tr:nth-child(3) {
            background: #efefef;
            font-weight: 600;
        }

        #footer {
            margin: auto;
            margin-top: 14px;
            left: 4%;
            bottom: 4%;
            right: 4%;
            border-top: solid grey 1px;
        }

        #footer p {
            margin-top: 3px;
            font-size: 65%;
            line-height: 140%;
            text-align: center;
        }

        .sinbode tr td,
        .sinbode tr,
        .sinbode tr th {
            border: none !important;
        }

        .summarys {
            border: 1px solid grey;
            margin-top: 10px;
            padding: 10px;
        }

        #fromto > #from,
        #fromto > #qr {
            width: 11%;
            min-height: 90px;
            margin-top: 30px;
            font-size: 85%;
            padding: 1.5%;
            line-height: 120%;
        }

        #fromto > #from {
            width: 46%;
            min-height: 90px;
            margin-top: 30px;
            font-size: 85%;
            padding: 1.5%;
            line-height: 120%;
        }

        #fromto > #qr {
            float: right;
        }

        .summary #firma table td,
        .summary #firma table tr {
            border: none !important;
        }

        #empresa-header {
            text-align: center;
            font-size: 14px;
        }

        #empresa-header1 {
            text-align: center;
            font-size: 10px;
        }

        td,
        th {
            text-align: left;
            padding: 2px 4px;
        }

        .table td,
        .table th {
            font-size: 10px;
            font-weight: 400;
            padding: 6px 4px;
            border: 1px solid rgb(222, 226, 230);
            border-collapse: collapse;
            vertical-align: top;
        }

        .table tr:nth-child(odd) td {
            background-color: rgb(248, 249, 250);
        }

        .table th {
            font-weight: bold;
        }

        .text-word {
            text-align: justify;
            text-justify: inter-word;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }

        .vertical-align-top {
            vertical-align: top;
        }

    </style>
</head>
<body style="margin: 3%">
<table width="100%">
    <tr>
        <td style="width: 25%;" class="text-center vertical-align-top">
            <div id="reference">
                <div id="empresa-header">
                    <strong>AUDITORIA DE FACTURAS</strong><br>
                </div>
                <p>FECHA DE GENERACION: <br>
                {{ \Carbon\Carbon::now()->format('Y-m-d') }}
            </div>
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div id="empresa-header">
                <strong>CI ESTRADA VELASQUEZ Y CIA. SAS</strong><br>
            </div>
            <div id="empresa-header1">
                CRA 55 29C 14 - Medellín - Antioquia - Colombia <br>
                Telefono - 2656665 <br>
                Correo electronico: info@estradavelasquez.com <br>
                FPA-GVC-001
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img style="width: 80px; height: auto; margin-top: -10px" src="{{ $imgBase64 }}" alt="logo">
        </td>
    </tr>
</table>

@foreach($errors as $key => $error)
    <table class="table" style="width: 100%; margin-top: 2%">
        <thead>
        <tr style="text-align: center !important;">
            <th colspan="5" style="text-align: center !important;">{{ $key }}</th>
        </tr>
        <tr>
            <th class="text-center">CAMPO</th>
            <th class="text-center">MAX</th>
            <th class="text-center">DMS</th>
            <th class="text-center">ESTADO</th>
            <th class="text-center">MENSAJE</th>
        </tr>
        </thead>
        <tbody>
            @foreach($error as $value)
                <tr>
                    <td>{{ $value->field }}</td>
                    <td>{{ $value->MAX }}</td>
                    <td>{{ $value->DMS }}</td>
                    <td>{{ $value->state }}</td>
                    <td>{{ $value->msg }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endforeach
</body>
</html>
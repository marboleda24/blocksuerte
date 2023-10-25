
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <tbody>
        <tr>
            <td width="25%" style="font-weight: bold">ARTICULO</td>
            <td width="25%">{{ $proposal->product }}</td>
            <td width="25%" style="font-weight: bold">VENDEDOR(A)</td>
            <td width="25%">{{ $proposal->header->seller->name }}</td>
        </tr>
        <tr>
            <td width="25%" style="font-weight: bold">TAMAÑO</td>
            <td width="25%">{{ $proposal->measure }}</td>
            <td width="25%" style="font-weight: bold">MATERIAL</td>
            <td width="25%">{{ $proposal->material->material->name }}</td>
        </tr>
        <tr>
            <td width="25%" style="font-weight: bold">PESO(GR)</td>
            <td width="25%">{{ $proposal->weight }}</td>
            <td width="25%" style="font-weight: bold">DISEÑADORA</td>
            <td width="25%">{{ $proposal->header->assigned_designer ? $proposal->header->assigned_designer->name : 'SIN ASIGNAR' }}</td>
        </tr>
        <tr>
            <td width="25%" style="font-weight: bold">MARCA</td>
            <td width="25%">{{ $proposal->header->brand->name }}</td>
            <td width="25%" style="font-weight: bold">FECHA</td>
            <td width="25%">{{ $proposal->created_at->format('Y-m-d h:m a') }}</td>
        </tr>
        <tr>
            <td width="25%" style="font-weight: bold">CARACTERÍSTICA</td>
            <td width="25%">{{ $proposal->feature->name }}</td>
            <td width="25%" style="font-weight: bold">DETALLES</td>
            <td width="25%">{{ $proposal->details }}</td>
        </tr>
        @if ($proposal->features_detail)
            <tr>
                <td colspan="2" width="50%" style="font-weight: bold">CARACTERÍSTICAS</td>
                <td colspan="2" width="50%">{{ $proposal->features_detail }}</td>
            </tr>
        @endif
    </tbody>
</table>
<br>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
        <tr class="text-center">
            <td width="50%" style="font-weight: bold">DIBUJO 2D</td>
            <td width="50%" style="font-weight: bold">PLANO CON MEDIDAS</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="50%" style="justify-content: center; text-align: center; align-items: center">
                @if ($proposal->url2D)
                    <img src="{{ $proposal->url2D }}" alt="image_2D" style="width: 370px; height: 370px; object-fit: scale-down;">
                @endif
            </td>
            <td width="50%" style="justify-content: center; text-align: center; align-items: center">
                @if ($proposal->blueprint)
                    <img src="{{ $proposal->blueprint->miniature }}" alt="image_blueprint" style="width: 370px; height: 370px; object-fit: scale-down;">
                @else
                <span> pendientes por gestionar en 3D Y Planos </span>
                @endif
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>

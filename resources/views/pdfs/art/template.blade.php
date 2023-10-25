
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
        <td width="25%">{{ "{$art->product->code} – {$art->product->description}" }}</td>
        <td width="25%" style="font-weight: bold">MATERIAL</td>
        <td width="25%">{{ $art->product->material->material->name }}</td>
    </tr>
    <tr>
        <td width="25%" style="font-weight: bold">PESO</td>
        <td width="25%">{{ $art->proposal->weight }}</td>
        <td width="25%" style="font-weight: bold">MARCA</td>
        <td width="25%">{{ $art->design_requirement->brand->name }}</td>
    </tr>
    <tr>
        <td width="25%" style="font-weight: bold">VENDEDOR(A)</td>
        <td width="25%">{{ $art->design_requirement->seller->name }}</td>
        <td width="25%" style="font-weight: bold">DISEÑADOR(A)</td>
        <td width="25%">{{ $art->design_requirement->assigned_designer->name }}</td>
    </tr>
    <tr>
        <td colspan="2" width="50%" style="font-weight: bold">FECHA</td>
        <td colspan="2" width="50%">{{ $art->design_requirement->created_at }}</td>
    </tr>
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
            @if ($art->proposal->url2D)
                <img src="{{ $art->proposal->url2D }}" alt="image_2D" style="width: 370px; height: 370px; object-fit: scale-down;">
            @endif
        </td>
        <td width="50%" style="justify-content: center; text-align: center; align-items: center">
            @if ($art->proposal->blueprint)
                <img src="{{ $art->proposal->blueprint->miniature }}" alt="image_blueprint" style="width: 370px; height: 370px; object-fit: scale-down;">
            @endif
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>

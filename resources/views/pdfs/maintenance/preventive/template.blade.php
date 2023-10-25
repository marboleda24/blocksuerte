<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
   <thead>
   <tr>
       <td width="10%">SEMANA</td>
       <td width="45%">ACTIVO</td>
       <td width="45%">PRÓXIMO MANTENIMIENTO</td>
   </tr>
   </thead>
    <tbody
    @foreach($query as $key => $item)>
        @foreach($item as $idx => $value)
            <tr>
                @if($item->keys()->first() === $idx)
                    <td width="10%" class="center" rowspan="{{ count($item) }}">{{ $key }}</td>
                @endif
                <td width="45%">
                    {{ $value->code }} – {{ $value->name }}
                </td>
                <td width="45%">
                    {{ $value->next }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>

</body>
</html>

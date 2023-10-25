<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

@foreach($box_list as $key => $item)
    @if($key > 0)
        <br>
        <br>
    @endif

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
        <tbody>
        <tr>
            <td width="50%" style="font-size: 15px; padding: 15px; vertical-align: middle" class="vertical-center">
                <div style="font-size: 20px">
                    <p>
                        <b>CLIENTE: </b> {{ $customer_data[0]['customer'] }}
                    </p>

                    <p>
                        <b>DIRECCION: </b> {{ $customer_data[0]['address'] }}
                    </p>

                    <p>
                        <b>CIUDAD: </b> {{ $customer_data[0]['city'] }} – {{ $customer_data[0]['country'] }}
                    </p>

                    <p>
                        <b>TELEFONO: </b> {{ $customer_data[0]['phone'] }}
                    </p>
                </div>
            </td>
            <td width="50%" class="centered">
                <img src="img/logo_ev_new.png" alt="" width="10%" style="margin-bottom: 10px; margin-top: 10px">

                <h1>CAJA # {{ $key+1 }}</h1>
                <h2>REMITE</h2>
                <h3>CR 55 #29C – 14 <br> ZONA INDUSTRIAL DE BELEN</h3>
                <h3>(604) 2656665</h3>
                <h3>info@estradavelasquez.com</h3>
                <h3>www.estradavelasquez.com</h3>
                <h3>Medellin – Colombia</h3>
            </td>
        </tr>
        </tbody>
    </table>

    @if(($key + 1) % 3 === 0 && ($key + 1) > 1)
        <pagebreak>
    @else
        <br>
        <dottab/>
    @endif


@endforeach

</body>
</html>

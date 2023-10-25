<x-mail::message>

    Cordial saludo,


    EVPIU le informa que la facturación del día de hoy ({{ $date }}) fue:

## NACIONALES:
<x-mail::table>
    |          BRUTO         	|          DESCUENTO         	|          SUBTOTAL         	|          IVA         	|          TOTAL         	|
    |:----------------------:	|:--------------------------:	|:-------------------------:	|:--------------------:	|:----------------------:	|
    | {{ number_format($national->BRUTO, 2, ',' , '.') }} 	| {{ number_format($national->DESCUENTO, 2, ',' , '.') }} 	| {{ number_format($national->SUBTOTAL, 2, ',' , '.') }} 	| {{ number_format($national->IVA, 2, ',' , '.') }} 	| {{ number_format($national->TOTAL, 2, ',' , '.') }} 	|
</x-mail::table>


## CI:
<x-mail::table>
    |          BRUTO         	|          DESCUENTO         	|          SUBTOTAL         	|          IVA         	|          TOTAL         	|
    |:----------------------:	|:--------------------------:	|:-------------------------:	|:--------------------:	|:----------------------:	|
    | {{ number_format($ci->BRUTO, 2, ',' , '.') }} 	| {{ number_format($ci->DESCUENTO, 2, ',' , '.') }} 	| {{ number_format($ci->SUBTOTAL, 2, ',' , '.') }} 	| {{ number_format($ci->IVA, 2, ',' , '.') }} 	| {{ number_format($ci->TOTAL, 2, ',' , '.') }} 	|
</x-mail::table>


## TOTAL:
<x-mail::table>
    |          BRUTO         	|          DESCUENTO         	|          SUBTOTAL         	|          IVA         	|          TOTAL         	|
    |:----------------------:	|:--------------------------:	|:-------------------------:	|:--------------------:	|:----------------------:	|
    | {{ number_format($total->BRUTO, 2, ',' , '.') }} 	| {{ number_format($total->DESCUENTO, 2, ',' , '.') }} 	| {{ number_format($total->SUBTOTAL, 2, ',' , '.') }} 	| {{ number_format($total->IVA, 2, ',' , '.') }} 	| {{ number_format($total->TOTAL, 2, ',' , '.') }} 	|
</x-mail::table>


## MES ACTUAL:
<x-mail::table>
    |          BRUTO         	|          DESCUENTO         	|          SUBTOTAL         	|          IVA         	|          TOTAL         	|
    |:----------------------:	|:--------------------------:	|:-------------------------:	|:--------------------:	|:----------------------:	|
    | {{ number_format($current_month->BRUTO, 2, ',' , '.') }} 	| {{ number_format($current_month->DESCUENTO, 2, ',' , '.') }} 	| {{ number_format($current_month->SUBTOTAL, 2, ',' , '.') }} 	| {{ number_format($current_month->IVA, 2, ',' , '.') }} 	| {{ number_format($current_month->TOTAL, 2, ',' , '.') }} 	|
</x-mail::table>


## MISMO MES AÑO ANTERIOR:
<x-mail::table>
    |          BRUTO         	|          DESCUENTO         	|          SUBTOTAL         	|          IVA         	|          TOTAL         	|
    |:----------------------:	|:--------------------------:	|:-------------------------:	|:--------------------:	|:----------------------:	|
    | {{ number_format($last_year->BRUTO, 2, ',' , '.') }} 	| {{ number_format($last_year->DESCUENTO, 2, ',' , '.') }} 	| {{ number_format($last_year->SUBTOTAL, 2, ',' , '.') }} 	| {{ number_format($last_year->IVA, 2, ',' , '.') }} 	| {{ number_format($last_year->TOTAL, 2, ',' , '.') }} 	|
</x-mail::table>

    Gracias,
    Equipo de sistemas de Estrada velasquez.
</x-mail::message>

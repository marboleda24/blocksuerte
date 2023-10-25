<template>
    <div>
        <template v-if="Object.keys(op).length > 0">
            <div class="box" v-for="(item, key) in op" :class="{'mt-5' : key}">
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <th colspan="7">CLIENTE: {{key}}</th>
                                <th colspan="6">VENDEDOR: {{}}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-center">ORDEN DE VENTA</th>
                                <th colspan="4" class="text-center">PRODUCTO</th>
                                <th colspan="2" class="text-center">FECHAS</th>
                                <th colspan="4" class="text-center">CANTIDADES</th>
                            </tr>

                            <tr class="text-center">
                                <th>PEDIDO EVPIU</th>
                                <th>NUMERO</th>
                                <th>ORDEN DE COMPRA</th>
                                <th>REFERENCIA</th>
                                <th>DESCRIPCIÓN</th>
                                <th>CPC</th>
                                <th>NOTAS</th>
                                <th>OV</th>
                                <th>DESPACHO</th>
                                <th>INICIAL</th>
                                <th>ENVIADA</th>
                                <th>FACTURADA</th>
                                <th>PENDIENTE</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in item">

                                <td class="text-center">
                                    {{}}
                                </td>
                                <td class="text-right">{{ parseInt() }}</td>
                                <td class="text-right">{{ parseInt() }}</td>
                                <td class="text-right">{{ parseInt() }}</td>
                                <td class="text-right">{{ parseInt() }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script >
export default {
    props: {
        op: {
            Array
        },
    },
    data() {
        return {

        }
    }
}


</script>

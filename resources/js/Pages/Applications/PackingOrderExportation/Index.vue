<template>
    <div>
        <Head title="Órdenes de exportacion"/>

        <portal to="application-title">
            Órdenes de exportación
        </portal>

        <portal to="actions">
            <Link :href="route('packing-order-exportation.pack-list')" class="btn btn-secondary">
                <font-awesome-icon icon="boxes-packing" class="mr-2"/>
                Empaque
            </Link>
        </portal>

        <div>
            <template v-if="Object.keys(orders).length > 0">
                <div class="box" v-for="(item, key) in orders" :class="{'mt-5' : key}">
                    <div class="p-5">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th colspan="7">CLIENTE: {{ key }}</th>
                                    <th colspan="6">VENDEDOR: {{ item[0].NOMBRE_VENDEDOR }}</th>
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
                                    <td class="text-center">{{ row.PEDIDO_EVPIU }}</td>
                                    <td class="text-center">{{ row.OV_ITEM }}</td>
                                    <td class="text-center">{{ row.OC }}</td>
                                    <td class="text-center">{{ row.COD_PRODUCTO }}</td>
                                    <td class="text-left">{{ row.DESCRIPCION_PRODUCTO }}</td>
                                    <td class="text-left">{{ row.COD_PROD_CLIENTE }}</td>
                                    <td class="text-left">{{ row.NOTAS }}</td>
                                    <td class="text-center">{{ $h.formatDate(row.FECHA_OV, 'YYYY-MM-DD') }}</td>
                                    <td class="text-center">
                                        {{ row.FECHA_DESPACHO ? $h.formatDate(row.FECHA_DESPACHO, 'YYYY-MM-DD') : '-' }}
                                    </td>
                                    <td class="text-right">{{ parseInt(row.CANT_ACTUAL) }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_DESPACHADA) }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_FACTURADA) }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_PENDIENTE) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";

export default {
    props: {
        orders: Object
    },

    components: {
        Link,
        Head
    },
}

</script>

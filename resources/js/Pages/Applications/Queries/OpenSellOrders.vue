<template>
    <div>
        <Head title="Órdenes de venta abiertas"/>

        <portal to="application-title">
            Órdenes de venta abiertas
        </portal>

        <div>
            <template v-if="Object.keys(query).length > 0">
                <div class="box" v-for="(item, key) in query" :class="{'mt-5' : key}">
                    <div class="p-5">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th colspan="6">CLIENTE: {{ key }}</th>
                                    <th colspan="5">TIPO CLIENTE: {{ item[0].CLASIFICACION_CLIENTE }}</th>
                                    <th rowspan="2"></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-center">ORDEN DE VENTA</th>
                                    <th colspan="3" class="text-center">PRODUCTO</th>
                                    <th colspan="3" class="text-center">DESPACHO</th>
                                </tr>

                                <tr class="text-center">
                                    <th>NUMERO</th>
                                    <th>FECHA</th>
                                    <th>ORDEN DE COMPRA</th>
                                    <th>CANTIDAD</th>
                                    <th>REFERENCIA</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th>CPC</th>
                                    <th>NOTAS</th>
                                    <th>FECHA</th>
                                    <th>CANT.ENV</th>
                                    <th>CANT.FACT</th>
                                    <th>CANT. PENDIENTE</th>
                                    <th>X FACTURAR</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in item">
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-secondary" @click="view(row.OV_ITEM.substring(0, 12), key, item[0].NOMBRE_VENDEDOR)">
                                            {{ row.OV_ITEM.substring(0, 12) }}
                                        </button>
                                    </td>
                                    <td class="text-center">{{ $h.formatDate(row.FECHA_OV , 'YYYY-MM-DD')}}</td>
                                    <td class="text-center">{{ row.OC }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_ACTUAL) }}</td>
                                    <td class="text-center">{{ row.COD_PRODUCTO }}</td>
                                    <td class="text-left">{{ row.DESCRIPCION_PRODUCTO}}</td>
                                    <td class="text-left">{{ row.COD_PROD_CLIENTE}}</td>
                                    <td class="text-left">{{ row.NOTAS}}</td>
                                    <td class="text-center">{{ row.FECHA_DESPACHO ? $h.formatDate(row.FECHA_DESPACHO , 'YYYY-MM-DD') : '-' }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_DESPACHADA) }}</td>
                                    <td class="text-right" :class="{'bg-red-200 dark:bg-red-800': row.CANT_FACTURADA !== row.CANT_DESPACHADA }">
                                        {{ parseInt(row.CANT_FACTURADA) }}
                                    </td>
                                    <td class="text-right">{{ parseInt(row.CANT_PENDIENTE) }}</td>
                                    <td class="text-right">{{ parseInt(row.CANT_DESPACHADA) - parseInt(row.CANT_FACTURADA) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>

            <template v-else>
                <div class="box p-5 text-danger text-center">
                    Ningún registro encontrado
                </div>
            </template>

            <jet-dialog-modal :show="modal.open" @close="closeModal" max-width=5xl>
                <template #title>
                    Seguimiento OV
                </template>

                <template #content>
                    <div class="grid grid-cols-2 px-5 font-bold text-lg">
                        <div>
                            VENDEDOR: {{ modal.seller }}
                        </div>
                        <div>
                            CLIENTE: {{ modal.customer }}
                        </div>
                    </div>

                    <div class="box mt-5" v-if="modal.data.manufacture_orders.length > 0">
                        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto uppercase">
                                ordenes de manufactura
                            </h2>
                        </div>
                        <div>
                            <div class="overflow-x-auto" v-for="(order, key) in modal.data.manufacture_orders" :class="{'mt-5' : key > 0}">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="whitespace-nowrap">ORDEN DE MANUFACTURA No. {{ order.ORDNUM_10 }}</th>
                                            <th colspan="3" class="whitespace-nowrap">FECHA DE LIBERACION: </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="whitespace-nowrap">PRODUCTO MANUFACTURADO: {{ `${order.PRTNUM_10} - ${order.PMDES1_01}` }}</th>
                                            <th colspan="3" class="whitespace-nowrap">
                                                ESTADO DE LA ORDEN:
                                                <span class="badge-sm badge-rounded"
                                                      :class="{'badge-danger': order.STATUS_10 === '3', 'badge-success': order.STATUS_10 === '4'}">
                                                    {{ order.STATUS_10 === '3' ? 'Abierta' : 'Cerrada' }}
                                                </span>
                                            </th>
                                        </tr>
                                        <tr class="text-center">
                                            <th colspan="2" class="whitespace-nowrap">CENTO DE TRABAJO</th>
                                            <th colspan="2" class="whitespace-nowrap">CANTIDADES</th>
                                            <th colspan="3" class="whitespace-nowrap">FECHAS</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th class="whitespace-nowrap">OPERACIÓN</th>
                                            <th class="whitespace-nowrap">PROCESO</th>
                                            <th class="whitespace-nowrap">EN PROCESO</th>
                                            <th class="whitespace-nowrap">COMPLETADA</th>
                                            <th class="whitespace-nowrap">DESECHADA</th>
                                            <th class="whitespace-nowrap">SALIDA</th>
                                            <th class="whitespace-nowrap">ENTREGA/MAX</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in order.detail" :class="{'bg-green-200': item.QUECDE_14 === 'Y'}">
                                            <td class="text-center">{{ item.OPRDES_14 }}</td>
                                            <td class="text-center">{{ item.WRKCTR_14}}</td>
                                            <td class="text-right">{{ parseInt(item.QTYREM_14) }}</td>
                                            <td class="text-right">{{ parseInt(item.QTYCOM_14) }}</td>
                                            <td class="text-right">{{ parseInt(item.ASCRAP_14) }}</td>
                                            <td class="text-center">{{ item.MOVDTE_14 ? $h.formatDate(item.MOVDTE_14, 'YYYY-MM-DD') : '–' }}</td>
                                            <td class="text-center">{{ item.ORGCOM_14 ? $h.formatDate(item.ORGCOM_14, 'YYYY-MM-DD') : '–'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="box p-5 text-danger text-center mt-5" v-else>
                        NO HAY ORDENES DE MANUFACTURA PARA ESTA ORDEN DE VENTA
                    </div>


                    <div class="box mt-5" v-if="modal.data.invoices.length > 0">
                        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto uppercase">
                                FACTURAS GENERADAS DE LA OV
                            </h2>
                        </div>
                        <div>
                            <div class="overflow-x-auto">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="whitespace-nowrap">NUMERO</th>
                                            <th class="whitespace-nowrap">REFERENCIA</th>
                                            <th class="whitespace-nowrap">DESCRIPCIÓN</th>
                                            <th class="whitespace-nowrap">FECHA</th>
                                            <th class="whitespace-nowrap">CANTIDAD</th>
                                            <th class="whitespace-nowrap">PRECIO UNIT</th>
                                            <th class="whitespace-nowrap">TOTAL ITEM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="invoice in modal.data.invoices">
                                            <td class="text-center">{{ invoice.Factura }}</td>
                                            <td class="text-center">{{ invoice.CodigoProducto }}</td>
                                            <td class="text-center">{{ invoice.DescripcionProducto }}</td>
                                            <td class="text-center">{{ $h.formatDate(invoice.FechaFacturacion, 'YYYY-MM-DD hh:mm a') }}</td>
                                            <td class="text-right">{{ parseInt(invoice.Cantidad) }}</td>
                                            <td class="text-right">{{ $h.formatCurrency(invoice.Precio) }}</td>
                                            <td class="text-right">{{ $h.formatCurrency(invoice.TotalItem) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>


                    </div>
                    <div class="box p-5 text-danger text-center mt-5" v-else>
                        NO HAY FACTURAS PARA ESTA ORDEN DE VENTA
                    </div>

                </template>

                <template #footer>
                    <button class="btn btn-secondary" @click="closeModal">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props: {
        query: Object,
        entity: String
    },

    components: {
        Head,
        JetDialogModal
    },

    data(){
        return {
            modal: {
                customer: '',
                seller: '',
                data: {},
                open: false
            }
        }
    },

    methods: {
        view(ov, customer, seller){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('open-sell-order.show-ov', [this.entity, ov])).then(resp => {
                this.$swal.close()

                this.modal = {
                    customer: customer,
                    seller: seller,
                    data: resp.data,
                    open: true
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err)
            })
        },

        closeModal(){
            this.modal = {
                customer: '',
                seller: '',
                data: {},
                open: false
            }
        }
    }
}
</script>

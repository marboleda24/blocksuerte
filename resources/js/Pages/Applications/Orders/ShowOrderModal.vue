<template>
    <div>
        <jet-dialog-modal :show="isOpen" max-width=5xl @close="closeModal">
            <template #title v-if="infoModal">
                Pedido # {{ infoModal.consecutive }}
            </template>

            <template #content>
                <div class="overflow-x-auto">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">DETALLES DEL CLIENTE</th>
                                <th colspan="2" class="text-center">INFORMACIÓN GENERAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="w-1/4 font-bold">RAZON SOCIAL</td>
                                <td class="w-1/4">{{ infoModal.customer.RAZON_SOCIAL }}</td>
                                <td class="w-1/4 font-bold">OC</td>
                                <td class="w-1/4">{{ infoModal.oc ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/4 font-bold">NIT</td>
                                <td class="w-1/4">{{ infoModal.customer.NIT }}</td>
                                <td class="w-1/4 font-bold">VENDEDOR(A)</td>
                                <td class="w-1/4">{{ infoModal.seller.name }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/4 font-bold">DIRECCION</td>
                                <td class="w-1/4">{{ infoModal.customer.DIRECCION }}</td>
                                <td class="w-1/4 font-bold">DESTINO</td>
                                <td class="w-1/4">
                                    <span class="badge badge-sm badge-rounded badge-success"
                                         v-if="infoModal.destiny === 'C'">
                                        Bodega
                                    </span>

                                    <span class="badge badge-sm badge-rounded badge-primary"
                                         v-else-if="infoModal.destiny === 'P'">
                                        Producción
                                    </span>

                                    <span class="badge badge-sm badge-rounded badge-purple"
                                         v-else>
                                        Troqueles
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-1/4 font-bold">TELEFONO</td>
                                <td class="w-1/4">{{ infoModal.customer.TEL1 }}</td>
                                <td class="w-1/4 font-bold">TIPO PEDIDO</td>
                                <td class="w-1/4">
                                    <div class="badge badge-rounded badge-success"
                                         v-if="infoModal.type === 'national'">
                                        Nacional
                                    </div>

                                    <div class="badge badge-sm badge-rounded badge-success"
                                         v-if="infoModal.type === 'nationalUSD'">
                                        Nacional USD
                                    </div>

                                    <div class="badge badge-sm badge-rounded badge-danger"
                                         v-else-if="infoModal.type === 'export'">
                                        Exportacion
                                    </div>
                                    <div class="badge badge-sm badge-rounded badge-warning"
                                         v-else-if="infoModal.type === 'forecast'">
                                        Pronostico
                                    </div>
                                    <div class="badge badge-sm badge-rounded badge-primary"
                                         v-else-if="infoModal.type === 'samples'">
                                        Muestras
                                    </div>
                                    <div class="badge badge-sm badge-rounded badge-purple"
                                         v-else-if="infoModal.type === 'services'">
                                        Servicios
                                    </div>
                                    <div class="badge badge-sm badge-rounded badge-success"
                                         v-else-if="infoModal.type === 'point_of_sale'">
                                        Punto de Venta
                                    </div>

                                    <div class="badge badge-sm badge-rounded badge-success"
                                         v-else-if="infoModal.type === 'delivered_merchandise'">
                                        Mercancía entregada
                                    </div>

                                    <div class="badge badge-sm badge-rounded badge-success"
                                         v-else-if="infoModal.type === 'recycling'">
                                        Recuperación – Reciclaje
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-1/4 font-bold">CONDICION DE PAGO</td>
                                <td class="w-1/4">{{ infoModal.customer.PLAZO }}</td>
                                <td class="w-1/4 font-bold">NUMERO PEDIDO</td>
                                <td class="w-1/4">{{ infoModal.consecutive }}</td>
                            </tr>
                            <tr>
                                <td class="w-2/4 font-bold items-center" colspan="2">
                                    <div class="grid grid-cols-2">
                                        <div>
                                            SEGUIMIENTO
                                        </div>
                                        <button class="btn btn-sm btn-primary ml-auto" @click="showLog(infoModal.id)">
                                            <font-awesome-icon icon="eye"/>
                                        </button>
                                    </div>
                                </td>
                                <td class="w-2/4 font-bold text-right" colspan="2">FPA-GVC-001</td>
                            </tr>

                            <tr>
                                <td colspan="4">
                                    <span class="font-bold">NOTAS:</span> {{ infoModal.notes }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto mt-5">
                    <table class="table table-sm table-bordered">
                        <thead>
                        <tr class="text-center">
                            <th>PRODUCTO</th>
                            <th>CPC</th>
                            <th>NOTAS</th>
                            <th>MARCA</th>
                            <th>ARTE</th>
                            <th>ARTE 2</th>
                            <th>TIPO</th>
                            <th>UM</th>
                            <th>CANT</th>
                            <th>PRECIO</th>
                            <th>SUBTOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="items in infoModal.details" v-bind:key="items.id">
                                <td>
                                    <p class="font-s">
                                        {{ items.product_info.description }}
                                    </p>
                                    <p class="text-gray-600 text-xs">
                                        {{ items.product_info.code }}
                                    </p>
                                </td>
                                <td>{{ items.customer_product_code }}</td>
                                <td>{{ items.notes }}</td>
                                <td>{{ items.brand }}</td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" v-if="items.art"
                                            @click="showArt(items.art)">
                                        {{ items.art }}
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" v-if="items.art2"
                                            @click="showArt(items.art2)">
                                        {{ items.art2 }}
                                    </button>
                                </td>
                                <td>
                                    <div class="badge badge-sm badge-primary badge-rounded"
                                         v-if="items.type === 'new'">
                                        Nuevo
                                    </div>
                                    <div class="badge badge-sm badge-purple badge-rounded"
                                         v-else>
                                        Reprogramación
                                    </div>
                                </td>
                                <td>{{ unitMeasurement(items.unit_measurement) }}</td>
                                <td class="text-right">{{ items.quantity }}</td>
                                <td class="text-right">{{ $h.formatCurrency(items.price) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(items.price * items.quantity) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7" rowspan="6"></td>
                            <td colspan="2" class="font-bold">BRUTO</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency(infoModal.bruto) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-bold">DESCUENTO</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency(infoModal.discount) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-bold">SUBTOTAL</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency(infoModal.subtotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-bold">RETENCION</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency(infoModal.retention) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-bold">IVA</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency(infoModal.taxes) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-bold">TOTAL</td>
                            <td colspan="2" class="text-right">{{ $h.formatCurrency((parseFloat(infoModal.subtotal) - parseFloat(infoModal.retention)) + parseFloat(infoModal.taxes)) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </template>

            <template #footer>
                <template v-if="markButtons">
                    <button @click="mark(infoModal.id, 'star')" type="button" class="btn btn-secondary mr-2">
                        <font-awesome-icon icon="star" size="lg"
                                           :class="{'text-yellow-400': infoModal.mark === 'star'}"/>
                    </button>

                    <button @click="mark(infoModal.id, 'moon')" type="button" class="btn btn-secondary mr-auto">
                        <font-awesome-icon icon="moon" size="lg"
                                           :class="{'text-yellow-400': infoModal.mark === 'moon'}"/>
                    </button>
                </template>

                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button v-if="approveButton" @click="approve" class="btn btn-primary mr-2">
                    Aprobar
                </button>

                <button @click="print(infoModal.id)" type="button" class="btn btn-primary">
                    Imprimir
                </button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="artModal.open" max-width=3xl @close="hideArt">
            <template #title>
                Visualizar arte
            </template>

            <template #content>
                <template v-if="artModal.url">
                    <embed class="pdfobject"
                           :src="artModal.url"
                           type="application/pdf"
                           style="overflow: auto; width: 100%; height: 600px;">
                </template>
            </template>

            <template #footer>
                <button @click="hideArt()" type="button" class="btn btn-secondary">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="logModal.open" max-width=3xl @close="hideLog">
            <template #title v-if="infoModal">
                Seguimiento pedido # {{ infoModal.consecutive }}
            </template>

            <template #content>
                <div class="px-0 sm:px-0 py-2 sm:py-2">
                    <div class="overflow-x-auto">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th>TIPO</th>
                                <th>CENTRO</th>
                                <th>CREADO POR</th>
                                <th>FECHA</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="log in logModal.data" v-bind:key="log.id">
                                <td>{{ log.description }}</td>
                                <td>
                                    <div v-if="log.type === 'user'"
                                         class="badge badge-rounded badge-primary">
                                        Usuario
                                    </div>

                                    <div v-else-if="log.type === 'system'"
                                         class="badge badge-rounded badge-pink">
                                        Sistema
                                    </div>

                                    <div v-else
                                         class="badge badge-rounded badge-purple">
                                        Centro de trabajo
                                    </div>
                                </td>
                                <td>
                                    <div v-if="log.work_center === 'sales'"
                                         class="badge badge-rounded badge-pink">
                                        Ventas
                                    </div>

                                    <div v-else-if="log.work_center === 'wallet'"
                                         class="badge badge-rounded badge-purple">
                                        Cartera
                                    </div>

                                    <div v-else-if="log.work_center === 'costs'"
                                         class="badge badge-rounded badge-primary">
                                        Costos
                                    </div>

                                    <div v-else-if="log.work_center === 'cellar'"
                                         class="badge badge-rounded badge-primary">
                                        Bodega
                                    </div>

                                    <div v-else-if="log.work_center === 'production'"
                                         class="badge badge-rounded badge-warning">
                                        Producción
                                    </div>

                                    <div v-else
                                         class="badge badge-rounded badge-success">
                                        Troqueles
                                    </div>
                                </td>
                                <td>{{ log.user.name }}</td>
                                <td>{{ $h.formatDate(log.created_at) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template #footer>
                <button @click="hideLog()" type="button" class="btn btn-secondary">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        infoModal: Object,
        isOpen: {
            type: Boolean,
            default: false
        },
        approveButton: {
            type: Boolean,
            default: false
        },

        markButtons: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            artModal: {
                open: false,
                url: null
            },
            logModal: {
                open: false,
                data: {}
            }
        }
    },

    components: {
        FontAwesomeIcon,
        JetDialogModal
    },

    methods: {
        closeModal() {
            this.$emit('close')
        },

        print(id) {
            let url = route('orders.view-pdf', id);
            window.open(url, '_blank').focus();
        },

        showArt(art) {
            this.artModal = {
                open: true,
                url: `http://192.168.1.12/intranet_ci/assets/Artes/${art}.pdf`
            }
        },

        hideArt() {
            this.artModal = {
                open: false,
                url: null
            }
        },

        showLog(id) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.get(route('orders.log_data'), {
                params: {
                    id: id,
                }
            }).then(resp => {
                this.$swal.close()
                this.logModal = {
                    open: true,
                    data: resp.data
                }
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            });
        },

        hideLog() {
            this.logModal = {
                open: false,
                data: {}
            }
        },

        approve(){
            this.$emit('approve', this.infoModal.id)
        },

        mark(id, icon){
            this.$emit('mark', id, icon)
        },

        unitMeasurement(unit){
            switch (unit) {
                case "units":
                    return "Unidades"
                case "kilos":
                    return "Kilos"
                case "liters":
                    return "Litros"
                default:
                    return "Millar"
            }
        }
    }
}
</script>

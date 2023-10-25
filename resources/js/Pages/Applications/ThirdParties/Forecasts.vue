<template>
    <div>
        <Head title="Pronósticos"/>

        <portal to="application-title">
            Pronósticos
        </portal>


        <portal to="actions">
            <div class="flex">
                <button class="btn btn-primary" @click="get_data(3)">ABIERTOS</button>
                <button class="btn btn-primary ml-2" @click="get_data(4)">COMPLETADOS</button>
                <button class="btn btn-danger ml-2" @click="get_data(5)">CERRADOS</button>
                <button class="btn btn-danger ml-2" @click="get_data(6)">ANULADOS</button>
            </div>
        </portal>

        <div>
            <v-client-table :data="TableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:NumeroPronostico="{row}">
                    <button class="btn btn-secondary btn-sm" @click="getOrders(row.NumeroPronostico)">
                        {{ row.NumeroPronostico }}
                    </button>
                </template>

                <template v-slot:Referencia="{row}">
                    <button class="btn btn-secondary btn-sm" @click="getInventory(row.Referencia)">
                        {{ row.Referencia }}
                    </button>
                </template>

                <template v-slot:Estado="{row}">
                    <div class="text-center">
                        <span v-if="row.Estado === '3'" class="badge badge-purple badge-rounded">
                            Abierto
                        </span>
                            <span v-else-if="row.Estado === '4'" class="badge badge-success badge-rounded">
                            Completado
                        </span>
                            <span v-else-if="row.Estado === '5'" class="badge badge-danger badge-rounded">
                            Cerrado
                        </span>
                            <span v-else class="badge badge-danger badge-rounded">
                            Anulado
                        </span>
                    </div>
                </template>

            </v-client-table>

            <jet-dialog-modal :show="isOpenInventory" max-width=3xl>
                <template #title v-if="infoInventoryModal">
                    Referencia # {{ infoInventoryModal.Pieza }}
                </template>

                <template #content v-if="infoInventoryModal">
                    <div class="grid grid-cols-6 gap-2 my-10 mx-2 p-2 text-base divide-x text-center border rounded-lg">
                        <div class="flex flex-col">
                            <strong>REFERENCIA </strong> {{ infoInventoryModal.Pieza }}
                        </div>

                        <div class="flex flex-col">
                            <strong>DESCRIPCIÓN </strong> {{ infoInventoryModal.Descripcion }}
                        </div>

                        <div class="flex flex-col">
                            <strong>CANT COMPROMETIDA </strong>
                            <button @click="get_committed_amount(infoInventoryModal.Pieza)"
                                    class="btn btn-primary btn-sm">
                                {{ infoInventoryModal.CantComprometida }}
                            </button>
                        </div>

                        <div class="flex flex-col">
                            <strong>CANT DISPONIBLE </strong>
                            {{ infoInventoryModal.Cant - infoInventoryModal.CantComprometida }}
                        </div>

                        <div class="flex flex-col">
                            <strong>TOTAL </strong> {{ infoInventoryModal.Cant }}
                        </div>

                        <div class="flex flex-col">
                            <strong>DETALLE POR LOTE </strong>
                            <button class="btn btn-primary btn-sm" @click="getLotDetails(infoInventoryModal.Pieza)">
                                VER
                            </button>
                        </div>
                    </div>


                    <div class="py-2 sm:py-2 p-2 mb-10" v-if="showLot">
                        <div class="overflow-x-auto rounded-lg border mb-10">
                            <table class="table table--sm">
                                <thead>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center text-base"
                                        colspan="3">
                                        DETALLE POR LOTE
                                    </th>
                                </tr>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> LOTE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> BODEGA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANTIDAD</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in infoInventoryModal.lots_detail" v-bind:key="row.Lote">
                                    <td class="border-b dark:border-dark-5">
                                        <span v-if="row.Lote">{{ row.Lote }}</span>
                                        <span v-else
                                              class="text-xs font-semibold inline-block py-1 px-2 rounded text-pink-600 bg-pink-200 uppercase last:mr-0 mr-1">N/A</span>
                                    </td>
                                    <td class="border-b dark:border-dark-5">{{ row.Bodega }}</td>
                                    <td class="border-b dark:border-dark-5">
                                        <span v-if="row.CantLote">{{ row.CantLote }}</span>
                                        <span v-else>0</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="py-2 sm:py-2 p-2" v-if="showCommitedAmount">
                        <div class="overflow-x-auto rounded-lg border mb-10">
                            <table class="table table--sm">
                                <thead>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center text-base"
                                        colspan="6">
                                        CANTIDADES COMPROMETIDAS
                                    </th>
                                </tr>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> ORDEN DE VENTA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CLIENTE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT PEDIDA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT ENVIADA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT FACTURADA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT PENDIENTE</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in infoInventoryModal.comitted_amount" v-bind:key="row.OV">
                                    <td class="border-b dark:border-dark-5">{{ row.OV }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.RAZON_SOCIAL }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.CANT_ACTUAL }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.CANT_DESPACHADA }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.CANT_FACTURADA }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.CANT_PENDIENTE }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>

            </jet-dialog-modal>

            <jet-dialog-modal :show="isOpenForecast" max-width=3xl>
                <template #title v-if="infoModal">
                    Pronostico # {{ infoModal.number }}
                </template>

                <template #content v-if="infoModal">

                    <div class="py-2 sm:py-2 p-2 text-center" v-for="forecast in infoModal.forecasts">
                        <div class="overflow-x-auto rounded-lg border mb-6">
                            <table class="table table--sm text-center">
                                <thead>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">ORDEN DE PRODUCCION
                                    </th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">PRODUCTO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">FECHA DE LIBERACION
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="border-b dark:border-dark-5">{{ forecast.OP }}</td>
                                    <td class="border-b dark:border-dark-5">
                                        {{ `${forecast.Referencia} - ${forecast.Descripcion}` }}
                                    </td>
                                    <td class="border-b dark:border-dark-5">{{ forecast.FechaOP }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table--sm text-center">
                                <thead>
                                <tr>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">OPERACION</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">PROCESO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">CANT EN PROCESO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">COMPLETADA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">DESECHADA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">SALIDA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">ENTREGA / MAX</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap">ESTADO</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in forecast.orders" v-bind:key="row.id"
                                    :class="row.CTActual === 'Y' ? 'bg-green-100' : ''">
                                    <td class="border-b dark:border-dark-5">{{ row.WRKCTR_14 }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.OPRDES_14 }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.QTYREM_14 }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.QTYCOM_14 }}</td>
                                    <td class="border-b dark:border-dark-5">{{ row.Desecho }}</td>
                                    <td class="border-b dark:border-dark-5">{{ $h.formatDate(row.REVDTE_14) }}</td>
                                    <td class="border-b dark:border-dark-5">{{ $h.formatDate(row.RLSDTE_14) }}</td>
                                    <td class="border-b dark:border-dark-5">
                                        <span v-if="row.STATUS_10 === '1'" class="badge badge-pink badge-rounded">Planeada</span>
                                        <span v-else-if="row.STATUS_10 === '2'"
                                              class="badge badge-purple badge-rounded">Autorizada</span>
                                        <span v-else-if="row.STATUS_10 === '3'"
                                              class="badge badge-warning badge-rounded">Liberada</span>
                                        <span v-else-if="row.STATUS_10 === '4'"
                                              class="badge badge-success badge-rounded">Concluida</span>
                                        <span v-else-if="row.STATUS_10 === '5'"
                                              class="badge badge-primary badge-rounded">Cerrada</span>
                                        <span v-else-if="row.STATUS_10 === '6'"
                                              class="badge badge-danger badge-rounded">Cancelada</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>

    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'

export default {
    components: {
        JetDialogModal,
        Head
    },

    props: {
        table_data: Array
    },

    data() {
        return {
            columns: [
                'NumeroPronostico',
                'FechaPronostico',
                'Referencia',
                'Descripcion',
                'Detalle',
                'Acabado',
                'Cantidad',
                'Cliente',
                'RazonSocial',
                'Estado',
                'Vendedor'
            ],

            options: {
                headings: {
                    NumeroPronostico: '#',
                    FechaPronostico: 'FECHA',
                    Referencia: 'REFERENCIA',
                    Descripcion: 'DESCRIPCIÓN',
                    Detalle: 'DETALLE',
                    Acabado: 'ACABADO',
                    Cliente: 'CLIENTE',
                    RazonSocial: 'RAZON SOCIAL',
                    Estado: 'ESTADO',
                    Vendedor: 'VENDEDOR',
                },

                clientSorting: false,
                sortable: ['NumeroPronostico', 'FechaPronostico', 'Referencia', 'Descripcion', 'Detalle', 'Acabado', 'Cliente', 'RazonSocial', 'Estado', 'Vendedor'],

            },
            TableData: this.table_data,
            infoModal: null,
            isOpenForecast: false,
            infoInventoryModal: null,
            isOpenInventory: false,
            showLot: false,
            showCommitedAmount: false
        }
    },

    methods: {
        loading(bool) {
            if (bool === true) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información...',
                    text: 'Este proceso puede tardar algunos segundos.',
                });
            } else {
                this.$swal.close()
            }
        },

        closeModal: function () {
            this.isOpenForecast = false;
            this.isOpenInventory = false;
            this.infoModal = null;
            this.infoInventoryModal = null;
            this.showLot = false;
            this.showCommitedAmount = false;
        },

        openModalForecast: function () {
            this.isOpenForecast = true;
        },

        openModalInventory: function () {
            this.isOpenInventory = true;
        },

        get_data(stateId) {
            this.loading(true);
            axios.get(route('forecasts.get-data'), {
                params: {
                    state: stateId
                }
            }).then(resp => {
                this.TableData = resp.data;
                this.loading(false);
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

        getOrders(forecastId) {
            this.loading(true);
            axios.get(route('forecasts.get-orders'), {
                params: {
                    forecast: forecastId
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModalForecast();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

        getInventory(reference) {
            this.loading(true);
            axios.get(route('forecasts.get-inventory'), {
                params: {
                    reference: reference
                }
            }).then(resp => {
                this.infoInventoryModal = resp.data;
                this.loading(false);
                this.openModalInventory();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

        getLotDetails(reference) {
            this.loading(true);
            axios.get(route('forecasts.get-lots-detail'), {
                params: {
                    reference: reference
                }
            }).then(resp => {
                this.infoInventoryModal.lots_detail = resp.data;
                this.showLot = true;
                this.loading(false);
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

        get_committed_amount(reference) {
            this.loading(true);
            axios.get(route('forecasts.get-committed-amount'), {
                params: {
                    reference: reference
                }
            }).then(resp => {
                this.infoInventoryModal.comitted_amount = resp.data;
                this.showCommitedAmount = true;
                this.loading(false);
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

    }
}

</script>

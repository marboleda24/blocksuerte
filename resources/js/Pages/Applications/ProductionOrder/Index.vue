<template>
    <div>
        <Head title="Órdenes de producción"/>

        <portal to="application-title">
            Órdenes de producción
        </portal>


        <portal to="actions">
            <div class="grid grid-cols-3 gap-3">
                <select class="form-select w-64" v-model="form.type">
                    <option value="" selected disabled>Seleccione…</option>
                    <option value="number">Búsqueda por numero</option>
                    <option value="date">Búsqueda por Fecha</option>
                    <option value="product">Búsqueda por Producto</option>
                </select>
                <template v-if="form.type === 'number'">
                    <input type="number" class="form-control" v-model="form.number">
                </template>

                <template v-else-if="form.type === 'date'">
                    <Litepicker
                        v-model="form.date"
                        :options="{
                            autoApply: true,
                            singleMode: false,
                            numberOfColumns: 2,
                            numberOfMonths: 2,
                            showWeekNumbers: true,
                            format: 'DD-MM-YYYY',
                            lang: 'es-ES',
                            dropdowns: {
                                minYear: 2021,
                                maxYear: null,
                                months: true,
                                years: true
                            },
                            maxDate: current_date
                        }"
                        class="form-control"
                    />
                </template>

                <template v-else-if="form.type === 'product'">
                    <input type="text" class="form-control" v-model="form.product">
                </template>

                <button class="btn btn-primary w-full h-full" @click="search()" :disabled="form.type === ''">
                    <font-awesome-icon icon="magnifying-glass" class="mr-2"/>
                    Consultar Ordenes
                </button>
            </div>
        </portal>


        <v-client-table :data="table.data" :columns="table.columns" :options="table.options">
            <template v-slot:action="{row}">
                <div class="text-center">
                    <button class="btn btn-secondary" @click="generateBarcode(row.order)">
                        <font-awesome-icon icon="qrcode"/>
                    </button>
                </div>
            </template>
        </v-client-table>


        <jet-dialog-modal :show="modal.open" max-width="lg">
            <template #title>
                Orden de producción {{modal.data[0].ORDNUM_14}}
            </template>

            <template #content>
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm" >
                        <thead>
                        <tr class="text-center" >
                            <th class="whitespace-nowrap" colspan="2">OV: {{modal.data[0].OV}}</th>
                            <th colspan="4" class="whitespace-nowrap" >
                                ESTADO DE LA ORDEN:
                                <span class="badge-sm badge-rounded"
                                      :class="{'badge-danger': modal.data[0].STATUS_10 === '3', 'badge-success': modal.data[0].STATUS_10 === '4'}">
                                                    {{ modal.data[0].STATUS_10 === '3' ? 'Abierta' : 'Cerrada' }}
                                </span>
                            </th>
                        </tr>

                        </thead>
                    </table>
                    <table class="table table-bordered table-sm" >
                        <thead>
                        <tr class="text-center">
                            <th class="whitespace-nowrap">OPERACIÓN</th>
                            <th class="whitespace-nowrap">PROCESO</th>
                            <th class="whitespace-nowrap">EN PROCESO</th>
                            <th class="whitespace-nowrap">COMPLETADA</th>



                        </tr>
                        </thead>
                        <tbody  v-for="(order, key) in modal.data" :class="{'mt-5' : key > 0}">

                        <tr  :class="{'bg-green-200': order.CTActual === 'Y'}">
                            <td class="text-center">{{ order.OPRDES_14 }} - {{ order.OPRSEQ_14 }}</td>
                            <td class="text-center">{{ order.WRKCTR_14}}</td>
                            <td class="text-right">{{ parseInt(order.QTYREM_14) }}</td>
                            <td class="text-right">{{ parseInt(order.QTYCOM_14) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    components: {
        JetDialogModal,
        Head
    },

    data() {
        return {
            form: {
                type: '',
                number: '',
                date: '',
                product: ''
            },
            table: {
                data: [],
                columns: [
                    'order',
                    'code',
                    'description',
                    'quantity',
                    'action'
                ],
                options: {
                    headings: {
                        order: 'ORDEN',
                        code: 'CÓDIGO',
                        description: 'DESCRIPCIÓN',
                        quantity: 'CANTIDAD',
                        action: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['order', 'code', 'description', 'quantity', 'sync'],
                    cellClasses: {
                        quantity: [{class: 'text-right', condition: row => row}],
                        sync: [{class: 'text-center', condition: row => row}],
                        action: [{class: 'text-center', condition: row => row}],
                    }
                }
            },

            modal: {
                open: false,
                data: [],
            }
        }
    },

    methods: {
        search: function () {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Órdenes…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('production-order.search-data'), this.form).then(resp => {
                this.$swal.close()
                this.table.data = resp.data

            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        generateBarcode(order) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Órdenes…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('order-production.order_modal' ),{
                order:order
            }).then(resp => {
                this.$swal.close()
                this.modal = {
                    open: true,
                    data: resp.data,
                }

            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        closeModal() {
            this.modal = {
                open: false,
                data: [],
            }
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
    }
}
</script>

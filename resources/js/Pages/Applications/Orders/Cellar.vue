<template>
    <div>
        <Head title="Pedidos Pendientes (Bodega)"/>

        <portal to="application-title">
            Pedidos Pendientes (Bodega)
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <a
                            id="pending-tab"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            Pendientes
                        </a>
                    </li>

                    <li class="nav-item w-full">
                        <a
                            id="finished-tab"
                            data-tw-toggle="tab"
                            data-tw-target="#finished"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Finalizados
                        </a>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="pending" class="tab-pane active p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="tableData" :columns="table_pending.columns"
                                        :options="table_pending.options" ref="table1" class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="finalize(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'check-double']" class="mr-1"/>
                                                Finalizar
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="refuse(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Rechazar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="finished" class="tab-pane p-2" role="tabpanel" aria-labelledby="finished-tab">
                        <v-client-table :data="finishedTD" :columns="table_finished.columns"
                                        :options="table_finished.options" ref="table1" class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <button @click="view(row.id)"
                                        class="text-center btn btn-secondary">
                                    <font-awesome-icon :icon="['far', 'eye']"/>
                                </button>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <show-order-modal :info-modal="infoModal" :is-open="isOpen" @close="closeModal"/>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import ShowOrderModal from "@/Pages/Applications/Orders/ShowOrderModal.vue";
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

export default {
    components: {
        ShowOrderModal,
        Head
    },

    props: {
        data: Array,
        finished: Array
    },

    data() {
        return {
            table_pending: {
                columns: [
                    'consecutive',
                    'oc',
                    'customer_name',
                    'customer_term',
                    'discount',
                    'subtotal',
                    'taxes',
                    'type',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        oc: 'OC',
                        customer_name: 'RAZON SOCIAL',
                        customer_term: 'CONDICIÓN PAGO',
                        subtotal: 'SUBTOTAL',
                        discount: 'DESCUENTO',
                        taxes: 'IVA',
                        type: 'TIPO',
                        created_at: 'CREADO',
                        actions: '',
                    },
                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['consecutive', 'oc', 'customer_code', 'customer_name', 'term', 'created_at'],
                    templates: {
                        bruto(h, row){
                            if (row.type === 'export'){
                                return `USD ${this.$h.formatCurrency(row.bruto)}`
                            }else {
                                return this.$h.formatCurrency(row.bruto)
                            }
                        },
                        discount(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.discount}`
                            }else {
                                return this.$h.formatCurrency(row.discount)
                            }
                        },
                        subtotal(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.subtotal}`
                            }else {
                                return this.$h.formatCurrency(row.subtotal)
                            }
                        },
                        taxes(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.taxes}`
                            }else {
                                return this.$h.formatCurrency(row.taxes)
                            }
                        },
                        oc(h, row) {
                            return row.oc ? row.oc : '–'
                        },
                        type(h, row) {
                            switch (row.type) {
                                case "national":
                                    return <span class="badge badge-success">Nacional</span>
                                case "nationalUSD":
                                    return <span class="badge badge-success">Nacional USD</span>
                                case "export":
                                    return <span class="badge badge-danger">Exportación</span>
                                case "forecast":
                                    return <span class="badge badge-warning">Pronostico</span>
                                case "samples":
                                    return <span class="badge badge-primary">Muestras</span>
                                case "elena":
                                    return <span class="badge badge-purple">Elena</span>
                                case "point_of_sale":
                                    return <span class="badge badge-success">Punto de Venta</span>
                                case "services":
                                    return <span class="badge badge-purple">Servicios</span>
                                case "delivered_merchandise":
                                    return <span class="badge badge-purple">Mercancía entregada</span>
                                case "claim":
                                    return <span class="badge badge-purple">reclamo</span>
                                case "recycling":
                                    return <span class="badge badge-purple">Recuperación – Reciclaje</span>
                            }
                        },
                    },
                    cellClasses: {
                        bruto: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        discount: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        subtotal: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        taxes: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        type: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        actions: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                },
            },
            table_finished: {
                columns: [
                    'order_max',
                    'consecutive',
                    'oc',
                    'customer_name',
                    'customer_term',
                    'discount',
                    'subtotal',
                    'taxes',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        order_max: 'PEDIDO MAX',
                        consecutive: '#',
                        oc: 'OC',
                        customer_name: 'RAZON SOCIAL',
                        customer_term: 'CONDICIÓN PAGO',
                        subtotal: 'SUBTOTAL',
                        discount: 'DESCUENTO',
                        taxes: 'IVA',
                        created_at: 'CREADO',
                        actions: '',
                    },
                    perPageValues: [10, 250],
                    clientSorting: false,
                    sortable: ['order_max', 'consecutive', 'oc', 'customer_name', 'customer_term', 'created_at'],
                    templates: {
                        bruto(h, row){
                            if (row.type === 'export'){
                                return `USD ${this.$h.formatCurrency(row.bruto)}`
                            }else {
                                return this.$h.formatCurrency(row.bruto)
                            }
                        },
                        discount(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.discount}`
                            }else {
                                return this.$h.formatCurrency(row.discount)
                            }
                        },
                        subtotal(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.subtotal}`
                            }else {
                                return this.$h.formatCurrency(row.subtotal)
                            }
                        },
                        taxes(h, row) {
                            if (row.type === 'export'){
                                return `USD ${row.taxes}`
                            }else {
                                return this.$h.formatCurrency(row.taxes)
                            }
                        },
                        oc(h, row) {
                            return row.oc ? row.oc : '–'
                        },
                        order_max(h, row) {
                            return row.order_max
                                ? <span class="badge badge-success">{row.order_max}</span>
                                : <span class="badge badge-danger">N/A</span>
                        }
                    },
                    cellClasses: {
                        order_max: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        bruto: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        discount: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        subtotal: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        taxes: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        actions: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                },
            },
            tableData: this.data,
            finishedTD: this.finished,
            isOpen: false,
            infoModal: null,
        }
    },

    methods: {
        finalize(row) {
            if (row.destiny === 'C' && row.type === 'point_of_sale' || row.destiny === 'C' && row.type === 'delivered_merchandise') {
                this.$swal({
                    title: '¡Selecciona una razón!',
                    text: 'El pedio seleccionado es para un punto de venta o de mercancía entregada, por favor seleccione el almacén',
                    icon: 'info',
                    input: 'select',
                    inputOptions: {
                        '30': 'Itagui',
                        '31': 'Cali',
                        '33': 'Bogota',
                        '35': 'Pereira'
                    },
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Continuar',
                    inputValidator: (inputValue) => {
                        return !inputValue && 'Debes seleccionar una razon'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        const point_of_sale_reason = inputValue.value;
                        this.$swal({
                            title: '¿Finalizar pedido?',
                            text: 'Escribe una justificación para esta acción',
                            icon: 'question',
                            input: 'textarea',
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Si, Finalizar',
                            inputValidator: (inputValue) => {
                                return !inputValue && 'La justificación es obligatoria'
                            }
                        }).then((inputValue) => {
                            if (inputValue.value) {
                                this.$swal({
                                    iconHtml: this.$h.loadIcon(),
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    title: 'Finalizando pedido...',
                                    text: 'Este proceso puede tardar unos segundos.',
                                });

                                axios.post(route('orders.finalize_order'), {
                                    id: row.id,
                                    point_of_sale_reason: point_of_sale_reason,
                                    justify: inputValue.value,
                                    state: '4'
                                }).then(res => {
                                    this.tableData = res.data.orders
                                    this.finishedTD = res.data.finished

                                    let og = res.data.order_generated

                                    this.$swal({
                                        title: '¡Pedido Finalizado!',
                                        text: `Numero de orden: ${og}`,
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }).catch(err => {
                                    this.$swal({
                                        icon: 'error',
                                        title: '¡Ups!',
                                        text: 'Hubo un error procesando la solicitud.',
                                        confirmButtonText: 'Aceptar',
                                    });
                                    console.log(err);
                                });
                            } else {
                                inputValue.dismiss = this.$swal.DismissReason.cancel
                            }
                        });
                    } else {
                        inputValue.dismiss = this.$swal.DismissReason.cancel
                    }
                });
            }else if (row.destiny === 'C' && row.type !== 'point_of_sale') {
                this.$swal({
                    title: '¿Finalizar pedido?',
                    text: 'Escribe una justificación para esta acción',
                    icon: 'question',
                    input: 'textarea',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Si, Finalizar',
                    inputValidator: (inputValue) => {
                        return !inputValue && 'La justificación es obligatoria'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Finalizando pedido...',
                            text: 'Este proceso puede tardar unos segundos.',
                        });

                        axios.post(route('orders.finalize_order'), {
                            id: row.id,
                            justify: inputValue.value,
                            state: '4'
                        }).then(res => {
                            this.tableData = res.data.orders
                            this.finishedTD = res.data.finished

                            let og = res.data.order_generated

                            this.$swal({
                                title: '¡Pedido Finalizado!',
                                text: `Numero de orden: ${og}`,
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            });
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err);
                        });
                    } else {
                        inputValue.dismiss = this.$swal.DismissReason.cancel
                    }
                });
            }
        },

        refuse(row) {
            this.$swal({
                title: '¿Rechazar pedido?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Rechazar',
                inputValidator: (inputValue) => {
                    return !inputValue && 'La justificación es obligatoria'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Rechazando pedido...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('orders.cellar.refuse'), {
                        'id': row,
                        'justify': inputValue.value
                    }).then(res => {
                        this.tableData = res.data.orders
                        this.finishedTD = res.data.finished

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El pedido ha sido rechazado!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            });
        },

        openModal() {
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
            this.infoModal = null;
        },

        openLogModal() {
            this.isOpenLog = true;
        },

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

        view(row) {
            this.loading(true);

            if (source) {
                source.cancel();
            }
            source = CancelToken.source();

            axios.get(route('orders.view'), {
                cancelToken: source.token,
                params: {
                    id: row,
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModal();
            }).catch(error => {
                if (axios.isCancel(error)) {
                    console.log('Operation canceled by the user.');
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error);
                }
            });
        },
    }
}
</script>

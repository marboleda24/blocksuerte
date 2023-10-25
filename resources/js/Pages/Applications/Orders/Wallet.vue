<template>
    <div>
        <Head title="Pedidos Pendientes (Cartera)"/>

        <portal to="application-title">
            Pedidos Pendientes (Cartera)
        </portal>


        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
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
                                   @click="approve(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                    Aprobar
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
            <show-order-modal :info-modal="infoModal" :is-open="isOpen" @close="closeModal" :approve-button="true" @approve="approve"/>
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

export default{
    components: {
        ShowOrderModal,
        Head
    },

    props: {
        data: Array,
    },

    data() {
        return {
            columns: [
                'consecutive',
                'oc',
                'customer_name',
                'seller',
                'term',
                'type',
                'destiny',
                'discount',
                'subtotal',
                'taxes',
                'created_at',
                'actions'
            ],
            options: {
                headings: {
                    consecutive: '#',
                    oc: 'OC',
                    customer_name: 'RAZON SOCIAL',
                    seller: 'VENDEDOR',
                    type: 'TIPO',
                    destiny: 'DESTINO',
                    term: 'CONDICIÓN PAGO',
                    subtotal: 'SUBTOTAL',
                    discount: 'DESCUENTO',
                    taxes: 'IVA',
                    created_at: 'CREADO',
                    actions: '',
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['consecutive', 'oc', 'customer_name', 'term', 'created_at'],
                templates: {
                    created_at: function (h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                    },
                    customer_name: function (h, row) {
                        return row.customer.CODIGO_CLIENTE + ' - ' + row.customer.RAZON_SOCIAL
                    },
                    type: function (h, row) {
                        switch (row.type) {
                            case "national":
                                return <span class="badge badge-success">Nacional</span>
                            case "nationalUSD":
                                return <span class="badge badge-success">Nacional USD</span>
                            case "export":
                                return <span class="badge badge-danger">Exportacion</span>
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
                                return <span class="badge badge-purple">Mercancia entregada</span>
                            case "claim":
                                return <span class="badge badge-purple">Reclamo</span>
                            case "recycling":
                                return <span class="badge badge-purple">Recuperación – Reciclaje</span>
                        }
                    },

                    substate: function (h, row) {
                        switch (row.substate) {
                            case "P":
                                return <span class="badge badge-warning badge-rounded">Pendiente</span>
                            case "R":
                                return <span class="badge badge-danger badge-rounded">Rechazado</span>
                            case "D":
                                return <span class="badge badge-pink badge-rounded">Retenido</span>

                        }
                    },
                    destiny: function (h, row) {
                        switch (row.destiny) {
                            case "C":
                                return <span class="badge badge-success">Bodega</span>
                            case "D":
                                return <span class="badge badge-purple">Troqueles</span>
                            case "P":
                                return <span class="badge badge-primary">Producción</span>

                        }
                    },
                    seller: function (h, row) {
                        return row.seller.name
                    },
                    term: function (h, row) {
                        return row.customer.PLAZO
                    },
                    subtotal: function (h, row) {
                        return this.$h.formatCurrency(row.subtotal)
                    },
                    taxes: function (h, row) {
                        return this.$h.formatCurrency(row.taxes)
                    },
                    discount: function (h, row) {
                        return this.$h.formatCurrency(row.discount)
                    },
                    oc: function (h, row) {
                        return row.oc
                            ? <span class="badge badge-success">{row.oc}</span>
                            : <span class="badge badge-danger">N/A</span>
                    }
                },
                filterAlgorithm: {
                    customer_name(row, query) {
                        return (row.customer.CODIGO_CLIENTE + ' - ' + row.customer.RAZON_SOCIAL).includes(query)
                    },
                }
            },
            tableData: this.data,
            isOpen: false,
            infoModal: null,
        }
    },

    methods: {
        approve(row) {
            this.$swal({
                title: '¿Aprobar pedido?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Aprobar',
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
                        title: 'Enviando a costos...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('orders.wallet.approve'), {
                        'id': row,
                        'justify': inputValue.value
                    }).then(res => {
                        this.tableData = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El pedido fue enviado a costos!",
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

                    axios.post(route('orders.wallet.refuse'), {
                        'id': row,
                        'justify': inputValue.value
                    }).then(res => {
                        this.tableData = res.data;

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

        loading(bool) {
            if (bool) {
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

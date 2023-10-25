<template>
    <div>
        <Head title="Ordenes de venta enviadas y no facturadas"/>

        <portal to="application-title">
            Ordenes de venta enviadas y no facturadas
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="viewOrder(row.order_number)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </a>

                                <a href="javascript:void(0)"
                                   @click="closeOrder(row.order_number)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'xmark']" class="mr-1"/>
                                    Cerrar
                                </a>

                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="modal.isOpen" @close="closeModal" max-width="5xl">
                <template #title>
                    OV {{ modal.data.header.order_number }}
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm ">
                            <thead class="table-dark">
                            <tr class="text-center">
                                <th class="whitespace-nowrap">OV</th>
                                <th class="whitespace-nowrap">FECHA</th>
                                <th class="whitespace-nowrap">FECHA ENVIO</th>
                                <th class="whitespace-nowrap">CLIENTE</th>
                                <th class="whitespace-nowrap">VENDEDOR(A)</th>
                                <th class="whitespace-nowrap">RAZON</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <td>{{ modal.data.header.order_number }}</td>
                                <td>{{ modal.data.header.order_date }}</td>
                                <td>{{ modal.data.header.shipment_date }}</td>
                                <td>{{ `${modal.data.header.customer_code} - ${modal.data.header.customer_name}` }}</td>
                                <td>{{ `${modal.data.header.seller_code} - ${modal.data.header.seller_name}` }}</td>
                                <td>{{ `${modal.data.header.reason_code} - ${modal.data.header.reason_description}` }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="mt-5">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr class="text-center">
                                        <th class="whitespace-nowrap">ITEM</th>
                                        <th class="whitespace-nowrap">ESTADO</th>
                                        <th class="whitespace-nowrap">PRODUCTO</th>
                                        <th class="whitespace-nowrap">FECHA DESPACHO</th>
                                        <th class="whitespace-nowrap">CANT. ORIGINAL</th>
                                        <th class="whitespace-nowrap">CANT. ACTUAL</th>
                                        <th class="whitespace-nowrap">CANT. ENVIADA</th>
                                        <th class="whitespace-nowrap">CANT. PENDIENTE</th>
                                        <th class="whitespace-nowrap">CANT. FACTURADA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in modal.data.details">
                                        <td class="text-center">{{ `${row.lin}${row.del}` }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-rounded " :class="{'badge-warning' : row.status === '3', 'badge-success' : row.status === '4'}">
                                                {{ row.status === '3' ? 'Pendiente' : 'Facturado' }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ `${row.product_code} - ${row.product_description}` }}</td>
                                        <td class="text-center">{{ row.shipment_date ?? '-' }}</td>
                                        <td class="text-right">{{ row.orginal_quantity }}</td>
                                        <td class="text-right">{{ row.actual_quantity }}</td>
                                        <td class="text-right">{{ row.shipment_quantity }}</td>
                                        <td class="text-right">{{ row.pending_quantity }}</td>
                                        <td class="text-right">{{ row.invoiced_quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cerrar
                    </button>

                    <button @click="closeOrder(modal.data.header.order_number)" class="btn btn-primary">
                        Cerrar OV
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
        orders: Array
    },

    components: {
        Head,
        JetDialogModal
    },

    data() {
        return {
            table: {
                data: this.orders,
                columns: [
                    'order_number',
                    'order_date',
                    'shipment_date',
                    'customer',
                    'seller',
                    'reason',
                    'oc',
                    'actions',
                ],
                options: {
                    headings: {
                        order_number: 'OV',
                        order_date: 'FECHA',
                        shipment_date: 'FECHA ENVIO',
                        customer: 'CLIENTE',
                        seller: 'VENDEDOR(A)',
                        reason: 'RAZON',
                        oc: 'OC',
                        actions: '',
                    },
                    sortable: ['order_date', 'shipment_date', 'order_number', 'customer', 'seller', 'reason', 'oc'],
                    templates: {
                        customer(h, row) {
                            return `${row.customer_code} - ${row.customer_name}`
                        },
                        seller(h, row) {
                            return `${row.seller_code} - ${row.seller_name}`
                        },
                        reason(h, row) {
                            return `${row.reason_code} - ${row.reason_description}`
                        }
                    },
                    customSorting: {
                        customer(ascending) {
                            return function (a, b) {
                                const lastA = (`${a.customer_code} - ${a.customer_name}`).toLowerCase();
                                const lastB = (`${b.customer_code} - ${b.customer_name}`).toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },
                        seller(ascending) {
                            return function (a, b) {
                                const lastA = (`${a.seller} - ${a.seller_name}`).toLowerCase();
                                const lastB = (`${b.seller} - ${b.seller_name}`).toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },
                        reason(ascending) {
                            return function (a, b) {
                                const lastA = (`${a.reason_code} - ${a.reason_description}`).toLowerCase();
                                const lastB = (`${b.reason_code} - ${b.reason_description}`).toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        }
                    },
                    filterAlgorithm: {
                        customer(row, query) {
                            return (`${row.customer_code} - ${row.customer_name}`).includes(query)
                        },
                        seller(row, query) {
                            return (`${row.seller} - ${row.seller_name}`).includes(query)
                        },
                        reason(row, query) {
                            return (`${row.reason_code} - ${row.reason_description}`).includes(query)
                        }
                    }
                }
            },

            modal: {
                isOpen: false,
                data: {}
            }
        }
    },
    methods: {
        openModal(data) {
            this.modal = {
                isOpen: true,
                data: data
            }
        },

        closeModal() {
            this.modal = {
                isOpen: false,
                data: {}
            }
        },

        viewOrder(order_number) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('queries.order-shipped-not-invoiced.show', order_number)).then(resp => {
                this.$swal.close()
                this.openModal(resp.data)
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err)
            })
        },

        closeOrder(order_number) {
            this.$swal({
                icon: 'question',
                title: '¿Cerrar orden de venta?',
                text: `¿Esta seguro de cerrar la orden de venta ${order_number}?, recuerde que esta acción NO ES REVERSIBLE`,
                showCancelButton: true,
                confirmButtonText: '¡Si, Cerrar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Cerrando orden de venta…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('queries.order-shipped-not-invoiced.close-order'), {
                        order_number: order_number
                    }).then(resp => {
                        this.closeModal()
                        this.table.data = resp.data
                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Orden de venta cerrada con éxito',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error recuperando la información.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err)
                    })
                }
            })
        }
    }
}
</script>

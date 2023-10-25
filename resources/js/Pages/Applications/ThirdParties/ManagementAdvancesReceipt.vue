<template>
    <div>
        <Head title="Gestion de RC y Anticipos"/>

        <portal to="application-title">
            Gestion de RC y Anticipos
        </portal>

        <div>
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Recibos de caja</h2>
                </div>
                <div class="p-5">
                    <v-client-table :data="cashReceiptTableData" :columns="cash_receipt_table.columns"
                                    :options="cash_receipt_table.options" ref="table1" class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="dropdown text-center">
                                <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                        data-tw-toggle="dropdown">
                                    <font-awesome-icon icon="bars"/>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-content">
                                        <a href="javascript:void(0)"
                                           @click="viewReceipt(row.id)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                            Ver
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="approveReceipt(row)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['fas', 'check-double']" class="mr-1"/>
                                            Aprobar
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="refuseReceipt(row)"
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
            </div>

            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Anticipos</h2>
                </div>
                <div class="p-5">
                    <v-client-table :data="advancesTableData" :columns="advances_table.columns"
                                    :options="advances_table.options" ref="table2" class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="dropdown text-center">
                                <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                        data-tw-toggle="dropdown">
                                    <font-awesome-icon icon="bars"/>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-content">
                                        <a href="javascript:void(0)"
                                           @click="viewAdvance(row.id)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                            Ver
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="approveAdvance(row)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['fas', 'check-double']" class="mr-1"/>
                                            Aprobar
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="refuseAdvance(row)"
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
            </div>

            <jet-dialog-modal :show="isOpenReceipt" max-width=4xl>
                <template #title v-if="infoModal">
                    RC #{{ infoModal.consecutive }}
                </template>

                <template #content v-if="infoModal">
                    <div class="overflow-x-auto">
                        <table class="table table-sm table-bordered">
                            <tbody class="uppercase">
                            <tr>
                                <td scope="row">TIPO</td>
                                <td class="text-right">{{ infoModal.type_name }}</td>
                            </tr>
                            <tr v-if="infoModal.type === 'export'">
                                <td scope="row">TRM FECHA PAGO	</td>
                                <td class="text-right">{{ $h.formatCurrency(infoModal.trm) }}</td>
                            </tr>
                            <tr>
                                <td scope="row">CLIENTE</td>
                                <td class="text-right">{{ infoModal.customer }}</td>
                            </tr>
                            <tr>
                                <td scope="row">FECHA DE CREACIÓN</td>
                                <td class="text-right">{{ $h.formatDate(infoModal.created_at) }}</td>
                            </tr>
                            <tr>
                                <td scope="row">FECHA DE CONSIGNACIÓN</td>
                                <td class="text-right">{{ $h.formatDate(infoModal.payment_date) }}</td>
                            </tr>
                            <tr>
                                <td scope="row">TOTAL RC</td>
                                <td class="text-right">{{ $h.formatCurrency(infoModal.total_paid) }}</td>
                            </tr>
                            <tr>
                                <td scope="row">COMENTARIOS</td>
                                <td class="text-right">{{ infoModal.comments }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">FACTURA</th>
                                <th class="text-center" v-if="infoModal.type === 'export'">
                                    TRM
                                </th>
                                <th class="text-center">BRUTO</th>
                                <th class="text-center">DESCUENTO</th>
                                <template v-if="infoModal.type === 'national'">
                                    <th class="text-center">RETENCION</th>
                                    <th class="text-center">RETEIVA</th>
                                    <th class="text-center">RETEICA</th>
                                </template>
                                <th class="text-center">OTRAS DEDUCCIONES</th>
                                <th class="text-center">OTROS INGRESOS</th>
                                <th class="text-center">TOTAL</th>
                                <th v-if="infoModal.type === 'export'" class="text-center">
                                    DIFERENCIA EN CAMBIO
                                </th>
                                <th v-if="infoModal.type === 'export'" class="text-center">
                                    SALDO A FAVOR
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in infoModal.details" v-bind:key="row.id">
                                <td>
                                    <button class="btn btn-sm btn-secondary" @click="info_invoice(row.invoice)">
                                        {{ row.invoice }}
                                    </button>
                                </td>
                                <td v-if="infoModal.type === 'export'"> {{ $h.formatCurrency(row.trm) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.bruto) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.discount) }}</td>
                                <template v-if="infoModal.type === 'national'">
                                    <td class="text-right">{{ $h.formatCurrency(row.retention) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.reteiva) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.reteica) }}</td>
                                </template>
                                <td class="text-right">{{ $h.formatCurrency(row.other_deductions) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.other_income) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.total) }}</td>
                                <td class="text-right" v-if="infoModal.type === 'export'">
                                    COP {{ $h.formatCurrency(row.change_difference) }}
                                </td>
                                <td class="text-right" v-if="infoModal.type === 'export'">
                                    COP {{ $h.formatCurrency(row.positive_balance) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-primary mr-auto" @click="logModal = true">
                        Ver Log
                    </button>

                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="isOpenAdvance" max-width=3xl>
                <template #title v-if="infoModal">
                    Anticipo # {{ infoModal.consecutive }}
                </template>

                <template #content v-if="infoModal">
                    <button class="btn btn-sm btn-primary w-1/2 mb-2"
                            @click="logModal = true">
                        Ver Log
                    </button>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <tbody class="uppercase">
                            <tr>
                                <th class="border" scope="row">CLIENTE</th>
                                <td class="text-right">{{ infoModal.customer.RAZON_SOCIAL }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">NIT</th>
                                <td class="text-right">{{ infoModal.customer.NIT }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">FECHA DE CREACIÓN</th>
                                <td class="text-right">{{ $h.formatDate(infoModal.created_at) }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">TOTAL RC</th>
                                <td class="text-right">{{ $h.formatCurrency(infoModal.total_paid) }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">COMENTARIOS</th>
                                <td class="text-right">{{ infoModal.details }}</td>
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

            <jet-dialog-modal :show="openInfoInvoiceModal" max-width=lg>
                <template #title v-if="InfoInvoiceModal">
                    FACTURA # {{ InfoInvoiceModal.numero }}
                </template>

                <template #content v-if="InfoInvoiceModal">
                    <div class="px-0 sm:px-0 py-2 sm:py-2">
                        <div class="overflow-x-auto">
                            <table class="table table--sm">
                                <tbody class="uppercase">
                                <tr>
                                    <th class="border" scope="row">Fecha factura</th>
                                    <td class="border text-right">{{ $h.formatDate(InfoInvoiceModal.fecha) }}</td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Plazo</th>
                                    <td class="border text-right">{{ InfoInvoiceModal.descripción }}</td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Fecha vencimiento</th>
                                    <td class="border text-right">{{ $h.formatDate(InfoInvoiceModal.vencimiento) }}</td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Bruto</th>
                                    <td class="border text-right">{{
                                            $h.formatCurrency(InfoInvoiceModal.valor_mercancia)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Descuento (-)</th>
                                    <td class="border text-right">{{
                                            $h.formatCurrency(InfoInvoiceModal.descuento_pie)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">IVA (+)</th>
                                    <td class="border text-right">{{ $h.formatCurrency(InfoInvoiceModal.iva) }}</td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Retencion (-)</th>
                                    <td class="border text-right">{{
                                            $h.formatCurrency(InfoInvoiceModal.retencion)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Abono (-)</th>
                                    <td class="border text-right">{{
                                            $h.formatCurrency(InfoInvoiceModal.valor_aplicado)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Total a pagar</th>
                                    <td class="border text-right">{{
                                            $h.formatCurrency(InfoInvoiceModal.ValorTotal)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Vendedor</th>
                                    <td class="border text-right">{{ InfoInvoiceModal.NombreVendedor }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="CloseInfoInvoiceModal" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="logModal" max-width="lg" @close="logModal = false">
                <template #title>
                    Log
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody v-if="infoModal.log.length > 0">
                            <tr v-for="item in infoModal.log">
                                <td>{{ item.description }}</td>
                                <td>{{ item.user.name }}</td>
                                <td>{{ item.created_at }}</td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="3" class="text-center text-danger">Sin registros</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-secondary" @click="logModal = false">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head, Link} from '@inertiajs/vue3'

export default {
    components: {
        JetDialogModal,
        Head,
        Link
    },

    props: {
        cash_receipts: Array,
        advances: Array
    },

    data() {
        return {
            cash_receipt_table: {
                columns: [
                    'consecutive',
                    'customer',
                    'seller',
                    'total_paid',
                    'comments',
                    'approved_by',
                    'state',
                    'dms_cash_receipt',
                    'payment_date',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        customer: 'CLIENTE',
                        seller: 'VENDEDOR',
                        total_paid: 'TOTAL RC',
                        comments: 'COMENTARIOS',
                        approved_by: 'APROBO',
                        state: 'ESTADO',
                        dms_cash_receipt: 'RC DMS',
                        payment_date: 'FECHA CONSIGNACIÓN',
                        created_at: 'CREADO EN',
                        actions: '',
                    },
                    clientSorting: false,
                    sortable: ['consecutive', 'customer', 'seller', 'total_paid', 'state'],
                    templates: {
                        customer(h, row) {
                            return `${row.customer_code} - ${row.customer.RAZON_SOCIAL}`
                        },
                        seller(h, row) {
                            return row.customer.NOMBRE_VENDEDOR
                        },
                        total_paid(h, row) {
                            return this.$h.formatCurrency(row.total_paid)
                        },
                        approved_by(h, row) {
                            return row.approvedby ? row.approvedby.name : <span class="badge badge-danger">NA</span>
                        },
                        payment_date(h, row) {
                            return this.$h.formatDate(row.payment_date, 'DD-MM-YYYY')
                        },
                        created_at(h, row) {
                            return this.$h.formatDate(row.created_at, 'DD-MM-YYYY')
                        },
                        dms_cash_receipt(h, row) {
                            return row.dms_cash_receipt ?? <span class="badge badge-danger">NA</span>
                        },
                        state(h, row) {
                            switch (row.state) {
                                case "0":
                                    return <span class="badge badge-danger">Anulado</span>
                                case "1":
                                    return <span class="badge badge-warning">Borrado</span>
                                case "2":
                                    return <span class="badge badge-purple">Pendiente</span>
                                case "3":
                                    return <span class="badge badge-danger">Rechazado</span>
                                case "4":
                                    return <span class="badge badge-success">Finalizado</span>
                            }
                        }
                    }
                },
            },
            advances_table: {
                columns: [
                    'consecutive',
                    'customer',
                    'total_paid',
                    'details',
                    'approved_by',
                    'state',
                    'dms_cash_receipt',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        customer: 'CLIENTE',
                        total_paid: 'TOTAL ANTICIPO',
                        details: 'COMENTARIOS',
                        approved_by: 'APROBO',
                        state: 'ESTADO',
                        dms_cash_receipt: 'RC DMS',
                        created_at: 'CREADO EN',
                        actions: '',
                    },
                    clientSorting: false,
                    sortable: ['consecutive', 'customer', 'total_paid', 'state'],
                    templates: {
                        customer(h, row) {
                            return `${row.customer_code} - ${row.customer.RAZON_SOCIAL}`
                        },
                        total_paid(h, row) {
                            return this.$h.formatCurrency(row.total_paid)
                        },
                        approved_by(h, row) {
                            return row.approvedby ? row.approvedby.name : <span class="badge badge-danger">NA</span>
                        },
                        created_at(h, row) {
                            return this.$h.formatDate(row.created_at, 'DD-MM-YYYY')
                        },
                        dms_cash_receipt(h, row) {
                            return row.dms_cash_receipt ?? <span class="badge badge-danger">NA</span>
                        },
                        state(h, row) {
                            switch (row.state) {
                                case "0":
                                    return <span class="badge badge-danger">Anulado</span>
                                case "1":
                                    return <span class="badge badge-warning">Borrado</span>
                                case "2":
                                    return <span class="badge badge-purple">Pendiente</span>
                                case "3":
                                    return <span class="badge badge-danger">Rechazado</span>
                                case "4":
                                    return <span class="badge badge-success">Finalizado</span>
                            }
                        }
                    }
                }
            },
            cashReceiptTableData: this.cash_receipts,
            advancesTableData: this.advances,
            infoModal: null,
            isOpenReceipt: false,
            isOpenAdvance: false,
            openInfoInvoiceModal: false,
            InfoInvoiceModal: null,
            logModal: false,

            infoLogModal: {
                open: false,
                data: []
            }
        }
    },

    methods: {
        closeModal() {
            this.isOpenReceipt = false;
            this.isOpenAdvance = false;
            this.infoModal = null;
        },

        openModalReceipt() {
            this.isOpenReceipt = true;
        },

        openModalAdvance() {
            this.isOpenAdvance = true;
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

        viewReceipt(row) {
            this.loading(true);
            axios.get(route('cash-register-receipts.view'), {
                params: {
                    id: row,
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModalReceipt();
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

        viewAdvance(row) {
            this.loading(true);
            axios.get(route('advances.view'), {
                params: {
                    id: row,
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModalAdvance();
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

        approveReceipt(row) {
            this.$swal({
                title: '¿Aprobar RC y subir a DMS?',
                html: 'Esta acción <span class="badge badge-danger badge-rounded">NO es reversible</span>, verifique la toda información antes de continuar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, Continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Aprobando y subiendo a DMS...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('management-advances-receipt.approve-receipt'), row).then(res => {
                        this.cashReceiptTableData = res.data.cash_receipts;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El recibo de caja " + res.data.dms_number + " fue aprobado y subido a DMS!",
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
                }
            });
        },

        refuseReceipt(row) {
            this.$swal({
                title: '¿Rechazar recibo de caja?',
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
                        title: 'Rechazando recibo de caja...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('management-advances-receipt.refuse-receipt'), {
                        id: row.id,
                        justify: inputValue.value
                    }).then(res => {
                        this.cashReceiptTableData = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El recibo de caja ha sido rechazado!",
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

        approveAdvance(row) {
            this.$swal({
                title: '¿Aprobar Anticipo y subir a DMS?',
                html: 'Esta acción <span class="badge badge-danger badge-rounded">NO es reversible</span>, verifique la toda información antes de continuar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Aprobando y subiendo a DMS...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('management-advances-receipt.approve-advance'), row).then(res => {
                        this.advancesTableData = res.data.advances;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El anticipo " + res.data.dms_number + " fue aprobado y subido a DMS!",
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
                }
            });
        },

        refuseAdvance(row) {
            this.$swal({
                title: '¿Rechazar anticipo?',
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
                        title: 'Rechazando anticipo...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('management-advances-receipt.refuse-advance'), {
                        id: row.id,
                        justify: inputValue.value
                    }).then(res => {
                        this.advancesTableData = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El anticipo ha sido rechazado!",
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

        info_invoice(invoice) {
            this.loading(true);
            axios.get(route('cash-register-receipts.search-document'), {
                params: {
                    invoice: invoice,
                }
            }).then(resp => {
                this.InfoInvoiceModal = resp.data;
                this.loading(false);
                this.openInfoInvoiceModal = true;
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifique que toda la información sea correcta, si el problema persiste comuníquese con sistemas',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error.data);
            })
        },

        CloseInfoInvoiceModal() {
            this.openInfoInvoiceModal = false;
        },

        viewLog(log) {
            this.infoLogModal = {
                open: true,
                data: log
            }
        },
    }
}
</script>

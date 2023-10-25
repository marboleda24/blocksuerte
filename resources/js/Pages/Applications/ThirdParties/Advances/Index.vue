<template>
    <div>
        <Head title="Anticipos"/>

        <portal to="application-title">
            Anticipos
        </portal>

        <portal to="actions">
            <Link :href="route('advances.create')" class="btn btn-primary mr-2">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo anticipo
            </Link>

            <button class="btn btn-primary" @click="isReportModal = true">
                <font-awesome-icon icon="circle-dollar-to-slot" class="mr-2"/>
                Ver Acumulado
            </button>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="refuse-tab"
                            tag="button"
                            content="Rechazados"
                            data-tw-toggle="tab"
                            data-tw-target="#refuse"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Rechazados({{ refuseRows.length }})
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="erase-tab"
                            tag="button"
                            content="Borrador"
                            data-tw-toggle="tab"
                            data-tw-target="#erase"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Borrador({{ eraseRows.length }})
                        </Tippy>
                    </li>


                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Pendientes({{ pendingRows.length }})
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="finish-tab"
                            tag="button"
                            content="Finalizados"
                            data-tw-toggle="tab"
                            data-tw-target="#finish"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Finalizados({{ finishRows.length }})
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="cancel-tab"
                            tag="button"
                            content="Anulado"
                            data-tw-toggle="tab"
                            data-tw-target="#cancel"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Anulados({{ cancelRows.length }})
                        </Tippy>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="refuse" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="refuse-tab">
                        <v-client-table :data="refuseRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-44">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="sendWallet(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a Cartera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                    <div id="erase" class="tab-pane p-2" role="tabpanel" aria-labelledby="erase-tab">
                        <v-client-table :data="eraseRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-44">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="sendWallet(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a Cartera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="pending" class="tab-pane p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pendingRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-44">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="sendWallet(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a Cartera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finishRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-44">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="sendWallet(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a Cartera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                        <v-client-table :data="cancelRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-44">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="sendWallet(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a Cartera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=xl @close="closeModal">
                <template #title v-if="infoModal">
                    Anticipo # {{ infoModal.consecutive }}
                </template>

                <template #content v-if="infoModal">
                    <button class="btn btn-sm btn-primary w-1/2 mb-2"
                            @click="logModal = true">
                        Ver Log
                    </button>
                    <div class="overflow-x-auto rounded-lg border">
                        <table class="table table--sm">
                            <tbody class="uppercase">
                            <tr>
                                <th class="border" scope="row">CLIENTE</th>
                                <td class="border text-right">{{ infoModal.customer.RAZON_SOCIAL }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">NIT</th>
                                <td class="border text-right">{{ infoModal.customer.NIT }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">FECHA DE CREACIÓN</th>
                                <td class="border text-right">{{ $h.formatDate(infoModal.created_at) }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">FECHA DE CONSIGNACIÓN</th>
                                <td class="border text-right">{{ $h.formatDate(infoModal.payment_date) }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">TOTAL ANTICIPO</th>
                                <td class="border text-right">{{ $h.formatCurrency(infoModal.total_paid) }}</td>
                            </tr>
                            <tr>
                                <th class="border" scope="row">COMENTARIOS</th>
                                <td class="border text-right">{{ infoModal.details }}</td>
                            </tr>
                            <tr v-if="infoModal.cancel_justify && infoModal.state === '0'">
                                <th class="border" scope="row">MOTIVO DE ANULACIÓN</th>
                                <td class="border text-right">{{ infoModal.cancel_justify }}</td>
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

            <jet-dialog-modal :show="isReportModal" max-width=lg @close="closeModal">
                <template #title>
                    Acumulado por fecha
                </template>

                <template #content>
                    <div class="flex flex-row">
                        <Tippy tag="a"
                               content="Busque y seleccione una fecha o rango de fechas para cargar el recaudo"
                               class="w-full ml-auto"
                               :options="{ placement: 'top' }" href="javascript:;">
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
                        </Tippy>
                    </div>

                    <div class="border-2 rounded-lg mt-2">
                        <div class="p-2 text-base text-center">
                            El acumulado de anticipos para las fechas {{ form.date }} es de
                            {{ $h.formatCurrency(report_data) }}
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>

            </jet-dialog-modal>

            <jet-dialog-modal :show="logModal" max-width="lg" @close="logModal = false">
                <template #title>
                    Log de anticipo
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">Descripcion</th>
                                    <th class="whitespace-nowrap">Usuario</th>
                                    <th class="whitespace-nowrap">Fecha</th>
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
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    components: {
        JetDialogModal,
        Head,
        Link
    },

    props: {
        advances: Array,
    },

    data() {
        return {
            columns: [
                'consecutive',
                'customer_name',
                'customer_code',
                'total_paid',
                'details',
                'approved_by',
                'dms_cash_receipt',
                'payment_date',
                'created_at',
                'actions'
            ],
            options: {
                headings: {
                    consecutive: '#',
                    customer_name: 'CLIENTE',
                    customer_code: 'CÓDIGO DEL CLIENTE',
                    total_paid: 'TOTAL ANTICIPO',
                    details: 'COMENTARIOS',
                    approved_by: 'APROBO',
                    dms_cash_receipt: 'RC DMS',
                    payment_date: 'FECHA DE CONSIGNACIÓN',
                    created_at: 'CREADO EN',
                    actions: '',
                },

                clientSorting: false,
                sortable: ['consecutive', 'customer_name', 'customer_nit', 'total_paid', 'state'],
                templates: {
                    customer_name(h, row) {
                        return row.customer?.RAZON_SOCIAL
                    },
                    total_paid: function (h, row) {
                        return this.$h.formatCurrency(row.total_paid)
                    },
                    approved_by(h, row) {
                        return row.approved_by
                            ? row.approvedby.name
                            : <span class="badge badge-danger">N/A</span>
                    },
                    payment_date(h, row) {
                        return dayjs(row.payment_date).format('DD-MM-YYYY')
                    },
                    created_at(h, row) {
                        return dayjs(row.created_at).format('DD-MM-YYYY')
                    },
                    dms_cash_receipt(h, row) {
                        return row.dms_cash_receipt
                            ? <span class="badge badge-success">{row.dms_cash_receipt}</span>
                            : <span class="badge badge-danger">N/A</span>
                    },
                },
                filterAlgorithm: {
                    customer_name(row, query) {
                        return (row.customer.RAZON_SOCIAL.toLowerCase()).includes(query.toLowerCase())
                    },
                },
                customSorting: {
                    customer_name(ascending) {
                        return function (a, b) {
                            const lastA = a.customer.RAZONSOCIAL[0].toLowerCase();
                            const lastB = b.customer.RAZONSOCIAL[0].toLowerCase();

                            if (ascending)
                                return lastA >= lastB ? 1 : -1;

                            return lastA <= lastB ? 1 : -1;
                        }
                    }
                },

            },
            tableData: this.advances,
            infoModal: null,
            isOpen: false,
            form: {
                date: '',
                count: 0
            },

            report_data: 0,
            isReportModal: false,
            logModal: false
        }
    },

    methods: {
        closeModal: function () {
            this.isOpen = false;
            this.infoModal = null;
            this.isReportModal = false;
        },

        openModal: function () {
            this.isOpen = true;
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
            axios.get(route('advances.view'), {
                params: {
                    id: row,
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModal();
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

        cancel(row) {
            if (row.state === '1' || row.state === '3') {
                this.$swal({
                    title: '¿Anular anticipo?',
                    text: "Esta acción solo es reversible por un administrador, ¿Quieres continuar?",
                    icon: 'question',
                    input: 'textarea',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Si, Anular',
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
                            title: 'Anulando anticipo…',
                            text: 'Este proceso puede tardar unos segundos.',
                        });
                        axios.post(route('advances.cancel'), {
                            id: row.id,
                            justify: inputValue.value
                        }).then(res => {
                            this.tableData = res.data;

                            this.$swal({
                                title: '¡Éxito!',
                                text: "¡El anticipo ha sido anulado!",
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
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Solo puedes anular anticipos en estado borrador o rechazados.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },

        sendWallet(row) {
            if (row.state === '1') {
                this.$swal({
                    title: '¿Enviar a cartera?',
                    text: "Este anticipo sera enviado al area de cartera",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '¡Si, enviar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Enviando a cartera...',
                            text: 'Este proceso puede tardar unos segundos.',
                        });

                        axios.post(route('advances.send_wallet'), {'id': row.id}).then(res => {
                            this.tableData = res.data;

                            this.$swal({
                                title: '¡Éxito!',
                                text: "¡El anticipo fue enviado a cartera!",
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
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Solo puedes enviar a cartera recibos de caja en estado borrador.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },
        get_report_advances() {
            axios.get(route('advances.report'), {
                params: {
                    date: this.form.date
                }
            }).then(resp => {
                this.report_data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err);
            })
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        report_advance_date() {
            return this.form.date
        },

        cancelRows() {
            return this.tableData.filter(row => row.state === '0').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        },
        eraseRows() {
            return this.tableData.filter(row => row.state === '1')
        },
        pendingRows() {
            return this.tableData.filter(row => row.state === '2')
        },
        refuseRows() {
            return this.tableData.filter(row => row.state === '3')
        },
        finishRows() {
            return this.tableData.filter(row => row.state === '4')
        }
    },

    watch: {
        report_advance_date: function () {
            this.count === 0 ? this.count++ : this.get_report_advances()
        }
    }
}
</script>

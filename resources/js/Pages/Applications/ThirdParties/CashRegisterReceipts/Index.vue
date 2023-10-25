<template>
    <div>
        <Head title="Mis Recibos de Caja"/>

        <portal to="application-title">
            Mis Recibos de Caja
        </portal>

        <portal to="actions" >
            <Link :href="route('cash-register-receipts.create')" class="btn btn-primary mr-2">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Recibo de Caja
            </Link>

            <button class="btn btn-primary" @click="isReportModal = true">
                <font-awesome-icon icon="circle-dollar-to-slot" class="mr-2"/>
                Ver Acumulado
            </button>
        </portal>

        <div>

            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800" role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="refuse-tab"
                            tag="button"
                            content="Rechazados"
                            data-tw-toggle="tab"
                            data-tw-target="#refuse"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Rechazados({{refuseRows.length}})
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
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Borrador({{eraseRows.length}})
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
                            Pendientes({{pendingRows.length}})
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
                            Finalizados({{finishRows.length}})
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
                            Anulados({{cancelRows.length}})
                        </Tippy>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="refuse" class="tab-pane p-2" role="tabpanel" aria-labelledby="refuse-tab">
                        <v-client-table :data="refuseRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-x-auto">
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
                                               @click="download(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                                Descarga
                                            </a>


                                            <template v-if="(row.state === '0' || row.state === '1' || row.state === '3')">
                                                <Link :href="route('cash-register-receipts.edit', row.id)"
                                                      class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                    Editar
                                                </Link>
                                            </template>

                                            <template v-if="(row.state === '1' || row.state === '3')">
                                                <a href="javascript:void(0)"
                                                   @click="cancel(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                    Anular
                                                </a>
                                            </template>

                                            <template v-if="(row.state === '1')">
                                                <a href="javascript:void(0)"
                                                   @click="sendWallet(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                    Enviar a Cartera
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="erase" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="erase-tab">
                        <v-client-table :data="eraseRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-x-auto">
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
                                               @click="download(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                                Descarga
                                            </a>



                                            <template v-if="(row.state === '0' || row.state === '1' || row.state === '3')">
                                                <Link :href="route('cash-register-receipts.edit', row.id)"
                                                      class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                    Editar
                                                </Link>
                                            </template>

                                            <template v-if="(row.state === '1' || row.state === '3')">
                                                <a href="javascript:void(0)"
                                                   @click="cancel(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                    Anular
                                                </a>
                                            </template>

                                            <template v-if="(row.state === '1')">
                                                <a href="javascript:void(0)"
                                                   @click="sendWallet(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                    Enviar a Cartera
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="pending" class="tab-pane p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pendingRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-x-auto">
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
                                               @click="download(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                                Descarga
                                            </a>



                                            <template v-if="(row.state === '0' || row.state === '1' || row.state === '3')">
                                                <Link :href="route('cash-register-receipts.edit', row.id)"
                                                      class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                    Editar
                                                </Link>
                                            </template>

                                            <template v-if="(row.state === '1' || row.state === '3')">
                                                <a href="javascript:void(0)"
                                                   @click="cancel(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                    Anular
                                                </a>
                                            </template>

                                            <template v-if="(row.state === '1')">
                                                <a href="javascript:void(0)"
                                                   @click="sendWallet(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                    Enviar a Cartera
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finishRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-x-auto">
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
                                               @click="download(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                                Descarga
                                            </a>

                                            <template v-if="(row.state === '0' || row.state === '1' || row.state === '3')">
                                                <Link :href="route('cash-register-receipts.edit', row.id)"
                                                      class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                    Editar
                                                </Link>
                                            </template>

                                            <template v-if="(row.state === '1' || row.state === '3')">
                                                <a href="javascript:void(0)"
                                                   @click="cancel(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                    Anular
                                                </a>
                                            </template>

                                            <template v-if="(row.state === '1')">
                                                <a href="javascript:void(0)"
                                                   @click="sendWallet(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                    Enviar a Cartera
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                        <v-client-table :data="cancelRows" :columns="columns" :options="options" ref="table1"
                                        class="overflow-x-auto">
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
                                               @click="download(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                                Descarga
                                            </a>


                                            <template v-if="(row.state === '0' || row.state === '1' || row.state === '3')">
                                                <Link :href="route('cash-register-receipts.edit', row.id)"
                                                      class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                    Editar
                                                </Link>
                                            </template>

                                            <template v-if="(row.state === '1' || row.state === '3')">
                                                <a href="javascript:void(0)"
                                                   @click="cancel(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                    Anular
                                                </a>
                                            </template>

                                            <template v-if="(row.state === '1')">
                                                <a href="javascript:void(0)"
                                                   @click="sendWallet(row)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                    Enviar a Cartera
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title v-if="infoModal">
                    Recibo de caja # {{ infoModal.consecutive }}
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
                                <td class="text-right"> US:{{ $h.formatCurrency(infoModal.trm) }}</td>
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
                                <td class="text-right" v-if="infoModal.type === 'export'"> US:{{ $h.formatCurrency(infoModal.total_paid) }}</td>
                                <td class="text-right" v-else>{{ $h.formatCurrency(infoModal.total_paid) }}</td>
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
                            <template  v-if="infoModal.type === 'export'">
                                <th class="text-center">FACTURA</th>
                                <th class="text-center">TRM</th>
                                <th class="text-center">BRUTO</th>
                                <th class="text-center">DESCUENTO</th>
                                <th class="text-center">OTRAS DEDUCCIONES</th>
                                <th class="text-center">OTROS INGRESOS</th>
                                <th class="text-center">GASTOS BANCARIOS</th>
                                <th class="text-center">TOTAL</th>
                                <th class="text-center">DIFERENCIA EN CAMBIO</th>
                                <th class="text-center">SALDO A FAVOR</th>
                            </template>

                            <template v-else>
                                <th class="text-center">FACTURA</th>
                                <th class="text-center">BRUTO</th>
                                <th class="text-center">DESCUENTO</th>
                                <th class="text-center">RETENCION</th>
                                <th class="text-center">RETEIVA</th>
                                <th class="text-center">RETEICA</th>
                                <th class="text-center">OTRAS DEDUCCIONES</th>
                                <th class="text-center">OTROS INGRESOS</th>
                                <th class="text-center">TOTAL</th>
                            </template>

                            </thead>
                            <tbody>
                            <tr v-for="row in infoModal.details" v-bind:key="row.id">
                                <template  v-if="infoModal.type === 'export'">
                                    <td>{{ row.invoice }}</td>
                                    <td> {{ $h.formatCurrency(row.trm) }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.bruto)  }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.discount) }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.other_deductions) }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.other_income) }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.financial_expenses) }}</td>
                                    <td class="text-right">US {{ $h.formatCurrency(row.total) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.change_difference) }}</td>
                                    <td class="text-right"> US {{ $h.formatCurrency(row.positive_balance) }}</td>
                                </template>

                                <template v-else>
                                    <td>{{ row.invoice }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.bruto)  }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.discount) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.retention) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.reteiva) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.reteica) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.other_deductions) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.other_income) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.total) }}</td>
                                </template>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-primary mr-auto" @click="viewLog(infoModal.log)">
                        Ver Log
                    </button>

                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="isReportModal" max-width=lg>
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
                            El acumulado de RC para las fechas {{ form.date }} es de {{ $h.formatCurrency(report_data) }}
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="infoLogModal.open" @close="closeLogModal()" max-width=3xl>
                <template #title v-if="infoLogModal">
                    Seguimiento recibo de caja
                </template>

                <template #content>
                    <div class="px-0 sm:px-0 py-2 sm:py-2">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th>DESCRIPCIÓN</th>
                                    <th>RESPONSABLE</th>
                                    <th>FECHA</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="log in infoLogModal.data" v-bind:key="log.id">
                                    <td>{{ log.description }}</td>
                                    <td>{{ log.user.name }}</td>
                                    <td>{{ $h.formatDate(log.created_at) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeLogModal()" type="button" class="btn btn-secondary">
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
        cash_receipt: Array,
    },

    data() {
        return {
            columns: [
                'type_name',
                'consecutive',
                'customer',
                'total_paid',
                'comments',
                'approved_by',
                'dms_cash_receipt',
                'payment_date',
                'created_at',
                'actions'
            ],
            options: {
                headings: {
                    type_name: 'TIPO',
                    consecutive: '#',
                    customer: 'CLIENTE',
                    total_paid: 'TOTAL RC',
                    comments: 'COMENTARIOS',
                    approved_by: 'APROBO',
                    dms_cash_receipt: 'RC DMS',
                    payment_date: 'FECHA DE CONSIGNACIÓN',
                    created_at: 'CREADO EN',
                    actions: '',
                },
                clientSorting: false,
                sortable: ['consecutive', 'customer', 'total_paid'],
                templates: {
                    total_paid(h, row) {
                        return row.type_name === 'Exportación' ? 'US'+this.$h.formatCurrency(row.total_paid) : this.$h.formatCurrency(row.total_paid)
                    },
                    created_at(h, row) {
                        return this.$dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                    },
                    payment_date(h, row) {
                        return this.$dayjs(row.payment_date).format('DD-MM-YYYY')
                    },
                    dms_cash_receipt(h, row) {
                        return row.dms_cash_receipt
                            ? <span class="badge badge-success">{row.dms_cash_receipt}</span>
                            : <span class="badge badge-danger">N/A</span>
                    },
                }
            },
            tableData: this.cash_receipt,
            infoModal: null,
            isOpen: false,
            form: {
                date: '',
                count: 0
            },
            report_data: 0,
            isReportModal: false,

            infoLogModal: {
                open: false,
                data: []
            }
        }
    },

    methods: {
        viewLog(log) {
            this.infoLogModal = {
                open: true,
                data: log
            }
        },
        closeModal () {
            this.isOpen = false;
            this.infoModal = null;
            this.isReportModal = false;
        },

        closeLogModal() {
            this.infoLogModal = {
                open: false,
                data: []
            }
        },

        openModal () {
            this.isOpen = true;
        },

        view(row) {
            this.loading(true);
            axios.get(route('cash-register-receipts.view'), {
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

        cancel(row) {
            if (row.state === '1' || row.state === '3') {
                this.$swal({
                    title: '¿Anular recibo de caja?',
                    text: 'Escribe una justificación para esta acción',
                    icon: 'question',
                    input: 'textarea',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Si, anular',
                    inputValidator: (inputValue) => {
                        return !inputValue && 'Por favor, escribe una justificación'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Anulando recibo de caja...',
                            text: 'Este proceso puede tardar unos segundos.',
                        });
                        axios.post(route('cash-register-receipts.cancel'), {
                            id: row.id,
                            justify: inputValue.value
                        }).then(res => {
                            this.tableData = res.data;

                            this.$swal({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: "¡El recibo de caja fue anulado!",
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
                    }else {
                        inputValue.dismiss = this.$swal.DismissReason.cancel
                    }
                })

            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Solo puedes anular recibos de caja en estado borrador o rechazados.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },

        sendWallet(row) {
            if (row.state === '1') {
                this.$swal({
                    title: '¿Enviar a cartera?',
                    text: "Este recibo de caja sera enviado al area de cartera",
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

                        axios.post(route('cash-register-receipts.send_wallet'), {'id': row.id}).then(res => {
                            this.tableData = res.data;

                            this.$swal({
                                title: '¡Éxito!',
                                text: "¡El recibo de caja fue enviado a cartera!",
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

        get_report_cash_receipt() {
            axios.get(route('cash-register-receipts.report'), {
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
        },
        download(row){
            axios.get(route('receipt.download_cash_receipts_export',[row]), {
                params: {
                    id: row,
                },
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');

                link.href = url;
                link.setAttribute('download', 'recibos-de-caja:.pdf');

                document.body.appendChild(link);
                link.click();

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

    },

    computed: {
        current_date() {
            return this.$dayjs()
        },

        report_cash_receipt_date() {
            return this.form.date
        },

        cancelRows(){
            return this.tableData.filter(row => row.state === '0')
        },

        eraseRows(){
            return this.tableData.filter(row => row.state === '1')
        },

        pendingRows() {
            return this.tableData.filter(row => row.state === '2').sort((a, b) => {
                return a.consecutive - b.consecutive
            })
        },

        refuseRows(){
            return this.tableData.filter(row => row.state === '3').sort((a, b) => {
                return a.consecutive - b.consecutive
            })
        },

        finishRows(){
            return this.tableData.filter(row => row.state === '4')
        }
    },

    watch: {
        report_cash_receipt_date: function () {
            this.count === 0 ? this.count++ : this.get_report_cash_receipt()
        }
    }
}
</script>

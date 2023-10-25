<template>
    <div>
        <Head title="Mis Pedidos"/>

        <portal to="application-title">
            Mis Pedidos
        </portal>

        <portal to="actions">
            <Link :href="route('orders.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Pedido
            </Link>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="erase-tab"
                            tag="button"
                            content="Borradores"
                            data-tw-toggle="tab"
                            data-tw-target="#erase"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            BORRADORES
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Rechazados"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            RECHAZADOS
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="wallet-tab"
                            tag="button"
                            content="Cartera"
                            data-tw-toggle="tab"
                            data-tw-target="#wallet"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            CARTERA
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="costs-tab"
                            tag="button"
                            content="Costos"
                            data-tw-toggle="tab"
                            data-tw-target="#costs"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            COSTOS
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="production-tab"
                            tag="button"
                            content="Producción"
                            data-tw-toggle="tab"
                            data-tw-target="#production"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            PRODUCCION
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="cellar-tab"
                            tag="button"
                            content="Bodega"
                            data-tw-toggle="tab"
                            data-tw-target="#cellar"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            BODEGA
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="dies-tab"
                            tag="button"
                            content="Troqueles"
                            data-tw-toggle="tab"
                            data-tw-target="#dies"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            TROQUELES
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="cancel-tab"
                            tag="button"
                            content="Anulados"
                            data-tw-toggle="tab"
                            data-tw-target="#cancel"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            ANULADOS
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
                            FINALIZADOS
                        </Tippy>
                    </li>
                </ul>
                <div class="post__content tab-content p-2">
                    <div id="erase" class="tab-pane p-2" role="tabpanel" aria-labelledby="erase-tab">

                        <v-client-table :data="erase_rows" :columns="columns" :options="options" ref="table1">
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
                                               @click="sendWallet(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar a cartera
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <Link :href="route('orders.edit', row.id)"
                                                  class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                Editar
                                            </Link>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="pending" class="tab-pane active p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pending_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               v-if="row.state === '7'"
                                               @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <Link :href="route('orders.edit', row.id)"
                                                  class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                Editar
                                            </Link>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="wallet" class="tab-pane p-2" role="tabpanel" aria-labelledby="wallet-tab">
                        <v-client-table :data="wallet_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="costs" class="tab-pane p-2" role="tabpanel" aria-labelledby="costs-tab">
                        <v-client-table :data="costs_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               v-if="row.state === '7'"
                                               @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>

                                            <Link :href="route('orders.edit', row.id)"
                                                  class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                Editar
                                            </Link>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="production" class="tab-pane p-2" role="tabpanel" aria-labelledby="production-tab">
                        <v-client-table :data="production_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cellar" class="tab-pane p-2" role="tabpanel" aria-labelledby="cellar-tab">
                        <v-client-table :data="cellar_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="dies" class="tab-pane p-2" role="tabpanel" aria-labelledby="dies-tab">
                        <v-client-table :data="dies_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                        <v-client-table :data="cancel_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <Link :href="route('orders.edit', row.id)"
                                                  class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                                Editar
                                            </Link>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finish_rows" :columns="columns" :options="options" ref="table1"
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
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="clone(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                Clonar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <show-order-modal :info-modal="infoModal" :is-open="isOpen" @close="closeModal"/>
            <jet-dialog-modal :show="isOpenClone">
                <template #title v-if="cloneform.orderid">
                    Clonar pedido # {{ cloneform.consecutive }}
                </template>

                <template #content>

                    <div class="p-2">
                        <div class="mb-4">
                            <jet-label for="customer" value="Cliente"/>

                            <autocomplete
                                url="/orders/search-customer"
                                show-field="name"
                                @selected-value="getDataCustomer"
                            />
                            <jet-input-error v-if="errors.customer" :message="errors.customer[0]" class="mt-2"/>
                        </div>

                        <div class="mb-4">
                            <jet-label for="type" value="Tipo"/>
                            <select id="type" class="form-control" v-model="cloneform.type">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="national">Nacional</option>
                                <option value="export">Exportacion</option>
                                <option value="forecast">Pronostico</option>
                                <option value="samples">Muestras</option>
                                <option value="elena">Elena</option>
                                <option value="point_of_sale">Punto de venta</option>
                                <option value="services">Servicios</option>
                                <option value="delivered_merchandise">Mercancía Entregada</option>
                                <option value="recycling">Recuperación – Reciclaje</option>
                            </select>
                            <jet-input-error v-if="errors.type" :message="errors.type[0]" class="mt-2"/>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeCloneModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click.prevent="save(cloneform)" :disabled="cloneform.processing"
                            type="submit" class="btn btn-primary">
                        Clonar
                    </button>
                </template>
            </jet-dialog-modal>

        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import ShowOrderModal from "@/Pages/Applications/Orders/ShowOrderModal.vue";
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

export default {
    components: {
        Autocomplete,
        JetDialogModal,
        JetLabel,
        JetInputError,
        Head,
        Link,
        ShowOrderModal
    },

    props: {
        data: Array,
    },

    data() {
        return {
            columns: [
                'consecutive',
                'oc',
                'order_max',
                'customer_name',
                'customer_term',
                'type',
                'destiny',
                'bruto',
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
                    order_max: 'PEDIDO MAX',
                    customer_name: 'RAZON SOCIAL',
                    customer_term: 'CONDICIÓN PAGO',
                    type: 'TIPO',
                    destiny: 'DESTINO',
                    bruto: 'BRUTO',
                    discount: 'DESCUENTO',
                    subtotal: 'SUBTOTAL',
                    taxes: 'IVA',
                    created_at: 'CREADO',
                    actions: '',
                },
                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['consecutive', 'oc', 'order_max', 'type', 'destiny', 'customer_name', 'customer_term', 'created_at'],
                templates: {
                    customer_name: function (h, row) {
                        return row.customer_code + ' - ' + row.customer_name
                    },
                    type: function (h, row) {
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
                                return <span class="badge badge-purple">Reclamo</span>
                            case "recycling":
                                return <span class="badge badge-purple">Recuperación – Reciclaje</span>
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
                    bruto(h, row) {
                        if (row.type === 'export') {
                            return 'USD ' + this.$h.formatCurrency(row.bruto, row.trm)
                        } else {
                            return this.$h.formatCurrency(row.bruto)
                        }
                    },
                    subtotal(h, row) {
                        if (row.type === 'export') {
                            return 'USD ' + this.$h.formatCurrency(row.subtotal, row.trm)
                        } else {
                            return this.$h.formatCurrency(row.subtotal)
                        }
                    },
                    taxes(h, row) {
                        return this.$h.formatCurrency(row.taxes)
                    },
                    discount(h, row) {
                        if (row.type === 'export') {
                            return 'USD ' + this.$h.formatCurrency(row.discount, row.trm)
                        } else {
                            return this.$h.formatCurrency(row.discount)
                        }
                    },
                    oc(h, row) {
                        return row.oc
                            ? <span class="badge badge-success">{row.oc}</span>
                            : <span class="badge badge-danger">N/A</span>
                    },
                    order_max(h, row) {
                        return row.order_max
                            ? <span class="badge badge-success">{row.order_max}</span>
                            : <span class="badge badge-danger">N/A</span>

                    }
                },
                filterAlgorithm: {
                    customer_name(row, query) {
                        return (row.customer_code + ' - ' + row.customer_name).toLowerCase().includes(query)
                    }
                },
                cellClasses: {
                    bruto: [{class: 'text-right', condition: row => row}],
                    subtotal: [{class: 'text-right', condition: row => row}],
                    discount: [{class: 'text-right', condition: row => row}],
                    taxes: [{class: 'text-right', condition: row => row}],
                }
            },
            tableData: this.data,
            erase_rows: [],
            pending_rows: [],
            wallet_rows: [],
            costs_rows: [],
            production_rows: [],
            cellar_rows: [],
            dies_rows: [],
            cancel_rows: [],
            finish_rows: [],
            cloneform: {
                orderid: null,
                customer: null,
                customer_name: null,
                processing: false,
                type: null,
            },
            vueAutocompleteInstance: null,
            isOpen: false,
            infoModal: null,
            isOpenClone: false,
            infoCloneModal: null,
            errors: {}
        }
    },

    methods: {
        sendWallet(row) {
            this.$swal({
                title: '¿Enviar a cartera?',
                text: "Este pedido sera enviado al area de cartera",
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

                    axios.post(route('orders.send_wallet'), {'id': row}).then(res => {
                        this.tableData = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El pedido fue enviado a cartera!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: err.response.data,
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    });
                }
            });
        },

        cancel(row) {
            this.$swal({
                title: '¿Anular pedido?',
                text: 'Escribe una justificación para esta acción',
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
                        title: 'anulando pedido...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });
                    axios.post(route('orders.cancel'), {
                        'id': row,
                        'justify': inputValue.value
                    }).then(res => {
                        this.tableData = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El pedido ha sido anulado!",
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

        reopen(row) {
            this.$swal({
                title: '¿Reabrir pedido?',
                text: "Este pedido sera reabierto y quedara en modo borrador para que lo puedas editar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, reabrir!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Reabriendo pedido...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });


                    axios.post(route('orders.reopen'), {'id': row}).then(res => {
                        this.tableData = res.data;

                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: "¡El pedido fue reabierto!",
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

        openModal: function () {
            this.isOpen = true;
        },

        closeModal: function () {
            this.isOpen = false;
            this.infoModal = null;
        },

        openCloneModal: function () {
            this.isOpenClone = true;
        },

        closeCloneModal: function () {
            this.isOpenClone = false;
            this.infoCloneModal = null;
            this.cloneform = {
                orderid: null,
                customer: null,
                customer_name: null,
                processing: false,
                type: null
            }
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

        clone(row) {
            this.cloneform.orderid = row;
            this.openCloneModal();
        },

        getDataCustomer(obj) {
            this.cloneform.customer = obj.code;
        },

        save: function (data) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Clonando pedido…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('orders.clone'), data).then(res => {
                this.closeCloneModal();
                this.errors = {};
                this.tableData = res.data.orders;

                this.$swal({
                    title: '¡Éxito!',
                    text: `Pedido clonado con éxito!, Nuevo pedido #${res.data.new_id}`,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => this.errors = err.response.data.errors)
        },

        update_rows() {
            this.erase_rows = this.tableData.filter(function (obj) {
                return obj.state === '1'
            });

            this.pending_rows = this.tableData.filter(function (obj) {
                return obj.state === '7'
            });

            this.wallet_rows = this.tableData.filter(function (obj) {
                return obj.state === '2'
            });

            this.costs_rows = this.tableData.filter(function (obj) {
                return obj.state === '3'
            });

            this.cellar_rows = this.tableData.filter(function (obj) {
                return obj.state === '4'
            });

            this.production_rows = this.tableData.filter(function (obj) {
                return obj.state === '5'
            });

            this.dies_rows = this.tableData.filter(function (obj) {
                return obj.state === '6'
            });

            this.cancel_rows = this.tableData.filter(function (obj) {
                return obj.state === '0'
            });

            this.finish_rows = this.tableData.filter(function (obj) {
                return obj.state === '10'
            });
        }
    },

    mounted() {
        this.update_rows();
    },

    watch: {
        tableData: function () {
            this.update_rows();
        }
    }
}
</script>


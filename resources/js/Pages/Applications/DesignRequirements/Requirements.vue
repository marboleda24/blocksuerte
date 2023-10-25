<template>
    <div>
        <Head title="Requerimientos de Diseño Grafico"/>

        <portal to="application-title">
            Requerimientos de Diseño Grafico
        </portal>

        <portal to="actions">
            <Link :href="route('design-requirements.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Requerimiento
            </Link>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800" role="tablist">

                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes revision"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            PENDIENTES REVISION {{ `(${pendingRows.length})` }}
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="refuse-tab"
                            tag="button"
                            content="Rechazados"
                            data-tw-toggle="tab"
                            data-tw-target="#refuse"
                            href="javascript:void(0)"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            RECHAZADOS {{ `(${refuseRows.length})` }}
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="assigned-tab"
                            tag="button"
                            content="Asignados"
                            data-tw-toggle="tab"
                            data-tw-target="#assigned"
                            href="javascript:void(0)"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            ASIGNADOS {{ `(${assignedRows.length})` }}
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="started-tab"
                            tag="button"
                            content="Iniciados"
                            data-tw-toggle="tab"
                            data-tw-target="#started"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            INICIADOS {{ `(${startedRows.length})` }}
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="correct-tab"
                            tag="button"
                            content="Por Corregir"
                            data-tw-toggle="tab"
                            data-tw-target="#correct"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            POR CORREGIR {{ `(${correctRows.length})` }}
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
                            FINALIZADOS {{ `(${finishRows.length})` }}
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
                            ANULADOS {{ `(${cancelRows.length})` }}
                        </Tippy>
                    </li>
                </ul>
                <div class="post__content tab-content p-2">
                    <div id="pending" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pendingRows" :columns="table.columns" :options="table.options" ref="pending-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="refuse" class="tab-pane p-2" role="tabpanel" aria-labelledby="refuse-tab">
                        <v-client-table :data="refuseRows" :columns="table.columns" :options="table.options" ref="refuse-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="assigned" class="tab-pane p-2" role="tabpanel" aria-labelledby="assigned-tab">
                        <v-client-table :data="assignedRows" :columns="table.columns" :options="table.options" ref="assigned-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="started" class="tab-pane p-2" role="tabpanel" aria-labelledby="started-tab">
                        <v-client-table :data="startedRows" :columns="table.columns" :options="table.options" ref="started-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="correct" class="tab-pane p-2" role="tabpanel" aria-labelledby="correct-tab">
                        <v-client-table :data="correctRows" :columns="table.columns" :options="table.options" ref="correct-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finishRows" :columns="table.columns" :options="table.options" ref="finish-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                        <v-client-table :data="cancelRows" :columns="table.columns" :options="table.options" ref="cancel-table"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <Link :href="route('design-requirements.requirement', row.id)" class="btn btn-secondary">
                                    <font-awesome-icon icon="arrow-right"/>
                                </Link>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3"

export default {
    components: {
        Head,
        Link
    },

    props: {
        requirements: Array
    },

    data(){
        return {
            table: {
                data: [],
                columns: [
                    'consecutive',
                    'customer',
                    'seller',
                    'designer',
                    'product',
                    'measure',
                    'render',
                    'proposals',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: "#",
                        customer: "CLIENTE",
                        seller: "VENDEDOR(A)",
                        designer: "DISEÑADOR(A)",
                        product: "PRODUCTO",
                        measure: "MEDIDA",
                        render: "¿RENDER?",
                        proposals: "PROPUESTAS",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    clientSorting: false,
                    sortable: ['consecutive', 'customer', 'seller', 'designer', 'product', 'measure', 'render', 'created_at'],
                    templates: {
                        customer: function (h, row) {
                            return row.customer.RAZON_SOCIAL
                        },
                        seller: function (h, row) {
                            return row.seller.name
                        },
                        designer: function (h, row) {
                            return row.assigned_designer ? row.assigned_designer.name : <span class="badge badge-danger">sin asignar</span>
                        },
                        render: function (h, row) {
                            return row.render === 'yes' ? 'SI' : 'NO'
                        },
                        proposals: function (h, row) {
                            return row.count_proposals
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    }
                }
            }
        }
    },

    computed: {
        cancelRows(){
            return this.requirements.filter(row => row.state === '0')
        },
        pendingRows() {
            return this.requirements.filter(row => row.state === '1')
        },
        assignedRows(){
            return this.requirements.filter(row => row.state === '2')
        },
        startedRows(){
            return this.requirements.filter(row => row.state === '3')
        },
        correctRows(){
            return this.requirements.filter(row => row.state === '4')
        },
        finishRows(){
            return this.requirements.filter(row => row.state === '5').sort((a, b) => {
                return b.id - a.id
            })
        },
        refuseRows(){
            return this.requirements.filter(row => row.state === '6')
        },
    }
}
</script>

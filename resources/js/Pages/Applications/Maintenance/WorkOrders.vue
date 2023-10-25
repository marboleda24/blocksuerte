<template>
    <div>
        <Head title="Ordenes de trabajo"/>

        <portal to="application-title">
            Ordenes de trabajo
        </portal>

        <div class="post intro-y overflow-hidden box">
            <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                role="tablist">
                <li class="nav-item w-full">
                    <Tippy
                        id="process-tab"
                        tag="button"
                        content="En Proceso"
                        data-tw-toggle="tab"
                        data-tw-target="#process"
                        href="javascript:;"
                        class="nav-link tooltip w-full py-4"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                    >
                        EN PROCESO
                    </Tippy>
                </li>

                <li class="nav-item w-full">
                    <Tippy
                        id="finish-tab"
                        tag="button"
                        content="Borradores"
                        data-tw-toggle="tab"
                        data-tw-target="#finish"
                        href="javascript:;"
                        class="nav-link tooltip w-full py-4"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                    >
                        FINALIZADO
                    </Tippy>
                </li>

                <li class="nav-item w-full">
                    <Tippy
                        id="cancel-tab"
                        tag="button"
                        content="Cancelado"
                        data-tw-toggle="tab"
                        data-tw-target="#cancel"
                        href="javascript:;"
                        class="nav-link tooltip w-full py-4"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                    >
                        CANCELADO
                    </Tippy>
                </li>

                <li class="nav-item w-full">
                    <Tippy
                        id="null-tab"
                        tag="button"
                        content="Anulado"
                        data-tw-toggle="tab"
                        data-tw-target="#null"
                        href="javascript:;"
                        class="nav-link tooltip w-full py-4 active"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                    >
                        ANULADO
                    </Tippy>
                </li>
            </ul>
            <div class="post__content tab-content p-2">
                <div id="process" class="tab-pane p-2" role="tabpanel" aria-labelledby="process-tab">
                    <v-client-table :data="process" :columns="table.columns" :options="table.options"
                                    class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <Link class="btn btn-secondary" :href="route('maintenance.view-work-order', row.id)">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']"/>
                                </Link>
                            </div>
                        </template>
                    </v-client-table>
                </div>

                <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                    <v-client-table :data="finish" :columns="table.columns" :options="table.options"
                                    class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <Link class="btn btn-secondary" :href="route('maintenance.view-work-order', row.id)">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']"/>
                                </Link>
                            </div>
                        </template>
                    </v-client-table>
                </div>

                <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                    <v-client-table :data="cancel" :columns="table.columns" :options="table.options"
                                    class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <Link class="btn btn-secondary" :href="route('maintenance.view-work-order', row.id)">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']"/>
                                </Link>
                            </div>
                        </template>
                    </v-client-table>
                </div>

                <div id="null" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="null-tab">
                    <v-client-table :data="nulled" :columns="table.columns" :options="table.options"
                                    class="overflow-y-auto">
                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <Link class="btn btn-secondary" :href="route('maintenance.view-work-order', row.id)">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']"/>
                                </Link>
                            </div>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'

export default {
    props: {
        work_orders: Array
    },

    components: {
        Head,
        Link
    },

    data(){
        return {
            table: {
                data: [],
                columns: [
                    'consecutive',
                    'description',
                    'asset',
                    'type',
                    'cost',
                    'assigned_to',
                    'created_at',
                    'created_by',
                    'closing_date',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        description: 'DESCRIPCIÓN',
                        asset: 'ACTIVO',
                        type: 'TIPO',
                        cost: 'COSTO',
                        assigned_to: 'ENCARGADO',
                        created_at: 'CREADA EL',
                        created_by: 'CREADA POR',
                        closing_date: 'FECHA CIERRE',
                        actions: '',
                    },
                    sortable: ['id', 'description', 'type', 'cost', 'assigned_to', 'created_at', 'created_by', 'closing_date'],
                    templates: {
                        asset (h, row) {
                            return row.request.asset.name
                        },
                        type (h, row) {
                            switch (row.type){
                                case "preventive":
                                    return <span class="badge badge-success badge-rounded">Preventivo</span>
                                case "corrective":
                                    return <span class="badge badge-warning badge-rounded">Correctivo</span>
                                case "locative":
                                    return <span class="badge badge-pink badge-rounded">Locativo</span>
                                case "improvement":
                                    return <span class="badge badge-primary badge-rounded">Mejora</span>
                            }
                        },
                        cost(h, row) {
                            return this.$h.formatCurrency(row.activities.reduce(function (a, c) {
                                return a + Number((c.cost) || 0)
                            }, 0))
                        },
                        assigned_to(h, row) {
                            return row.assignedto.name
                        },
                        created_at(h, row) {
                            return this.$h.formatDate(row.created_at)
                        },
                        created_by(h, row) {
                            return row.createdby.name
                        },
                        closing_date(h, row) {
                            return row.closing_date
                                ? this.$h.formatDate(row.closing_date)
                                : <span class="badge badge-danger">NA</span>
                        },
                    }
                }
            }
        }
    },

    computed: {
        process(){
            return this.work_orders.filter(elem => elem.state === '1').sort((a, b) => {
                return a.id - b.id
            });
        },

        nulled(){
            return this.work_orders.filter(elem => elem.state === '0').sort((a, b) => {
                return b.id - a.id
            });
        },

        cancel(){
            return this.work_orders.filter(elem => elem.state === '2').sort((a, b) => {
                return b.id - a.id
            });
        },

        finish(){
            return this.work_orders.filter(elem => elem.state === '3').sort((a, b) => {
                return b.id - a.id
            });
        }
    }
}
</script>

<template>
    <div>
        <Head :title="asset.name"/>

        <portal to="actions">
            <Link :href="route('maintenance.assets.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="box p-5">
            <div class="flex flex-col lg:flex-row py-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative"><img
                        alt="EVPIU" class=""
                        src="/dist/images/box.png">
                    </div>
                    <div class="ml-5">
                        <div class="truncate sm:whitespace-normal font-medium text-lg"> {{ asset.name }}</div>
                        <div class="text-slate-500">{{ asset.code }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <font-awesome-icon :icon="['far', 'circle']" class="lucide w-4 h-4 mr-2"/>
                            <strong class="mr-1">Centro:</strong>  {{ asset.work_center.name }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <font-awesome-icon :icon="['far', 'circle']" class="lucide w-4 h-4 mr-2"/>
                            <strong class="mr-1">Clasificacion :</strong> {{ asset.classification.name }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <font-awesome-icon :icon="['far', 'circle']" class="lucide w-4 h-4 mr-2"/>
                            <strong class="mr-1">Estado:</strong> {{ asset.state === 'good' ? 'Buena' : (asset.state === 'repair' ? 'Reparada': 'Desechada') }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <font-awesome-icon :icon="['far', 'circle']" class="lucide w-4 h-4 mr-2"/>
                            <strong class="mr-1">Creado:</strong> {{ $h.formatDate(asset.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-5 mt-5">
            <div class="box p-5">
                <div class="relative">
                    <doughnut-chart :width="width"
                                    :height="height"
                                    :chart-data="maintenance_data"
                                    :chart-options="options"/>

                    <div class="flex flex-col justify-center items-center absolute w-full h-full top-0 left-0">
                        <div class="text-xl 2xl:text-2xl font-medium">{{ asset.maintenances.length }}</div>
                        <div class="text-slate-500 mt-0.5">Mantenimientos</div>
                        <div class="text-slate-500">Realizados</div>
                    </div>
                </div>

                <div class="mx-auto w-10/12 2xl:w-2/3 mt-8">
                    <div class="flex items-center"
                         v-for="maintenance in maintenances">
                        <div class="w-2 h-2 bg-dark rounded-full mr-3"></div>
                        <span class="truncate">
                            {{ $h.translate_types_maintenance(maintenance.type) }}
                        </span>
                        <span class="font-medium xl:ml-auto">{{ maintenance.total }}</span>
                    </div>
                </div>

            </div>
            <div class="box p-5 col-span-3">
                <v-client-table :data="asset.maintenances" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                    <template v-slot:actions="{row}">
                        <div class="text-center">
                            <button class="btn btn-sm btn-secondary" @click="view(row)">
                                <font-awesome-icon :icon="['far', 'eye']"/>
                            </button>
                        </div>
                    </template>
                </v-client-table>

                <jet-dialog-modal :show="modal.open" max-width=5xl @close="closeModal">
                    <template #title>
                        Visualizar Mantenimiento
                    </template>

                    <template #content>
                        <div class="overflow-x-auto">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="w-1/4 font-bold">CONSECUTIVO</td>
                                        <td class="w-1/4">{{ modal.data.consecutive }}</td>
                                        <td class="w-1/4 font-bold">ACTIVO</td>
                                        <td class="w-1/4">{{ `${asset.code} – ${asset.name}` }}</td>
                                    </tr>

                                    <tr>
                                        <td class="w-1/4 font-bold">SOLICITANTE</td>
                                        <td class="w-1/4">{{ modal.data.applicant.name }}</td>
                                        <td class="w-1/4 font-bold">DESCRIPCION</td>
                                        <td class="w-1/4">{{ modal.data.description }}</td>
                                    </tr>

                                    <tr>
                                        <td class="w-1/4 font-bold">TIPO</td>
                                        <td class="w-1/4">{{ modal.data.type_name }}</td>
                                        <td class="w-1/4 font-bold">ESTADO</td>
                                        <td class="w-1/4">{{ modal.data.state_name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="overflow-x-auto mt-5" v-if="modal.data.work_orders.length > 0">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <td colspan="7">ORDENES DE TRABAJO</td>
                                    </tr>
                                    <tr class="text-center">
                                        <td>#</td>
                                        <td>CREADO EL</td>
                                        <td>CERRADO EL</td>
                                        <td>DESCRIPCIÓN</td>
                                        <td>COSTO</td>
                                        <td>TIPO</td>
                                        <td>ESTADO</td>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="work_order in modal.data.work_orders">
                                        <td>{{ work_order.consecutive }}</td>
                                        <td>{{ work_order.created_at }}</td>
                                        <td>{{ work_order.closing_date }}</td>
                                        <td>{{ work_order.description }}</td>
                                        <td class="text-right">{{ parseFloat(work_order.cost).toFixed(2) }}</td>
                                        <td class="text-center">
                                            <span v-if="work_order.type === 'preventive'"
                                                  class="badge badge-success badge-rounded">
                                                Preventivo
                                            </span>
                                            <span v-else-if="work_order.type === 'corrective'"
                                                  class="badge badge-warning badge-rounded">
                                                Correctivo
                                            </span>
                                            <span v-else-if="work_order.type === 'locative'"
                                                  class="badge badge-pink badge-rounded">
                                                Locativo
                                            </span>
                                            <span v-else-if="work_order.type === 'improvement'"
                                                  class="badge badge-primary badge-rounded">
                                                Mejora
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span v-if="work_order.state === '0'"
                                                  class="badge badge-rounded badge-danger">
                                                Anulado
                                            </span>
                                            <span v-else-if="work_order.state === '1'"
                                                  class="badge badge-rounded badge-primary">
                                                en revision
                                            </span>
                                            <span v-else-if="work_order.state === '2'"
                                                  class="badge badge-rounded badge-success">
                                                aprobada
                                            </span>
                                            <span v-else-if="work_order.state === '3'"
                                                  class="badge badge-rounded badge-warning">
                                                en proceso
                                            </span>
                                            <span v-else-if="work_order.state === '4'"
                                                  class="badge badge-rounded badge-success">
                                                finalizada
                                            </span>
                                            <span v-else
                                                  class="badge badge-rounded badge-danger">
                                                Rechazado
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <template #footer>
                        <button class="btn btn-primary" @click="closeModal">
                            Cerrar
                        </button>
                    </template>
                </jet-dialog-modal>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {computed} from "vue";
import {useDarkModeStore} from "@/store/dark-mode";
import {useColorSchemeStore} from "@/store/color-scheme";
import {colors} from "@/utils/colors";
import {Head, Link} from '@inertiajs/vue3';
import DoughnutChart from "@/GlobalComponents/Charts/DoughnutChart.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {h} from 'vue';

export default {
    components: {
        JetDialogModal,
        Head,
        Link,
        DoughnutChart
    },

    props: {
        maintenances: Array,
        asset: Object
    },

    setup(){
        return {
            darkMode$: computed(() => useDarkModeStore().darkMode),
            colorScheme$: computed(() => useColorSchemeStore().colorScheme),
            chartColors$: () => [
                colors.google.blue,
                colors.google.red,
                colors.google.yellow,
                colors.google.green,
            ]
        }
    },

    data() {
        return {
            options: {
                legend: {
                    display: false,
                },
                cutoutPercentage: 75,
            },
            maintenance_data: {
                labels: this.maintenances.map(row => row.type),
                datasets: [{
                    label: "Reporte de mantenimientos",
                    data: this.maintenances.map(row => row.total),
                    backgroundColor: this.colorScheme$ ? this.chartColors$() : "",
                    hoverBackgroundColor: this.colorScheme$ ? this.chartColors$() : "",
                    borderWidth: 5,
                    borderColor: this.darkMode$
                        ? colors.darkmode[700]()
                        : colors.slate[200](),
                }]
            },
            table: {
                columns: [
                    "consecutive",
                    "type",
                    "state",
                    "closing_date",
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        type: 'TIPO',
                        state: 'ESTADO',
                        closing_date: 'FECHA CIERRE',
                        actions: ''
                    },
                    sortable: ['consecutive', 'type', 'state', 'closing_date'],
                    templates: {
                        type(h, row) {
                            if (row.type === 'preventive') {
                                return <span class="badge badge-success badge-rounded"> Preventivo </span>
                            } else if (row.type === 'corrective') {
                                return <span class="badge badge-danger badge-rounded"> Correctivo </span>
                            } else if (row.type === 'locative') {
                                return <span class="badge badge-warning badge-rounded"> Locativo </span>
                            } else {
                                return <span class="badge badge-primary badge-rounded"> Mejorativo </span>
                            }
                        },
                        state(h, row) {
                            if (row.state === '0') {
                                return <span class="badge badge-danger badge-rounded">
                                    Anulado
                                </span>
                            } else if (row.state === '1') {
                                return <span class="badge badge-primary badge-rounded">
                                    En revision
                                </span>
                            } else if (row.state === '2') {
                                return <span class="badge badge-success badge-rounded">
                                    Aprobada
                                </span>
                            } else if (row.state === '3') {
                                return <span
                                    class="badge badge-warning badge-rounded">
                                    En proceso
                                </span>
                            } else if (row.state === '4') {
                                return <span class="badge badge-success badge-rounded">
                                    Finalizada
                                </span>
                            } else {
                                return <span class="badge badge-danger badge-rounded">
                                    Rechazado
                                </span>
                            }
                        },
                        closing_date(h, row) {
                            return row.closing_date
                                ? this.$h.formatDate(row.closing_date)
                                : <span class="badge badge-danger badge-rounded"> NA </span>
                        }
                    },
                    cellClasses: {
                        type: [{class: 'text-center', condition: row => row}],
                        state: [{class: 'text-center', condition: row => row}],
                        closing_date: [{class: 'text-center', condition: row => row}],
                    }
                }
            },
            modal: {
                open: false,
                data: {}
            }
        }
    },

    methods: {
        view(row){
            this.modal = {
                open: true,
                data: row
            }
        },

        closeModal(){
            this.modal = {
                open: false,
                data: {}
            }
        },
    },

    computed: {
        width(){
            return 0
        },

        height(){
            return 250
        },
    }
}
</script>

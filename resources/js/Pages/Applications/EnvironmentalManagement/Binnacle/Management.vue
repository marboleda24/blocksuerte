<template>
    <div>
        <Head title="Gestion"/>

        <portal to="application-title">
            Gestion
        </portal>

        <div>
            <div class="grid grid-cols-2 gap-6">
                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">P0XX</h2>
                    </div>
                    <div class="p-5">
                        <v-client-table :data="p0xx_datatable" :columns="p0xx.columns" :options="p0xx.options"
                                        ref="p0xx_table" class="overflow-y-auto">
                            <template v-slot:action="{row}">
                                <div class="text-center">
                                    <button @click="p0xx_details(row.date)" class="btn btn-primary">
                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                    </button>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>

                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">HL1</h2>
                    </div>
                    <div class="p-5">
                        <v-client-table :data="hl1_datatable" :columns="hl1.columns" :options="hl1.options"
                                        ref="hl1_table" class="overflow-y-auto">
                            <template v-slot:action="{row}">
                                <div class="text-center">
                                    <button @click="hl1_details(row.date)" class="btn btn-primary">
                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                    </button>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mt-6">
                <div class="intro-y box col-span-2">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">CARGA DE OPERACION DIARIA P0XX</h2>
                    </div>
                    <div class="p-5">
                        <line-chart :width="width"
                                    :height="height"
                                    :chart-data="p0xx_chart"
                                    :chart-options="options"/>

                    </div>
                </div>

                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">CARGA DE OPERACION DIARIA HL1</h2>
                    </div>
                    <div class="p-5">
                        <line-chart :width="width"
                                    :height="height"
                                    :chart-data="hl1_chart"
                                    :chart-options="options"/>
                    </div>
                </div>

                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">ESTADO FILTROS HL1</h2>
                    </div>
                    <div class="p-5">
                        <line-chart :width="width"
                                    :height="height"
                                    :chart-data="hl1_filters_chart"
                                    :chart-options="options"/>
                    </div>
                </div>
            </div>


            <jet-dialog-modal :show="p0xx_infoModal_isOpen">
                <template #title v-if="p0xx_infoModal">
                    Bitácora {{ p0xx_infoModal.date }}
                </template>

                <template #content v-if="p0xx_infoModal">
                    <div v-if="p0xx_infoModal.workshift_1.length > 0">
                        <div
                            class="grid grid-cols-1 gap-2 mx-2 p-2 text-base divide-x text-center border rounded-lg">
                            <div class="flex flex-col">
                                <strong>TURNO </strong> 06:00 AM - 02:00 PM
                            </div>
                        </div>

                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="table table--sm">
                                    <thead>
                                    <tr>
                                        <th class="border-b-2 dark:border-dark-5"> MAQUINA</th>
                                        <th class="border-b-2 dark:border-dark-5"> TB</th>
                                        <th class="border-b-2 dark:border-dark-5"> RZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> VZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> Z</th>
                                        <th class="border-b-2 dark:border-dark-5"> TOTAL</th>
                                        <th class="border-b-2 dark:border-dark-5"> %CO</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in p0xx_infoModal.workshift_1" v-bind:key="row.id">
                                        <td class="border-b dark:border-dark-5">{{ row.machine.reference }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.tb }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.rz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.vz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.z }}</td>
                                        <td class="border-b dark:border-dark-5">{{
                                                row.tb + row.rz + row.vz + row.z
                                            }}
                                        </td>
                                        <td class="border-b dark:border-dark-5">{{
                                                parseFloat((((row.tb * 25) / 17) + ((row.rz * 50) / 8.3) + ((row.vz * 15) / 14) + ((row.z * 10) / 3)).toFixed(2))
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div v-if="p0xx_infoModal.workshift_2.length > 0">
                        <div
                            class="grid grid-cols-1 gap-2 mt-10 mx-2 p-2 text-base divide-x text-center border rounded-lg">
                            <div class="flex flex-col">
                                <strong>TURNO </strong> 02:00 PM - 10:00 PM
                            </div>
                        </div>

                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border mb-10">
                                <table class="table table--sm">
                                    <thead>
                                    <tr>
                                        <th class="border-b-2 dark:border-dark-5"> MAQUINA</th>
                                        <th class="border-b-2 dark:border-dark-5"> TB</th>
                                        <th class="border-b-2 dark:border-dark-5"> RZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> VZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> Z</th>
                                        <th class="border-b-2 dark:border-dark-5"> TOTAL</th>
                                        <th class="border-b-2 dark:border-dark-5"> %CO</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in p0xx_infoModal.workshift_2" v-bind:key="row.id">
                                        <td class="border-b dark:border-dark-5">{{ row.machine.reference }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.tb }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.rz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.vz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.z }}</td>
                                        <td class="border-b dark:border-dark-5">{{
                                                row.tb + row.rz + row.vz + row.z
                                            }}
                                        </td>
                                        <td class="border-b dark:border-dark-5">{{
                                                parseFloat((((row.tb * 25) / 17) + ((row.rz * 50) / 8.3) + ((row.vz * 15) / 14) + ((row.z * 10) / 3)).toFixed(2))
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-if="p0xx_infoModal.workshift_3.length > 0">
                        <div
                            class="grid grid-cols-1 gap-2 mt-10 mx-2 p-2 text-base divide-x text-center border rounded-lg">
                            <div class="flex flex-col">
                                <strong>TURNO </strong> 10:00 PM - 06:00 AM
                            </div>
                        </div>

                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border mb-10">
                                <table class="table table--sm">
                                    <thead>
                                    <tr>
                                        <th class="border-b-2 dark:border-dark-5"> MAQUINA</th>
                                        <th class="border-b-2 dark:border-dark-5"> TB</th>
                                        <th class="border-b-2 dark:border-dark-5"> RZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> VZ</th>
                                        <th class="border-b-2 dark:border-dark-5"> Z</th>
                                        <th class="border-b-2 dark:border-dark-5"> TOTAL</th>
                                        <th class="border-b-2 dark:border-dark-5"> %CO</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in p0xx_infoModal.workshift_3" v-bind:key="row.id">
                                        <td class="border-b dark:border-dark-5">{{ row.machine.reference }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.tb }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.rz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.vz }}</td>
                                        <td class="border-b dark:border-dark-5">{{ row.z }}</td>
                                        <td class="border-b dark:border-dark-5">{{
                                                row.tb + row.rz + row.vz + row.z
                                            }}
                                        </td>
                                        <td class="border-b dark:border-dark-5">{{
                                                parseFloat((((row.tb * 25) / 17) + ((row.rz * 50) / 8.3) + ((row.vz * 15) / 14) + ((row.z * 10) / 3)).toFixed(2))
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="hl1_infoModal_isOpen">
                <template #title v-if="hl1_infoModal">
                    Bitacora {{ hl1_infoModal.date }}
                </template>

                <template #content v-if="hl1_infoModal">
                    <div v-if="hl1_infoModal">
                        <div
                            class="grid grid-cols-3 gap-2 mx-2 p-2 text-base divide-x text-center border rounded-lg">
                            <div class="flex flex-col">
                                <strong>MAQUINA </strong> {{ hl1_infoModal.machine.reference }}
                            </div>
                            <div class="flex flex-col">
                                <strong>TOTAL LINGOTES </strong> {{ hl1_infoModal.ingots }}
                            </div>

                            <div class="flex flex-col">
                                <strong>%CO </strong> {{ parseFloat(((hl1_infoModal.ingots * 100) / 202)).toFixed(2) }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>


<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'
import LineChart from "@/GlobalComponents/Charts/LineChart.vue";


export default {
    components: {
        JetDialogModal,
        Head,
        LineChart
    },

    props: {
        hl1_datatable: Array,
        p0xx_datatable: Array,
        operational_load_p0xx: Object,
        p0xx_datasets: Array,
        p0xx_labels: Array,
        hl1_datasets: Array,
        hl1_labels: Array,
        filters_hl1_datasets: Array,
        filters_hl1_labels: Array,
        width: {
            type: Number,
            default: 800
        },
        height: {
            type: Number,
            default: 400
        },
    },

    data() {
        return {
            p0xx: {
                columns: [
                    'date',
                    'action'
                ],
                options: {
                    headings: {
                        date: 'FECHA',
                        action: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['date'],
                }
            },

            hl1: {
                columns: [
                    'date',
                    'action'
                ],
                options: {
                    headings: {
                        date: 'FECHA',
                        action: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    responsive: false,
                    sortable: ['date'],
                }
            },

            p0xx_chart: {
                labels: this.p0xx_labels,
                datasets: this.p0xx_datasets
            },

            hl1_chart: {
                labels: this.hl1_labels,
                datasets: this.hl1_datasets
            },

            hl1_filters_chart: {
                labels: this.filters_hl1_labels,
                datasets: this.filters_hl1_datasets
            },

            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            unitStepSize: 1, // I'm using 3 hour intervals here
                            tooltipFormat: 'DD-MMM-YYYY',
                            displayFormats: {
                                'day': 'MMM DD'
                            }
                        },
                        ticks: {
                            major: {
                                enabled: true, // <-- This is the key line
                                fontStyle: 'bold', //You can also style these values differently
                                fontSize: 12 //You can also style these values differently
                            },
                        },
                        gridLines: {
                            display: false
                        },

                    },
                    yAxes: {
                        ticks: {
                            fontSize: "12",
                            fontColor: "#777777",
                        },
                        gridLines: {
                            color: "#D8D8D8",
                            zeroLineColor: "#D8D8D8",
                            borderDash: [2, 2],
                            zeroLineBorderDash: [2, 2],
                            drawBorder: false
                        }
                    },
                },
                legend: {
                    display: false,
                },
                responsive: true,
                maintainAspectRatio: false
            },

            p0xx_infoModal: null,
            p0xx_infoModal_isOpen: false,
            hl1_infoModal: null,
            hl1_infoModal_isOpen: false,
        }
    },


    methods: {
        closeModal: function () {
            this.p0xx_infoModal_isOpen = false;
            this.hl1_infoModal_isOpen = false;
            this.p0xx_infoModal = null;
            this.hl1_infoModal = null;
        },

        p0xx_openModal: function () {
            this.p0xx_infoModal_isOpen = true;
        },

        hl1_openModal: function () {
            this.hl1_infoModal_isOpen = true;
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

        p0xx_details(date) {
            this.loading(true);
            axios.get(route('binnacle-omff.registry.p0xx.details'), {
                params: {
                    date: date,
                }
            }).then(resp => {
                this.p0xx_infoModal = resp.data;
                this.loading(false);
                this.p0xx_openModal();

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


        hl1_details(date) {
            this.loading(true);
            axios.get(route('binnacle-omff.registry.hl1.details'), {
                params: {
                    date: date,
                }
            }).then(resp => {
                this.hl1_infoModal = resp.data;
                this.loading(false);
                this.hl1_openModal();

            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        }
    }
}
</script>

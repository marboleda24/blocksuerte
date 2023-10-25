<template>
    <div>
        <Head title="Nomina Electronica"/>

        <portal to="application-title">
            Nomina Electronica
        </portal>

        <div class="intro-y box p-5">
            <div class="grid grid-cols-5 gap-6">
                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Año
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.year.$error }"
                            v-model.number="form.year"
                            @change="getMonths(form.year)"
                            :disabled="table.data.length > 0">
                        <option value="" disabled selected>Seleccione...</option>
                        <option v-for="year in years" :value="year.ano">{{ year.ano }}</option>
                    </select>

                    <template v-if="v$.form.year.$error">
                        <div v-if="!v$.form.year.required" class="text-theme-6 mt-2">
                            Campo obligatorio
                        </div>
                    </template>
                </div>

                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Mes
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.month.$error }"
                            v-model.number="form.month"
                            :disabled="table.data.length > 0">
                        <option value="" disabled selected>Seleccione...</option>
                        <option v-for="month in months" :value="month.month">{{ month.name }}</option>
                    </select>

                    <template v-if="v$.form.month.$error">
                        <div v-if="!v$.form.month.required" class="text-theme-6 mt-2">
                            Campo obligatorio
                        </div>
                    </template>
                </div>

                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Periodo Inicial
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input type="number"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.start_period.$error }"
                           v-model="form.start_period"
                           :disabled="table.data.length > 0">

                    <template v-if="v$.form.start_period.$error">
                        <div v-if="!v$.form.start_period.required" class="text-theme-6 mt-2">
                            Campo obligatorio
                        </div>
                    </template>
                </div>


                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Periodo Final
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input type="number"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.end_period.$error }"
                           v-model="form.end_period"
                           :disabled="table.data.length > 0">

                    <template v-if="v$.form.end_period.$error">
                        <div v-if="!v$.form.end_period.required" class="text-theme-6 mt-2">
                            Campo obligatorio
                        </div>
                    </template>
                </div>

                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Tipo de operacion
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select" :class="{ 'border-danger': v$.form.type_operation.$error }"
                            v-model="form.type_operation" disabled>
                        <option value="">Seleccione...</option>
                        <option value="payroll">Nomina</option>
                        <option value="adjust">Ajuste</option>
                        <option value="destroy">Eliminación</option>
                    </select>

                    <template v-if="v$.form.type_operation.$error">
                        <div v-if="!v$.form.type_operation.required" class="text-theme-6 mt-2">
                            Campo obligatorio
                        </div>
                    </template>
                </div>

                <div class="mt-2">
                    <button class="btn btn-primary w-full h-full"
                            v-if="table.data.length === 0"
                            @click="getEmployees(form.year, form.month, form.start_period, form.end_period)">
                        <font-awesome-icon icon="user-friends" size="lg" class="mr-2"/>
                        Obtener Empleados
                    </button>

                    <button class="btn btn-warning w-full h-full"
                            v-if="table.data.length > 0"
                            @click="clear">
                        Generar nueva búsqueda
                    </button>
                </div>
            </div>

            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table_payroll">
            </v-client-table>


            <div class="mt-5 mb-10 text-right">
                <button class="btn btn-primary"
                        @click="generatePayroll(form)">
                    Generar Nomina Electronica
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    props: {
        years: Array
    },

    validations: {
        form: {
            year: {required},
            month: {required},
            start_period: {required},
            end_period: {required},
            type_operation: {required},
        }
    },

    data() {
        return {
            table: {
                data: [],
                columns: [
                    'document',
                    'employee',
                    'payments',
                    'deductions',
                    'total'
                ],
                options: {
                    headings: {
                        document: 'DOCUMENTO DE IDENTIDAD',
                        employee: 'EMPLEADO',
                        payments: 'PAGOS',
                        deductions: 'DEDUCCIONES',
                        total: 'NETO A PAGAR'
                    },
                    uniqueKey: 'IDENTIFICACION',
                    sortable: ['document', 'employee', 'total'],
                    selectable: {
                        mode: 'multiple', // or 'multiple'
                        selectAllMode: 'all', // or 'page',
                        programmatic: false,
                        only: function (row) {
                            return !row.current_document
                                || row.current_document && row.current_document.status === 'pending'
                                || row.current_document && row.current_document.status === 'failed'
                        },
                    },
                    templates: {
                        document(h, row) {
                            return row.IDENTIFICACION
                        },
                        employee(h, row) {
                            return row.EMPLEADO
                        },
                        payments(h, row) {
                            return this.$h.formatCurrency(parseFloat(row.PAGO))
                        },
                        deductions(h, row) {
                            return this.$h.formatCurrency(parseFloat(row.DEDUCCIONES))
                        },
                        total(h, row) {
                            return this.$h.formatCurrency(parseFloat(row.NETO))
                        }
                    },
                    customSorting: {
                        document(ascending) {
                            return function (a, b) {
                                const lastA = a.IDENTIFICACION.toLowerCase();
                                const lastB = b.IDENTIFICACION.toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },

                        total(ascending) {
                            return function (a, b) {
                                const lastA = parseInt(a.NETO);
                                const lastB = parseInt(b.NETO);

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        }
                    },
                    cellClasses: {
                        payments: [{class: 'text-right', condition: row => row}],
                        deductions: [{class: 'text-right', condition: row => row}],
                        total: [{class: 'text-right', condition: row => row}],
                    },
                    rowClassCallback(row) {
                        if (row.current_document && row.current_document.status === 'success') {
                            return 'bg-green-200 dark:bg-green-800'
                        }else if (row.current_document && row.current_document.status === 'pending'){
                            return 'bg-yellow-200 dark:bg-yellow-800'
                        }else if (row.current_document && row.current_document.status === 'failed'){
                            return 'bg-red-200 dark:bg-red-800'
                        }
                    }
                }
            },

            form: {
                year: '',
                month: '',
                start_period: 1,
                end_period: 20,
                type_operation: 'payroll',
                employees: []
            },
            months: [],
        }
    },

    methods: {
        clear() {
            this.$refs.table_payroll.$refs.table.resetSelectedRows();

            this.form = {
                year: '',
                month: '',
                start_period: 1,
                end_period: 20,
                type_operation: 'payroll',
                employees: []
            }
            this.table.data = []
        },

        getMonths(year) {
            this.months = [];
            this.periods = [];

            axios.get(route('human-resource.proof-payment.get-months'), {
                params: {
                    year: year
                }
            }).then(resp => {
                this.months = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        getEmployees(year, month, start_period, end_period) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Obteniendo Empleados...',
                    text: 'Estamos obteniendo los empleados, un momento por favor.',
                });
                this.$refs.table_payroll.$refs.table.resetSelectedRows();
                this.table.data = []
                axios.get(route('payroll.get-employees'), {
                    params: {
                        year: year,
                        month: month,
                        start_period: start_period,
                        end_period: end_period
                    }
                }).then(resp => {
                    this.table.data = resp.data
                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err.data);
                })
            }
        },

        freshData(year, month, start_period, end_period) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Actualizando empleados...',
                text: 'Estamos actualizando los empleados, un momento por favor.',
            });

            this.$refs.table_payroll.$refs.table.resetSelectedRows();
            this.table.data = []
            axios.get(route('payroll.get-employees'), {
                params: {
                    year: year,
                    month: month,
                    start_period: start_period,
                    end_period: end_period
                }
            }).then(resp => {
                this.table.data = resp.data
                this.$swal.close();
                this.form.employees = []
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        generatePayroll(data) {
            this.v$.$touch();
            if (this.v$.$invalid || this.selected.length ===  0) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Enviando Documentos…',
                    text: 'Estamos generando la nomina electronica, un momento por favor.',
                });

                data.employees = this.selected
                axios.post(route('payroll.send-api'), data).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: 'Documentos en lista de envio',
                        text: 'Los documentos fueron puestos en la lista de envió, por favor espere al menos 10 minutos ' +
                            'para enviar nuevamente el mismo documento, recuerde que dependiendo del volumen de envíos ' +
                            'diarios esto puede tomar más tiempo de lo habitual, recuerde actualizar esta pagina para ver los cambios recientes',
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar'
                    });
                    this.$refs.table_payroll.$refs.table.resetSelectedRows();

                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err.data);
                })
            }
        },

        validateStatus(employee_document, documents) {
            const match = documents.find(row => {
                return parseInt(row.year) === this.form.year
                    && parseInt(row.month) === this.form.month
                    && row.employee_id === employee_document
            });

            return !!match
        }
    },

    computed: {
        selected() {
            return this.$refs.table_payroll?.$refs.table.selectedRows
        }
    },

    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
    },


}
</script>


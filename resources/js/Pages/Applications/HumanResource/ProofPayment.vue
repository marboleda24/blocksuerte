<template>
    <div>
        <Head title="Comprobantes de Nomina"/>

        <portal to="application-title">
            Comprobantes de Nomina
        </portal>

        <div class="grid grid-cols-3 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-6">
            <div class="">
                <label class="flex flex-col sm:flex-row">
                    Año
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                </label>

                <select class="form-select" required autofocus :class="{ 'border-danger': v$.form.year.$error }"
                        v-model="form.year" @change="getMonths(form.year)">
                    <option value="" disabled selected>Seleccione...</option>
                    <option v-for="year in years" :value="year.ano">{{ year.ano }}</option>
                </select>

                <template v-if="v$.form.year.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.year.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="">
                <label class="flex flex-col sm:flex-row">
                    Mes
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                </label>

                <select class="form-select" :class="{ 'border-danger': v$.form.month.$error }" v-model="form.month"
                        @change="getPeriods(form.year, form.month)">
                    <option value="" disabled selected>Seleccione...</option>
                    <option v-for="month in months" :value="month.month">{{ month.name }}</option>
                </select>

                <template v-if="v$.form.month.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.month.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="">
                <label class="flex flex-col sm:flex-row">
                    Periodo
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                </label>

                <select class="form-select" :class="{ 'border-danger': v$.form.period.$error }" v-model="form.period">
                    <option value="" disabled selected>Seleccione...</option>
                    <option v-for="period in periods" :value="period.period">
                        {{period.period + ' - ' + period.notes }}
                    </option>
                </select>

                <template v-if="v$.form.period.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.period.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="">
                <label class="flex flex-col sm:flex-row">
                    Tipo
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                </label>

                <select class="form-select" :class="{ 'border-danger': v$.form.period.$error }" v-model="form.type">
                    <option value="" disabled selected>Seleccione...</option>
                    <option value="massive">Masivo</option>
                    <option value="unique">Empleado</option>
                </select>

                <template v-if="v$.form.type.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.type.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="" v-if="form.type === 'unique'">
                <label class="flex flex-col sm:flex-row">
                    Empleado
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio*
                    </span>
                </label>

                <autocomplete
                    url="/human-resource/search-employee"
                    show-field="nombres"
                    @selected-value="employeeInfo"
                />

                <template v-if="v$.form.employee_nit.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.employee_nit.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="flex flex-row">
                <button class="btn btn-primary w-full" @click="sendMail(form)">
                    <font-awesome-icon :icon="['far', 'envelope']" class="mr-2"/>
                    {{ form.type === 'unique' ? 'Enviar Comprobante' : 'Enviar Comprobantes' }}
                </button>

                <template v-if="form.type === 'unique'">
                    <button class="btn btn-secondary w-full ml-2" @click="downloadFile(form)">
                        <font-awesome-icon :icon="['fas', 'download']" class="mr-2"/>
                        Descargar
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, requiredIf} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Head
    },

    props: {
        years: Array
    },

    validations: {
        form: {
            year: {required},
            month: {required},
            period: {required},
            type: {required},
            employee_nit: {
                required: requiredIf(function () {
                    return this.form.type === 'unique'
                })
            }
        }
    },

    data() {
        return {
            form: {
                year: '',
                month: '',
                period: '',
                type: '',
                employee_nit: '',
                period_str: ''
            },
            months: [],
            periods: []
        }
    },

    methods: {
        sendMail(data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {

                if (data.type === 'global') {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Enviado comprobantes...',
                        text: 'Este proceso puede tardar unos segundos...',
                    });

                    axios.post(route('human-resource.proof-payment.send-mail'), data).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Comprobante de pago enviado correctamente',
                            confirmButtonText: 'Cerrar'
                        });
                        console.log(resp.data)
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Cerrar'
                        });
                        console.log(err);
                    })
                } else {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Enviado comprobante...',
                        text: 'Este proceso puede tardar unos segundos...',
                    });

                    axios.post(route('human-resource.proof-payment.send-mail'), data).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Comprobante de pago enviado correctamente',
                            confirmButtonText: 'Cerrar'
                        });
                        console.log(resp.data)

                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Cerrar'
                        });
                        console.log(err);
                    })
                }
            }
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

        getPeriods(year, month) {
            this.periods = [];

            axios.get(route('human-resource.proof-payment.get-periods'), {
                params: {
                    year: year,
                    month: month
                }
            }).then(resp => {
                this.periods = resp.data.map(function (x) {
                    return {
                        period: x.periodo,
                        notes: x.notas,
                        date: `del ${dayjs(new Date(x.fecha_inicial)).format('DD MMMM YYYY')} al ${dayjs(new Date(x.fecha_final)).format('DD MMMM YYYY')}`
                    }
                })
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
        employeeInfo(obj) {
            this.form.employee_nit = obj.nit
        },

        downloadFile(data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Descargando comprobante...',
                    text: 'Estamos generando el archivo solicitado, un momento por favor.',
                });

                axios.post(route('human-resource.proof-payment.download'), data, {
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');
                    link.href = url;

                    const filename = `${this.form.employee_nit}_${this.form.year}_${this.form.month}_${this.form.period}.pdf`;

                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.error(err);
                })
            }
        }
    },

    computed: {
        period_str: function () {
            return this.periods ? this.periods.find(element => element.period === this.form.period) : ''
        }
    },

    watch: {
        period_str() {
            this.form.period_str = this.period_str.date
        }
    }
}
</script>


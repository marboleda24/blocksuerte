<template>
    <div>
        <Head title="Solicitudes de Empleados"/>

        <Link :href="route('pre-login')" class="btn btn-secondary ml-4 my-4">
            <font-awesome-icon icon="arrow-left" class="mr-2"/>
            Atrás
        </Link>

        <template v-if="app_name === null">
            <div class="pos intro-y grid grid-cols-12 gap-5 mt-5 mx-2">
                <a href="javascript:void(0)"
                   class="intro-x cursor-pointer box relative flex items-center p-5 zoom-in col-span-12 sm:col-span-6 xxl:col-span-6"
                   @click="view('working_letter')">
                    <div class="flex-none image-fit mr-2">
                        <font-awesome-icon icon="link" size="2x"/>
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="font-medium text-base flex items-center">CARTAS LABORALES</div>
                        <div class="w-full truncate text-gray-600">Solicitar carta laboral</div>
                    </div>
                </a>

                <a href="javascript:void(0)"
                   class="intro-x cursor-pointer box relative flex items-center p-5 zoom-in col-span-12 sm:col-span-6 xxl:col-span-6"
                   @click="view('vacation_request')">
                    <div class="flex-none image-fit mr-2">
                        <font-awesome-icon icon="link" size="2x"/>
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="font-medium text-base flex items-center">Solicitud de Vacaciones</div>
                        <div class="w-full truncate text-gray-600">Solicitar periodo de vacaciones</div>
                    </div>
                </a>

            </div>
        </template>


        <template v-if="app_name === 'working_letter'">
            <div class="intro-y box m-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Solicitud de carta laboral</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="mt-2">
                            <label>Nombre empleado</label>
                            <autocomplete
                                url="/employee-requests/employee-info"
                                show-field="nombres"
                                @selected-value="info_employee"
                            />

                            <template v-if="v$.form.employee_document.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.employee_document.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2">
                            <label>Dirigido a </label>
                            <input type="text" class="form-control" v-model="form.addressed_to">
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary w-full h-full" @click="save(form)">
                                Solicitar
                            </button>
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary w-full h-full" @click="app_name = null">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="app_name === 'vacation_request'">
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Solicitud de vacaciones</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="mt-2">
                            <label>Nombre empleado</label>
                            <autocomplete
                                url="/employee-requests/employee-info"
                                show-field="nombres"
                                @selected-value="info_employee_request_vacation"
                            />

                            <template v-if="v$.form_vacation.employee_document.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form_vacation.employee_document.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2">
                            <label>Nombre jefe de area</label>
                            <autocomplete
                                url="/employee-requests/boss-info"
                                show-field="nombres"
                                @selected-value="info_boss_request_vacation"
                            />

                            <template v-if="v$.form_vacation.boss_document.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form_vacation.boss_document.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2">
                            <label> Fechas </label>

                            <Litepicker
                                v-model="form_vacation.date"
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
                                    minDate: current_date
                                }"
                                class="form-control"
                            />
                        </div>

                        <div class="mt-2">
                            <label> justificación </label>
                            <input type="text" class="form-control" placeholder="justificación"
                                   v-model="form_vacation.justify">
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary w-full h-full" @click="save_vacation_request(form_vacation)">
                                Solicitar
                            </button>
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary w-full h-full" @click="app_name = null">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="jsx">
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head, Link} from '@inertiajs/vue3'
import Empty from '@/Layouts/Empty.vue'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import dom from "@left4code/tw-starter/dist/js/dom";

import dayjs from "dayjs";


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Empty,
        Head,
        Link
    },

    layout: Empty,

    validations() {
        return {
            form: {
                employee_document: {
                    required
                },
            },
            form_vacation: {
                employee_document: {
                    required
                },
                boss_document: {
                    required
                }
            }
        }

    },

    data() {
        return {
            form: {
                employee_document: null,
                addressed_to: null,
            },
            form_vacation: {
                employee_document: null,
                boss_document: null,
                vacation_date: null,
                justify: null,
            },
            app_name: null,
        }
    },

    methods: {

        resetForm() {
            this.form_vacation = {
                employee_document: '',
                boss_document: '',
                vacation_date: '',
                justify: ''
            }
        },

        resetForm2() {
            this.form = {
                employee_document: '',
                addressed_to: ''
            }
        },

        view(app_name) {
            this.app_name = app_name
        },

        info_boss(obj) {
            this.form.employee_document = obj.nit;
        },

        info_employee(obj) {
            this.form.employee_document = obj.nit;
        },

        info_employee_request_vacation(obj) {
            this.form_vacation.employee_document = obj.nit;
        },

        info_boss_request_vacation(obj) {
            this.form_vacation.boss_document = obj.nit;
        },

        save(data) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                axios.post(route('employee-requests.save-request'), data).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Su solicitud ha sido creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    this.resetForm2();
                    this.app_name = null
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.$swal({
                            icon: 'error',
                            title: 'error',
                            text: error.response.data,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 6000,
                        });
                        this.resetForm2();
                    }
                })
            }
        },

        save_vacation_request(data) {
            this.v$.form_vacation.$touch();
            if (!this.v$.form_vacation.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Aceptando Solicitud...',
                    text: 'Este proceso puede tardar unos segundos.',
                });
                axios.post(route('employee-requests.save-vacation-request'), data).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Su solicitud ha sido creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    this.resetForm();
                    this.app_name = null

                }).catch(error => {
                    if (error.response.status === 422) {
                        this.$swal({
                            icon: 'error',
                            title: 'error',
                            text: error.response.data,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 6000,
                        });
                    }
                })
            }
        }
    },
    mounted() {
        dom("body").removeClass("login")

    },
    computed: {
        current_date() {
            return dayjs()
        }
    },
}
</script>



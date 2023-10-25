<template>
    <div>
        <Head title=" Nueva Solicitud"/>

        <portal to="application-title">
            Nueva Solicitud
        </portal>

        <portal to="actions">
            <Link :href="route('maintenance.my-requests')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Mis solicitudes
            </Link>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="p-10">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="mt-2">
                            <label class="flex flex-col sm:flex-row">
                                Fecha
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <Litepicker
                                v-model="form.date"
                                :options="{
                                    autoApply: true,
                                    singleMode: true,
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
                                }"
                                class="form-control"
                                ref="datepicker"
                            />

                            <template v-if="v$.form.date.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.date.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2">
                            <label class="flex flex-col sm:flex-row">
                                Activo afectado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select" v-model.trim="v$.form.asset.$model"
                                    :class="{ 'border-danger': v$.form.asset.$error }">
                                <option value="" selected disabled>Seleccione...</option>
                                <option :value="asset.id" v-for="asset in assets">{{ asset.name }}</option>
                            </select>

                            <template v-if="v$.form.asset.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.asset.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2">
                            <label class="flex flex-col sm:flex-row">
                                Tipo de mantenimiento
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select" v-model.trim="v$.form.type.$model"
                                    :class="{ 'border-danger': v$.form.type.$error }">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="preventive">Preventivo</option>
                                <option value="corrective">Correctivo</option>
                                <option value="locative">Locativo</option>
                                <option value="improvement">Mejorativo</option>
                            </select>

                            <template v-if="v$.form.type.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.type.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mt-2 col-span-3">
                            <label class="flex flex-col sm:flex-row">
                                Descripción del problema
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5" class="form-control" v-model.uppercase="v$.form.description.$model"
                                      :class="{ 'border-danger': v$.form.description.$error }"/>

                            <template v-if="v$.form.description.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.description.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                    <div class="row justify-content-center text-center pt-6">
                        <button @click.prevent="save(form)" :disabled="form.processing" type="submit"
                                class="btn btn-primary uppercase">
                            <font-awesome-icon icon="save" class="mr-2"/>
                            Crear solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Link
    },

    props: {
        assets: Array
    },

    validations() {
        return {
            form: {
                date: {required},
                asset: {required},
                type: {required},
                description: {required}
            }
        }
    },

    data() {
        return {
            form: {
                date: '',
                asset: '',
                type: '',
                description: '',
                processing: false
            }
        }
    },

    methods: {
        save(data){
            this.v$.$touch();
            if (!this.v$.$invalid) {
                this.form.processing = true;
                axios.post(route('maintenance.store'), data).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Solicitud generada con éxito",
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar'
                    });
                    this.resetForm();
                    this.v$.form.$reset()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud. Mensaje de error: '+ err.data,
                        confirmButtonText: 'Aceptar',
                    });
                    this.form.processing = false;
                })
            }
        },

        resetForm(){
            this.form = {
                asset: '',
                type: '',
                details: '',
                processing: false
            }
        }
    },
}
</script>


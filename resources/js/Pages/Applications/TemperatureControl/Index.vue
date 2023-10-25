<template>
    <div>
        <Head title="Registro Entrada de Personal"/>

        <portal to="application-title">
            Registro Entrada de Personal
        </portal>


        <div class="grid grid-cols-3 gap-5 intro-y box p-5" @keypress.enter="save(form)">
            <div class="mt-2">
                <label class="font-medium">
                    Empleado:
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                         Por favor, seleciona una opcion.
                    </span>
                </label>
                <autocomplete
                    url="/employee-requests/employee-info"
                    show-field="nombres"
                    @selected-value="selectEmployee"
                    placeholder="Nombre empleado">
                </autocomplete>


                <template v-if="v$.form.employee_document.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.employee_document.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="mt-2">
                <label class="font-medium">
                    Temperatura:
                </label>
                <input type="text" class="form-control" v-model="form.temperature">

                <template v-if="v$.form.temperature.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.form.temperature.$errors" :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <div class="mt-2">
                <label class="font-medium">
                    Hora de ingreso:
                </label>
                <div class="flex flex-row">
                    <input type="text" class="form-control" v-model="form.time_of_entry">
                    <div class="flex -mr-px">
                        <span
                            class="flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border border-l-0 border-grey-light px-3 whitespace-no-wrap text-grey-dark text-sm">
                             <button class="btn btn-secondary btn-sm mx-1" @click="beforeDestroy(interval)">
                                <font-awesome-icon icon="hand-paper"/>
                             </button>

                            <button class="btn btn-secondary btn-sm mx-1" @click="created">
                                <font-awesome-icon icon="arrow-circle-right"/>
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-2 col-span-3">
                <table class="table table-bordered mt-2">
                    <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="">
                            REGISTRO DE PERSONAL QUE INGRESA
                        </th>
                        <th class="text-center">NO/SI</th>
                    </tr>
                    <tr>
                        <th class="">¿Fiebre?</th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.fever" class="form-check-input">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="">¿Tos seca?</th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.cough" class="form-check-input">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="">¿Dolor de garganta?</th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.throat_pain" class="form-check-input ">
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <th class="">¿Dificultad para respirar?</th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.respiratory_distress" class="form-check-input">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="">¿Pérdida del gusto y/o del olfato?</th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.loss_of_taste" class="form-check-input">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class=""> ¿Ha tenido Usted contacto durante los últimos 14 días
                            con alguna persona
                            a quien le sospechen o le haya diagnosticado coronavirus?
                        </th>
                        <td class="">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" v-model="form.contact_infected_person" class="form-check-input">
                            </div>
                        </td>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class=" col-span-3 p-2 mt-2">
                <label class="font-medium">
                    Notas/Observaciones:
                </label>
                <textarea class="form-control" cols="5" rows="5" v-model="form.observations"></textarea>
            </div>
        </div>
        <div class=" justify-center text-center mt-5">
            <button class=" btn btn-primary" @click="save(form)">
                Registrar ingreso
            </button>
        </div>
    </div>
</template>
<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue"
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength} from '@/utils/i18n-validators'


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Head
    },

    validations() {
        return {
            form: {
                temperature: {
                    required,
                    minLength: minLength(2)
                },
                employee_document: {
                    required
                },
            }
        }
    },

    data() {
        return {
            form: {
                employee_document: null,
                temperature: null,
                fever: false,
                cough: false,
                throat_pain: false,
                respiratory_distress: false,
                loss_of_taste: false,
                contact_infected_person: false,
                observations: null,
                time_of_entry: null
            },
            interval: null,

        }
    },

    methods: {
        resetForm() {
            this.form = {
                employee_document: null,
                temperature: null,
                fever: false,
                cough: false,
                throat_pain: false,
                respiratory_distress: false,
                loss_of_taste: false,
                contact_infected_person: false,
                observations: null,
                time_of_entry: null
            }
        },

        selectEmployee(obj) {
            this.form.employee_document = obj.nit;
        },

        save(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                axios.post(route('temperature-control.save'), data).then(resp => {
                    this.$swal({
                        title: '¡Registro Guardado!',
                        text: "Guardado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                    this.$inertia.visit(route('temperature-control.index'));
                    this.resetForm();
                    console.log(resp.data);
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error guardando la información.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(error.data);
                })
            }
        },

        beforeDestroy() {
            clearInterval(this.interval)
        },

        created() {
            this.interval = setInterval(() => {
                this.form.time_of_entry = Intl.DateTimeFormat(navigator.language, {
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric'
                }).format()
            }, 1000)
        },
    },

    created() {
        this.interval = setInterval(() => {
            this.form.time_of_entry = Intl.DateTimeFormat(navigator.language, {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric'
            }).format()
        }, 1000)
    },


}
</script>

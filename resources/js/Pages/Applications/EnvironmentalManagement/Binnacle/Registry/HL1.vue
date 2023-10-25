<template>
    <div>
        <Head title="Registro HL1"/>

        <portal to="application-title">
            Registro HL1
        </portal>

        <div class="intro-y box p-5">
            <div class="grid grid-cols-4 gap-3">
                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Maquina
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                    </label>

                    <select v-model.trim="v$.form.machine_id.$model" class="form-select"
                            :class="{ 'border-danger': v$.form.machine_id.$error }" placeholder="Turno">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option v-for="machine in machines" v-bind:key="machine.id" :value="machine.id">
                            {{ machine.reference }}
                        </option>
                    </select>

                    <template v-if="v$.form.machine_id.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.machine_id.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>


                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Fecha
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                    </label>

                    <input v-model.trim="v$.form.date.$model" type="date" class="form-control"
                           :class="{ 'border-danger': v$.form.date.$error }" placeholder="Fecha"/>

                    <template v-if="v$.form.date.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.date.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>


                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Hora inicio
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input v-model.trim="v$.form.start_time.$model" type="time" class="form-control"
                           :class="{ 'border-danger': v$.form.start_time.$error }" placeholder="Hora inico"/>

                    <template v-if="v$.form.start_time.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.start_time.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Hora fin
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input v-model.trim="v$.form.end_time.$model" type="time" class="form-control"
                           :class="{ 'border-danger': v$.form.end_time.$error }" placeholder="Hora fin"/>

                    <template v-if="v$.form.end_time.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.end_time.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>


                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Operario
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <select v-model.trim="v$.form.operator_id.$model" class="form-select"
                            :class="{ 'border-danger': v$.form.operator_id.$error }" placeholder="Turno">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option v-for="operator in operators" v-bind:key="operator.id" :value="operator.id">
                            {{ operator.name }}
                        </option>
                    </select>

                    <template v-if="v$.form.operator_id.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.operator_id.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Lingotes
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input v-model.trim="v$.form.ingots.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.ingots.$error }" placeholder="Tapa Boton"/>

                    <template v-if="v$.form.ingots.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.ingots.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Presión de filtros
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>

                    <input v-model.trim="v$.form.filter_pressure.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.filter_pressure.$error }" placeholder="Tapa Boton"/>

                    <template v-if="v$.form.filter_pressure.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.filter_pressure.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="col-span-4">
                    <label class="flex flex-col sm:flex-row">
                        Observaciones
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Opcional
                        </span>
                    </label>
                    <textarea v-model.trim="form.observations" class="form-control" cols="30" rows="2"
                              placeholder="Observaciones" style="resize:none"></textarea>
                </div>
            </div>
        </div>
        <div class="row justify-content-left">
            <button @click.prevent="save(form)" :disabled="form.processing" type="submit"
                    class="btn btn-primary uppercase mt-5">
                <font-awesome-icon icon="save" class="mr-2"/>
                Guardar Registro
            </button>
        </div>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {Head} from '@inertiajs/vue3'
import {required, minValue, numeric} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    props: {
        machines: Array,
        operators: Array
    },

    data() {
        return {
            form: {
                machine_id: '',
                date: '',
                start_time: '',
                end_time: '',
                operator_id: '',
                ingots: 0,
                filter_pressure: 0,
                observations: '',
                processing: false
            }
        }
    },

    validations: {
        form: {
            machine_id: {
                required
            },
            date: {
                required,
            },
            start_time: {
                required
            },
            end_time: {
                required
            },
            operator_id: {
                required,
            },
            ingots: {
                required,
                minValue: minValue(0),
                numeric
            },
            filter_pressure: {
                required,
                minValue: minValue(0),
                numeric
            }
        }
    },

    methods: {
        resetForm() {
            this.form = {
                machine_id: '',
                date: '',
                start_time: '',
                end_time: '',
                operator_id: '',
                ingots: 0,
                filter_pressure: 0,
                observations: '',
                processing: false
            }
        },

        save(data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                this.form.processing = true;
                axios.post(route('binnacle-omff.registry.hl1.store'), data
                ).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Registro guardado con éxito",
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar'
                    });
                    this.resetForm();
                    this.v$.$reset()

                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(error.data);
                    this.form.processing = false;
                });
            }
        }
    }
}
</script>

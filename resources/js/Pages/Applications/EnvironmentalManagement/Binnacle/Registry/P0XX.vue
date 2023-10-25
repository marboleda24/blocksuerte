<template>
    <div>
        <Head title="Registro P0XX"/>

        <portal to="application-title">
            Registro P0XX
        </portal>

        <div class="intro-y box p-5">
            <div class="grid grid-cols-4 gap-3 py-5">
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
                        Turno
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                    </label>

                    <select v-model.trim="v$.form.workshift.$model" class="form-control"
                            :class="{ 'border-danger': v$.form.workshift.$error }" placeholder="Turno">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option value="1">06:00 AM - 02:00 PM</option>
                        <option value="2">02:00 PM - 10:00 PM</option>
                        <option value="3">10:00 PM - 06:00 AM</option>
                    </select>

                    <template v-if="v$.form.workshift.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.workshift.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

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
                        TB - Tapa Boton
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                    </label>

                    <input v-model.trim="v$.form.tb.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.tb.$error }" placeholder="Tapa Boton"/>

                    <template v-if="v$.form.tb.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.tb.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        RZ - Remache Zamac
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                    </label>

                    <input v-model.trim="v$.form.rz.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.rz.$error }" placeholder="Remache Zamac"/>

                    <template v-if="v$.form.rz.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.rz.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        VZ - Varios Zamac
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                    </label>

                    <input v-model.trim="v$.form.vz.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.vz.$error }" placeholder="Varios Zamac"/>

                    <template v-if="v$.form.vz.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.vz.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Z - Botones Camisero
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Obligatorio
                    </span>
                    </label>

                    <input v-model.trim="v$.form.z.$model" type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.z.$error }" placeholder="Botones Camisero"/>

                    <template v-if="v$.form.z.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.z.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Mantenimiento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Opcional
                    </span>
                    </label>

                    <select v-model.trim="form.maintenance" class="form-select" placeholder="Mantenimiento">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option value="preventive">Preventivo</option>
                        <option value="corrective">Correctivo</option>
                    </select>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Tipo de mantenimiento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Opcional
                    </span>
                    </label>

                    <select v-model.trim="form.type_maintenance" class="form-select"
                            placeholder="Tipo de mantenimiento">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option value="electric">Eléctrico</option>
                        <option value="hydraulic">Hidráulico</option>
                        <option value="mechanical">Mecánico</option>
                        <option value="pneumatic">Neumático</option>
                    </select>
                </div>

                <div class="">
                    <label class="flex flex-col sm:flex-row">
                        Operario
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Opcional
                    </span>
                    </label>

                    <select v-model.trim="form.maintenance_operator_id" class="form-select" placeholder="Operario">
                        <option value="" selected="selected" disabled>Seleccione...</option>
                        <option v-for="operator in operators" v-bind:key="operator.id" :value="operator.id">
                            {{ operator.name }}
                        </option>
                    </select>
                </div>
                <div class="col-span-4">
                    <label class="flex flex-col sm:flex-row">
                        Observaciones
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                        Opcional
                    </span>
                    </label>

                    <textarea v-model.trim="form.observations" class="form-control" cols="30" rows="5"
                              placeholder="Observaciones" style="resize:none"></textarea>
                </div>
            </div>
        </div>

        <div class="row justify-content-left">
            <button @click.prevent="save(form)" :disabled="form.processing" type="submit"
                    class="btn btn-primary uppercase mt-5">
                <font-awesome-icon icon="save" class="mr-2"/>
                Guardar registro
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
                date: '',
                workshift: '',
                machine_id: '',
                operator_id: '',
                tb: 0,
                rz: 0,
                vz: 0,
                z: 0,
                maintenance: '',
                type_maintenance: '',
                maintenance_operator_id: '',
                observations: '',
                processing: false
            }
        }
    },

    validations() {
        return {
            form: {
                date: {
                    required,
                },
                workshift: {
                    required
                },
                machine_id: {
                    required
                },
                operator_id: {
                    required
                },
                tb: {
                    required,
                    minValue: minValue(0),
                    numeric
                },
                rz: {
                    required,
                    minValue: minValue(0),
                    numeric
                },
                vz: {
                    required,
                    minValue: minValue(0),
                    numeric
                },
                z: {
                    required,
                    minValue: minValue(0),
                    numeric
                }
            }
        }
    },

    methods: {

        resetForm() {
            this.form = {
                date: '',
                workshift: '',
                machine_id: '',
                operator_id: '',
                tb: 0,
                rz: 0,
                vz: 0,
                z: 0,
                maintenance: '',
                type_maintenance: '',
                observations: '',
                maintenance_operator_id: '',
                processing: false
            }
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
                this.form.processing = true;
                axios.post(route('binnacle-omff.registry.p0xx.store'), data
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

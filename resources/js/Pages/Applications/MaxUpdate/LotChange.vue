<template>
    <div>
        <Head title="Modificación de lote"/>

        <portal to="application-title">
            Modificación de lote
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-4 gap-4">
                    <div class="intro-y">
                        <label class="flex flex-col sm:flex-row">
                            Orden de produccion
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="number"
                               class="form-control"
                               :class="{'border-danger': v$.form.production_order.$error}"
                               v-model="form.production_order">

                        <template v-if="v$.form.production_order.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.production_order.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y">
                        <label class="flex flex-col sm:flex-row">
                            Valor a cambiar
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text"
                               class="form-control"
                               :class="{'border-danger': v$.form.old_value.$error}"
                               v-model="form.old_value">

                        <template v-if="v$.form.old_value.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.old_value.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y">
                        <label class="flex flex-col sm:flex-row">
                            Nuevo Valor
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text"
                               class="form-control"
                               v-model="form.new_value">
                    </div>

                    <button class="btn btn-primary" @click="replaceData(form)">
                        Reemplazar Información
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {helpers, maxLength, minLength, numeric, required} from '@/utils/i18n-validators'

const validNumber = helpers.regex(/^(5{1,2})/i)

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    validations() {
        return {
            form: {
                production_order: {
                    required,
                    minLength: minLength(8),
                    maxLength: maxLength(8),
                    validNumber: helpers.withMessage('la orden debe comenzar con 5', validNumber),
                    numeric
                },
                old_value: {
                    required,
                    minLength: minLength(1)
                },
            }
        }
    },

    data() {
        return {
            form: {
                production_order: '',
                old_value: '',
                new_value: ''
            }
        }
    },

    methods: {
        replaceData(form) {
            this.v$.form.$touch()
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando lote…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('max.lot-change.replace-data'), form).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Lote actualizado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err.data)
                })
            }
        },

        resetForm(){
            this.form = {
                production_order: '',
                old_value: '',
                new_value: ''
            }
            this.v$.form.$reset()
        }
    }
}
</script>

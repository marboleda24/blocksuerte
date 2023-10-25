<template>
    <div>
        <Head title="Registro Parametros Baños Galvano"/>

        <portal to="application-title">
            Registro Parametros Baños Galvano
        </portal>

        <div class="intro-y box mb-16">
            <div class="p-5">
                <div class="grid sm:grid-cols-1 md:sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">Baño</label>
                        <select class="form-select"
                                :class="{ 'border-danger': v$.form.bath.$error }"
                                v-model="form.bath">
                            <option value="">Seleccione…</option>
                            <option value="Cobre 1">Cobre 1</option>
                            <option value="Cobre 2">Cobre 2</option>
                            <option value="Cobre 3">Cobre 3</option>
                            <option value="Estaño">Estaño</option>
                            <option value="Galvanizado">Galvanizado</option>
                            <option value="Latón 1">Latón 1</option>
                            <option value="Latón 2">Latón 2</option>
                            <option value="Níquel 1">Níquel 1</option>
                            <option value="Níquel 2">Níquel 2</option>
                            <option value="Níquel 3">Níquel 3</option>
                            <option value="Níquel Mate">Níquel Mate</option>
                            <option value="Níquel Negro">Níquel Negro</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Order de Producción</label>
                        <autocomplete
                            :url="route('galvano-bath-parameter.search-op')"
                            show-field="ORDNUM_10"
                            @selected-value="getOrderInfo"
                            ref="op_search">
                        </autocomplete>
                    </div>

                    <template v-if="form.product_code && form.product_description">
                        <div>
                            <label class="form-label">Producto</label>
                            <input type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.form.product_code.$error }"
                                   v-model="form.product_description"
                                   disabled/>
                        </div>
                        <div>
                            <label class="form-label">Marca</label>
                            <input type="text"
                                   class="form-control"
                                   v-model="form.brand"
                                   disabled/>
                        </div>
                        <div>
                            <label class="form-label">PH</label>
                            <input type="number"
                                   v-model="form.ph"
                                   :class="{ 'border-danger': v$.form.ph.$error }"
                                   class="form-control"/>
                        </div>
                        <div>
                            <label class="form-label">Densidad</label>
                            <input type="number"
                                   v-model="form.density"
                                   :class="{ 'border-danger': v$.form.density.$error }"
                                   class="form-control"/>
                        </div>
                        <div>
                            <label class="form-label">Temperatura</label>
                            <input type="number"
                                   v-model="form.temperature"
                                   :class="{ 'border-danger': v$.form.temperature.$error }"
                                   class="form-control"/>
                        </div>
                        <div>
                            <label class="form-label">Hora Entrada</label>
                            <input type="time"
                                   v-model="form.entry_time"
                                   :class="{ 'border-danger': v$.form.entry_time.$error }"
                                   class="form-control"/>
                        </div>
                        <div>
                            <label class="form-label">Hora Salida</label>
                            <input type="time"
                                   v-model="form.exit_time"
                                   :class="{ 'border-danger': v$.form.exit_time.$error }"
                                   class="form-control"/>
                        </div>
                        <div>
                            <label class="form-label">Notas / Observaciones</label>
                            <input type="text"
                                   v-model="form.notes"
                                   class="form-control"/>
                        </div>

                        <button class="btn btn-primary" @click="save(form)">
                            Guardar
                        </button>
                    </template>
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import useVuelidate from '@vuelidate/core'
import {minValue, required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Autocomplete
    },

    validations() {
        return {
            form: {
                production_order: {required},
                bath: {required},
                product_code: {required},
                product_description: {required},
                ph: {required, minValue: minValue(1)},
                density: {required, minValue: minValue(1)},
                temperature: {required, minValue: minValue(1)},
                entry_time: {required},
                exit_time: {required}
            }
        }
    },

    data() {
        return {
            form: {
                production_order: '',
                bath: '',
                product_code: '',
                product_description: '',
                brand: '',
                ph: '',
                density: '',
                temperature: '',
                entry_time: '',
                exit_time: '',
                notes: ''
            }
        }
    },

    methods: {
        getOrderInfo(obj) {
            this.form.production_order = obj.ORDNUM_10
            this.form.product_code = obj.PRTNUM_10
            this.form.product_description = obj.PMDES1_01
            this.form.brand = obj.LOTNUM_10
        },

        save(form) {
            this.v$.form.$touch()

            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('galvano-bath-parameter.store'), form).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Registro guardado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        timer: 1500,
                        timerProgressBar: true
                    });
                    this.resetForm()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        resetForm(){
            this.$refs.op_search.showInput(true)
            this.form = {
                production_order: '',
                bath: '',
                product_code: '',
                product_description: '',
                brand: '',
                ph: '',
                density: '',
                temperature: '',
                entry_time: '',
                exit_time: '',
                notes: ''
            }
            this.v$.form.$reset()
        }
    }
}
</script>

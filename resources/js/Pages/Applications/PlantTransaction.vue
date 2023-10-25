<template>
    <div>
        <Head title="Transacciones de planta" />

        <portal to="application-title">
            Transacciones de planta
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label for="">INGRESE LA OP</label>
                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.production_order.$error }"
                               v-model="form.production_order">

                        <template v-if="v$.form.production_order.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.production_order.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <button class="btn btn-primary" @click="search">
                        Buscar
                    </button>
                </div>
            </div>
        </div>

        <div class="box mt-5" v-if="data.length > 0">
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th colspan="8" class="text-center">
                                    {{ `${data[0].COD_PRODUCTO} – ${data[0].DESC_PRODUCTO}` }}
                                </th>
                            </tr>
                            <tr>
                                <th>SEQ</th>
                                <th>CODIGO TRANSACCIÓN</th>
                                <th>CANTIDAD</th>
                                <th>USUARIO</th>
                                <th>FECHA</th>
                                <th>HORA</th>
                                <th>DEFECTO</th>
                                <th>NOTAS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="element in data">
                                <td>{{ element.OPERACION_SEC }}</td>
                                <td>{{ element.COD_TRANSACCION }}</td>
                                <td class="text-right">{{ element.CANT }}</td>
                                <td>{{ element.USUARIO }}</td>
                                <td>{{ $h.formatDate(element.FECHA, 'DD-MM-YYYY') }}</td>
                                <td>{{ element.HORA }}</td>
                                <td>{{ [element.COD_DEFECTO, element.DESC_DEFECTO].join(' – ') }}</td>
                                <td>{{ [element.NOTAS.trim(), element.NOTAS1.trim()].join(' ') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {maxLength, minLength, numeric, required} from "@/utils/i18n-validators";
import useVuelidate from "@vuelidate/core";
import {Head} from "@inertiajs/vue3";

export default  {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    validations(){
        return {
            form: {
                production_order: {
                    required,
                    numeric,
                    minLength: minLength(8),
                    maxLength: maxLength(8)
                }
            },
        }
    },

    data(){
        return {
            form: {
                production_order: ''
            },

            data: []
        }
    },

    methods: {
        search(){
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando Información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.get(route('plant-transaction.search'), {
                    params: {
                        production_order: this.form.production_order
                    }
                }).then(resp => {
                    this.data = resp.data
                    this.$swal.close()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        }
    }
}
</script>

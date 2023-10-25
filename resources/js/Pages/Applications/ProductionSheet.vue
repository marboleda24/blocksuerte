<template>
    <div>
        <Head title="Hoja de produccion"/>

        <portal to="application-title">
            Hoja de produccion
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label>OP Inicial</label>

                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.start.$error }"
                               v-model.number="form.start">

                        <template v-if="v$.form.start.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.start.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label>OP Final</label>

                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.end.$error }"
                               v-model.number="form.end">

                        <template v-if="v$.form.end.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.end.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <button class="btn btn-primary" @click="generateDocuments(this.form.start, this.form.end)">
                        <font-awesome-icon icon="download" class="mr-2"/>
                        Generar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">

import {maxValue, minLength, minValue, numeric, required} from '@/utils/i18n-validators'
import useVuelidate from "@vuelidate/core";
import {Head, Link} from "@inertiajs/vue3";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Link
    },

    validations() {
        return {
            form: {
                start: {
                    required,
                    numeric,
                    maxValue: maxValue(this.form.end),
                    minLength: minLength(8)
                },
                end: {
                    required,
                    numeric,
                    minValue: minValue(this.form.start),
                    minLength: minLength(8)
                }
            }
        }
    },

    data() {
        return {
            form: {
                start: '',
                end: ''
            }
        }
    },

    methods: {
        generateDocuments(start, end) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('production-sheet.pdf'), {
                    start: start,
                    end: end
                }, {
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `${start}-${end}-hoja-de-produccion.pdf`);

                    document.body.appendChild(link);
                    link.click();

                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        },


    }
}
</script>

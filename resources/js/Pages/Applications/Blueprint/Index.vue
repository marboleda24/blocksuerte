<template>
    <div>
        <Head title="Planoteca"/>

        <portal to="application-title">
            Planoteca
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openNewModal = true">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Plano
            </button>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)" @click="downloadPdf(row.NUMERO)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)" @click="resend(row.NUMERO)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'code-branch']" class="mr-1"/>
                                    Versiones
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="openNewModal" @close="closeModal" max-width="2xl">
                <template #title>
                    Registro de plano
                </template>

                <template #content>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Linea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.line_code.$error }"
                                    @change="getSublines"
                                    v-model="form.line_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="line in lines" :value="line.code">{{ line.name }}</option>
                            </select>

                            <template v-if="v$.form.line_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.line_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Sublinea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.subline_code.$error }"
                                    @change="getAnotherInputs"
                                    v-model="form.subline_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="subline in sublines" :value="subline.code">{{ subline.name }}</option>
                            </select>

                            <template v-if="v$.form.subline_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.subline_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Característica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.feature_code.$error }"
                                    v-model="form.feature_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="feature in features" :value="feature.code">{{ feature.name }}</option>
                            </select>

                            <template v-if="v$.form.feature_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.feature_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Material
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.material_id.$error }"
                                    v-model="form.material_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="material in materials" :value="material.id">
                                    {{material.material.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.material_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.material_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Medida
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.measurement_id.$error }"
                                    v-model="form.measurement_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="measurement in measurements" :value="measurement.id">
                                    {{ $h.denominationCreator(measurement.detail) }}
                                </option>
                            </select>

                            <template v-if="v$.form.measurement_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.measurement_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Archivo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="file"
                                   ref="file"
                                   accept="application/pdf"
                                   @change="imageFileChanged($event)"
                                   class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                                   :class="{ 'border-danger': v$.form.file.$error }"/>

                            <template v-if="v$.form.file.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.file.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>


                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Miniatura
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="file"
                                   ref="miniature"
                                   accept="image/*"
                                   @change="imageMiniatureChanged($event)"
                                   class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                                   :class="{ 'border-danger': v$.form.miniature.$error }"/>

                            <template v-if="v$.form.miniature.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.miniature.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form col-span-2">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Descripción
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control resize-none"
                                      :class="{ 'border-danger': v$.form.description.$error }"
                                      v-model="form.description" cols="30" rows="3"></textarea>

                            <template v-if="v$.form.description.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.description.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="store(form)" type="button" class="btn btn-primary">
                        Guardar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        blueprints: Array,
        lines: Array
    },

    components: {
        Head,
        JetDialogModal
    },

    validations(){
        return {
            form: {
                line_code: {required},
                subline_code: {required},
                feature_code: {required},
                material_id: {required},
                measurement_id: {required},
                description: {required},
                file: {required},
                miniature: {required}
            }
        }
    },

    data(){
        return {
            table: {
                data: this.blueprints,
                columns: [
                    'id',
                    'version',
                    'product',
                    'measure',
                    'created_user',
                    'description',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        id: "#",
                        version: "VER.",
                        product: "PRODUCTO",
                        measure: "MEDIDA",
                        created_user: "CREADO POR",
                        description: "DESCRIPCIÓN",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    clientSorting: false,
                    sortable: ['id', 'product', 'measure', 'created_user', 'description', 'created_at'],
                    templates: {
                        created_user: function (h, row) {
                            return row.created_user.name
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    }
                }
            },
            sublines: [],
            features: [],
            materials: [],
            measurements: [],
            form: {
                line_code: '',
                subline_code: '',
                feature_code: '',
                material_id: '',
                measurement_id: '',
                description: '',
                file: '',
                miniature: ''
            },
            openNewModal: false
        }
    },

    methods: {
        closeModal(){
            this.openNewModal = false
            this.v$.form.$reset()
        },

        getSublines(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-sublines'), {
                params: {
                    line_code: this.form.line_code
                }
            }).then(resp => {
                this.sublines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getAnotherInputs() {
            this.resetChangeSubline()

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-other-inputs'), {
                params: {
                    line_code: this.form.line_code,
                    subline_code: this.form.subline_code
                }
            }).then(resp => {
                this.features = resp.data.features
                this.materials = resp.data.materials
                this.measurements = resp.data.measurements
                this.$swal.close();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        resetChangeSubline() {
            this.features = []
            this.materials = []
            this.measurements = []
        },

        imageFileChanged(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.file = ''
            }
            this.form.file = files[0];
        },

        imageMiniatureChanged(event){
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.miniature = ''
            }
            this.form.miniature = files[0];
        },


        store(form){
            this.v$.form.$touch()
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                const formData = new FormData();
                Object.keys(form).forEach(key => formData.append(key, form[key]));

                let file = this.$refs.file.files[0];
                let miniature = this.$refs.miniature.files[0];

                formData.append('file', file);
                formData.append('miniature', miniature);

                axios.post(route('blueprints.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.closeModal()
                    this.table.data = resp.data

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Plano registrado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
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
        }
    }
}
</script>

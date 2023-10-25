<template>
    <div>
        <Head title="Codificador"/>

        <portal to="application-title">
            Codificador
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()" data-target="#open_modal">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Código
            </button>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">

                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)" @click="edit(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)" @click="deleteRow(row.code)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width="xl">
                <template #title>
                    {{ title }}
                </template>

                <template #content>
                    <div class="px-4 pt-5 sm:p-2">
                        <form @change="generate_code_and_description">
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Tipo de producto
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.product_type.$model" class="form-select"
                                            :class="{ 'border-danger': v$.form.product_type.$error }"
                                            :disabled="editMode"
                                            @change="get_lines">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="product_type in product_types" v-bind:key="product_type.code"
                                                :value="product_type.code">{{ product_type.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.product_type.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.product_type.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Linea
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.line.$model" class="form-select"
                                            :class="{ 'border-danger': v$.form.line.$error }"
                                            @change="get_sublines"
                                            :disabled="editMode">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="line in lines" v-bind:key="line.code" :value="line.code">
                                            {{ line.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.line.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.line.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Sublinea
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.subline.$model" class="form-select"
                                            :class="{ 'border-danger': v$.form.subline.$error }"
                                            @change="get_other_inputs"
                                            :disabled="editMode">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="subline in sublines" v-bind:key="subline.code"
                                                :value="subline.code">{{ subline.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.subline.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.subline.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Característica
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.feature.$model" class="form-select"
                                            :class="{ 'border-danger': v$.form.feature.$error }">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="feature in features" v-bind:key="feature.code"
                                                :value="feature.code">{{ feature.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.feature.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.feature.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Material
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.material.$model" class="form-select"
                                            :class="{ 'border-danger': v$.form.material.$error }">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="material in materials" v-bind:key="material.id"
                                                :value="material.id">{{ material.material.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.material.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.material.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Medida
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                                    </label>

                                    <div class="input-group">

                                        <TomSelect v-model="v$.form.measurement.$model"
                                                   class="w-full"
                                                   :class="{ 'border-danger': v$.form.measurement.$error }"
                                                   @change="generate_code_and_description">
                                            <option value="" selected>Seleccione...</option>
                                            <option v-for="measurement in measurements" v-bind:key="measurement.id"
                                                    :value="measurement.id">
                                                {{ $h.denominationCreator(measurement.detail) }}
                                            </option>
                                        </TomSelect>

                                        <button class="btn btn-secondary ml-1" type="button" @click="get_measurements"
                                                :disabled="!form.line && !form.subline">
                                            <font-awesome-icon icon="sync-alt" spin/>
                                        </button>
                                    </div>

                                    <template v-if="v$.form.measurement.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.measurement.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Acabado Galvánico
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.galvanic_finish.$model"
                                            class="form-select"
                                            :class="{ 'border-danger': v$.form.galvanic_finish.$error }">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="galvanic_finish in galvanic_finishes"
                                                v-bind:key="galvanic_finish.code" :value="galvanic_finish.code">
                                            {{ galvanic_finish.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.galvanic_finish.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.galvanic_finish.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="flex flex-col sm:flex-row">
                                        Opcion Decorativa
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>

                                    <select v-model.trim="v$.form.decorative_option.$model"
                                            class="form-select"
                                            :class="{ 'border-danger': v$.form.decorative_option.$error }">
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option v-for="decorative_option in decorative_options"
                                                v-bind:key="decorative_option.code" :value="decorative_option.code">
                                            {{ decorative_option.name }}
                                        </option>
                                    </select>

                                    <template v-if="v$.form.decorative_option.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.decorative_option.$errors"
                                                :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mb-4 col-span-2">
                                    <label class="flex flex-col sm:flex-row">
                                        Arte
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>

                                    <autocomplete
                                        ref="art"
                                        url="/encoder/codes/search-art"
                                        show-field="CodigoArte"
                                        @selected-value="get_art_data"
                                    />

                                </div>

                                <div class="mb-4 col-span-2">
                                    <label class="flex flex-col sm:flex-row">
                                        Comentarios
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <textarea class="form-control" name="comments" id="comments" cols="30" rows="5"
                                              v-model="form.comments"></textarea>
                                </div>

                                <div class="mb-4 col-span-2">
                                    <label class="flex flex-col sm:flex-row">
                                        Código
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Autogenerado
                                        </span>
                                    </label>
                                    <input type="text" v-model="form.code" class="form-control" disabled>
                                </div>

                                <div class="mb-4 col-span-2">
                                    <label class="flex flex-col sm:flex-row">
                                        Descripción
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Autogenerado
                                        </span>
                                    </label>

                                    <input type="text"
                                           v-model="form.description"
                                           class="form-control"
                                           :class="{ 'border-danger': v$.form.decorative_option.$error }" disabled>

                                    <template v-if="v$.form.description.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger"
                                                v-for="(error, index) of v$.form.description.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click="save(form)"
                            :disabled="form.processing" type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click="update(form)" type="button"
                            class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import dayjs from 'dayjs';
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {helpers, required} from '@/utils/i18n-validators'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";

import 'dayjs/locale/es'
dayjs.locale('es')

const {withAsync} = helpers
const CancelToken = axios.CancelToken;
let source;

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Autocomplete,
        Head
    },

    props: {
        data: Array,
        product_types: Array,
        galvanic_finishes: Array,
        decorative_options: Array
    },

    validations() {
        return {
            form: {
                code: {required},
                description: {
                    required,
                    isUnique: helpers.withMessage('Este producto ya se encuentra registrado', withAsync(async (value) => {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value) {
                            try {
                                return await axios.post(route('code.validate-description'), {
                                    description: value
                                }, {
                                    cancelToken: source.token,
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                console.log(error)
                                return false
                            }
                        } else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                product_type: {required},
                line: {required},
                subline: {required},
                feature: {required},
                material: {required},
                measurement: {required},
                galvanic_finish: {required},
                decorative_option: {required},
            },
        }
    },

    data() {
        return {
            columns: [
                'code',
                'description',
                'product_type',
                'line',
                'subline',
                'feature',
                'material',
                'measurement',
                'galvanic_finish',
                'decorative_option',
                'comments',
                'actions'
            ],
            options: {
                headings: {
                    code: 'CÓDIGO',
                    description: 'DESCRIPCIÓN',
                    base_cost: 'COSTO BASE',
                    product_type: 'TIPO PRODUCTO',
                    line: 'LINEA',
                    subline: 'SUBLINEA',
                    feature: 'CARACTERISTICA',
                    material: 'MATERIAL',
                    measurement: 'MEDIDA',
                    galvanic_finish: 'A. GALVANICO',
                    decorative_option: 'OPC.DECORATIVA',
                    generic: 'GENERICO',
                    comments: 'COMENTARIOS',
                    actions: 'ACCIONES',
                },

                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['code', 'description', 'product_type', 'line', 'subline', 'feature',
                    'material', 'measurement', 'galvanic_finish', 'decorative_option'
                ],
                templates: {
                    measurement(h, row) {
                        return row.measurement.measurement
                    },

                },
            },
            editMode: false,
            isOpen: false,
            form: {
                code: null,
                description: null,
                product_type: null,
                line: null,
                subline: null,
                feature: null,
                material: null,
                measurement: '',
                art: '',
                galvanic_finish: null,
                decorative_option: null,
                comments: null
            },
            errors: {},
            tableData: this.data,
            title: 'Crear código',
            lines: [],
            sublines: [],
            features: [],
            materials: [],
            measurements: [],
        }
    },

    methods: {
        openModal() {
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
            this.reset();
            this.editMode = false;
            this.errors = {};
        },

        deleteRow(row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tomar unos segundos, espere por favor…',
                    });

                    axios.delete(route('codes.destroy', row)).then(resp => {
                        this.tableData = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Registro eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })

                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error eliminando el registro.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },

        save(data) {
            this.v$.form.$touch()
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });
                axios.post(route('codes.store'), data).then(res => {
                    this.closeModal();
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Código creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.tableData = res.data;
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

        edit(data) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('codes.verify-product', data.code)).then(resp => {
                if (resp.data) {
                    this.$swal.close()

                    this.form = {
                        product_type: data.product_type_code,
                        line: '',
                        subline: '',
                        feature: '',
                        material: '',
                        measurement: '',
                        galvanic_finish: '',
                        decorative_option: '',
                        art: data.art_code,
                        comments: data.comments
                    }

                    this.get_lines();
                    this.form.line = data.line_code

                    this.get_sublines();
                    this.form.subline = data.subline_code

                    this.get_other_inputs();
                    this.form.feature = data.feature_code
                    this.form.material = data.material_id
                    this.form.measurement = data.measurement_id.toString()
                    this.form.galvanic_finish = data.galvanic_finish_code
                    this.form.decorative_option = data.decorative_option_code

                    this.editMode = true;
                    this.title = 'Editar ' + data.description;
                    this.form.old_code = data.code
                    this.form.code = data.code
                    this.form.description = data.description
                    this.openModal();
                    this.$refs.art.setValue(data.art_code)

                } else {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Este producto ya ha sido cargado a MAX y no puede ser modificado',
                        confirmButtonText: 'Aceptar',
                    });
                    return false;
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err);
                return false;
            })
        },

        update(row) {
            axios.put(route('codes.update', row.old_code), row).then(resp => {
                this.tableData = resp.data;
                this.reset();
                this.closeModal();
                this.errors = {};

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => this.errors = err.response.data.errors);
        },

        get_lines(event) {
            this.reset_onchange_product_type();
            axios.get(route('codes.get-lines'), {
                params: {
                    product_type: this.form.product_type
                }
            }).then(resp => {
                this.lines = resp.data;
            }).catch(error => {
                console.log(error.data);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        get_sublines(event) {
            this.reset_onchange_line();
            axios.get(route('codes.get-sublines'), {
                params: {
                    line_code: this.form.line
                }
            }).then(resp => {
                this.sublines = resp.data;
            }).catch(error => {
                console.log(error.data);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        get_other_inputs() {
            this.reset_onchange_subline();
            axios.get(route('codes.get-other-inputs'), {
                params: {
                    line_code: this.form.line,
                    subline_code: this.form.subline
                }
            }).then(resp => {
                this.features = resp.data.features
                this.materials = resp.data.materials
                this.measurements = resp.data.measurements
            }).catch(error => {
                console.log(error.data);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        reset() {
            this.form = {
                code: null,
                description: null,
                base_cost: null,
                product_type: null,
                line: null,
                subline: null,
                feature: null,
                material: null,
                measurement: '',
                galvanic_finish: null,
                decorative_option: null,
                art: null,
                comments: null
            }
            this.title = 'Crear código'
        },

        reset_onchange_product_type() {
            this.form.line = null
            this.form.subline = null
            this.form.feature = null
            this.form.material = null
            this.form.measurement = ''
            this.form.galvanic_finish = null
            this.form.decorative_option = null

            this.lines = []
            this.sublines = []
            this.features = []
            this.materials = []
            this.measurements = []
        },

        reset_onchange_line() {
            this.form.subline = null,
                this.form.feature = null,
                this.form.material = null,
                this.form.measurement = '',
                this.form.galvanic_finish = null,
                this.form.decorative_option = null,

                this.sublines = [],
                this.features = [],
                this.materials = [],
                this.measurements = []
        },

        reset_onchange_subline() {
            this.form.feature = null
            this.form.material = null
            this.form.measurement = ''
            this.form.galvanic_finish = null
            this.form.decorative_option = null
            this.features = []
            this.materials = []
            this.measurements = []
        },

        generate_code_and_description() {
            let product_type = this.product_types.filter(x => x.code === this.form.product_type).map(function (x) {
                return x.code
            });
            let line = this.lines.filter(x => x.code === this.form.line).map(function (x) {
                return {code: x.code, abbreviation: x.abbreviation}
            });
            let subline = this.sublines.filter(x => x.code === this.form.subline).map(function (x) {
                return {code: x.code, abbreviation: x.abbreviation}
            });
            let feature = this.features.filter(x => x.code === this.form.feature).map(function (x) {
                return {code: x.code, abbreviation: x.abbreviation}
            });
            let material = this.materials.filter(x => x.id === this.form.material).map(function (x) {
                return x.material.code
            });
            let measurement = this.measurements.filter(x => x.id === parseInt(this.form.measurement)).map(function (x) {
                return x.detail
            });
            let galvanic_finish = this.galvanic_finishes.filter(x => x.code === this.form.galvanic_finish).map(function (x) {
                return x.abbreviation
            });
            let decorative_option = this.decorative_options.filter(x => x.code === this.form.decorative_option).map(function (x) {
                return x.abbreviation
            });

            let code = '';
            let description = '';

            if (product_type)
                code += product_type
            description += `${product_type}-`
            if (line.length > 0) {
                code += line[0].code
                description += `${line[0].abbreviation} `
            }
            if (subline.length > 0) {
                code += subline[0].code.substring(2, 4)
                description += `${subline[0].abbreviation} `
            }
            if (feature.length > 0) {
                description += `${feature[0].abbreviation} `
            }
            if (material.length > 0) {
                code += material[0]
                description += `${material[0]} `
            }
            if (measurement.length > 0) {
                description += `${this.$h.denominationCreator(measurement[0])} `
            }

            if (String(galvanic_finish).trim().length > 0 && String(decorative_option).trim().length > 0){
                description += `${galvanic_finish}*${decorative_option} `
            }else {
                if (galvanic_finish) {
                    description += `${galvanic_finish} `
                }
                if (decorative_option) {
                    description += `${decorative_option} `
                }
            }

            if (this.form.art) {
                description += this.form.art
            }

            axios.get(route('codes.get-list-codes'), {
                params: {
                    product_type: this.form.product_type,
                    line: this.form.line,
                    subline: this.form.subline,
                    material: this.form.material,
                }
            }).then(resp => {
                if (!this.editMode){
                    this.form.code = this.alphaNumericIncrementWithCode(resp.data, code, 4)
                }
                this.form.description = description.trim().replace(/ +(?= )/g, '');
            }).catch(err => {

            })
        },

        alphaNumericIncrementWithCode(code_list, code, length) {
            let incremental = 0;
            let chart_string_range = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            let vector = [];
            let t = 0;
            let numberf = 0;

            for (let i = 0; i < code_list.length; i++) {
                if (code === code_list[i].substring(0, 6) && code_list[i].length === 10) {
                    const string = code_list[i].substring(6);
                    let text = string.split('').reverse().join('');
                    text = text.split('');

                    for (let k = 0; k < length; k++) {
                        for (var j = 0; j < 36; j++) {
                            if (text[k] === chart_string_range[j]) {
                                break;
                            }
                        }
                        numberf += j * Math.pow(36, k);
                    }
                    vector[t] = numberf;
                    t++;
                    numberf = 0;
                }
            }

            let maxvector = Math.max.apply(Math, vector); //saca el valor máximo de un arreglo
            if (maxvector >= 0) {
                incremental = maxvector + 1;
            }

            let text2 = '';
            let incretemp = incremental;
            for (let i = 0; i < length; i++) {
                incretemp = Math.floor(incretemp) / 36;
                text2 += chart_string_range.charAt(Math.round((incretemp - Math.floor(incretemp)) * 36));
            }
            text2 = text2.split('').reverse().join('');
            return code + text2;
        },

        get_art_data(obj) {
            this.form.art = obj.CodigoArte;
            this.generate_code_and_description();
        },

        get_measurements() {
            axios.get(route('codes.get-measurements'), {
                params: {
                    line_code: this.form.line,
                    subline_code: this.form.subline
                }
            }).then(resp => {
                this.measurements = resp.data
                this.get_other_inputs()
            }).catch(error => {
                console.log(error.data);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        }
    },
}
</script>

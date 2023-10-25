<template>
    <div>
        <Head title="Materiales"/>

        <portal to="application-title">
            Materiales
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Material
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
                                <a href="javascript:void(0)"
                                   @click="edit(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="deleteRow(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    {{ title }}
                </template>

                <template #content>
                    <div class="p-2">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Linea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.line.$model" type="text" class="form-select"
                                    :class="{ 'border-danger': v$.form.line.$error }"
                                    @change="getSublines" autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="line in lines" v-bind:key="line.code" :value="line.code">{{
                                        line.name
                                    }}
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

                            <select v-model.trim="v$.form.subline.$model" type="text" class="form-select"
                                    :class="{ 'border-danger': v$.form.subline.$error }"
                                    autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="subline in sublines" v-bind:key="subline.code" :value="subline.code">
                                    {{ subline.name }}
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
                                Material
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.material_code.$model" type="text" class="form-select"
                                    :class="{ 'border-danger': v$.form.material_code.$error }"
                                    autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="material in materials" v-bind:key="material.code" :value="material.code">
                                    {{ material.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.material_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.material_code.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="">
                            <label class="flex flex-col sm:flex-row">
                                Comentarios
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea class="form-control" name="comments" id="comments" cols="30" rows="5"
                                      v-model="form.comments"></textarea>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)"
                            :disabled="form.processing" type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click="update(form)" type="button"
                            :disabled="form.processing" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {helpers, required} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

const {withAsync} = helpers
export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Head
    },

    props: {
        data: Array,
        lines: Array,
        materials: Array
    },

    validations() {
        return {
            form: {
                line: {required},
                subline: {required},
                material_code: {
                    required,
                    isUnique: helpers.withMessage('Este material ya se encuentra registrada', withAsync(async (value) => {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value) {
                            try {
                                return await axios.get(route('materials.validate'), {
                                    cancelToken: source.token,
                                    params: {
                                        line: this.form.line,
                                        subline: this.form.subline,
                                        material_code: value
                                    }
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
            }
        }

    },

    data() {
        return {
            columns: [
                'line',
                'subline',
                'code',
                'name',
                'comments',
                'created_by',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    line: 'LINEA',
                    subline: 'SUBLINEA',
                    code: 'CÓDIGO',
                    name: 'NOMBRE',
                    comments: 'COMENTARIOS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: '',
                },

                uniqueKey: "code",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['line', 'subline', 'code', 'name', 'created_by', 'created_at', 'updated_at'],
                dateColumns: ['created_at', 'updated_at'],
                templates: {
                    line: function (h, row) {
                        return `${row.line.code} - ${row.line.name}`

                    },
                    subline: function (h, row) {
                        return `${row.subline.code} - ${row.subline.name}`
                    },
                    code: function (h, row) {
                        return row.material.code
                    },
                    name: function (h, row) {
                        return row.material.name
                    },
                    created_by: function (h, row) {
                        return row.created_by.name
                    },
                    created_at: function (h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                    },
                    updated_at: function (h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY');
                    }
                }
            },
            editMode: false,
            isOpen: false,
            form: {
                line: '',
                subline: '',
                material_code: '',
                comments: '',
            },
            errors: {},
            tableData: this.data,
            title: 'Crear material',
            sublines: []
        }
    },
    methods: {
        openModal: function () {
            this.isOpen = true;
        },

        closeModal: function () {
            this.isOpen = false;
            this.reset();
            this.editMode = false;
            this.errors = {};
            this.v$.$reset()
        },

        reset: function () {
            this.form = {
                line: null,
                subline: null,
                material_code: null,
                comments: null,
            }
            this.title = 'Crear material'
        },

        save: function (data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                axios.post(route('materials.store'), data).then(res => {
                    this.closeModal();
                    this.tableData = res.data;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Material creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.errors = err.response.data.errors
                    this.$swal({

                        icon: 'error',
                        title: 'Ups..',
                        text: 'Hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    })
                });
            }
        },

        deleteRow: function (row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('materials.destroy', row)).then(resp => {
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

        edit: function (data) {
            this.form = {
                id: data.id,
                line: data.line.code,
                subline: data.subline.code,
                material_code: data.material.code,
                comments: data.comments
            };
            this.getSublines();
            this.editMode = true;
            this.title = 'Editar ' + data.material.name
            this.openModal();
        },

        update: function (row) {

            axios.put(route('materials.update', row.id), row).then(resp => {
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

        getSublines: function (event) {
            axios.get(route('features.get-sublines'), {
                params: {
                    line_code: this.form.line
                }
            }).then(resp => {
                this.sublines = resp.data
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
    }
}
</script>

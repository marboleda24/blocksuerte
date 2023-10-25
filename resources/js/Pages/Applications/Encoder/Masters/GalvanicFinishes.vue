<template>
    <div>
        <Head title="Acabados Galvánicos"/>

        <portal to="application-title">
            Acabados Galvánicos
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Acabado Galvánico
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
                                   @click="deleteRow(row.code)"
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
                                Nombre
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.name.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.name.$error }" placeholder="Nombre"/>

                            <template v-if="v$.form.name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Abreviatura
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.abbreviation.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.abbreviation.$error }"
                                   placeholder="Abreviatura"/>

                            <template v-if="v$.form.abbreviation.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.abbreviation.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Comentarios
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea class="form-control" name="comments" id="comments" cols="30" rows="5"
                                      v-model="form.comments"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Código
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Autogenerado
                                </span>
                            </label>
                            <input class="form-control" type="text" v-model="form.code" disabled/>
                        </div>
                    </div>

                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)" :disabled="form.processing"
                            type="submit" class="btn btn-primary">
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
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength, maxLength} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Head
    },

    props: {
        data: Array
    },

    validations() {
        return {
            form: {
                code: {
                    required,
                    minLength: minLength(1),
                    maxLength: maxLength(2)
                },
                name: {
                    required,
                    minLength: minLength(4),
                    maxLength: maxLength(255),
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('galvanic-finish.validate-name', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
                abbreviation: {
                    required,
                    minLength: minLength(2),
                    maxLength: maxLength(255),
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('galvanic-finish.validate-abbreviation', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
            }
        }

    },

    data() {
        return {
            columns: [
                'code',
                'name',
                'abbreviation',
                'comments',
                'created_by',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    code: 'CÓDIGO',
                    name: 'NOMBRE',
                    abbreviation: 'ABREVIATURA',
                    comments: 'COMENTARIOS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: 'ACCIONES',
                },
                uniqueKey: "code",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['code', 'name', 'abbreviation', 'created_by', 'created_at', 'updated_at'],
                templates: {
                    created_at: function (h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                    },
                    updated_at: function (h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY');
                    },
                    created_by: function (h, row) {
                        return row.created_by.name
                    }
                }
            },
            editMode: false,
            isOpen: false,
            form: {
                code: null,
                name: null,
                abbreviation: null,
                comments: null,
            },
            errors: {},
            tableData: this.data,
            title: 'Crear acabado galvánico',
        }
    },
    methods: {
        openModal: function () {
            this.isOpen = true;
            this.generate_code();
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
                code: null,
                name: null,
                abbreviation: null,
                comments: null,
            },
                this.title = 'Crear acabado galvánico'
        },

        save: function (data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                axios.post(route('galvanic-finishes.store', data)).then(res => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Acabado galvánico creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.closeModal();
                    this.tableData = res.data;
                }).catch(err => this.errors = err.response.data.errors)
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
                    axios.delete(route('galvanic-finishes.destroy', row)).then(resp => {
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
            this.form = Object.assign({}, data);
            this.editMode = true;
            this.openModal();
            this.title = 'Editar ' + data.code;
            this.form.code = data.code;
        },

        update: function (row) {
            axios.put(route('galvanic-finishes.update', row.code), row).then(resp => {
                this.tableData = resp.data;
                this.closeModal();

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => {
                this.errors = err.response.data.errors;
                console.log(err);
            })
        },

        generate_code: function () {
            if (!this.editMode) {
                axios.get(route('galvanic-finishes.get-latest-code')).then(resp => {
                    this.form.code = this.$h.alphaNumericIncrement(resp.data, 2)
                }).catch(error => {
                    console.log(error);
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
}
</script>

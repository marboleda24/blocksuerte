<template>
    <div>
        <Head title="Tipos de Productos"/>

        <portal to="application-title">
            Tipos de Productos
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Tipo de Producto
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
                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Código
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.code.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.code.$error }" placeholder="Código"
                                   :disabled="editMode"/>

                            <template v-if="v$.form.code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.code.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
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
                                    <li class="text-danger" v-for="(error, index) of v$.form.name.$errors" :key="index">
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

                    <button wire:click.prevent="store()" v-show="!editMode" @click="save(form)"
                            :disabled="form.processing"
                            type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button wire:click.prevent="store()" v-show="editMode" @click="update(form)" type="button"
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
                    maxLength: maxLength(1),
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('product-types.validate-code', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
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
                            const resp = await axios.get(route('product-types.validate-name', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                }
            }
        }

    },

    data() {
        return {
            columns: [
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
                    code: 'CÓDIGO',
                    name: 'NOMBRE',
                    comments: 'COMENTARIOS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: 'ACCIONES',
                },

                uniqueKey: "code",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['code', 'name', 'created_by', 'created_at', 'updated_at'],
                templates: {
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
                code: '',
                name: '',
                comments: null
            },
            errors: {},
            tableData: this.data,
            title: 'Crear tipo de producto'
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
                code: '',
                name: '',
                comments: null
            }
            this.title = 'Crear tipo de producto'
            this.v$.$reset()
        },

        save: function (data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                axios.post(route('products-types.store', data)).then(res => {
                    this.$swal({

                        icon: 'success',
                        title: 'Tipo de producto agregado correctamente!',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                    this.closeModal();
                    this.tableData = res.data;
                }).catch(err => {
                    this.errors = err.response.data.errors
                    this.$swal({

                        icon: 'error',
                        title: 'Ups..',
                        text: ' hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                })
            }

        },

        deleteRow: function (row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, Eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('products-types.destroy', row)).then(resp => {
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
            this.title = 'Editar ' + data.name
        },

        update: function (row) {
            axios.put(route('products-types.update', row.code), row).then(resp => {
                this.closeModal();
                this.tableData = resp.data;

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => {
                this.errors = err.response.data.errors
                this.$swal({

                    icon: 'error',
                    title: 'Ups.. ',
                    text: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: false,
                    timer: 6000,
                });
            })
        },
    }
}
</script>

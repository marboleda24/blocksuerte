<template>
    <div>
        <Head title="Automatización - posición arancelaria"/>

        <portal to="application-title">
            Automatización - posición arancelaria
        </portal>


        <portal to="actions">
            <button class="btn btn-primary ml-4" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>

                Agregar query
            </button>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
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
                                <a href="javascript:void(0)" @click="deleteRow(row.id)"
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
                                Descripción
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="$v.form.description.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': $v.form.description.$error }" placeholder="Descripción"/>

                            <template v-if="$v.form.description.$error">
                                <div v-if="!$v.form.description.required" class="text-theme-6 mt-2">
                                    Campo obligatorio
                                </div>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Query
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="$v.form.query.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': $v.form.query.$error }" placeholder="Query"/>

                            <template v-if="$v.form.query.$error">
                                <div v-if="!$v.form.query.required" class="text-theme-6 mt-2">
                                    Campo obligatorio
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)" :disabled="form.processing"
                            type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click.prevent="update(form)" type="button"
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
import {required} from '@/utils/i18n-validators';
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        queries: Array
    },

    components: {
        JetDialogModal,
        Head,
    },

    validations() {
        return {
            form: {
                description: {required},
                query: {required},
            }
        }

    },

    data() {
        return {
            table: {
                data: this.queries,
                columns: [
                    'id',
                    'description',
                    'query',
                    'created_id',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        id: '#',
                        description: 'NOMBRE',
                        query: 'COMENTARIOS',
                        created_id: 'CREADO POR',
                        created_at: 'CREADO',
                        updated_at: 'ACTUALIZADO',
                        actions: '',
                    },
                    uniqueKey: "code",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['id', 'description', 'query', 'created_id'],
                    templates: {
                        created_id: function (h, row) {
                            return row.createdby.name
                        },
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                        },
                        updated_at: function (h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY');
                        }
                    }
                }

            },
            title: 'Agregar Query',
            editMode: false,
            isOpen: false,
            form: {
                description: '',
                query: '',
                processing: false
            },
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
            this.$v.form.$reset()
        },

        reset() {
            this.form = {
                description: '',
                query: '',
                processing: false
            }
            this.title = 'Agregar query'
            this.$v.form.$reset()
        },

        save(form) {
            this.$v.form.$touch();
            if (this.$v.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                this.form.processing = true
                axios.post(route('automation.tariff-position-query.store', form)).then(res => {
                    this.$swal.close()
                    this.closeModal();
                    this.table.data = res.data;
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: 'Ups.. hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                    console.log(err.data)
                })
            }
        },

        update(row) {
            axios.put(route('automation.tariff-position-query.update', row.id), row).then(resp => {
                this.closeModal();
                this.table.data = resp.data;

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            })
        },

        edit(row) {
            this.form = Object.assign({}, row);
            this.editMode = true;
            this.openModal();
            this.title = 'Editar query'
        },

        deleteRow(row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, Eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('automation.tariff-position-query.destroy', row)).then(resp => {
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
        }
    }
}
</script>

<style scoped>

</style>

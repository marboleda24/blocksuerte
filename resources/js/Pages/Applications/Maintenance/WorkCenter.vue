<template>
    <div>
        <Head title="Centros de trabajo - Mantenimiento"/>

        <portal to="application-title">
            Centros de trabajo - Mantenimiento
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Crear Centro de trabajo
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
                                <a href="javascript:void(0)"
                                   @click="edit(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="destroy(row.id)"
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
                                    <li class="text-danger" v-for="(error, index) of v$.form.name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                CT
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.ct.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.ct.$error }" placeholder="Nombre"/>

                            <template v-if="v$.form.ct.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.ct.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>


                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Comentarios
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea v-model.trim="form.comments" class="form-control resize-none" cols="30" rows="5"></textarea>
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
import {required} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es');

const CancelToken = axios.CancelToken;
let source;

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        work_centers: Array
    },

    components: {
        JetDialogModal,
        Head
    },

    validations() {
        return {
            form: {
                name: {
                    required,
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('maintenance.work-center.validate-name', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },

                ct: {
                    required
                }
            }
        }

    },

    data() {
        return {
            table: {
                data: this.work_centers,
                columns: [
                    'id',
                    'name',
                    'ct',
                    'comments',
                    'created_by',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        id: '#',
                        name: 'NOMBRE',
                        ct: "CENTRO DE TRABAJO",
                        comments: 'COMENTARIOS',
                        created_by: 'CREADO POR',
                        created_at: 'CREADO EL',
                        updated_at: 'ACTUALIZADO EN',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['id', 'name', 'ct', 'comments', 'created_by', 'created_at', 'updated_at'],
                    templates: {
                        created_by: function (h, row) {
                            return row.created_by.name
                        },
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY hh:mm a')
                        },
                        updated_at: function (h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY hh:mm a')
                        },
                    }
                },
            },
            form: {
                name: '',
                ct: '',
                comments: '',
                processing: false
            },
            isOpen: false,
            editMode: false,
            title: 'Centros de trabajo'
        }
    },

    methods: {
        openModal() {
            this.isOpen = true
        },

        closeModal() {
            this.form = {
                name: '',
                ct: '',
                comments: '',
                processing: false
            }
            this.isOpen = false
            this.editMode = false
            this.v$.form.$reset()
        },

        save(form) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            }else{
                this.form.processing = true;

                axios.post(route('maintenance.work-center.store'), form).then(resp => {
                    this.table.data = resp.data
                    this.closeModal();
                    this.v$.form.$reset()
                    this.form.processing = false;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Centro de trabajo creado con éxito",
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar'
                    });
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud. Mensaje de error: '+ err.data,
                        confirmButtonText: 'Aceptar',
                    });
                    this.form.processing = false;
                })
            }
        },

        edit(row) {
            this.form = Object.assign({}, row);
            this.editMode = true
            this.openModal()
        },

        destroy(id) {
            this.$swal({
                title: '¿Eliminar Centro de trabajo?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, Eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('maintenance.work-center.destroy', id)).then(resp => {
                        this.table.data = resp.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Centro de trabajo eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error eliminando el Centro de trabajo.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },

        update(form) {
            axios.put(route('maintenance.work-center.update', form.id), form).then(resp => {
                this.table.data = resp.data;
                this.closeModal()

                this.$swal({
                    title: '¡Éxito!',
                    text: "Centro de trabajo actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                })
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error actualizando el Centro de trabajo.',
                    confirmButtonText: 'Aceptar',
                });
            });
        },
    }
}
</script>


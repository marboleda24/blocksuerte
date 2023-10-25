<template>
    <div>
        <Head title="Maquinas"/>

        <portal to="application-title">
            Maquinas
        </portal>

        <portal to="actions">
            <button class="btn btn-primary ml-4" @click="openModal()">
                <span>
                    <font-awesome-icon icon="plus" class="mr-2"/>
                </span>
                Agregar Maquina
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
                                   @click="deleteRow(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=md>
                <template #title>
                    Crear / Actualizar
                </template>

                <template #content>
                    <div class="p-2">
                        <div class="mb-4">
                            <label>Referencia</label>
                            <input class="form-control" type="text" v-model="form.reference"/>
                            <jet-input-error v-if="errors.reference"
                                             :message="errors.reference[0]"
                                             class="mt-2"/>
                        </div>
                        <div class="mb-4">
                            <label>Marca</label>
                            <input class="form-control" type="text" v-model="form.brand"/>
                            <jet-input-error v-if="errors.brand" :message="errors.brand[0]" class="mt-2"/>
                        </div>
                        <div class="mb-4">
                            <label>BTU/h</label>
                            <input class="form-control" type="number" v-model="form.btu_tc"/>
                            <jet-input-error v-if="errors.btu_tc" :message="errors.btu_tc[0]" class="mt-2"/>
                        </div>
                        <div class="mb-4">
                            <label>Kcal/h</label>
                            <input class="form-control" type="number" v-model="form.kcal_tc"/>
                            <jet-input-error v-if="errors.kcal_tc" :message="errors.kcal_tc[0]" class="mt-2"/>
                        </div>
                        <div class="mb-4">
                            <label>Tipo</label>
                            <input class="form-control" type="text" v-model="form.type"/>
                            <jet-input-error v-if="errors.type" :message="errors.type[0]" class="mt-2"/>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)" :disabled="form.processing" type="submit"
                            class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click.prevent="update(form)" type="button" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";

export default {
    props: {
        machines: Array
    },

    components: {
        JetDialogModal,
        JetInput,
        JetLabel,
        JetInputError,
        Head
    },

    data() {
        return {
            columns: [
                'id',
                'reference',
                'brand',
                'btu_tc',
                'kcal_tc',
                'actions'
            ],

            options: {
                headings: {
                    id: '#',
                    reference: 'REFERENCIA',
                    brand: 'MARCA',
                    btu_tc: 'BTU/H',
                    kcal_tc: 'KCAL/H',
                    actions: 'ACCIONES',
                },

                clientSorting: false,
                sortable: ['id', 'reference', 'brand', 'btu_tc', 'kcal_tc'],
            },
            tableData: this.machines,
            editMode: false,
            isOpen: false,
            form: {
                reference: null,
                brand: null,
                btu_tc: null,
                kcal_tc: null,
                type: null
            },
            errors: {},
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
        },

        save: function (data) {
            axios.post(route('env-management.machines.store'), data)
                .then(res => {
                    this.reset();
                    this.closeModal();
                    this.editMode = false;
                    this.errors = {};
                    this.tableData = res.data;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "La maquina ha sido creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                this.errors = err.response.data.errors,
                    console.log(err.response)
            })
        },

        reset: function () {
            this.form = {
                reference: null,
                brand: null,
                btu_tc: null,
                kcal_tc: null,
                type: null
            }
        },

        edit: function (data) {
            this.form = Object.assign({}, data);
            this.editMode = true;
            this.openModal();
        },

        update: function (data) {
            axios.post(route('env-management.machines.update', data.id), data).then(resp => {
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
            }).catch(err => {
                this.errors = err.response.data.errors,
                    console.log(err.response)
            });
        },

        deleteRow: function (data) {
            this.$swal({
                title: '¿Eliminar maquina?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(route('env-management.machines.delete', data.id)).then(resp => {
                        this.tableData = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Registro eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err.response)
                    });
                }
            });
        },
    }
}
</script>

<template>
    <div>
        <Head title="Accesos remotos"/>

        <portal to="application-title">
            Accesos remotos
        </portal>

        <portal to="actions" >
            <button class="btn btn-primary ml-4" @click="openModal()" data-target="#open_modal">
                <span>
                    <font-awesome-icon icon="plus" class="mr-2"/>
                </span>
                Agregar acceso
            </button>
        </portal>

        <div>
            <div class="pos intro-y grid grid-cols-12 gap-5 mt-5">
                <a :href="row.url" target="_blank"  class="intro-x cursor-pointer box relative flex items-center p-5 zoom-in col-span-12 sm:col-span-4 xxl:col-span-3" v-for="row in remote_access_data" v-bind:key="row.id">
                    <div class="flex-none image-fit mr-2">
                        <font-awesome-icon :icon="row.icon" size="2x" v-if="row.icon"/>
                        <font-awesome-icon icon="link" size="2x" v-else/>
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="font-medium text-base flex items-center">{{row.name}}</div>
                        <div class="w-full truncate text-gray-600">{{row.url}}</div>
                    </div>
                </a>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    Nuevo acceso remoto
                </template>

                <template #content>
                    <div class="p-3">
                        <div class="mb-2">
                            <jet-label for="name" value="Nombre" />
                            <jet-input id="name" type="text" v-model="form.name" autofocus />
                            <jet-input-error v-if="errors.name" :message="errors.name[0]" class="mt-2" />
                        </div>
                        <div class="mb-2">
                            <jet-label for="url" value="Url" />
                            <jet-input id="url" type="text" v-model="form.url" />
                            <jet-input-error v-if="errors.url" :message="errors.url[0]" class="mt-2" />
                        </div>
                        <div class="mb-2">
                            <jet-label for="icon" value="Icono" />
                            <jet-input id="icon" type="text" v-model="form.icon" />
                            <jet-input-error v-if="errors.icon" :message="errors.icon[0]" class="mt-2" />
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)" :disabled="form.processing" type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click="update(form)" type="button" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>

            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import {Head} from '@inertiajs/vue3'

export default {
    metaInfo: {
        title: 'Accesos remotos'
    },

    components: {
        JetDialogModal,
        JetInput,
        JetLabel,
        JetInputError,
        Head
    },

    props: {
        remote_access: Array,
    },

    data() {
        return {
            editMode: false,
            isOpen: false,
            form:{
                name: null,
                url: null,
                icon: null
            },
            errors: {},
            remote_access_data: this.remote_access
        }
    },

    methods: {
        openModal: function () {
            this.isOpen = true;
        },

        closeModal: function () {
            this.isOpen = false;
            this.reset();
            this.editMode=false;
        },
        save: function (data) {
            axios.post('remote_access/save', data).then(res => {
                this.reset();
                this.closeModal();
                this.editMode = false;
                this.errors = {};
                this.remote_access_data = res.data;

                this.$swal({
                    title: '¡Éxito!',
                    text: "Acceso remoto creado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => this.errors = err.response.data.errors)
        },
        reset: function () {
            this.form = {
                name: null,
                url: null,
                icon: null
            }
        },
        edit: function (data) {
            this.form = Object.assign({}, data);
            this.editMode = true;
            this.openModal();
        },
        update: function (data) {
            data._method = 'PUT';
            this.$inertia.post('/posts/' + data.id, data)
            this.reset();
            this.closeModal();
        },
        deleteRow: function (data) {
            this.$swal({
                title: '¿Eliminar acceso?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    data._method = 'DELETE';
                    this.$inertia.post('/remote_access/delete/' + data.id, data)
                    this.reset();
                }
            });
        },

        getResults(page = 1) {
            this.$inertia.visit('remote_access?page=' + page);
        }
    },

    mounted() {
        console.log(this.$gates)
        console.log(this.$gates.getPermissions())
        console.log(this.$gates.getRoles())
    }
}
</script>


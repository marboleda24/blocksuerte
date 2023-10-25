<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Crear grupo de permisos
            </h2>
        </portal>

        <portal to="action_header">
            <inertia-link :href="route('permission-groups.index')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </inertia-link>
        </portal>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="save(form)">
                    <template #title>
                        Crear grupo de permisos
                    </template>

                    <template #description>
                        Este formulario te ayudará a agrupar los permisos en la plataforma.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label value="Nombre del grupo" />
                            <input class="form-control" type="text" v-model="form.name"/>
                            <jet-input-error v-if="errors.name" :message="errors.name[0]" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4" v-if="permissions.length > 0">
                            <h4>Permisos disponibles ({{ permissions.length }})</h4>
                            <ul class="grid gap-4 grid-cols-4 mt-5">
                                <li v-for="permission in permissions" v-bind:key="permission.id">
                                    <div class="flex items-center text-gray-700 dark:text-gray-500">
                                        <input id="protected" type="checkbox" class="form-check-input mr-2"  :value="permission.id" v-model="form.permissions">
                                        <label for="horizontal-remember-me" class="cursor-pointer select-none">
                                            {{permission.description}}
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-span-6 sm:col-span-4 text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600" v-else>
                            <span class="text-xl inline-block mr-1 align-middle">
                                <font-awesome-icon icon="ban"/>
                            </span>
                            <span class="inline-block align-middle mr-8">
                                <b class="capitalize">Ups!</b> No hay permisos disponibles para asociar a un grupo
                            </span>
                        </div>
                    </template>

                    <template #actions>
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing || permissions.length == 0">
                            Guardar
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>

        </div>
    </div>
</template>

<script lang="jsx">
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'

    export default {
        metaInfo: {
            title: 'Crear grupo de permisos'
        },

        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: {
            permissions: Array,
        },

        data() {
            return {
                form:{
                    name: "",
                    permissions: [],
                },
                errors: {},
                processing: false
            }
        },


        methods: {
            save: function (data) {
                this.processing = true;

                axios.post(route('permission-groups.store', data)).then(res => {
                    this.errors = {};
                    this.reset();
                    this.$swal({
                        title: '¡Éxito!',
                        text: "grupo de permisos creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.$forceUpdate();
                }).catch(err => {
                    this.errors = err.response.data.errors,
                    console.log(err);
                    this.processing = false;
                })
            },

            reset: function () {
                this.form = {
                    name: "",
                    permissions: [],
                },
                this.processing = false
            }
        }
    }
</script>

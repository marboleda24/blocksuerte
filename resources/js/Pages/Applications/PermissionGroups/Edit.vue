<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Editar grupo de permisos
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
                        Modificar grupo de permisos
                    </template>

                    <template #description>
                        Este formulario te ayudará a actualizar la información de un grupo de permisos existente en la aplicación.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="name" value="Nombre del grupo" />
                            <jet-input type="text" v-model="form.name"/>
                            <jet-input-error v-if="errors.name" :message="errors.name[0]" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4" v-if="permissionGroup.permissions.length > 0">
                            <h4>Permisos asociados ({{ permissionGroup.permissions.length }})</h4>
                            <ul class="grid gap-4 grid-cols-4 mt-5">
                                <li v-for="permission in permissionGroup.permissions" v-bind:key="permission.id">
                                    <div class="flex items-center text-gray-700 dark:text-gray-500">
                                        <input id="protected" type="checkbox" class="form-check-input mr-2" :value="permission.id" v-model="form.assigned_perms">
                                        <label for="horizontal-remember-me" class="cursor-pointer select-none">
                                            <span v-if="permission.description">
                                                {{permission.description}}
                                            </span>
                                            <span v-else>
                                                {{permission.name }}
                                            </span>
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
                                <b class="capitalize">Ups!</b> Este grupo no cuenta con permisos asociados
                            </span>
                        </div>

                        <div class="col-span-6 sm:col-span-4" v-if="availablePermissions.length > 0">
                            <h4>Permisos disponibles ({{ availablePermissions.length }})</h4>

                            <ul class="grid gap-4 grid-cols-4 mt-5">
                                <li v-for="permission in availablePermissions" v-bind:key="permission.id">
                                    <div class="flex items-center text-gray-700 dark:text-gray-500">
                                        <input type="checkbox" class="form-check-input mr-2" :value="permission.id" v-model="form.avail_perms">
                                        <label for="horizontal-remember-me" class="cursor-pointer select-none">
                                            <span v-if="permission.description">
                                                {{permission.description}}
                                            </span>
                                            <span v-else>
                                                {{permission.name }}
                                            </span>
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
                                <b class="capitalize">Ups!</b> No hay permisos disponibles para asociar
                            </span>
                        </div>
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
            title: 'Editar grupo de permisos'
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
            permissionGroup: Object,
            availablePermissions: Object
        },

        data() {
            return {
                form:{
                    name: this.permissionGroup.name,
                    assigned_perms: [],
                    avail_perms: []
                },
                errors: {},
                processing: false
            }
        },


    }
</script>

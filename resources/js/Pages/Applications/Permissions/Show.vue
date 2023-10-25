<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Mostrar permiso
            </h2>
        </portal>

        <portal to="action_header">
            <inertia-link :href="route('permissions.index')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </inertia-link>
        </portal>
        <div>
            <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                <div class="col-span-12 lg:col-span-12">
                    <div class="intro-y box">
                        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">DETALLES DEL PERMISO</h2>
                        </div>
                        <div class="p-5">
                            <p>
                                <strong>NOMBRE:</strong> {{permission.name}}
                            </p>
                            <p>
                                <strong>TIPO:</strong> {{ permission.guard_name }}
                            </p>
                            <p>
                                <strong>DESCRIPCIÓN:</strong> {{ permission.description }}
                            </p>
                            <p>
                                <strong>CREADO EN:</strong> {{ permission.created_at }}
                            </p>
                            <p>
                                <strong>ACTUALIZADO EN:</strong> {{ permission.updated_at }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <div class="intro-y box">
                        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">USUARIOS ASOCIADOS ({{ permissionUsers.length }})</h2>
                        </div>
                        <div class="p-5">
                            <div class="col-span-12 lg:col-span-3" v-if="permissionUsers.length > 0">
                                <ul class="grid gap-4 grid-cols-4">
                                    <li></li>
                                </ul>
                            </div>
                            <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600" v-else>
                                <span class="text-xl inline-block mr-1 align-middle">
                                    <font-awesome-icon icon="ban"/>
                                </span>
                                <span class="inline-block align-middle mr-8">
                                    <b class="capitalize">Ups!</b> Este permiso no tiene usuarios asociados
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <div class="intro-y box">
                        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">ROLES ASOCIADOS ({{ permission_roles.length }})</h2>
                        </div>
                        <div class="p-5">
                            <div class="col-span-12 lg:col-span-3" v-if="permission_roles.length > 0">
                                <ul class="grid gap-4 grid-cols-4">
                                    <li v-for="permission_role in permission_roles" v-bind:key="permission_role.id">
                                        <inertia-link :href="route('roles.show', permission_role.id)" class="no-underline hover:underline">
                                            {{ permission_role.name }}
                                        </inertia-link>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600" v-else>
                                <span class="text-xl inline-block mr-1 align-middle">
                                    <font-awesome-icon icon="ban"/>
                                </span>
                                <span class="inline-block align-middle mr-8">
                                    <b class="capitalize">Ups!</b> Este permiso no tiene roles asociados
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</template>

<script lang="jsx">
    import {defineComponent} from "vue";

    export default defineComponent({
        metaInfo: {
            title: 'Mostrar permiso'
        },

        props: {
            permission: Object,
            permissionUsers: Array,
            permission_roles: Array
        },
    })
</script>

<template>
    <div>
        <Head :title="user.name"/>

        <portal to="application-title">
            {{ user.name }}
        </portal>

        <portal to="actions">
            <Link :href="route('users.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y grid grid-cols-12 gap-5 mt-5">
                <div class="col-span-12 lg:col-span-6">
                    <div class="intro-y box h-full">
                        <div
                            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">DETALLES DEL PERMISO</h2>
                        </div>
                        <div class="p-5">
                            <p>
                                <strong>NOMBRE:</strong> {{ user.name }}
                            </p>
                            <p>
                                <strong>CORREO ELECTRÓNICO:</strong> {{ user.email }}
                            </p>
                            <p>
                                <strong>USUARIO:</strong> {{ user.username }}
                            </p>
                            <p>
                                <strong>CREADO EN:</strong> {{ $h.formatDate(user.created_at, 'YYYY-MM-DD hh:mm a') }}
                            </p>
                            <p>
                                <strong>ACTUALIZADO EN:</strong> {{
                                    $h.formatDate(user.updated_at, 'YYYY-MM-DD hh:mm a')
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <div class="intro-y box h-full">
                        <div
                            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">ROLES ASOCIADOS ({{ user.roles.length }})</h2>
                        </div>
                        <div class="p-5">
                            <div class="col-span-12 lg:col-span-3" v-if="user.roles.length > 0">
                                <ul class="grid gap-4 grid-cols-4">
                                    <li v-for="role in user.roles" v-bind:key="role.id">
                                        <inertia-link :href="route('roles.show', role.id)"
                                                      class="no-underline hover:underline">
                                            {{ role.name }}
                                        </inertia-link>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600" v-else>
                                <span class="text-xl inline-block mr-1 align-middle">
                                    <font-awesome-icon icon="ban"/>
                                </span>
                                <span class="inline-block align-middle mr-8">
                                    <b class="capitalize">Ups!</b> Este usuario no tiene roles asociados
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-12">
                    <div class="intro-y box">
                        <div
                            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">PERMISOS ASOCIADOS ({{
                                    userPermissions.length
                                }})</h2>
                        </div>
                        <div class="p-5">
                            <div class="col-span-12 lg:col-span-3" v-if="userPermissions.length > 0">
                                <ul class="grid gap-4 grid-cols-4">
                                    <li v-for="permission in userPermissions" v-bind:key="permission.id">
                                        <inertia-link :href="route('permissions.show', permission.id)"
                                                      class="no-underline hover:underline">
                                            {{ permission.name }}
                                        </inertia-link>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600" v-else>
                                <span class="text-xl inline-block mr-1 align-middle">
                                    <font-awesome-icon icon="ban"/>
                                </span>
                                <span class="inline-block align-middle mr-8">
                                    <b class="capitalize">Ups!</b> Este usuario no tiene permisos asociados
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
import {Head, Link} from '@inertiajs/vue3'

export default {
    components: {
        Head,
        Link
    },

    props: {
        user: Object,
        userPermissions: Array,
    },
}
</script>

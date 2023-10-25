<template>
    <div>
        <Head :title="`Rol ${role.description}`"/>

        <portal to="application-title">
            Rol {{ role.description }}
        </portal>

        <portal to="actions">
            <Link :href="route('roles.index')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="mt-2 flex flex-col sm:flex-row">
                            Nombre:
                            <label class="text-gray-600 ml-2">
                                {{ role.description }}
                            </label>
                        </div>

                        <div class="mt-2 flex flex-col sm:flex-row">
                            Identificador:
                            <label class="text-gray-600 ml-2">
                                {{ role.name }}
                            </label>
                        </div>

                        <div class="mt-2 flex flex-col sm:flex-row">
                            Tipo:
                            <label class="text-gray-600 ml-2">
                                {{ role.guard_name }}
                            </label>
                        </div>

                        <div class="mt-2 flex flex-col sm:flex-row">
                            Creado el:
                            <label class="text-gray-600 ml-2">
                                {{  $h.formatDate(role.created_at) }}
                            </label>
                        </div>

                        <div class="mt-2 flex flex-col sm:flex-row">
                            Actualizado el:
                            <label class="text-gray-600 ml-2">
                                {{ $h.formatDate(role.updated_at) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5 mt-5">
                <div class="intro-y box">
                    <div class="p-5 flex flex-col-reverse sm:flex-row text-gray-600 border-b border-gray-200 dark:border-dark-1">
                        Permisos Asociados
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-4" v-if="rolePermissions.length > 0">
                            <label class="text-gray-600 ml-2" v-for="permission in rolePermissions">
                                {{ permission.description }}
                            </label>
                        </div>
                        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500" v-else>
                            <span class="text-xl inline-block mr-1 align-middle">
                                <font-awesome-icon icon="ban"/>
                            </span>
                            <span class="inline-block align-middle mr-8">
                                <b class="capitalize">Ups!</b> Este Rol no tiene permisos asociados
                            </span>
                        </div>
                    </div>
                </div>

                <div class="intro-y box">
                    <div class="p-5 flex flex-col-reverse sm:flex-row text-gray-600 border-b border-gray-200 dark:border-dark-1">
                        Usuarios Asociados
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-4" v-if="roleUsers.length > 0">
                            <label class="text-gray-600 ml-2" v-for="user in roleUsers">
                                {{ user.name }}
                            </label>
                        </div>
                        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500" v-else>
                            <span class="text-xl inline-block mr-1 align-middle">
                                <font-awesome-icon icon="ban"/>
                            </span>
                            <span class="inline-block align-middle mr-8">
                                <b class="capitalize">Ups!</b> Este Rol no tiene usuarios asociados
                            </span>
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
        role: Object,
        rolePermissions: Array,
        roleUsers: Array
    },
}
</script>

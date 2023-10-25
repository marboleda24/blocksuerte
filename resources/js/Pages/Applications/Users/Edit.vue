<template>
    <div>
        <Head :title="`Editar ${user.name}`"/>

        <portal to="application-title">
            Editar {{ user.name }}
        </portal>

        <portal to="actions">
            <Link :href="route('users.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <form>
                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                    <div class="col-span-12 lg:col-span-6">
                        <div class="intro-y box h-full">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto">
                                    {{ `ROLES ASOCIADOS (${user.roles.length})` }}
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-3" v-if="user.roles.length > 0">
                                    <div class="flex items-center mr-2 mt-2" v-for="role in user.roles"
                                         v-bind:key="role.id">
                                        <input :id="role.id" type="checkbox" class="form-check-input mr-2"
                                               v-model="form.assigned_roles" :value="role.id"/>
                                        <label class="cursor-pointer select-none"> {{ role.name }}</label>
                                    </div>
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

                    <div class="col-span-12 lg:col-span-6">
                        <div class="intro-y box h-full">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto">
                                    ROLES DISPONIBLES ({{availableRoles.length}})</h2>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-3" v-if="availableRoles.length > 0">
                                    <div class="flex items-center mr-2 mt-2"
                                         v-for="role in availableRoles" v-bind:key="role.id">
                                        <input :id="role.id" type="checkbox" class="form-check-input mr-2"
                                               v-model="form.avail_roles" :value="role.id"/>
                                        <label class="cursor-pointer select-none"> {{ role.name }}</label>
                                    </div>
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
                </div>

                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                    <div class="col-span-12 lg:col-span-12">
                        <div class="intro-y">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto">
                                    PERMISOS HEREDADOS
                                    ({{ inheritedPermissions.length }})
                                    <small class="font-sm">
                                        (<em>Solo lectura</em>)
                                    </small>
                                </h2>
                            </div>
                            <div class="p-5 grid grid-cols-2 gap-6 mt-5">
                                <div class="intro-y box" v-for="permissionGroup in inheritedPermissionsGroups"
                                     v-bind:key="permissionGroup.id">
                                    <div
                                        class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                        <h2 class="font-medium text-base mr-auto uppercase">
                                            {{ permissionGroup.name + '(' + permissionGroup.permissions.length + ')' }}
                                        </h2>
                                    </div>
                                    <div class="p-5">
                                        <div class="grid grid-cols-3" v-if="permissionGroup.permissions.length > 0">
                                            <div class="flex items-center mr-2 mt-2"
                                                 v-for="permission in permissionGroup.permissions"
                                                 v-bind:key="permission.id">
                                                <input :id="permission.id" type="checkbox" class="form-check-input mr-2"
                                                       v-model="form.inh_perms" :value="permission.id"
                                                       onclick="return false;" checked="checked"/>
                                                <label class="cursor-pointer select-none">
                                                    {{ permission.description }}</label>
                                            </div>
                                        </div>
                                        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600"
                                             v-else>
                                            <span class="text-xl inline-block mr-1 align-middle">
                                                <font-awesome-icon icon="ban"/>
                                            </span>
                                            <span class="inline-block align-middle mr-8">
                                                <b class="capitalize">Ups!</b> Este grupo no tiene permisos asociados
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                    <div class="col-span-12 lg:col-span-12">
                        <div class="intro-y">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto">
                                    PERMISOS ASOCIADOS
                                    ({{ associatedPermissions.length }})
                                </h2>
                            </div>
                            <div class="p-5 grid grid-cols-2 gap-6 mt-5" v-if="associatedPermissions.length > 0">
                                <div class="intro-y box"
                                     v-for="permissionGroup in associatedPermissionsGroups"
                                     v-bind:key="permissionGroup.id">
                                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                        <h2 class="font-medium text-base mr-auto uppercase">
                                            {{ permissionGroup.name + '(' + permissionGroup.permissions.length + ')' }}</h2>
                                    </div>
                                    <div class="p-5">
                                        <div class="grid grid-cols-3" v-if="permissionGroup.permissions.length > 0">
                                            <div class="flex items-center mr-2 mt-2"
                                                 v-for="permission in permissionGroup.permissions"
                                                 v-bind:key="permission.id">
                                                <input :id="permission.id" type="checkbox" class="form-check-input mr-2"
                                                       v-model="form.assigned_perms" :value="permission.id"/>
                                                <label class="cursor-pointer select-none">
                                                    {{ permission.description }}</label>
                                            </div>
                                        </div>
                                        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600"
                                             v-else>
                                            <span class="text-xl inline-block mr-1 align-middle">
                                                <font-awesome-icon icon="ban"/>
                                            </span>
                                            <span class="inline-block align-middle mr-8">
                                                <b class="capitalize">Ups!</b> Este grupo no tiene permisos asociados
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5" v-else>
                                <div class="col-span-12 lg:col-span-12">
                                    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600">
                                        <span class="text-xl inline-block mr-1 align-middle">
                                            <font-awesome-icon icon="ban"/>
                                        </span>
                                        <span class="inline-block align-middle mr-8">
                                            <b class="capitalize">Ups!</b> Este usuario no cuenta con permisos asociados
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                    <div class="col-span-12 lg:col-span-12">
                        <div class="intro-y">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto">
                                    PERMISOS DISPONIBLES
                                    ({{ availablePermissions.length }})
                                </h2>
                            </div>
                            <div class="p-5 grid grid-cols-2 gap-6 mt-5" v-if="availablePermissionsGroups.length > 0">
                                <div class="intro-y box" v-for="permissionGroup in availablePermissionsGroups"
                                     v-bind:key="permissionGroup.id">
                                    <div
                                        class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                        <h2 class="font-medium text-base mr-auto uppercase">
                                            {{ permissionGroup.name + '(' + permissionGroup.permissions.length + ')' }}</h2>
                                    </div>
                                    <div class="p-5">
                                        <div class="grid grid-cols-3" v-if="permissionGroup.permissions.length > 0">
                                            <div class="flex items-center mr-2 mt-2"
                                                 v-for="permission in permissionGroup.permissions"
                                                 v-bind:key="permission.id">
                                                <input :id="permission.id"
                                                       type="checkbox"
                                                       class="form-check-input mr-2"
                                                       v-model="form.avail_perms"
                                                       :value="permission.id"/>

                                                <label class="cursor-pointer select-none">
                                                    {{ permission.description }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600"
                                             v-else>
                                            <span class="text-xl inline-block mr-1 align-middle">
                                                <font-awesome-icon icon="ban"/>
                                            </span>
                                            <span class="inline-block align-middle mr-8">
                                                <b class="capitalize">Ups!</b> Este grupo no tiene permisos asociados
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5" v-else>
                                <div class="col-span-12 lg:col-span-12">
                                    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600">
                                        <span class="text-xl inline-block mr-1 align-middle">
                                            <font-awesome-icon icon="ban"/>
                                        </span>
                                        <span class="inline-block align-middle mr-8">
                                            <b class="capitalize">Ups!</b> No hay permisos disponibles para asignar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <button click.prevent="store()" @click="update(form)" type="button" class="btn btn-primary uppercase mt-5">
                Actualizar permisos
            </button>
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
        availableRoles: Array,
        inheritedPermissions: Array,
        inheritedPermissionsGroups: Array,
        associatedPermissions: Array,
        associatedPermissionsGroups: Array,
        availablePermissions: Array,
        availablePermissionsGroups: Array
    },

    data() {
        return {
            form: {
                id: this.user.id,
                assigned_roles: [],
                avail_roles: [],
                assigned_perms: [],
                avail_perms: [],
                inh_perms: [],
                name: null,
                comments: null,
            },
            errors: {},
        }
    },

    methods: {
        update: function (data) {
            axios.put(route('users.update', data.id), data).then(res => {
                this.errors = {};
                this.$swal({
                    title: '¡Éxito!',
                    text: "Permisos actualizados con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
                this.$inertia.reload()
            }).catch(err => {
                console.log(err);
            })
        },
    },

    mounted() {
        this.user.roles.forEach(element => {
            this.form.assigned_roles.push(element.id);
        });

        this.inheritedPermissionsGroups.forEach(permissionGroup => {
            permissionGroup.permissions.forEach(element => {
                this.form.inh_perms.push(element.id);
            });
        });

        this.associatedPermissions.forEach(permission => {
            this.form.assigned_perms.push(permission.id)
        })
    }
}
</script>

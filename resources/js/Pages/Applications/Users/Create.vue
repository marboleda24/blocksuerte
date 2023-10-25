<template>
    <div>
        <Head title="Nuevo Usuario"/>

        <portal to="application-title">
            Nuevo Usuario
        </portal>

        <portal to="actions">
            <Link :href="route('users.index')" class="btn btn-primary">
                <font-awesome-icon class="mr-2" icon="arrow-left"/>
                Atrás
            </Link>
        </portal>
    </div>

    <div class="intro-y box">
        <div class="p-5">
            <div class="grid grid-cols-4 gap-5">
                <div>
                    <label class="form-label">
                        Nombre Completo
                    </label>
                    <div class="input-group">
                        <input v-model="form.name"
                               :class="{ 'border-danger': v$.form.name.$error }"
                               class="form-control"
                               placeholder="Nombre Completo"
                               type="text"/>
                    </div>
                </div>

                <div>
                    <label class="form-label">
                        Usuario
                    </label>
                    <div class="input-group">
                        <input v-model="form.users"
                               :class="{ 'border-danger': v$.form.users.$error }"
                               class="form-control"
                               placeholder="usuario"
                               type="text"/>
                    </div>
                </div>

                <div>
                    <label class="form-label">
                        Contraseña
                    </label>
                    <div class="input-group">
                        <input v-model="form.password"
                               :class="{ 'border-danger': v$.form.password.$error }"
                               class="form-control"
                               placeholder="Contraseña"
                               type="password"/>
                    </div>
                    <template v-if="v$.form.password.$error">
                        <ul class="mt-1">
                            <li v-for="(error, index) of v$.form.password.$errors" :key="index"
                                class="text-danger">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div>
                    <label class="form-label">
                        Confirmar Contraseña
                    </label>
                    <div class="input-group">
                        <input v-model="form.password_confirmation"
                               :class="{ 'border-danger': v$.form.password_confirmation.$error }"
                               class="form-control"
                               placeholder="confirmacion Contraseña"
                               type="password"/>
                    </div>
                    <template v-if="v$.form.password_confirmation.$error">
                        <ul class="mt-1">
                            <li v-for="(error, index) of v$.form.password_confirmation.$errors" :key="index"
                                class="text-danger">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div>
                    <label class="form-label">
                        Correo Electrónico (42)
                    </label>
                    <div class="input-group">
                        <input v-model="form.email"
                               :class="{ 'border-danger': v$.form.email.$error }"
                               class="form-control"
                               placeholder="Correo Electrónico"
                               type="text">
                    </div>
                    <template v-if="v$.form.email.$error">
                        <ul class="mt-1">
                            <li v-for="(error, index) of v$.form.email.$errors" :key="index"
                                class="text-danger">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>

        <div
            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
                ROLES DISPONIBLES ({{ availableRoles.length }})</h2>
        </div>

        <div class="p-5">
            <div v-if="availableRoles.length > 0" class="grid grid-cols-3">
                <div v-for="role in availableRoles"
                     v-bind:key="role.id" class="flex items-center mr-2 mt-2">
                    <input :id="role.id" v-model="form.avail_roles" :value="role.id"
                           class="form-check-input mr-2" type="checkbox"/>
                    <label class="cursor-pointer select-none"> {{ role.name }}</label>
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
                            ({{ Permission.length }})
                        </h2>
                    </div>
                    <div v-if="Permission.length > 0" class="p-5 grid grid-cols-2 gap-6 mt-5">
                        <div v-for="permissionGroup in model.data" v-bind:key="permissionGroup.id"
                             class="intro-y box">
                            <div
                                class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                                <h2 class="font-medium text-base mr-auto uppercase">
                                    {{ permissionGroup.name + '(' + permissionGroup.permissions.length + ')' }}</h2>
                            </div>
                            <div class="p-5">
                                <div v-if="permissionGroup.permissions.length > 0" class="grid grid-cols-3">
                                    <div v-for="permission in permissionGroup.permissions"
                                         v-bind:key="permission.id"
                                         class="flex items-center mr-2 mt-2">
                                        <input :id="permission.id"
                                               v-model="form.avail_perms"
                                               :value="permission.id"
                                               class="form-check-input mr-2"
                                               type="checkbox"/>

                                        <label class="cursor-pointer select-none">
                                            {{ permission.description }}
                                        </label>
                                    </div>
                                </div>
                                <div v-else class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-600">
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
                    <div v-else class="p-5">
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

        <div class="intro-y col-span-12 flex items-center justify-center sm:justify-start mt-5">
            <button class="btn btn-primary w-24 ml-2" @click="store(form)">
                guardar
                <font-awesome-icon class="ml-1" icon=""/>
            </button>
        </div>
    </div>
</template>
<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {email, helpers, minLength, required} from '@/utils/i18n-validators'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {sameAs} from "../../../utils/i18n-validators";

const {withAsync} = helpers
const CancelToken = axios.CancelToken;
let source;
export default {
    setup() {
        return {v$: useVuelidate()}
    },
    components: {
        Head,
        Link,
        JetDialogModal,
    },
    props: {
        Permission: Array,
        availableRoles: Array,
    },
    validations() {
        return {
            form: {
                email: {email, required},
                name: {required},
                users: {required},
                password: {
                    required,
                    minLength: minLength(10)
                },
                avail_perms: {required},
                password_confirmation: {
                    required,
                    minLength: minLength(10),
                    sameAs: sameAs(this.form.password)
                },
            }
        }
    },
    data() {
        return {
            form: {
                name: "",
                users: "",
                password: "",
                email: "",
                avail_roles: [],
                avail_perms: [],
                password_confirmation: ''
            },
            model: {
                isOpen: false,
                data: this.Permission
            }

        }
    },
    methods: {
        openModal() {
            this.model.isOpen = true;

        },
        closeModal() {
            this.model.isOpen = false;
        },
        store(from) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                axios.post(route('store-users'), {
                    from
                }).then(res => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Usuario creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.form = {
                        name: "",
                        users: "",
                        password: "",
                        email: "",
                        occupation: "",
                        avail_perms: [],
                        password_confirmation: ''
                    }
                }).catch(err => {
                    this.errors = err.response.data.errors
                    this.$swal({
                        icon: 'error',
                        title: 'Ups..',
                        text: 'Hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: false,
                        timer: 6000,
                        confirmButtonText: 'Aceptar',
                    });
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: 'Ups..',
                    text: 'Hubo un error, verifica que toda la informacion sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            }
        },
    }
}
</script>

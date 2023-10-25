<template>
    <div>
        <Head title="Crear Rol"/>

        <portal to="application-title">
            Crear Rol
        </portal>

        <portal to="actions">
            <Link :href="route('roles.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="intro-y box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-4">
                    <div class="mt-2">
                        <label class="flex flex-col sm:flex-row">
                            Nombre
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control" v-model="form.name"
                               :class="{ 'border-danger': v$.form.name.$error }">

                        <template v-if="v$.form.name.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.name.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="mt-2">
                        <label class="flex flex-col sm:flex-row">
                            Descripción
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control" v-model="form.description"
                               :class="{ 'border-danger': v$.form.description.$error }">

                        <template v-if="v$.form.description.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.description.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="mt-2">
                        <label class="flex flex-col sm:flex-row">
                            Rol del sistema
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>
                        <input type="checkbox" class="form-check-input mt-1" v-model="form.protected">
                    </div>
                </div>
                <div class="mt-10">
                    <div id="group-accordion" class="accordion">
                        <div class="accordion-item" v-for="group in permissionGroups" v-bind:key="group.id">
                            <div :id="`group-accordion-content-${group.id}`" class="accordion-header">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-tw-toggle="collapse"
                                    :data-tw-target="`#group-accordion-collapse-${group.id}`"
                                    aria-expanded="true"
                                    :aria-controls="`group-accordion-collapse-${group.id}`"
                                >
                                    {{ `${group.name} (${group.permissions.length})` }}
                                </button>
                            </div>
                            <div
                                :id="`group-accordion-collapse-${group.id}`"
                                class="accordion-collapse collapse"
                                :aria-labelledby="`group-accordion-content-${group.id}`"
                                data-tw-parent="#group-accordion">
                                <div class="accordion-body text-slate-600 dark:text-slate-500 leading-relaxed">
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="form-check mt-2" v-for="permission in group.permissions">
                                            <input class="form-check-input" type="checkbox" v-model="form.permissions"
                                                   :value="permission.id"/>
                                            <label class="form-check-label">{{ permission.description }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row mt-5 text-center justify-center">
                    <button class="btn btn-primary" @click="save(form)">
                        CREAR ROL
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Link
    },

    props: {
        permissionGroups: Array,
        permissionsQuantity: Number
    },

    validations() {
        return {
            form: {
                name: {required},
                description: {required},
                permissions: {required, minLength: minLength(1)}
            }
        }
    },

    data() {
        return {
            form: {
                name: '',
                description: '',
                protected: false,
                guard_name: 'sanctum',
                permissions: [],
            }
        }
    },

    methods: {
        save(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Completa el formulario y agrega al menos un permiso a este Rol',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                axios.post(route('roles.store'), data).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Rol creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        timer: 1500,
                        timerProgressBar: true
                    });
                    this.$inertia.visit(route('roles.index'));
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err.data)
                })
            }
        }
    }
}
</script>

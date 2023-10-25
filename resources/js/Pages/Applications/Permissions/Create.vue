<template>
    <div>
        <Head title="Nuevo permiso"/>

        <portal to="application-title">
            Nuevo permiso
        </portal>

        <portal to="actions">
            <Link :href="route('permissions.index')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>


        <div class="max-w-8xl">
            <jet-form-section @submitted="save(form)">
                <template #title>
                    Crear permiso
                </template>

                <template #description>
                    Un permiso del sistema solo puede ser asignado a los usuarios y roles por un súper
                    administrador.
                </template>

                <template #form>
                    <div class="col-span-6 sm:col-span-4">
                        <label>Identificador</label>
                        <input type="text"
                               class="form-control"
                               :class="{'border-danger': v$.form.name.$error}"
                               v-model="form.name">

                        <template v-if="v$.form.name.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.name.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <label>Descripción</label>
                        <input type="text"
                               class="form-control"
                               :class="{'border-danger': v$.form.description.$error}"
                               v-model="form.description">

                        <template v-if="v$.form.description.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.description.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="flex items-center mt-2">
                        <input type="checkbox" class="form-check-input mr-2" v-model="form.protected"/>
                        <label>Permiso del sistema</label>
                    </div>
                </template>

                <template #actions>
                    <button class="btn btn-primary" @click="save(form)">
                        Guardar
                    </button>
                </template>
            </jet-form-section>
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
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength, helpers} from '@/utils/i18n-validators';

const CancelToken = axios.CancelToken;
let source;

const {withAsync} = helpers

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        Head,
        Link
    },

    validations() {
        return {
            form: {
                name: {
                    required,
                    isUnique: helpers.withMessage('Este identificador ya se encuentra registrado', withAsync(async (value) => {
                        if (value) {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            try {
                                return await axios.get(route('permission.validate-name', value), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                console.error(error)
                                return false
                            }
                        } else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                description: {
                    required,
                    minLength: minLength(10)
                }
            }
        }
    },

    data() {
        return {
            form: {
                name: "",
                description: "",
                protected: false,
            },
        }
    },

    methods: {
        save(data) {
            this.v$.form.$touch()
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Creando permiso…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('permissions.store'), data).then(res => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "permiso creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.$inertia.visit(route('permissions.index'));
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err);
                })
            }

        },
    }
}
</script>

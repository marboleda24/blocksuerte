<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Editar permiso
            </h2>
        </portal>

        <portal to="action_header">
            <inertia-link :href="route('permissions.index')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </inertia-link>
        </portal>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="update(form)">
                     <template #title>
                        Editar permiso
                    </template>

                    <template #description>
                        Un permiso del sistema solo puede ser asignado a los usuarios y roles por un súper administrador.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="name" value="Identificador del permiso" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"/>
                            <jet-input-error v-if="errors.name" :message="errors.name[0]" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="description" value="Descripción del permiso" />
                            <jet-input id="description" type="text" class="mt-1 block w-full" v-model="form.description"/>
                            <jet-input-error v-if="errors.description" :message="errors.description[0]" class="mt-2" />
                        </div>

                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-5">
                            <input id="protected" type="checkbox" class="form-check-input mr-2" v-model="form.protected"/>
                            <label>Permiso del sistema</label>
                        </div>
                    </template>

                    <template #actions>
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    import {defineComponent} from "vue";

    export default defineComponent({
        metaInfo: {
            title: 'Crear permiso'
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
            data: Object
        },

        data() {
            return {
                form:{
                    id: this.data.id,
                    name: this.data.name,
                    description: this.data.description,
                    protected: this.data.protected,
                },
                errors: {},
                processing: false
            }
        },

        methods:{
            update: function (data) {
                this.processing = true;

                axios.put(route('permissions.update', data.id), data).then(resp => {
                    this.errors = {};
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Permiso actualizado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.errors = err.response.data.errors,
                    console.log(err);
                    this.processing = false;
                })
            },
        }
    })
</script>

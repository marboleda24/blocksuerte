<template>
    <jet-form-section @submitted="updatePassword(form)">
        <template #title>
            Actualiza contraseña
        </template>

        <template #description>
            Asegúrese de que su cuenta esté usando una contraseña larga y dificil de adivinar para mantenerse seguro.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="current_password" value="Contraseña actual" />
                <jet-input id="current_password" type="password" class="mt-1 block w-full" v-model="form.current_password" ref="current_password" autocomplete="current-password" />
                <jet-input-error v-if="form.errors.current_password" :message="form.errors.current_password[0]" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password" value="Nueva contaseña" />
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" autocomplete="new-password" />
                <jet-input-error v-if="form.errors.password" :message="form.errors.password[0]" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password_confirmation" value="Confirmar contraseña" />
                <jet-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" autocomplete="new-password" />
                <jet-input-error v-if="form.errors.password_confirmation" :message="form.errors.password_confirmation[0]" class="mt-2" />
           </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Guardado.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar
            </jet-button>
        </template>
    </jet-form-section>
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
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: {
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                    errors: {}
                },
            }
        },

        methods: {
            updatePassword: function(data) {
                this.processing = true;

                axios.put(route('change-password.update', data)).then(res => {
                    this.errors = {};
                    this.reset();
                    this.$swal({
                        title: '¡Éxito!',
                        text: "permiso creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    this.$refs.current_password.focus()
                }).catch(err => {
                    this.form.errors = err.response.data.errors
                    this.processing = false;
                })
            },

            reset: function () {
                this.form = {
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }
                this.processing = false
            }
        },
    })
</script>

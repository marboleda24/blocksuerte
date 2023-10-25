<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <jet-dialog-modal :show="confirmingPassword" @close="confirmingPassword = false" max-width=lg>
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <div class="p-4">
                    {{ content }}

                    <div class="mt-4">
                        <jet-input type="password" class="mt-1" placeholder="Contraseña"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter.native="confirmPassword" />

                        <jet-input-error :message="form.error" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingPassword = false">
                    Cancelar
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="confirmPassword" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ button }}
                </jet-button>
            </template>
        </jet-dialog-modal>
    </span>
</template>

<script lang="jsx">
    import JetButton from './Button.vue'
    import JetDialogModal from './DialogModal.vue'
    import JetInput from './Input.vue'
    import JetInputError from './InputError.vue'
    import JetSecondaryButton from './SecondaryButton.vue'

    export default {
        props: {
            title: {
                default: 'Confirmar Contraseña',
            },
            content: {
                default: 'Por su seguridad, confirme su contraseña para continuar',
            },
            button: {
                default: 'Confirmar',
            }
        },

        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetSecondaryButton,
        },

        data() {
            return {
                confirmingPassword: false,

                form: this.$inertia.form({
                    password: '',
                    error: '',
                }, {
                    bag: 'confirmPassword',
                })
            }
        },

        methods: {
            startConfirmingPassword() {
                this.form.error = '';

                axios.get(route('password.confirmation')).then(response => {
                    if (response.data.confirmed) {
                        this.$emit('confirmed');
                    } else {
                        this.confirmingPassword = true;
                        this.form.password = '';

                        setTimeout(() => {
                            this.$refs.password.focus()
                        }, 250)
                    }
                })
            },

            confirmPassword() {
                this.form.processing = true;

                axios.post(route('password.confirm'), {
                    password: this.form.password,
                }).then(response => {
                    this.confirmingPassword = false;
                    this.form.password = '';
                    this.form.error = '';
                    this.form.processing = false;

                    this.$nextTick(() => this.$emit('confirmed'));
                }).catch(error => {
                    this.form.processing = false;
                    this.form.error = error.response.data.errors.password[0];
                });
            }
        }
    }
</script>

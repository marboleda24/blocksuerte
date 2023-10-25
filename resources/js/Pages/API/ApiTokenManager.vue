<template>
    <div>
        <!-- Generate API Token -->
        <jet-form-section @submitted="createApiToken">
            <template #title>
                Crear API Token
            </template>

            <template #description>
                Los API tokens permiten que los servicios de terceros se autentiquen con nuestra aplicación en su nombre.
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="name" value="Nombre" />
                    <input type="text" class="form-control mt-1" v-model="createApiTokenForm.name">
                    <jet-input-error v-if="createApiTokenForm.errors.name" :message="createApiTokenForm.errors.name[0]" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div class="col-span-6" v-if="availablePermissions.length > 0">
                    <jet-label for="permissions" value="Permisos" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions" v-bind:key="permission.id">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-check-input" :value="permission" v-model="createApiTokenForm.permissions">
                                <span class="ml-2 text-sm text-gray-600 uppercase">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <jet-action-message :on="createApiTokenForm.recentlySuccessful" class="mr-3">
                    Creado.
                </jet-action-message>

                <jet-button :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">
                    Crear
                </jet-button>
            </template>
        </jet-form-section>

        <div v-if="tokens.length > 0">
            <jet-section-border />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <jet-action-section>
                    <template #title>
                        API Tokens
                    </template>

                    <template #description>
                        Puede eliminar cualquiera de sus tokens si ya no los necesita.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between" v-for="token in tokens">
                                <div>
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-400" v-if="token.last_used_at">
                                        Ultimo uso {{ fromNow(token.last_used_at) }}
                                    </div>

                                    <button class="cursor-pointer ml-6 text-sm text-gray-400 underline focus:outline-none"
                                            @click="manageApiTokenPermissions(token)"
                                            v-if="availablePermissions.length > 0">
                                        Permisos
                                    </button>

                                    <a href="javascript:;" data-toggle="modal" data-target="#delete_token_modal" class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" @click="confirmApiTokenDeletion(token)">
                                        Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Delete Token Confirmation Modal -->
                        <div class="modal" id="delete_token_modal">
                            <div class="modal__content">
                                <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                                    <h2 class="font-medium text-base mr-auto">
                                        Eliminar API Token
                                    </h2>
                                </div>
                                <div class="p-5 gap-4 row-gap-3">
                                    ¿Está seguro de que desea eliminar este API Token?
                                </div>
                                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                                    <jet-secondary-button @click.native="apiTokenBeingDeleted = null" data-dismiss="modal">
                                        Cancelar
                                    </jet-secondary-button>

                                    <jet-danger-button class="ml-2" @click.native="deleteApiToken" :class="{ 'opacity-25': deleteApiTokenForm.processing }" :disabled="deleteApiTokenForm.processing">
                                        Eliminar
                                    </jet-danger-button>
                                </div>
                            </div>
                        </div>
                    </template>
                </jet-action-section>
            </div>
        </div>

        <!-- Token Value Modal -->
        <jet-dialog-modal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Copie su nuevo API token. Por su seguridad, no se volverá a mostrar.
                </div>

                <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500" v-if="$page.props.jetstream.flash.token">
                    {{ $page.props.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="displayingToken = false">
                    Cerrar
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <!-- API Token Permissions Modal -->
        <jet-dialog-modal :show="managingPermissionsFor" @close="managingPermissionsFor = null">
            <template #title>
                Permisos de API Token
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions" v-bind:key="permission.id">
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox" :value="permission" v-model="updateApiTokenForm.permissions">
                            <span class="ml-2 text-sm text-gray-600">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="managingPermissionsFor = null">
                    Cancelar
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateApiToken" :class="{ 'opacity-25': updateApiTokenForm.processing }" :disabled="updateApiTokenForm.processing">
                    Guardar
                </jet-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetActionSection from '@/Jetstream/ActionSection.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
import moment from "moment";

export default {
    components: {
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
    },

    props: [
        'tokens',
        'availablePermissions',
        'defaultPermissions',
    ],

    data() {
        return {
            createApiTokenForm: this.$inertia.form({
                name: '',
                permissions: this.defaultPermissions,
            }, {
                bag: 'createApiToken',
                resetOnSuccess: true,
            }),

            updateApiTokenForm: this.$inertia.form({
                permissions: []
            }, {
                resetOnSuccess: false,
                bag: 'updateApiToken',
            }),

            deleteApiTokenForm: this.$inertia.form(),

            displayingToken: false,
            managingPermissionsFor: null,
            apiTokenBeingDeleted: null,
            isOpen: false,
        }
    },

    methods: {
        createApiToken() {
            this.createApiTokenForm.post(route('api-tokens.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.displayingToken = true;
                    this.createApiTokenForm.reset();
                }
            })
        },

        manageApiTokenPermissions(token) {
            this.updateApiTokenForm.permissions = token.abilities

            this.managingPermissionsFor = token
        },

        updateApiToken() {
            this.updateApiTokenForm.put(route('api-tokens.update', this.managingPermissionsFor), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => (this.managingPermissionsFor = null)
            })
        },

        confirmApiTokenDeletion(token) {
            this.apiTokenBeingDeleted = token
        },

        deleteApiToken() {
            this.deleteApiTokenForm.delete(route('api-tokens.destroy', this.apiTokenBeingDeleted), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => (this.apiTokenBeingDeleted = null),
            });
        },

        fromNow(timestamp) {
            return moment(timestamp).local().fromNow()
        },
    },
}
</script>

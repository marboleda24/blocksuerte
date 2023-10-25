<template>
    <div>
        <div class="box">
            <div class="flex flex-col lg:flex-row items-center p-5">
                <div class="lg:mr-1 items-center">
                    <font-awesome-icon :icon="['fas', 'envelopes-bulk']" size="3x"/>
                </div>
                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                    <a href="" class="font-medium">
                        Correos Copia (FE)
                    </a>
                    <div class="text-slate-500 text-xs mt-0.5 overflow-hidden overflow-x-auto">
                        {{ copy_emails ? $h.truncateString(copy_emails, 52) : 'SIN REGISTRO' }}
                    </div>
                </div>
                <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                    <button class="btn btn-secondary"
                            @click="isOpen = true">
                        <EditIcon class="w-4 h-4"/>
                    </button>
                    <button class="btn btn-secondary ml-2"
                            @click="trash"
                            v-if="copy_emails">
                        <Trash2Icon class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="isOpen" @close="closeModal" max-width=lg>
            <template #title>
                Actualizar Correos Copia (FE)
            </template>

            <template #content>
                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Correos Copia
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.copy_emails.$error }"
                           v-model="form.copy_emails">

                    <template v-if="v$.form.copy_emails.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.copy_emails.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Justificación
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <textarea class="form-control resize-none"
                              :class="{ 'border-danger': v$.form.justify.$error }"
                              v-model="form.justify"
                              cols="30" rows="5"></textarea>

                    <template v-if="v$.form.justify.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button @click.prevent="update(form)" type="submit"
                        class="btn btn-primary">
                    Actualizar
                </button>
            </template>
        </jet-dialog-modal>
    </div>

</template>

<script lang="jsx">
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import useVuelidate from '@vuelidate/core'
import {required, minLength, maxLength, helpers} from '@/utils/i18n-validators'

const validMails = helpers.regex(/^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*;{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$/)

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        copy_emails: String,
        customer_code: String
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            form: {
                copy_emails: {
                    required,
                    maxLength: maxLength(255),
                    validMails: helpers.withMessage('La lista deben de ser correos validos separados por punto y coma (;)', validMails),
                },
                justify: {
                    required,
                    minLength: minLength(5),
                    maxLength: maxLength(255)
                }
            }
        }
    },

    data() {
        return {
            form: {
                customer_code: this.customer_code,
                copy_emails: this.copy_emails,
                justify: ''
            },
            isOpen: false
        }
    },

    methods: {
        closeModal() {
            this.isOpen = false
            this.form = {
                customer_code: this.customer_code,
                copy_emails: this.copy_emails,
                justify: ''
            }
            this.v$.form.$reset()
        },

        update(form) {
            this.v$.form.$touch()
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando información',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('customer.update-information.copy-mails'), form).then(resp => {
                    this.closeModal()
                    this.$emit('success', {
                        property: 'CORREOS_COPIA', value: resp.data
                    })

                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Correos copia actualizados con éxito.',
                        confirmButtonText: 'Aceptar',
                        timerProgressBar: true,
                        timer: 6000
                    });
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                        timerProgressBar: true,
                        timer: 6000
                    });
                    console.log(err)
                })
            }
        },

        trash(){
            this.$swal({
                title: '¿Eliminar Correos copia?',
                text: '¡Esta acción no es reversible!, por favor escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Eliminar',
                inputValidator: (inputValue) => {
                    return !inputValue && 'La justificación es obligatoria'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('customer.update-information.copy-mails'), {
                        customer_code: this.customer_code,
                        copy_emails: "",
                        justify: inputValue.value
                    }).then(resp => {
                        this.closeModal()
                        this.$emit('success', {
                            property: 'CORREOS_COPIA', value: ""
                        })

                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Correos copia eliminados con éxito.',
                            confirmButtonText: 'Aceptar',
                            timerProgressBar: true,
                            timer: 6000
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                            timerProgressBar: true,
                            timer: 6000
                        });
                        console.log(err)
                    })
                }else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        }
    }
}
</script>


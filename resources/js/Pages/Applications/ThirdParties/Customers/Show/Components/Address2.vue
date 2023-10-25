<template>
    <div>
        <div class="box">
            <div class="flex flex-col lg:flex-row items-center p-5">
                <div class="lg:mr-1 items-center">
                    <font-awesome-icon :icon="['fas', 'location-dot']" size="3x"/>
                </div>
                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                    <a href="" class="font-medium">
                        Dirección 2
                    </a>
                    <div class="text-slate-500 text-xs mt-0.5">
                        {{ address2.length > 0 ? address2 : 'SIN REGISTRO' }}
                    </div>
                </div>
                <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                    <button class="btn btn-secondary"
                            @click="isOpen = true">
                        <EditIcon class="w-4 h-4"/>
                    </button>
                    <button class="btn btn-secondary ml-2"
                            @click="trash"
                            v-if="address2">
                        <Trash2Icon class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="isOpen" @close="closeModal" max-width=lg>
            <template #title>
                Actualizar Dirección 2
            </template>

            <template #content>
                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Dirección
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.address2.$error }"
                           v-model="form.address2">

                    <template v-if="v$.form.address2.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.address2.$errors"
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
import {required, minLength, maxLength} from '@/utils/i18n-validators'


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        address2: String,
        customer_code: String
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            form: {
                address2: {
                    minLength: minLength(5),
                    maxLength: maxLength(64),
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
                address2: this.address2,
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
                address2: this.address2,
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

                axios.post(route('customer.update-information.address-2'), form).then(resp => {
                    this.closeModal()
                    this.$emit('success', {
                        property: 'DIRECCION2', value: resp.data
                    })

                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Dirección actualizado con éxito.',
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
                title: '¿Eliminar Dirección 2?',
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

                    axios.post(route('customer.update-information.address-2'), {
                        customer_code: this.customer_code,
                        address2: '',
                        justify: inputValue.value
                    }).then(resp => {
                        this.closeModal()
                        this.$emit('success', {
                            property: 'DIRECCION2', value: ''
                        })

                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Dirección 2 eliminada con éxito.',
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


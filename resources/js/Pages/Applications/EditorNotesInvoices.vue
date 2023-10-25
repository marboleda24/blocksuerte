<template>
    <div>
        <Head title="Editor notas facturas"/>

        <portal to="application-title">
            Editor notas facturas
        </portal>

        <div>
            <div class="intro-y box py-8 sm:pt-10 mt-5">
                <div class="mt-2 mx-10">
                    <label class="flex flex-col sm:flex-row">
                        Documento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                           busque aqui en documento
                        </span>
                    </label>

                    <div class="relative flex w-full flex-wrap items-stretch mb-3">
                        <input type="text" v-model.trim="inputSearch" placeholder="Escriba aqui en numero del documento"
                               class="form-control px-3 py-4 relative pr-10"/>
                        <span @click="getNotes(inputSearch)"
                              class="cursor-pointer z-10 h-full leading-snug font-normal text-center text-gray-400 absolute bg-transparent rounded text-lg items-center justify-center w-32 right-0 pr-3 py-4">
                            <font-awesome-icon icon="search" class="mr-1"/> Buscar
                        </span>
                    </div>
                </div>


                <template v-if="form.invoice">
                    <div class="mt-10 mx-10">
                        <label class="flex flex-col sm:flex-row">
                            Notas
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                notas del documento
                            </span>
                        </label>
                        <textarea v-model.trim="v$.form.notes.$model" name="notes" cols="30" rows="1"
                                  class="form-control" :class="{ 'border-danger': v$.form.oc.$error }"></textarea>
                        <template v-if="v$.form.notes.$error">
                            <div v-if="!v$.form.notes.maxLength" class="text-theme-6 mt-2">
                                El valor máximo para este campo es
                                {{ v$.form.notes.$params.maxLength.max }} caracteres.
                            </div>
                        </template>
                    </div>

                    <div class="mt-10 mx-10">
                        <label class="flex flex-col sm:flex-row">
                            Orden de compra
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Orden de compra
                            </span>
                        </label>
                        <input v-model.trim="v$.form.oc.$model" type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.oc.$error }" placeholder="Orden de compra"/>
                        <template v-if="v$.form.oc.$error">
                            <div v-if="!v$.form.oc.maxLength" class="text-theme-6 mt-2">
                                El valor máximo para este campo es
                                {{ v$.form.oc.$params.maxLength.max }} caracteres.
                            </div>
                        </template>
                    </div>

                    <div class="row justify-content-center text-center mt-10">
                        <button wire:click.prevent="store()" @click="save(form)" type="submit"
                                class="btn btn-primary mt-5 uppercase">
                            <font-awesome-icon icon="save" class="mr-1"/>
                            Guardar cambios
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {required, maxLength} from '@/utils/i18n-validators'
import {Head} from '@inertiajs/vue3'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    data() {
        return {
            inputSearch: '',
            form: {
                invoice: '',
                notes: '',
                oc: ''
            }
        }
    },

    validations() {
        return {
            form: {
                notes: {
                    maxLength: maxLength(90)
                },
                oc: {
                    maxLength: maxLength(25)
                }
            }
        }
    },

    methods: {
        getNotes(invoice) {
            if (invoice) {
                this.loading(true);
                axios.get(route('editor-notes-invoices.get-notes'), {
                    params: {
                        invoice: invoice
                    }
                }).then(resp => {
                    this.form.notes = resp.data.notes;
                    this.form.oc = resp.data.oc;
                    this.form.invoice = invoice;
                    this.loading(false);
                }).catch(error => {
                    console.log(error.response.status);
                    if (error.response.status === 422) {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'EL DOCUMENTO SOLICITADO NO FUE ENCONTRADO.',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                    console.log(error.data);
                });
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Debes escribir el numero del documento.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        save(data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                this.loading(true);
                axios.post(route('editor-notes-invoices.store'), data).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: 'Documento actualizado',
                        text: 'El documento fue actualizado con éxito.',
                        confirmButtonText: 'Aceptar'
                    });
                    this.resetForm();
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error.response.data);
                });
            }


        },

        resetForm() {
            this.inputSearch = '',
                this.form = {
                    invoice: '',
                    notes: '',
                    oc: ''
                }
        },

        loading(bool) {
            if (bool === true) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información...',
                    text: 'Este proceso puede tardar unos segundos.',
                });
            } else {
                this.$swal.close()
            }
        },
    }
}
</script>

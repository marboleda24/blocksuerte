<template>
    <div>
        <div class="box">
            <div class="flex flex-col lg:flex-row items-center p-5">
                <div class="lg:mr-1 items-center">
                    <font-awesome-icon :icon="['fas', 'user-gear']" size="3x"/>
                </div>
                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                    <a href="" class="font-medium">
                        Actividad Economica
                    </a>
                    <div class="text-slate-500 text-xs mt-0.5">
                        {{ economic_activity ? getName(economic_activity): 'SIN REGISTRO' }}
                    </div>
                </div>
                <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                    <button class="btn btn-secondary" @click="isOpen = true">
                        <EditIcon class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="isOpen" @close="closeModal" max-width=lg>
            <template #title>
                Actualizar Actividad Economica
            </template>

            <template #content>
                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Actividad Economica
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select  class="form-select"
                             :class="{ 'border-danger': v$.form.economic_activity.$error }"
                             v-model="form.economic_activity">
                        <option :value="null">Seleccione…</option>
                        <option :value="type.codigo.trim()"
                                v-for="type in types">
                            {{ `${type.codigo.trim()} - ${type.descripcion.trim()}` }}
                        </option>
                    </select>

                    <template v-if="v$.form.economic_activity.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.economic_activity.$errors"
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

                <button  @click.prevent="update(form)" type="submit"
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
        economic_activity: String,
        customer_code: String
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            form: {
                economic_activity: {
                    required,
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
                economic_activity: this.economic_activity,
                justify: ''
            },
            isOpen: false,
            types: []
        }
    },

    methods: {
        closeModal(){
            this.isOpen = false
            this.form = {
                customer_code: this.customer_code,
                economic_activity: this.economic_activity,
                justify: ''
            }
            this.v$.form.$reset()
        },

        update(form) {
            this.v$.form.$touch()
            if (!this.v$.form.$invalid){
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando información',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('customer.update-information.economic-activity'), form).then(resp => {
                    this.closeModal()
                    this.$emit('success', {
                        property:'ACTIVIDAD_PPAL',
                        value: resp.data
                    })

                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Actividad Economica actualizada con éxito.',
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
        getName(code){
            let coincidence = this.types.find(elem => elem.codigo.trim() === code)

            return coincidence
                ? `${coincidence.codigo} - ${coincidence.descripcion.trim()}`
                : code
        }
    },
    mounted() {
        axios.post(route('third-parties.economic-groups')).then(resp => {
            this.types = resp.data
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
}
</script>


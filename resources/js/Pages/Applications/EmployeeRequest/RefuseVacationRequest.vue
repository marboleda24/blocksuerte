<template>
    <div>
        <div class="alert alert-secondary show mb-2" role="alert">
            <div class="flex items-center">
                <div class="font-medium text-lg">Solicitud de vacaciones rechazada</div>
                <div class="text-xs bg-gray-600 px-1 rounded-md text-white ml-auto">info</div>
            </div>
            <div class="mt-3">
                Usted ha rechazado la solicitud del empleado {{ employee.nombres }} entre el {{ data.start_date }} y el {{ data.end_date }},
                le solicitamos llenar una justificación
                <textarea class="form-control" cols="10" rows="5"  v-model="form.observations"></textarea>
                <button type="button" class="btn btn-rounded-primary py-3 px-4 block mx-auto mt-8" @click="save_observation_vacation_refuse(form)">
                    ACEPTAR
                </button>

                <template v-if="v$.form.observations.$error">
                    <div v-if="!v$.form.observations.required" class="text-theme-6 mt-2">
                        Busque y seleccione el nombre de su jefe de area
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import Empty from '@/Layouts/Empty.vue'
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components : {
        Empty,
        Head
    },

    layout: Empty,

    props: {
        data: Object,
        employee: Object
    },

    validations() {
        return {
            form: {
                observations: {
                    required
                },
            },
        }
    },

    data() {
        return{
            form:{
                id: this.data.id,
                observations: null,
            }
        }
    },

    methods: {
        save_observation_vacation_refuse(data){
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando justificación...',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('employee-requests-vacation-request-refuse-request-observation'), data).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Su solicitud a sido creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(error => {
                    if (error.response.status === 422 ){
                        this.$swal({
                            icon: 'error',
                            title: 'error',
                            text: error.response.data,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 6000,
                        });
                    }
                })
            }
        }
    }
}
</script>

<template>
    <div>
        <Head title="Cartas Paz y Salvo"/>

        <portal to="application-title">
            Cartas Paz y Salvo
        </portal>

        <div class="grid grid-cols-2 gap-4">
            <div class="input-form">
                <label class="form-label w-full flex flex-col sm:flex-row">
                    Empleado
                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                        Opcional
                    </span>
                </label>

                <autocomplete
                    :url="route('search-retired-employee')"
                    show-field="name"
                    @selected-value="employeeInfo">
                </autocomplete>

                <template v-if="v$.employee_document.$error">
                    <ul class="mt-1">
                        <li class="text-danger" v-for="(error, index) of v$.employee_document.$errors"
                            :key="index">
                            {{ error.$message }}
                        </li>
                    </ul>
                </template>
            </div>

            <button class="btn btn-primary" @click="generateDocument(employee_document)">
                Generar Carta de retiro
            </button>
        </div>
    </div>
</template>

<script lang="jsx">
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    props: {
        signatures: Array
    },

    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Head
    },

    validations(){
        return {
            employee_document: {required}
        }
    },

    data(){
        return {
            employee_document: ''
        }
    },

    methods: {
        employeeInfo(obj){
            this.employee_document = obj.document
        },

        generateDocument(nit){
            this.v$.employee_document.$touch()
            if (!this.v$.employee_document.$invalid) {
                let signatures_arr = [];

                this.signatures.map(function (elem) {
                    signatures_arr[elem.id] = elem.name
                })

                this.$swal({
                    icon: 'info',
                    title: 'Firmar documento',
                    text: 'Por favor, selecciona la firma para este documento',
                    input: 'select',
                    inputOptions: signatures_arr,
                    showCancelButton: true,
                    inputAttributes: {
                        required: true
                    },
                    customClass: {
                        input: 'form-select w-full',
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary mr-2'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    confirmButtonText: 'Firmar y descargar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (inputValue) => {
                        if (inputValue === '0') return 'Debes seleccionar una opción...'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Generando Documento…',
                            text: 'Este proceso puede tardar unos segundos…',
                        });

                        axios.post(route('goja.peace-safe.generate-document'), {
                            document: nit,
                            signature: inputValue.value,
                        }, {
                            responseType: 'blob'
                        }).then(resp => {
                            const url = window.URL.createObjectURL(new Blob([resp.data]));
                            const link = document.createElement('a');
                            link.href = url;

                            const filename = 'carta_paz_y_salvo.pdf';

                            link.setAttribute('download', filename);
                            document.body.appendChild(link);
                            link.click();
                            this.$swal.close();
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err)
                        })
                    }
                })
            }
        }
    }
}
</script>

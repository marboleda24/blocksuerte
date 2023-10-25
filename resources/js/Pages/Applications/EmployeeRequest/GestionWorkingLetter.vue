<template>
    <div>
        <Head title="Solicitudes de carta laboral"/>

        <portal to="application-title">
            Solicitudes de carta laboral
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="showGenerateDocumentModal">
                <font-awesome-icon icon="file-pdf" class="mr-2"/>
                Generar carta laboral
            </button>
        </portal>

        <div>
            <v-client-table :data="table_data" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">

                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="approved(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'check-double']" class="mr-1"/>
                                    Aprobar
                                </a>

                                <a href="javascript:void(0)"
                                   @click="edit(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="refuse(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                    Rechazar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    Editar
                </template>

                <template #content>
                    <div class="p-5">
                        <label>Dirigido a</label>
                        <input type="text" class="form-control" v-model="form.addressed_to">
                    </div>
                </template>

                <template #footer>
                    <button @click="update(form)" type="button" class="btn btn-primary">
                        Guardar
                    </button>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>


            <jet-dialog-modal :show="generateDocumentModal.show" @close="closeModal" max-width="lg">
                <template #title>
                    Generar carta laboral
                </template>

                <template #content>
                    <div>
                        <label class="form-label">Empleado</label>
                        <autocomplete
                            url="/employee-requests/employee-info"
                            show-field="nombres"
                            @selected-value="info_employee"
                        />

                        <template v-if="v$.generateDocumentModal.form.employee.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.generateDocumentModal.form.employee.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Dirigido a</label>
                        <input type="text" class="form-control" v-model="generateDocumentModal.form.to">
                    </div>
                </template>

                <template #footer>
                    <button @click="generateDocument()" type="button" class="btn btn-primary mr-2">
                        Generar Documento
                    </button>

                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>

            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import useVuelidate from "@vuelidate/core";
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        data: Array,
        signatures: Array
    },

    components: {
        FontAwesomeIcon,
        JetDialogModal,
        Head,
        Autocomplete
    },

    validations(){
        return {
            generateDocumentModal: {
                form: {
                    employee: {required}
                }
            }
        }
    },

    data() {
        return {
            form: {
                addressed_to: null,
                id: null
            },
            columns: [
                'employee_document',
                'employee_name',
                'addressed_to',
                'actions'
            ],
            options: {
                headings: {
                    employee_document: 'DOCUMENTO',
                    employee_name: 'NOMBRE',
                    addressed_to: 'DIRIGIDO A',
                    actions: 'ACCIONES',
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['addressed_to', 'employee_document'],
                templates: {
                    employee_name: function (h, row) {
                        return row.employee.nombres
                    },
                }
            },
            table_data: this.data,
            isOpen: false,

            generateDocumentModal: {
                show: false,
                form: {
                    employee: '',
                    to: null
                }
            }
        }
    },

    methods: {
        async approved(id) {
            const rObj = {
                0: 'Seleccione…'
            };
            this.signatures.map(function (obj) {
                rObj[obj.id] = obj.name;
                return rObj;
            })

            const steps = ['1', '2']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                signature: '',
                type: ''
            }
            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: '¿Quien Firma?',
                        text: 'Selecciona el nombre de la firma para este documento',
                        inputValue: values.designer,
                        input: 'select',
                        inputOptions: rObj,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-select',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                    })
                } else if (currentStep === 1) {
                    result = await swalQueueStep.fire({
                        title: 'Justificación',
                        text: 'Por favor, escriba una justificación para esta acción',
                        input: 'select',
                        inputOptions: {
                            download: 'Descargar',
                            mail: 'Enviar por correo electrónico'
                        },
                        showCancelButton: currentStep > 0,
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-select',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        confirmButtonText: 'Generar documento',

                    })
                }

                if (result.value) {
                    currentStep === 0
                        ? values.signature = result.value
                        : values.type = result.value
                    currentStep++
                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    currentStep--
                } else {
                    break
                }
            }

            if (currentStep === steps.length) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Generando carta laboral…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('approve-working-letter'), {
                    id: id,
                    signature: values.signature,
                    type: values.type
                }, {
                    responseType: 'blob'
                }).then(resp => {
                    if (values.type === 'mail') {
                        this.$inertia.reload();
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡La carta laboral ha sido generada con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        const url = window.URL.createObjectURL(new Blob([resp.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'carta_laboral.pdf');

                        document.body.appendChild(link);
                        link.click();
                        this.$inertia.reload();
                        this.$swal.close();
                    }
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: 'Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err);
                })
            }
        },

        refuse(id) {
            this.$swal({
                title: '¿Rechazar Solicitud?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Rechazar',
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
                        title: 'Rechazando Solicitud...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('refuse-working-letter'), {
                        'id': id,
                        'justify': inputValue.value
                    }).then(res => {
                        this.table_data = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El pedido ha sido rechazado!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: 'Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            });
        },

        closeModal() {
            this.isOpen = false;
            this.generateDocumentModal = {
                show: false,
                form: {
                    employee: '',
                    to: null
                }
            }
        },

        edit(row) {
            this.form = {
                addressed_to: row.addressed_to,
                id: row.id
            }
            this.isOpen = true
        },

        update(data) {
            axios.put(route('edit-working-letter'), data).then(resp => {
                this.$swal({
                    title: 'Registro editado',
                    text: "Editado con éxito",
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                })
                this.table_data = resp.data
                this.closeModal()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups!',
                    text: 'Hubo un error guardando la información.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err.data);
            })
        },

        showGenerateDocumentModal(){
            this.generateDocumentModal.show = true;
        },

        generateDocument(){
            this.v$.generateDocumentModal.$touch();

            if (this.v$.generateDocumentModal.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Generando carta laboral…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('generate-working-letter'), this.generateDocumentModal.form, {
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'carta-laboral.pdf');

                    document.body.appendChild(link);
                    link.click();
                    this.closeModal()
                    this.$inertia.reload();
                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: 'Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar',
                    });
                })
            }
        },

        info_employee(obj) {
            this.generateDocumentModal.form.employee = obj.nit;
        },

    }

}
</script>

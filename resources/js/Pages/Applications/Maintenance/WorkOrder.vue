<template>
    <div>
        <Head :title="`Orden de trabajo # ${data.consecutive}`"/>

        <portal to="application-title">
            {{ `Mantenimiento #${data.request.consecutive} - Orden de trabajo #${data.consecutive}` }}
        </portal>

        <portal to="actions">
            <Link :href="route('maintenance.my-request.view', data.request.id)" class="btn btn-primary mr-2">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Volver a Mantenimiento
            </Link>

            <Link :href="route('maintenance.work-orders')" class="btn btn-primary mr-2">
                <font-awesome-icon icon="arrow-right" class="mr-2"/>
                Lista de Ordenes
            </Link>

            <button class="btn btn-danger"
                    v-if="data.state !== '0' && data.state !== '3'"
                    @click="cancel(data.id, data.request.id)">
                <font-awesome-icon icon="ban" class="mr-2"/>
                Anular
            </button>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Detalles del mantenimiento</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-6 mx-2 p-2 gap-2 mx-2 text-base divide-x text-center border rounded-lg">
                        <div class="flex flex-col">
                            <strong>ACTIVO</strong>
                            {{ data.request.asset.name }}
                        </div>
                        <div class="flex flex-col">
                            <strong>SOLICITANTE</strong>
                            {{ data.request.applicant.name }}
                        </div>
                        <div class="flex flex-col">
                            <strong>FECHA SOLICITUD</strong>
                            {{ format_date(data.request.created_at, 'DD-MM-YYYY hh:mm a') }}
                        </div>
                        <div class="flex flex-col">
                            <strong>FECHA INCIDENTE</strong>
                            {{ format_date(data.request.planning_date, 'DD MMMM YYYY hh:mm a') }}
                        </div>
                        <div class="flex flex-col">
                            <strong>ESTADO</strong>
                            <span v-if=" data.request.state === '0'"
                                  class="badge badge-rounded badge-danger mx-2"> Anulado </span>
                            <span v-else-if=" data.request.state === '1'"
                                  class="badge badge-rounded badge-primary mx-2"> en revision </span>
                            <span v-else-if=" data.request.state === '2'"
                                  class="badge badge-rounded badge-success mx-2"> aprobada </span>
                            <span v-else-if=" data.request.state === '3'"
                                  class="badge badge-rounded badge-warning mx-2"> en proceso </span>
                            <span v-else-if=" data.request.state === '4'"
                                  class="badge badge-rounded badge-success mx-2"> finalizada </span>
                            <span v-else
                                  class="badge badge-rounded badge-danger mx-2"> Rechazado </span>
                        </div>
                        <div class="flex flex-col">
                            <strong>TIPO</strong>
                            <span v-if="data.request.type === 'preventive'"
                                  class="badge badge-rounded badge-success mx-2"> Preventivo </span>
                            <span v-else-if="data.request.type === 'corrective'"
                                  class="badge badge-rounded badge-danger mx-2"> Correctivo </span>
                            <span v-else-if="data.request.type === 'locative'"
                                  class="badge badge-rounded badge-warning mx-2"> Locativo </span>
                            <span v-else
                                  class="badge badge-rounded badge-success mx-2"> Mejorativo </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Detalles de la orden de trabajo</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-5 mx-2 p-2 gap-2 mx-2 text-base divide-x text-center border rounded-lg">
                        <div class="flex flex-col p-2">
                            <strong>ORDEN NUMERO</strong>
                            {{ data.consecutive }}
                        </div>
                        <div class="flex flex-col p-2">
                            <strong>ENCARGADO</strong>
                            <select v-model="form.assigned_to" class="form-select form-select-sm">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="technician in technicians" :value="technician.id">
                                    {{ technician.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col p-2">
                            <strong>TIPO</strong>
                            <select v-model="form.type" class="form-select form-select-sm">
                                <option value="" selected disabled>Seleccione…</option>
                                <option value="preventive">Preventivo</option>
                                <option value="corrective">Correctivo</option>
                                <option value="locative">Locativo</option>
                                <option value="mejorative">Mejorativo</option>
                            </select>
                        </div>
                        <div class="flex flex-col p-2">
                            <strong>COSTO TOTAL</strong>
                            <input type="number" :value="total_cost" disabled class="form-control form-control-sm">
                        </div>
                        <div class="flex flex-col p-2">
                            <strong>ESTADO</strong>
                            <span v-if="data.state === '0'"
                                  class="badge badge-rounded badge-danger">
                                Anulado
                            </span>
                            <span v-else-if="data.state === '1'"
                                  class="badge badge-rounded badge-success mx-2">
                                en proceso
                            </span>

                            <span v-else-if="data.state === '2'"
                                  class="badge badge-rounded badge-danger mx-2">
                                cancelado
                            </span>
                            <span v-else
                                  class="badge badge-rounded badge-success mx-2">
                                Finalizado
                            </span>
                        </div>
                    </div>
                    <div class="p-2 mt-2">
                        <strong class="uppercase">Descripción de la orden de trabajo </strong>
                        <textarea class="form-control" cols="30" rows="2" v-model="form.description"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200 dark:border-dark-5">
                    <button class="btn btn-primary" @click="updateInfo" :disabled="data.state !== '1'">
                        Actualizar Información
                    </button>
                </div>
            </div>

            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Tareas asignadas</h2>
                </div>
                <div class="p-5">
                    <table class="table text-center" v-if="activities.length > 0">
                        <thead>
                        <tr>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                TIPO DE TRABAJO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                TAREA A REALIZAR
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                ENCARGADO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                COSTO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                CREADO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                ESTADO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">

                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="activity in activities">
                            <td class="border">{{ activity.work_type.name }}</td>
                            <td class="border">{{ activity.description }}</td>
                            <td class="border">{{ activity.assignedto.name }}</td>
                            <td class="border">{{ $h.formatCurrency(activity.cost) }}</td>
                            <td class="border">{{ format_date(activity.created_at, 'DD-MM-YYYY') }}</td>
                            <td class="border">
                                <span v-if="activity.state === '1'"
                                      class="badge badge-rounded badge-danger">
                                    pendiente
                                </span>
                                <span v-else-if="activity.state === '0'"
                                      class="badge badge-rounded badge-danger">
                                    anulado
                                </span>
                                <span v-else class="badge badge-rounded badge-success">
                                    finalizado
                                </span>
                            </td>
                            <td class="border text-center">
                                <button class="btn btn-sm btn-primary"
                                        v-if="activity.state === '1'"
                                        @click="record_conclusion(activity.id)">
                                    <font-awesome-icon icon="comment-dots" class="mr-2"/>
                                    Reportar conclusion
                                </button>

                                <button class="btn btn-sm btn-primary"
                                        v-if="activity.state === '1'"
                                        @click="cancel_activity(activity.id)">
                                    <font-awesome-icon icon="trash-can" class="mr-2"/>
                                    Anular
                                </button>

                                <button class="btn btn-sm btn-primary"
                                        v-else
                                        @click="view_details_activity(activity)">
                                    <font-awesome-icon icon="eye" class="mr-2"/>
                                    Ver detalles
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-else class="alert alert-danger-soft show flex items-center mb-2" role="alert">
                        <font-awesome-icon icon="exclamation-triangle" size="2x" class="mr-4"/>
                        Aun no hay actividades asignadas para esta orden de trabajo
                    </div>
                </div>

                <div class="items-center p-5 border-t border-gray-200 dark:border-dark-5"
                     v-if="data.state === '1'">
                    <button @click="generate_activity"
                            class="btn btn-outline-primary uppercase mr-auto">
                        <font-awesome-icon icon="star" class="mr-2"/>
                        Asignar tarea
                    </button>

                    <button @click="finalize(data.id)"
                            class="btn btn-outline-primary uppercase"
                            v-if="activities_open.length === 0 && activities.length > 0">
                        <font-awesome-icon icon="check" class="mr-2"/>
                        Finalizar orden de trabajo
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es');

export default {
    props: {
        data: Object,
        technicians: Array,
        work_types: Array
    },

    components: {
        Head,
        Link
    },

    data() {
        return {
            activities: this.data.activities,
            form: {
                type: this.data.type,
                assigned_to: this.data.assignedto.id,
                description: this.data.description
            }
        }
    },

    methods: {
        format_date(date, format) {
            return dayjs(new Date(date)).format(format);
        },

        async generate_activity() {
            let technicians = {};
            let work_types = {}

            this.technicians.map(function (elem) {
                technicians[elem.id] = elem.name
            });

            this.work_types.map(function (elem) {
                work_types[elem.id] = elem.name
            });

            const steps = ['1', '2', '3']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                work_type_id: '',
                assigned_to: '',
                description: ''
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Tipo de actividad',
                        text: 'Tipo de actividad a realizar',
                        input: 'select',
                        inputOptions: work_types,
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
                        title: 'Encargado',
                        text: '¿quien es el responsable?',
                        input: 'select',
                        inputOptions: technicians,
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
                    })
                } else if (currentStep === 2) {
                    result = await swalQueueStep.fire({
                        title: 'Descripción',
                        text: 'Detalles de este trabajo',
                        input: 'textarea',
                        showCancelButton: currentStep > 0,
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-control',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        confirmButtonText: 'Crear tarea',
                    })
                }

                if (result.value) {
                    switch (currentStep) {
                        case 0:
                            values.work_type_id = result.value;
                            break;
                        case 1:
                            values.assigned_to = result.value;
                            break;
                        case 2:
                            values.description = result.value
                    }
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
                    title: 'Generando actividad…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('maintenance.store-activity'), {
                    work_order_id: this.data.id,
                    request_id: this.data.request.id,
                    work_type_id: values.work_type_id,
                    assigned_to: values.assigned_to,
                    description: values.description
                }).then(resp => {
                    this.$swal.close()
                    this.activities = resp.data;
                }).catch(err => {
                    this.$swal({

                        icon: 'error',
                        title: 'hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                    console.log(err.log);
                })
            }
        },

        async record_conclusion(id) {
            const steps = ['1', '2']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                cost: '',
                conclusion: '',
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Costo',
                        text: 'Costo de la actividad',
                        input: 'number',
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
                        title: 'Acción realizada',
                        text: 'Detalles de la acción realizada',
                        input: 'textarea',
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
                    })
                }

                if (result.value) {
                    switch (currentStep) {
                        case 0:
                            values.cost = result.value;
                            break;
                        case 1:
                            values.conclusion = result.value;
                            break;
                    }
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
                    title: 'Reportando conclusion…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('maintenance.store-conclusion-activity'), {
                    id: id,
                    request_id: this.data.request.id,
                    work_order_id: this.data.id,
                    cost: values.cost,
                    conclusion: values.conclusion,
                }).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: 'Actividad actualizada con éxito',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                    this.activities = resp.data;
                }).catch(err => {
                    this.$swal({

                        icon: 'error',
                        title: 'hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    });
                    console.log(err.log);
                })
            }
        },

        view_details_activity(activity) {
            this.$swal({
                icon: 'info',
                title: 'Detalles de la actividad',
                html: `
                    <table class="table text-center mt-4">
                        <thead>
                            <tr>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    Fecha cierre
                                </th>
                                <td class="border">
                                    ${activity.finish_date}
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    Conclusion
                                </th>
                                <td class="border">
                                    ${activity.conclusion ?? 'N/A'}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `,
                confirmButtonText: 'Aceptar',
                showConfirmButton: true,
            });
            console.log(activity);
        },

        finalize(id) {
            if (this.data.state === '1') {
                this.$swal({
                    title: '¿Finalizar orden de trabajo?',
                    html: `
                    <strong class="text-red-500">
                        ¡Esta acción no es reversible!
                    </strong>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '¡Si, finalizar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(route('maintenance.store-finalize-work-order', id)).then(resp => {
                            this.$swal.close()
                            this.data.state = resp.data;
                        }).catch(err => {
                            this.$swal({

                                icon: 'error',
                                title: 'hubo un error procesando la solicitud',
                                timerProgressBar: true,
                                showConfirmButton: true,
                                timer: 6000,
                            });
                            console.log(err.log);
                        })
                    }
                })
            }
        },

        cancel(id, request_id) {
            this.$swal({
                title: '¿Anular orden de trabajo?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Anular',
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
                    axios.post(route('maintenance.cancel-work-order'), {
                        id: id,
                        request_id: request_id,
                        justify: inputValue.value
                    }).then(res => {
                        this.$inertia.visit(route('maintenance.view-work-order', this.data.id))
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Orden de trabajo anulada con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
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

        cancel_activity(id) {
            this.$swal({
                title: '¿Anular actividad?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Anular',
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
                    axios.post(route('maintenance.cancel-activity'), {
                        id: id,
                        request_id: this.data.request_id,
                        justify: inputValue.value
                    }).then(res => {
                        this.$inertia.visit(route('maintenance.view-work-order', this.data.id))

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Actividad anulada con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
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

        updateInfo() {
            this.$swal({
                title: '¿Actualizar orden de trabajo?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Actualizar',
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

                    axios.post(route('maintenance.update-work-order'), {
                        form: this.form,
                        id: this.data.id,
                        request_id: this.data.request.id,
                        justify: inputValue.value,
                    }).then(res => {
                        this.$inertia.visit(route('maintenance.view-work-order', this.data.id))

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Orden de trabajo actualizada con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        }
    },

    computed: {
        total_cost() {
            return this.activities.reduce(function (a, c) {
                return a + Number((c.cost) || 0)
            }, 0)
        },

        activities_open() {
            return this.activities.filter(function (obj) {
                return obj.state === '1'
            }).map(function (obj) {
                return obj.state
            })
        }
    }
}
</script>

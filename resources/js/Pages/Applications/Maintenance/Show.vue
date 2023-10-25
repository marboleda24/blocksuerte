<template>
    <div>
        <Head :title="`Solicitud # ${maintenance_request.consecutive}`"/>

        <portal to="application-title">
            {{ `Solicitud # ${maintenance_request.consecutive}` }}
        </portal>

        <portal to="actions">
            <Link :href="route('maintenance.my-requests')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Descripción de la solicitud</h2>
                </div>
                <div class="p-10">
                    <div class="grid grid-cols-6 mx-2 p-2 gap-2 text-base divide-x text-center border rounded-lg">
                        <div class="flex flex-col">
                            <strong>ACTIVO</strong>
                            {{ maintenance_request.asset.name }}
                        </div>
                        <div class="flex flex-col">
                            <strong>SOLICITANTE</strong>
                            {{ maintenance_request.applicant.name }}
                        </div>
                        <div class="flex flex-col">
                            <strong>FECHA SOLICITUD</strong>
                            {{ maintenance_request.created_at }}
                        </div>
                        <div class="flex flex-col">
                            <strong>FECHA INCIDENTE</strong>
                            {{ maintenance_request.planning_date }}
                        </div>
                        <div class="flex flex-col">
                            <strong>ESTADO</strong>
                            <span v-if=" maintenance_request.state === '0'" class="badge badge-rounded badge-danger"> Anulado </span>
                            <span v-else-if=" maintenance_request.state === '1'" class="badge badge-rounded badge-pink"> en revision </span>
                            <span v-else-if=" maintenance_request.state === '2'"
                                  class="badge badge-rounded badge-success"> aprobado </span>
                            <span v-else-if=" maintenance_request.state === '3'"
                                  class="badge badge-rounded badge-warning"> en proceso </span>
                            <span v-else-if=" maintenance_request.state === '4'"
                                  class="badge badge-rounded badge-success"> finalizado </span>
                            <span v-else class="badge badge-rounded badge-danger"> Rechazado </span>
                        </div>
                        <div class="flex flex-col">
                            <strong>TIPO</strong>
                            <span v-if="maintenance_request.type === 'preventive'"
                                  class="badge badge-rounded badge-success"> Preventivo </span>
                            <span v-else-if="maintenance_request.type === 'corrective'"
                                  class="badge badge-rounded badge-danger"> Correctivo </span>
                            <span v-else-if="maintenance_request.type === 'locative'"
                                  class="badge badge-rounded badge-warning"> Locativo </span>
                            <span v-else class="badge badge-rounded badge-primary"> Mejorativo </span>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 mx-2 p-2 gap-2 mx-2 text-base divide-x text-center border rounded-lg mt-10">
                        <div class="flex flex-col">
                            <strong>DETALLES DE LA SOLICITUD</strong>
                        </div>
                        <div class="flex flex-col">
                            {{ maintenance_request.description }}
                        </div>
                    </div>
                </div>
                <div class="items-center p-5 border-t border-gray-200 dark:border-dark-5">
                    <button @click="generate_work_order"
                            class="btn btn-outline-primary uppercase mr-2" v-if="parseInt(maintenance_request.state) > 0 && parseInt(maintenance_request.state) < 5">
                        Generar orden de trabajo
                    </button>

                    <button @click="refuse(maintenance_request.id)" v-if="parseInt(maintenance_request.state) > 0 && parseInt(maintenance_request.state) < 5"
                            class="btn btn-outline-warning uppercase mr-2">
                        Rechazar
                    </button>

                    <button @click="finalize_maintenance(maintenance_request.id)"
                            class="btn btn-outline-danger uppercase"
                            v-if="work_orders_open.length === 0 && maintenance_request.work_orders.length > 0 && maintenance_request.state === '3'">
                        Finalizar
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 py-5">
                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">Ordenes de trabajo asignadas</h2>
                    </div>
                    <div class="p-7">
                        <table class="table text-center" v-if="maintenance_request.work_orders.length > 0">
                            <thead>
                            <tr>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    CÓDIGO
                                </th>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    COSTO
                                </th>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    TIPO
                                </th>
                                <th class="border border-b-2 dark:border-dark-5 whitespace-nowrap">
                                    ESTADO
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="work_order in maintenance_request.work_orders">
                                <td class="border">
                                    <Tippy
                                        tag="a"
                                        content="Click para ver orden de trabajo"
                                        :options="{
                                            placement: 'top'
                                        }"
                                        href="javascript:;"
                                        class="btn btn-sm btn-rounded btn-dark-soft"
                                        @click.native="visit(work_order.id)"
                                    >
                                        {{ work_order.consecutive }}
                                    </Tippy>
                                </td>
                                <td class="border">{{ $h.formatCurrency(work_order.cost) }}</td>
                                <td class="border">
                                    <span v-if="work_order.type === 'preventive'"
                                          class="badge badge-rounded badge-success">
                                        Preventivo
                                    </span>

                                    <span v-else-if="work_order.type === 'corrective'"
                                          class="badge badge-rounded badge-danger">
                                        Correctivo
                                    </span>

                                    <span v-else-if="work_order.type === 'locative'"
                                          class="badge badge-rounded badge-pink">
                                        Locativo
                                    </span>

                                    <span v-else
                                          class="badge badge-rounded badge-primary">
                                        Mejorativo
                                    </span>
                                </td>
                                <td class="border">
                                     <span v-if="work_order.state === '0'"
                                           class="badge badge-rounded badge-danger">
                                        Anulado
                                    </span>
                                    <span v-else-if="work_order.state === '1'"
                                          class="badge badge-rounded badge-warning">
                                        en revision
                                    </span>

                                    <span v-else-if="work_order.state === '2'"
                                          class="badge badge-rounded badge-primary">
                                        en progreso
                                    </span>

                                    <span v-else
                                          class="badge badge-rounded badge-success">
                                        finalizado
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-else class="alert alert-danger-soft show flex items-center mb-2" role="alert">
                            <font-awesome-icon icon="exclamation-triangle" size="2x" class="mr-4"/>
                            Aun no hay ordenes de trabajo asignadas para esta solicitud
                        </div>
                    </div>
                </div>

                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto uppercase">Log de eventos</h2>
                    </div>
                    <div class="p-10">
                        <div class="max-h-56 overflow-y-auto" ref="scrollMeLog" v-if="filterLog.length > 0">
                            <div class="flex" v-for="(log, index) in filterLog"
                                 :class="{'mt-5': index > 0}">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 flex-none image-fit">
                                    <img :src=log.user?.profile_photo_url alt="photo" class="rounded-full">
                                </div>
                                <div class="ml-3 flex-1 pb-6 border-b">
                                    <div class="flex items-center">
                                        <a class="font-medium">{{ log.user.name }}</a>
                                        <div class="text-gray-600 text-xs sm:text-sm ml-auto">
                                            {{ log.created_at }}
                                        </div>
                                    </div>
                                    <div class="mt-2">{{ log.description }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="alert alert-danger-soft show flex items-center mb-2" role="alert">
                            <font-awesome-icon icon="exclamation-triangle" size="2x" class="mr-4"/>
                            Aun no hay eventos registrados para esta solicitud.
                        </div>
                    </div>
                </div>
            </div>

            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Comentarios</h2>
                </div>
                <div class="p-10">
                    <div class="max-h-56 overflow-y-auto" ref="scrollMe" v-if="filterComments.length > 0">
                        <div class="flex" v-for="(comment, index) in filterComments"
                             :class="{'mt-5': index > 0}">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 flex-none image-fit">
                                <img :src=comment.user?.profile_photo_url alt="" class="rounded-full">
                            </div>
                            <div class="ml-3 flex-1 pb-6 border-b">
                                <div class="flex items-center">
                                    <a class="font-medium">{{ comment.user?.name }}</a>
                                    <div class="text-gray-600 text-xs sm:text-sm ml-auto">
                                        {{ comment.created_at }}
                                    </div>
                                </div>
                                <div class="mt-2">{{ comment.description }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="alert alert-danger-soft show flex items-center mb-2" role="alert">
                        <font-awesome-icon icon="exclamation-triangle" size="2x" class="mr-4"/>
                        Aun no hay comentarios para esta solicitud
                    </div>

                    <div class="news__input relative mt-6">
                        <font-awesome-icon icon="comments"
                                           class="absolute my-auto inset-y-0 ml-6 left-0 text-gray-600" size="2x"/>
                        <textarea @keyup.enter.exact="add_comment" v-model="comment"
                                  @keydown.enter.exact.prevent
                                  class="form-control border-transparent bg-gray-200 pl-20 py-6 placeholder-theme-13 resize-none"
                                  rows="1"
                                  placeholder="Escribe un comentario para esta solicitud de mantenimiento"></textarea>
                    </div>
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
        technicians: Array
    },

    components: {
        Head,
        Link
    },

    data() {
        return {
            maintenance_request: this.data,
            comment: null,
        }
    },

    methods: {
        add_comment() {
            if (!this.comment) {
                this.$swal({

                    icon: 'error',
                    title: 'Debes escribir un comentario',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                axios.post(route('maintenance.add-comment'), {
                    id: this.maintenance_request.id,
                    comment: this.comment
                }).then(resp => {
                    this.$swal.close()
                    this.maintenance_request = resp.data;
                    this.comment = null;
                }).catch(err => {
                    this.$swal({

                        icon: 'error',
                        title: 'Ups…',
                        text: 'hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: false,
                        timer: 6000,
                    });
                    console.log(err.maintenance_request);
                })
            }
        },

        scrollToEnd() {
            let content = this.$refs.scrollMe;
            content.scrollTop = content.scrollHeight;

            let contentlog = this.$refs.scrollMeLog;
            contentlog.scrollTop = contentlog.scrollHeight;
        },

        async generate_work_order() {
            let technicians_array = {};

            this.technicians.map(function (elem) {
                technicians_array[elem.id] = elem.name
            });

            console.log(technicians_array)

            //technicians_array = technicians_array.sort((a, b) => a.localeCompare(b))

            const steps = ['1', '2', '3']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                request_id: this.maintenance_request.id,
                type: '',
                assigned_to: '',
                description: ''
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Tipo de trabajo',
                        text: 'Tipo de trabajo a realizar',
                        input: 'select',
                        inputOptions: {
                            preventive: 'Preventivo',
                            corrective: 'Correctivo',
                            locative: 'Locativo',
                            improvement: 'Mejorativo'
                        },
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
                        text: '¿Quien es el responsable?',
                        input: 'select',
                        inputOptions: technicians_array,
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
                }else if (currentStep === 2) {
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
                        confirmButtonText: 'Crear orden de trabajo',

                    })
                }

                if (result.value) {
                    switch (currentStep){
                        case 0:
                            values.type = result.value;
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
                    title: 'Generando orden de trabajo…',
                    text: 'Este proceso puede tardar unos segundos.',
                });
                axios.post(route('maintenance.store-work-order'), {
                    request_id: values.request_id,
                    type: values.type,
                    assigned_to: values.assigned_to,
                    description: values.description
                }).then(resp => {
                    this.$swal.close()
                    this.maintenance_request = resp.data;
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

        visit(id) {
            this.$inertia.visit(route('maintenance.view-work-order', id))
        },

        finalize_maintenance(id) {
            if (this.maintenance_request.state === '3') {
                this.$swal({
                    title: '¿Finalizar Mantenimiento?',
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
                        axios.post(route('maintenance.finalize', id)).then(resp => {
                            this.$swal.close()
                            this.maintenance_request = resp.data;
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

        refuse(id) {
            if (this.maintenance_request.work_orders.length > 0 && this.maintenance_request.work_orders.filter(row => row.state === '0').length !== this.maintenance_request.work_orders.length){
                this.$swal({
                    icon: 'error',
                    title: 'Ups!',
                    text: 'Todas las ordenes de trabajo deben estar anuladas',
                    confirmButtonText: 'Aceptar',
                });
                return;
            }

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

                    axios.post(route('maintenance.refuse'), {
                        id: id,
                        justify: inputValue.value
                    }).then(res => {
                        this.maintenance_request = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "La solicitud ha sido rechazada!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });

                        this.$inertia.visit(route('maintenance.my-requests'))
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
    },

    mounted() {
        if (this.work_orders_open.length === 0 && this.maintenance_request.work_orders.length > 0 && this.maintenance_request.state === '2') {
            axios.post(route('maintenance.update-state'), {
                id: this.maintenance_request.id
            }).then(resp => {
                this.maintenance_request = resp.data;
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


    computed: {
        filterComments() {
            return this.maintenance_request.log.filter(log => log.type === 'comment')
        },

        filterLog() {
            return this.maintenance_request.log.filter(log => log.type === 'log')
        },

        work_orders_open: function () {
            return this.maintenance_request.work_orders.filter(function (obj) {
                return obj.state === '1'
            }).map(function (obj) {
                return obj.state
            })
        }
    },

    watch: {
        logs() {
            this.filterComments()
            this.filterLog()
        }
    }
}
</script>


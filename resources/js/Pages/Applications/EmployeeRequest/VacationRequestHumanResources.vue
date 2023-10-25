<template>
    <div>
        <Head title="Solicitudes Vacaciones"/>

        <portal to="application-title">
            Solicitudes Vacaciones
        </portal>


        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            Pendientes
                        </Tippy>
                    </li>

                    <li class="nav-item">
                        <Tippy
                            id="finished-tab"
                            content="Finalizados"
                            tag="button"
                            data-tw-toggle="tab"
                            data-tw-target="#finished"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Finalizados
                        </Tippy>
                    </li>
                </ul>


                <div class="post__content tab-content p-2">
                    <div id="pending" class="tab-pane active p-2" role="tabpanel" aria-labelledby="pending-tab">
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
                    </div>

                    <div id="finished" class="tab-pane p-2" role="tabpanel" aria-labelledby="finished-tab">
                        <v-client-table :data="finishTable.data" :columns="finishTable.columns"
                                        :options="finishTable.options" ref="table1" class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="text-center">
                                    <button class="btn btn-secondary" @click="print(row.id)">
                                        <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-2"/>
                                        Descargar
                                    </button>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    Editar
                </template>

                <template #content>
                    <div class="p-5">
                        <label>Dirigido a</label>
                        <input type="text" class="form-control">
                    </div>
                </template>

                <template #footer>
                    <button type="button" class="btn btn-primary">
                        Guardar
                    </button>
                    <button type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>
<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    props: {
        data: Array,
        finish: Array,
        signatures: Array,

    },
    components: {
        JetDialogModal,
        Head
    },

    data() {
        return {
            finishTable: {
                data: this.finish,
                columns: [
                    'employee',
                    'start_date',
                    'end_date',
                    'boss',
                    'rrhh',
                    'actions'
                ],
                options: {
                    headings: {
                        employee: 'EMPLEADO',
                        start_date: 'DESDE',
                        end_date: 'HASTA',
                        boss: 'JEFE APROBO',
                        rrhh: 'RRHH APROBO',
                        actions: '',

                    },
                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['employee', 'boss', 'rrhh', 'start_date', 'end_date'],
                    templates: {
                        employee: function (h, row) {
                            return row.employee.nombres
                        },
                        boss: function (h, row) {
                            return row.boss.nombres
                        },
                        start_date: function (h, row) {
                            return dayjs(new Date(row.start_date)).format('DD MMMM YYYY');
                        },
                        end_date: function (h, row) {
                            return dayjs(new Date(row.end_date)).format('DD MMMM YYYY');
                        },
                        rrhh: function (h, row) {
                            return row.approved_rrhh.name
                        }
                    },

                },
            },

            form: {
                observations: null,
                id: this.data.id
            },
            columns: [
                'employee_document',
                'start_date',
                'end_date',
                'justify',
                'boss_document',
                'boss_approved_date',
                'actions'
            ],
            options: {
                headings: {
                    employee_document: 'NOMBRE EMPLEADO',
                    start_date: 'DESDE',
                    end_date: 'HASTA',
                    justify: 'justificación EMPLEADO',
                    boss_document: 'NOMBRE JEFE',
                    boss_approved_date: 'FECHA DE APROBACION JEFE',
                    actions: '',

                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['employee_document', 'boss_document', 'end_date', 'start_date'],
                templates: {
                    employee_document: function (h, row) {
                        return row.employee.nombres
                    },
                    boss_document: function (h, row) {
                        return row.boss.nombres
                    },
                    start_date: function (h, row) {
                        return dayjs(new Date(row.start_date)).format('DD MMMM YYYY');
                    },
                    end_date: function (h, row) {
                        return dayjs(new Date(row.end_date)).format('DD MMMM YYYY');
                    },
                    boss_approved_date: function (h, row) {
                        return dayjs(new Date(row.boss_approved_date)).format('DD MMMM YYYY');
                    }
                },

            },
            table_data: this.data,
            isOpen: false
        }
    },

    methods: {
        approved(id) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Aceptando Solicitud...',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.post(route('approve-vacation-human-resource'), {
                'id': id,
            }).then(resp => {
                this.table_data = resp.data;

                this.$swal({
                    title: '¡Éxito!',
                    text: "¡la solicitud ha sido aprobada!",
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
            })
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
                    axios.post(route('refuse-vacation-human-resource'), {
                        'id': id,
                        'observations': inputValue.value
                    }).then(res => {
                        this.table_data = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡La solicitud ha sido rechazada!",
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
        closeModal: function () {
            this.isOpen = false;
        },
        print(id) {
            let url = route('vacation-request-print-pdf', id);
            window.open(url, '_blank').focus();
        }
    }
}
</script>

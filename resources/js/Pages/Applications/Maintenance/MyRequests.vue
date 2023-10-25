<template>
    <div>
        <Head title="Solicitudes de mantenimiento"/>

        <portal to="application-title">
            Solicitudes de mantenimiento
        </portal>

        <portal to="actions">
            <Link :href="route('maintenance.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Crear Solicitud
            </Link>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="refuse-tab"
                            tag="button"
                            content="Rechazados"
                            data-tw-toggle="tab"
                            data-tw-target="#refuse"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            RECHAZADOS
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="revision-tab"
                            tag="button"
                            content="EN REVISION"
                            data-tw-toggle="tab"
                            data-tw-target="#revision"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            EN REVISION
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="approved-tab"
                            tag="button"
                            content="APROBADO"
                            data-tw-toggle="tab"
                            data-tw-target="#approved"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            APROBADO
                        </Tippy>
                    </li>
                    <li class="nav-item w-full">
                        <Tippy
                            id="process-tab"
                            tag="button"
                            content="EN PROCESO"
                            data-tw-toggle="tab"
                            data-tw-target="#process"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            EN PROCESO
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="finish-tab"
                            tag="button"
                            content="Finalizados"
                            data-tw-toggle="tab"
                            data-tw-target="#finish"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            FINALIZADOS
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="cancel-tab"
                            tag="button"
                            content="Anulados"
                            data-tw-toggle="tab"
                            data-tw-target="#cancel"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            ANULADOS
                        </Tippy>
                    </li>
                </ul>
                <div class="post__content tab-content p-2">
                    <div id="refuse" class="tab-pane p-2" role="tabpanel" aria-labelledby="refuse-tab">
                        <v-client-table :data="refuse_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)" @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="revision" class="tab-pane active p-2" role="tabpanel" aria-labelledby="revision-tab">
                        <v-client-table :data="revision_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)" @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="approved" class="tab-pane p-2" role="tabpanel" aria-labelledby="approved-tab">
                        <v-client-table :data="approved_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)" @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="process" class="tab-pane p-2" role="tabpanel" aria-labelledby="process-tab">
                        <v-client-table :data="process_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)" @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finish_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)" @click="cancel(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Anular
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="cancel" class="tab-pane p-2" role="tabpanel" aria-labelledby="cancel-tab">
                        <v-client-table :data="cancel_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)" @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
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
        requests: Array
    },

    components: {
        Head,
        Link
    },

    data() {
        return {
            table: {
                data: this.requests,
                columns: [
                    'consecutive',
                    'asset',
                    'applicant',
                    'created_at',
                    'type',
                    'description',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        asset: 'ACTIVO',
                        applicant: 'SOLICITANTE',
                        created_at: 'CREADO',
                        type: 'TIPO',
                        description: 'DETALLES',
                        actions: '',
                    },
                    uniqueKey: "id",
                    sortable: ['id', 'asset', 'applicant', 'type', 'state', 'created_at'],
                    dateColumns: ['created_at', 'updated_at'],
                    dateFormat: 'YYYY-MM-DD',

                    templates: {
                        asset: function (h, row) {
                            return row.asset.name
                        },
                        applicant: function (h, row) {
                            return row.applicant.name
                        },
                        type: function (h, row) {
                            if (row.type === 'preventive') {
                                return <span class="badge badge-success badge-rounded"> Preventivo </span>
                            } else if (row.type === 'corrective') {
                                return <span class="badge badge-danger badge-rounded"> Correctivo </span>
                            } else if (row.type === 'locative') {
                                return <span class="badge badge-warning badge-rounded"> Locativo </span>
                            } else {
                                return <span class="badge badge-primary badge-rounded"> Mejorativo </span>
                            }
                        },
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                }
            }
        }
    },

    methods: {
        view(id) {
            this.$inertia.visit(route('maintenance.my-request.view', id))
        },

        cancel(id) {
            this.$swal({
                title: '¿Anular Solicitud?',
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
                    axios.post(route('maintenance.cancel'), {
                        id: id,
                        justify: inputValue.value
                    }).then(res => {
                        this.table.data = res.data

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Solicitud anulada con éxito",
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
        }
    },

    computed: {
        cancel_rows(){
            return this.table.data.filter(row => row.state === '0').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        },

        revision_rows(){
            return this.table.data.filter(row => row.state === '1')
        },

        approved_rows(){
            return this.table.data.filter(row => row.state === '2')
        },

        process_rows(){
            return this.table.data.filter(row => row.state === '3')
        },

        finish_rows(){
            return this.table.data.filter(row => row.state === '4').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        },

        refuse_rows(){
            return this.table.data.filter(row => row.state === '5')
        }
    }


}
</script>

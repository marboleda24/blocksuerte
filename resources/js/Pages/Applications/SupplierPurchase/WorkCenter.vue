<template>
    <div>
        <Head title="Documentos proveedores - DIAN"/>

        <portal to="application-title">
            Documentos proveedores - DIAN
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="notify-tab"
                            tag="button"
                            content="Notificados"
                            data-tw-toggle="tab"
                            data-tw-target="#notify"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            NOTIFICADOS
                        </Tippy>
                    </li>

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
                            id="received-tab"
                            tag="button"
                            content="Serv/Merc recibidos"
                            data-tw-toggle="tab"
                            data-tw-target="#received"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            SERV/MERC RECIBIDOS
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="accepted-tab"
                            tag="button"
                            content="Serv/Merc aceptada"
                            data-tw-toggle="tab"
                            data-tw-target="#accepted"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            SERV/MERC ACEPTADA
                        </Tippy>
                    </li>
                </ul>
                <div class="post__content tab-content p-2">
                    <div id="notify" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="notify-tab">
                        <v-client-table :data="notify_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '032')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recepción
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '033')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar aceptación
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="openModa(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.xml_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar XML
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="refuse" class="tab-pane p-2" role="tabpanel" aria-labelledby="refuse-tab">
                        <v-client-table :data="refuse_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '032')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recepción
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '033')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar aceptación
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="openModa(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.xml_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar XML
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="received" class="tab-pane p-2" role="tabpanel" aria-labelledby="received-tab">
                        <v-client-table :data="received_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '032')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recepción
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '033')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar aceptación
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="openModa(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.xml_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar XML
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>

                    <div id="accepted" class="tab-pane p-2" role="tabpanel" aria-labelledby="accepted-tab">
                        <v-client-table :data="accepted_rows" :columns="table.columns" :options="table.options" ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '032')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recepción
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '033')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar aceptación
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '032'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="openModa(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.pdf_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="downloadFile(row.xml_path)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-1"/>
                                                Descargar XML
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=5xl>
                <template #title>
                    Documento electrónico
                </template>

                <template #content>
                    <template v-if="fileURL">
                        <embed class="pdfobject" :src="fileURL" type="application/pdf"
                               style="overflow: auto; width: 100%; height: 800px;">
                    </template>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
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

export default {
    components: {
        JetDialogModal,
        Head
    },

    props: {
        supplier_purchases: Array
    },

    data() {
        return {
            table: {
                data: this.supplier_purchases,
                columns: [
                    'prefix',
                    'consecutive',
                    'supplier_name',
                    'supplier_nit',
                    'date',
                    'upload_by',
                    'received_by',
                    'accepted_by',
                    'response_code',
                    'work_center',
                    'classification',
                    'actions'
                ],
                options: {
                    headings: {
                        prefix: 'PREFIJO',
                        consecutive: 'CONSECUTIVO',
                        supplier_name: 'PROVEEDOR',
                        supplier_nit: 'NIT',
                        date: 'FECHA',
                        upload_by: 'SUBIDO POR',
                        received_by: 'RECIBIDO POR',
                        accepted_by: 'ACEPTADO POR',
                        response_code: 'ESTADO DIAN',
                        work_center: 'AREA RESPONSABLE',
                        classification: 'CLASIFICACIÓN',
                        actions: '',
                    },
                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['consecutive', 'response_code', 'supplier_name', 'supplier_nit', 'upload_by'],
                    customSorting: {
                        supplier_name(ascending) {
                            return function (a, b) {
                                const lastA = a.supplier.Name[0].toLowerCase();
                                const lastB = b.supplier.Name[0].toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },

                        supplier_nit(ascending) {
                            return function (a, b) {
                                const lastA = a.supplier.CompanyID[0].toLowerCase();
                                const lastB = b.supplier.CompanyID[0].toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        }
                    },
                    templates: {
                        prefix(h, row) {
                            return row.document_information.Prefix
                        },
                        consecutive(h, row) {
                            return row.document_information.ID.replace(row.document_information.Prefix, '')
                        },
                        response_code(h, row) {
                            switch (row.dian_state) {
                                case "02":
                                    return "Documento valido";
                                case "04":
                                    return "Documento no valido";
                                case "030":
                                    return "Notificado";
                                case "031":
                                    return "Documento rechazado";
                                case "032":
                                    return "Serv/Merc recibida";
                                case "033":
                                    return "Serv/Merc aceptada";
                            }
                        },
                        supplier_name(h, row) {
                            return row.supplier.Name
                        },
                        supplier_nit(h, row) {
                            return row.supplier.CompanyID
                        },
                        date(h, row){
                            return row.application_response.IssueDate
                        },
                        upload_by(h, row) {
                            return row.upload_user.name
                        },
                        received_by(h, row) {
                            return row.received_user?.name ?? <span class="badge badge-rounded badge-warning">NA</span>
                        },
                        accepted_by(h, row) {
                            return row.accepted_user?.name ?? <span class="badge badge-rounded badge-warning">NA</span>
                        }
                    },
                    filterAlgorithm: {
                        prefix(row, query) {
                            return (row.document_information.Prefix).includes(query)
                        },
                        consecutive(row, query) {
                            return (row.document_information.ID.replace(row.document_information.Prefix, '')).includes(query)
                        },
                        supplier_name(row, query){
                            return (row.supplier.Name).includes(query)
                        },
                        supplier_nit(row, query){
                            return (row.supplier.CompanyID).includes(query)
                        }
                    }
                },
            },
            isOpen: false,
            fileURL: null,
        }
    },

    methods: {
        downloadFile(file_name) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Archivo…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.post(route('supplier-purchases.download-file'), {
                file_name: file_name
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', file_name);
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.error(err);
            })
        },

        openModa(file_name) {
            this.fileURL = route('supplier-purchases.view-pdf', file_name);
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
            this.artUrl = null;
        },

        notify(id, state){
            let title = '';
            let text = '';
            let buttonText = '';

            switch (state){
                case '030':
                    title = '¿Notificar recepción de documento?';
                    text = 'Recuerde que esta acción no es reversible';
                    buttonText = 'Si, Notificar';
                    break;
                case '031':
                    title = '¿Rechazar documento?';
                    text = 'Recuerde que esta acción no es reversible';
                    buttonText = 'Si, Rechazar';
                    break;
                case '032':
                    title = '¿Notificar recepción de mercancía?';
                    text = 'Una vez notificada la recepción de la mercancía no podrá solicitarle al proveedor nota crédito. Recuerde que esta acción no es reversible';
                    buttonText = 'Si, Notificar';
                    break;
                case '033':
                    title = '¿Aceptar mercancía?';
                    text = 'Recuerde que esta acción no es reversible';
                    buttonText = 'Si, Aceptar';
                    break;
            }
            this.$swal({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: buttonText
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('supplier-purchases.change-state'), {
                        id: id,
                        state: state
                    }).then(resp => {
                        this.table.data = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Documento procesado correctamente",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                        this.$inertia.visit(route('supplier-purchases.work-center'))
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        }
    },
    computed: {
        notify_rows(){
            return this.table.data.filter(row => row.dian_state === '030')
        },
        refuse_rows(){
            return this.table.data.filter(row => row.dian_state === '031')
        },
        received_rows(){
            return this.table.data.filter(row => row.dian_state === '032')
        },
        accepted_rows(){
            return this.table.data.filter(row => row.dian_state === '033')
        }

    }
}
</script>


<template>
    <div>
        <Head title="Documentos proveedores - DIAN"/>

        <portal to="application-title">
            Documentos proveedores - DIAN
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="uploadFile">
                <font-awesome-icon icon="cloud-arrow-up" class="mr-2"/>
                Cargar ZIP
            </button>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            PENDIENTES
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="notify-tab"
                            tag="button"
                            content="Notificados"
                            data-tw-toggle="tab"
                            data-tw-target="#notify"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
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
                    <div id="pending" class="tab-pane active p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pending_rows" :columns="table.columns" :options="table.options" ref="table1"
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
                                               @click="notify(row.id ,'030')"
                                               v-if="row.dian_state === '02'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recibido
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

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

                    <div id="notify" class="tab-pane p-2" role="tabpanel" aria-labelledby="notify-tab">
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
                                               @click="notify(row.id ,'030')"
                                               v-if="row.dian_state === '02'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recibido
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

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
                                               @click="notify(row.id ,'030')"
                                               v-if="row.dian_state === '02'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recibido
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

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
                                               @click="notify(row.id ,'030')"
                                               v-if="row.dian_state === '02'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recibido
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

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
                                               @click="notify(row.id ,'030')"
                                               v-if="row.dian_state === '02'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Notificar recibido
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="notify(row.id, '031')"
                                               v-if="row.dian_state === '030'"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'bell']" class="mr-1"/>
                                                Rechazar documento
                                            </a>

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
                    'upload_by',
                    'response_code',
                    'actions'
                ],
                options: {
                    headings: {
                        prefix: 'PREFIJO',
                        consecutive: 'CONSECUTIVO',
                        supplier_name: 'PROVEEDOR',
                        supplier_nit: 'NIT',
                        upload_by: 'SUBIDO POR',
                        response_code: 'ESTADO DIAN',
                        actions: '',
                    },
                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['consecutive', 'response_code', 'supplier_name', 'supplier_nit', 'upload_by'],
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
                        upload_by(h, row) {
                            return row.upload_user.name
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
                    },

                    customSorting: {
                        consecutive(ascending) {
                            return function (a, b) {
                                var lastA = (a.document_information.ID.replace(a.document_information.Prefix, ''))?.trim().toLowerCase();
                                var lastB = (b.document_information.ID.replace(b.document_information.Prefix, ''))?.trim().toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },

                        supplier_name(ascending){
                            return function (a, b) {
                                var lastA = (a.supplier.Name)?.trim().toLowerCase();
                                var lastB = (b.supplier.Name)?.trim().toLowerCase();

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        },

                        supplier_nit(ascending){
                            return function (a, b) {
                                var lastA = a.supplier.CompanyID?.toLowerCase();
                                var lastB = b.supplier.CompanyID?.toLowerCase();

                                console.log(lastA)

                                if (ascending)
                                    return lastA >= lastB ? 1 : -1;

                                return lastA <= lastB ? 1 : -1;
                            }
                        }
                    }
                },
            },
            isOpen: false,
            fileURL: null,
            classifications: [
                'Seleccione…',
                'ACEROS',
                'CONTADO',
                'HERRAMIENTAS',
                'INSUMOS',
                'M. DE EMPAQUE',
                'MAQUINARIA Y EQUIPO',
                'MATERIA PRIMA',
                'MTTO',
                'QUÍMICOS',
                'REPUESTOS',
                'SCOLL CO',
                'SERVICIOS',
                'VARIOS',
                'ASEO Y P.'
            ],
            work_centers: [
                'Seleccione…',
                'ADMINISTRACIÓN',
                'AUTOMÁTICAS',
                'CALIDAD',
                'CNC',
                'COMPRAS',
                'CONTABILIDAD',
                'CONTROL TROQUELES',
                'DISEÑO GRAFICO',
                'EXPORTACIONES',
                'IMPORTACIONES',
                'INGENIERÍA',
                'INYECCIÓN',
                'MANTENIMIENTO',
                'PINTURA SCOLL',
                'PINTURA UV',
                'PUNTO DE VENTA',
                'RECURSOS HUMANOS',
                'SISTEMAS',
                'SST',
                'TALLER DE FABRICACIÓN',
                'VENTAS',
                'GALVANOPLASTIA'
            ]
        }
    },

    methods: {
        uploadFile() {
            this.$swal({
                icon: 'info',
                title: 'Importación de documento electrónico',
                text: 'Selecciona un archivo .zip emitido por el proveedor',
                input: 'file',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Cargar Archivo',
                cancelButtonText: 'Cancelar',
                customClass: {
                    input: 'form-select',
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary mr-2'
                },
                buttonsStyling: false,
                reverseButtons: true,
                inputAttributes: {
                    required: true,
                    id: 'upload_file',
                    accept: '.zip'
                },
                inputValidator: (inputValue) => {
                    return !inputValue && 'Debes seleccionar un archivo'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando Documento…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    let formData = new FormData();
                    formData.append('file', inputValue.value);
                    axios.post(route('goja.supplier-purchases.import'), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(resp => {
                        this.table.data = resp.data
                        this.$swal({
                            icon: 'success',
                            title: 'Importación exitosa',
                            text: 'El documento ha sido importado con éxito.',
                            toast: true,
                            position: 'bottom-start',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            reverseButtons: false,
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: err.response.data.code,
                            html: err.response.data.msg,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 12000,
                        });
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        },

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
            axios.post(route('goja.supplier-purchases.download-file'), {
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

                    axios.post(route('goja.supplier-purchases.change-state'), {
                        id: id,
                        state: state
                    }).then(resp => {
                        this.table.data = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Documento procesado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })

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
        pending_rows(){
            return this.table.data.filter(row => row.dian_state === '02' || row.dian_state === '04')
        },
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


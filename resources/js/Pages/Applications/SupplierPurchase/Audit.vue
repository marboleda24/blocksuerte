<template>
    <div>
        <Head title="Auditoria de documentos - DIAN"/>

        <portal to="application-title">
            Auditoria de documentos - DIAN
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="uploadFile">
                <font-awesome-icon icon="cloud-arrow-up" class="mr-2"/>
                Cargar XLSX
            </button>
        </portal>

        <v-client-table :data="table.data"
                        :columns="table.columns"
                        :options="table.options"
                        class="overflow-y-auto">
        </v-client-table>

    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    name: "Audit",

    components: {
        Head
    },

    data(){
        return {
            table: {
                data: [],
                columns: [
                    'Events',
                    'UUID',
                    'Prefix',
                    'Consecutive',
                    'IssueDate',
                    'ReceptionDate',
                    'EmisorDocument',
                    'EmisorName',
                    'IVA',
                    'ICA',
                    'Total',
                ],
                options: {
                    headings: {
                        Events: 'EVENTOS',
                        UUID: 'CUFE',
                        Prefix: 'PREFIJO',
                        Consecutive: 'CONSECUTIVO',
                        IssueDate: 'FECHA GENERACION',
                        ReceptionDate: 'FECHA RECEPCION',
                        EmisorDocument: 'NIT',
                        EmisorName: 'PROVEEDOR',
                        IVA: 'IVA',
                        ICA: 'ICA',
                        Total: 'TOTAL'
                    },
                    clientSorting: false,
                    sortable: ['UUID', 'Prefix', 'Consecutive', 'IssueDate', 'ReceptionDate', 'EmisorDocument', 'EmisorName'],
                    uniqueKey: "UUID",
                    templates: {
                        Events(h, row){
                            if (row.Status.ResponseDian.StatusCode === '67'){
                               return<div class="text-center">0</div>
                            }else {
                                return<div class="text-center">{row.Status.Result.length}</div>
                            }
                        },
                        IVA(h, row){
                            return this.$h.formatCurrency(parseFloat(row.IVA))
                        },
                        ICA(h, row){
                            return this.$h.formatCurrency(parseFloat(row.ICA))
                        },
                        Total(h, row){
                            return this.$h.formatCurrency(parseFloat(row.Total))
                        }
                    },
                    cellClasses: {
                        Events: [
                            {
                                class: 'bg-red-200 dark:bg-red-800',
                                condition: row => row.Status.ResponseDian.StatusCode === '67'
                            },
                            {
                                class: 'bg-yellow-200 dark:bg-yellow-800',
                                condition: row => row.Status.Result.length > 1 && row.Status.Result.length <= 2
                            },
                            {
                                class: 'bg-green-200 dark:bg-green-800',
                                condition: row => row.Status.Result.length === 3
                            },
                        ],
                        IVA: [{class: 'text-right', condition: row => row}],
                        ICA: [{class: 'text-right', condition: row => row}],
                        Total: [{class: 'text-right', condition: row => row}],
                    },
                    childRow(h, row) {
                        if (row.Status.ResponseDian.StatusCode === '67') {
                            return <div class="text-center p-4">
                                <span class="badge badge-danger badge-rounded"> {row.Status.ResponseDian.StatusDescription} </span>
                            </div>
                        } else {
                            return<div class="overflow-x-auto p-5">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">Codigo</th>
                                            <th class="whitespace-nowrap">Descripcion</th>
                                            <th class="whitespace-nowrap">Fecha</th>
                                            <th class="whitespace-nowrap">UUID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     {row.Status.Result.map(row => {
                                         return  <tr>
                                            <td>{row.ResponseCode}</td>
                                            <td>{row.Description}</td>
                                            <td>{row.EffectiveDate}</td>
                                            <td>{row.UUID}</td>
                                        </tr>
                                     })}

                                    </tbody>
                                </table>
                            </div>

                        }
                    }
                }
            }
        }
    },

    methods: {
        uploadFile() {
            this.$swal({
                icon: 'info',
                title: 'Importación de documento electrónico',
                text: 'Selecciona un archivo .xlsx',
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
                    accept: '.xlsx'
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
                    axios.post(route('supplier-purchases.audit-check'), formData, {
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
    }
}
</script>

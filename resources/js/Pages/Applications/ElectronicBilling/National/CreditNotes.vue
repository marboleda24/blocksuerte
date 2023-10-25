<template>
    <div>
        <Head title="Notas Crédito Nacionales"/>

        <portal to="application-title">
            Notas Crédito Nacionales
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-2 gap-3">
                <Tippy tag="a" content="Busque y seleccione una fecha o rango de fechas para cargar las facturas"
                       :options="{ placement: 'top' }" href="javascript:;">
                    <Litepicker
                        v-model="date_range"
                        :options="{
                    autoApply: true,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2021,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                    maxDate: current_date
                }"
                        class="form-control w-80"
                    />
                </Tippy>

                <div class="ml-auto flex flex-row w-full">
                    <Tippy tag="button" content="Realizar auditoria de los ultimos dos meses"
                           class="btn btn-primary w-1/2 mx-2"
                           :options="{ placement: 'top' }"
                           @click.native="audit"
                    >
                        Auditoria
                    </Tippy>

                    <button class="btn btn-primary w-1/2 mx-2" @click="web_service(selected)">
                        Subir WebService
                    </button>
                </div>
            </div>
        </portal>

        <v-client-table :data="table_data" :columns="columns" :options="options" ref="table_invoices">
            <template v-slot:actions="{row}">
                <div class="dropdown text-center" v-if="row.api_document && row.api_document.state_document_id === 1">
                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                            data-tw-toggle="dropdown">
                        <font-awesome-icon icon="bars"/>
                    </button>
                    <div class="dropdown-menu">
                        <div class="dropdown-content">
                            <a href="javascript:void(0)" @click="downloadPdf(row.NUMERO)"
                               class="dropdown-item">
                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                Descargar
                            </a>
                            <a href="javascript:void(0)" @click="resend(row.NUMERO)"
                               class="dropdown-item">
                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                Reenviar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="text-center" v-else>
                    —
                </div>
            </template>
        </v-client-table>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import dom from "@left4code/tw-starter/dist/js/dom";
import {Head} from '@inertiajs/vue3'


import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    props: {
        entity: String
    },

    components: {
        Head
    },

    data() {
        return {
            date_range: '',
            count: 0,
            table_data: [],
            columns: [
                'number',
                'ov',
                'date',
                'oc',
                'customer',
                'document',
                'seller',
                'bruto',
                'discount',
                'iva',
                'state',
                'actions'
            ],
            options: {
                headings: {
                    number: '#',
                    ov: 'MEMO CREDITO',
                    date: 'FECHA',
                    oc: 'FACTURA',
                    customer: 'RAZON SOCIAL',
                    document: 'NIT/CC',
                    seller: 'VENDEDOR',
                    bruto: 'BRUTO',
                    discount: 'DESCUENTO',
                    iva: 'IVA',
                    state: 'ESTADO',
                    actions: ''
                },

                uniqueKey: "NUMERO",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['number', 'ov', 'date', 'fecha', 'term', 'customer', 'document', 'seller', 'state'],
                selectable: {
                    mode: 'multiple', // or 'multiple'
                    only(row) {
                        return !row.api_document
                    },
                    selectAllMode: 'all', // or 'page',
                    programmatic: false
                },
                cellClasses: {
                    bruto: [{class: 'text-right', condition: row => row}],
                    discount: [{class: 'text-right', condition: row => row}],
                    iva: [{class: 'text-right', condition: row => row}],
                    state: [{class: 'text-center', condition: row => row}],
                },
                templates: {
                    oc(h, row) {
                        return row.OC
                    },
                    number(h, row) {
                        return row.NUMERO
                    },
                    ov(h, row) {
                        return row.OV
                    },
                    date(h, row) {
                        return dayjs(new Date(row.FECHA)).format('DD-MM-YYYY')
                    },
                    term(h, row) {
                        return row.DESCPLAZO.trim()
                    },
                    customer(h, row) {
                        return row.RAZONSOCIAL.trim()
                    },
                    document(h, row) {
                        return row.IDENTIFICACION.trim()
                    },
                    seller(h, row) {
                        return row.NOMVENDEDOR.trim()
                    },
                    bruto(h, row) {
                        return this.$h.formatCurrency(row.BRUTO)
                    },
                    discount(h, row) {
                        return this.$h.formatCurrency(row.DESCUENTO)
                    },
                    iva(h, row) {
                        return this.$h.formatCurrency(row.IVA)
                    },
                    state(h, row) {
                        return row.api_document
                            ? (row.api_document.state_document_id === 1
                                ? <span class="badge badge-success badge-rounded">Aprobado</span>
                                : <span class="badge badge-warning badge-rounded">Pendiente</span>)
                            : <span class="badge badge-danger badge-rounded">Sin Enviar</span>
                    }
                }
            }
        }
    },

    methods: {
        get_invoices() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('electronic_billing.national.credit-notes.search-by-date', this.entity), {
                params: {
                    date: this.date_range
                }
            }).then(resp => {
                this.$swal.close();
                this.table_data = resp.data
                this.count++
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        web_service(invoices) {
            if (invoices.length > 0) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Enviando Documentos…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('electronic_billing.national.credit-notes.send-credit-note', this.entity), {
                    documents: invoices,
                    date: this.date_range
                }).then(resp => {
                    this.$refs.table_invoices.$refs.table.resetSelectedRows();

                    this.$swal({
                        icon: 'success',
                        title: 'Documentos en lista de envio',
                        text: 'Los documentos fueron puestos en la lista de envió, por favor espere al menos 10 minutos ' +
                            'para enviar nuevamente el mismo documento, recuerde que dependiendo del volumen de envíos ' +
                            'diarios esto puede tomar más tiempo de lo habitual, recuerde actualizar esta pagina para ver los cambios recientes',
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(resp.data)
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err.data);
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos un documento.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        resend(document) {
            this.$swal({
                title: `Reenviar Documento ${document}`,
                text: 'Por favor escribe los correos electronicos separados por coma',
                icon: 'info',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Enviar documento',
                inputValidator: (inputValue) => {
                    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    let values = inputValue.split(",").map(function (value) {
                        return re.test(value)
                    }).find(elem => elem === false) === undefined
                    return !values && 'Uno o varios correos electronicos no son validos'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Enviando Correo electrónico…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('send-mail-invoice-dian', this.entity), {
                        email: inputValue.value.split(","),
                        number: document,
                        prefix: 'NC'
                    }).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: 'Email(s) enviado(s) con éxito',
                            confirmButtonText: 'Aceptar',
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });
                        console.log(err.data)
                    })
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            });
        },

        downloadPdf(invoice) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Documento…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('download-invoice-dian', [this.entity_document, 'NCS-NC', invoice, this.entity])).then(resp => {
                const link = document.createElement('a');

                link.download = `${invoice}.pdf`;
                link.href = 'data:application/pdf;base64,' + resp.data;
                link.click();

                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err)
            })
        },

        audit() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Realizando auditoria…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('electronic-billing.missing-numbers-nc', this.entity)).then(resp => {
                if (resp.data.length > 0) {
                    let str = '<div class="grid grid-cols-4">';
                    resp.data.forEach(function (result) {
                        str += '<span class="mx-2">' + result + '</span>'
                    });
                    str += '</div>';

                    this.$swal({
                        icon: 'info',
                        title: 'Faltan documentos por enviar',
                        html: str,
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    this.$swal({
                        icon: 'success',
                        title: 'Auditoria completada',
                        text: 'No se encontraron documentos faltantes',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err)
            })
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        selected() {
            return this.$refs.table_invoices.$refs.table.selectedRows.map(row => row.NUMERO)
        },

        entity_document() {
            return this.entity === 'CIEV' ? 890926617 : 900349726
        }
    },

    watch: {
        date_range() {
            this.count === 0 ? this.count++ : this.get_invoices()
        }
    },

    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
    }
}
</script>

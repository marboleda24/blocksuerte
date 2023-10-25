<template>
    <div>
        <Head title="Facturas Nacionales"/>

        <portal to="application-title">
            Facturas Nacionales
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
                <div class="dropdown text-center">
                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                            data-tw-toggle="dropdown">
                        <font-awesome-icon icon="bars"/>
                    </button>
                    <div class="dropdown-menu w-40">
                        <div class="dropdown-content">


                            <a :href="route('electronic_billing.national.invoices.remission', [this.entity, row.NUMERO])"
                               target="_blank" class="dropdown-item">
                                <font-awesome-icon :icon="['far', 'file']" class="mr-1"/>
                                Generar remision
                            </a>

                            <a href="javascript:void(0)" @click="updateOC(row.NUMERO)"
                               class="dropdown-item" v-if="entity === 'GOJA' && !row.api_document">
                                <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                Actualizar OC
                            </a>

                            <template v-if="row.api_document && row.api_document.state_document_id === 1">
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
                            </template>
                        </div>
                    </div>
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
                'term',
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
                    ov: 'OV',
                    date: 'FECHA',
                    term: 'PLAZO',
                    customer: 'RAZON SOCIAL',
                    document: 'NIT/CC',
                    seller: 'VENDEDOR',
                    bruto: 'BRUTO',
                    discount: 'DESCUENTO',
                    iva: 'IVA',
                    state: 'ESTADO',
                    actions: ''
                },
                uniqueKey: 'NUMERO',
                sortable: ['number', 'ov', 'date', 'fecha', 'term', 'customer', 'document', 'seller', 'state'],
                selectable: {
                    mode: 'multiple', // or 'multiple'
                    only(row) {
                        return !row.api_document
                    },
                    selectAllMode: 'all', // or 'page',
                    programmatic: false
                },
                customSorting: {
                    customer(ascending) {
                        return function (a, b) {
                            const lastA = a.RAZONSOCIAL.toLowerCase();
                            const lastB = b.RAZONSOCIAL.toLowerCase();

                            if (ascending)
                                return lastA >= lastB ? 1 : -1;

                            return lastA <= lastB ? 1 : -1;

                        }
                    }
                },
                filterAlgorithm: {
                    number(row, query) {
                        return (row.NUMERO.toString()).includes(query)
                    },
                    ov(row, query) {
                        return (row.OV).includes(query)
                    },
                    term(row, query) {
                        return (row.DESCPLAZO.toLowerCase()).includes(query.toLowerCase())
                    },
                    customer(row, query) {
                        return (row.RAZONSOCIAL.toLowerCase()).includes(query.toLowerCase())
                    },
                    document(row, query) {
                        return (row.IDENTIFICACION).includes(query)
                    },
                    seller(row, query) {
                        return (row.NOMVENDEDOR.toLowerCase()).includes(query.toLowerCase())
                    }
                },
                cellClasses: {
                    bruto: [{class: 'text-right', condition: row => row}],
                    discount: [{class: 'text-right', condition: row => row}],
                    iva: [{class: 'text-right', condition: row => row}],
                    state: [{class: 'text-center', condition: row => row}],
                },
                templates: {
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
                        return this.$h.formatCurrency(parseFloat(row.BRUTO))
                    },
                    discount(h, row) {
                        return this.$h.formatCurrency(parseFloat(row.DESCUENTO))
                    },
                    iva(h, row) {
                        return this.$h.formatCurrency(parseFloat(row.IVA))
                    },
                    state(h, row) {
                        return row.api_document
                            ? (row.api_document.state_document_id === 1
                                ? <span class="badge badge-success badge-rounded">Aprobado</span>
                                : <span class="badge badge-warning badge-rounded">Enviado con errores</span>)
                            : <span class="badge badge-danger badge-rounded">Pendiente</span>
                    }
                },
                childRow(h, row) {
                    let counter = 0;
                    const errors = [];

                    if (parseFloat(row.BRUTO) > 20000000) {
                        errors.push('Bruto demasiado alto')
                        counter++
                    }
                    if (parseFloat(row.BRUTO) < 3000) {
                        errors.push('Bruto demasiado bajo')
                        counter++
                    }
                    if (row.BRUTO === null) {
                        errors.push('Bruto vacio')
                        counter++
                    }
                    if (row.FECHA === null) {
                        errors.push('Fecha vacia')
                        counter++
                    }
                    if (row.PLAZO === null) {
                        errors.push('Sin plazo')
                        counter++
                    }
                    if (row.RAZONSOCIAL === null) {
                        errors.push('Falta razon social')
                        counter++
                    }
                    if (row.TIPOCLIENTE === null) {
                        errors.push('Falta tipo cliente')
                        counter++
                    }
                    if (row.NOMVENDEDOR === null) {
                        errors.push('Falta Vendedor')
                        counter++
                    }
                    if ((parseFloat(row.DESCUENTO) / parseFloat(row.BRUTO) * 100) > 20) {
                        errors.push('Descuento demasiado alto')
                        counter++
                    }
                    if (row.CORREOFE === null || row.CORREOFE?.trim() === '') {
                        errors.push('Falta email facturación electronica')
                        counter++
                    }
                    if (row.MOTIVO !== '27' && ((parseFloat(row.IVA) / parseFloat(row.SUBTOTAL)) * 100) <= 18.95 || ((parseFloat(row.IVA) / parseFloat(row.SUBTOTAL)) * 100) >= 19.05) {
                        errors.push('Porcentaje de iva es mayor o menor a 19%')
                        counter++
                    }
                    if (parseFloat(row.IVA) === 0) {
                        errors.push('Documento sin IVA')
                        counter++
                    }
                    if (row.MOTIVO === null) {
                        errors.push('Falta motivo')
                        counter++
                    }
                    if (counter === 0) {
                        return <div class="text-center p-4"><span class="badge badge-success badge-rounded"> Documento OK </span>
                        </div>
                    } else {
                        return <div class="p-6 text-center text-base">
                            <ul class="list-none text-red-600">
                                <li>
                                    {errors.map(value => value)}
                                </li>
                            </ul>
                        </div>
                    }
                },
                rowClassCallback(row) {
                    let counter = 0;
                    if (parseFloat(row.BRUTO) > 20000000) {
                        counter++
                    }
                    if (parseFloat(row.BRUTO) < 3000) {
                        counter++
                    }
                    if (row.BRUTO === null) {
                        counter++
                    }
                    if (row.FECHA === null) {
                        counter++
                    }
                    if (row.PLAZO === null) {
                        counter++
                    }
                    if (row.RAZONSOCIAL === null) {
                        counter++
                    }
                    if (row.TIPOCLIENTE === null) {
                        counter++
                    }
                    if (row.NOMVENDEDOR === null) {
                        counter++
                    }
                    if ((parseFloat(row.DESCUENTO) / parseFloat(row.BRUTO) * 100) > 20) {
                        counter++
                    }
                    if (row.CORREOFE === null || row.CORREOFE.trim() === '') {
                        counter++
                    }
                    if (row.MOTIVO !== '27' && ((row.IVA / row.SUBTOTAL) * 100) <= 18.95 || ((row.IVA / row.SUBTOTAL) * 100) >= 19.05) {
                        counter++
                    }
                    if (parseFloat(row.IVA) === 0) {
                        counter++
                    }
                    if (row.MOTIVO === null) {
                        counter++
                    }

                    if (counter > 0) {
                        return 'bg-red-200'
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
                title: 'Cargando Documentos…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('electronic_billing.national.invoices.search-by-date', this.entity), {
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
                console.log(err);
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
                    title: 'Enviando documentos…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('electronic_billing.national.invoices.send-local-api', this.entity), {
                    invoices: invoices,
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
                    text: 'Selecciona al menos una factura.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        resend(document) {
            this.$swal({
                title: `Reenviar Documento ${document}`,
                text: 'Por favor escribe los correos electrónicos separados por coma',
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
                    return !values && 'Uno o varios correos electrónicos no son validos'
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
                        prefix: 'FE'
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

            axios.get(route('download-invoice-dian', [this.entity === 'CIEV' ? '890926617' : '900349726', 'FES-FE', invoice, this.entity])).then(resp => {
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

            axios.get(route('electronic-billing.missing-numbers-fe')).then(resp => {
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
        },

        updateOC(invoice) {
            this.$swal({
                title: 'Actualizar Order de compra',
                text: `Por favor, ingrese la orden de compra para la factura ${invoice}`,
                icon: 'info',
                input: 'text',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Enviar actualizacion',
                inputValidator: (inputValue) => {
                    return !inputValue && 'Este campo es obligatorio'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    axios.post(route('electronic_billing.national.invoices.updateOC', this.entity), {
                        invoice: invoice,
                        oc: inputValue.value
                    }).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Orden de compra actualizada con exito',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: err.response.data,
                            confirmButtonText: 'Aceptar'
                        });
                    })
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
        selected() {
            return this.$refs.table_invoices.$refs.table.selectedRows.map(row => row.NUMERO)
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



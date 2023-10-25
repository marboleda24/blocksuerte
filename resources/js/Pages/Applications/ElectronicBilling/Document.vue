<template>
    <div>
        <Head title="Documentos electrónicos"/>

        <portal to="application-title">
            Documentos electrónicos
        </portal>

        <div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label>Tipo de consultar</label>
                    <select class="form-select"  :class="{ 'border-danger': v$.form.type.$error }" v-model="form.type" >
                        <option value="customer">Cliente</option>
                        <option value="invoice">Factura</option>
                    </select>

                    <template v-if="v$.form.type.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.type.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div v-if="form.type === 'customer'">
                    <label>Cliente</label>
                    <autocomplete
                        :url="route('electronic-billing.documents.search-customer', this.entity)"
                        show-field="customer"
                        @selected-value="getCustomerData"
                    />

                    <template v-if="v$.form.nit.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.nit.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div v-else>
                    <label>Documento electrónico</label>

                    <input type="text" class="form-control" :class="{ 'border-danger': v$.form.invoice.$error }" v-model="form.invoice">

                    <template v-if="v$.form.invoice.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.invoice.$errors" :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>
                <div>
                    <button class="btn btn-primary h-full w-full" @click="getCustomerInvoices(form)">
                        {{ form.type === 'customer' ? 'Obtener Documentos Electrónicos' : 'Consultar Documento Electrónico' }}
                    </button>
                </div>
            </div>

            <template v-if="table.data.length > 0">
                <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                                ref="table_invoices" class="mt-5">

                    <template v-slot:actions="{row}">
                        <div class="dropdown text-center">
                            <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                    data-tw-toggle="dropdown">
                                <font-awesome-icon icon="bars"/>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-content">
                                    <a href="javascript:void(0)" @click="downloadPdf(row.file_pdf)"
                                       class="dropdown-item">
                                        <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-2"/>
                                        Descargar
                                    </a>
                                    <a href="javascript:void(0)"
                                       @click="resend(row.document, row.client.email, row.client.copy_mails, row.prefix)"
                                       class="dropdown-item">
                                        <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-2"/>
                                        Reenviar
                                    </a>
                                    <a href="javascript:void(0)"
                                       v-role="'super-admin'"
                                       @click="regenerate(row.document)"
                                       class="dropdown-item">
                                        <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-2"/>
                                        Regenerar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>
            </template>
        </div>
    </div>
</template>

<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, requiredIf} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    props: {
        entity: String
    },

    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Head
    },

    validations() {
        return {
            form: {
                type: {required},
                nit: {
                    required: requiredIf(function () {
                        return this.form.type === 'customer'
                    })
                },
                invoice: {
                    required: requiredIf(function (){
                        return this.form.type === 'invoice'
                    })
                }
            }
        }
    },

    data() {
        return {
            form: {
                type: 'customer',
                nit: '',
                invoice: ''
            },

            table: {
                data: [],
                columns: [
                    'number',
                    'date_invoice',
                    'date',
                    'type_document',
                    'actions'
                ],
                options: {
                    headings: {
                        number: 'DOCUMENTO ELECTRÓNICO',
                        date_invoice: 'FECHA FACTURA',
                        date: 'FECHA DE ENVÍO',
                        type_document: 'TIPO DOCUMENTO',
                        actions: ''
                    },
                    uniqueKey: "number",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['number', 'type_document'],
                    templates: {
                        date_invoice (h, row) {
                            return this.$h.formatDate(row.date_invoice, 'DD-MM-YYYY')
                        },
                        date (h, row) {
                            return dayjs(new Date(row.date)).format('DD-MM-YYYY')
                        },
                    },
                    rowClassCallback: function (row) {
                        return 'text-center'
                    }
                }
            },
            customers: []
        }
    },

    methods: {
        getCustomerData(obj){
            this.form.nit = obj.nit
        },

        getCustomerInvoices(form) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Obteniendo Documentos...',
                    text: 'Estamos obteniendo los documentos electrónicos, un momento por favor.',
                });

                this.invoices = []

                axios.get(route('electronic-billing.documents.get-documents', this.entity), {
                    params: {
                        type: form.type,
                        nit: form.nit,
                        invoice: form.invoice
                    }
                }).then(resp => {
                    this.table.data = resp.data
                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err.data);
                })
            }
        },

        resend(document, email, copy_mails, prefix) {
            let emails = `${email};${copy_mails}`;
            emails = emails.replace(/,/g, ';')
            emails = emails.replace(/;undefined/g, '')

            this.$swal({
                title: `Reenviar Documento ${document}`,
                text: 'Por favor escribe los correos electrónicos separados por coma',
                icon: 'info',
                input: 'textarea',
                inputValue: emails,
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
                            prefix: prefix
                        }, {
                            headers: {
                                Authorization: 'Bearer 16df96cf72fb2ccbd8109df585972476ac3923c61c4903ff0d646456537ee888'
                            }
                        }
                    ).then(resp => {
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

            axios.get(route('electronic-billing.documents.download', [this.entity, invoice])).then(resp => {
                const link = document.createElement('a');

                link.download = invoice;
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

        regenerate(document){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Regenerando Documento…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.post(route('electronic-billing.documents.regenerate'), {
                company: 890926617,
                document: document
            }).then(resp => {
                this.$swal({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: resp.data.message,
                    confirmButtonText: 'Aceptar'
                });
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
            })
        }
    }
}
</script>

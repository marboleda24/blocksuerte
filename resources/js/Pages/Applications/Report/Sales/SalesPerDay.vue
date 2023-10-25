<template>
    <div>
        <Head title="Reportes - Ventas - Facturación por dia"/>

        <portal to="application-title">
            Reportes - Ventas - Facturación por dia
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="downloadReport()">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </button>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="grid grid-cols-3 w-1/2 gap-5">
                <Litepicker
                    v-model="form.date_range"
                    :options="{
                    autoApply: true,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2022,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                    maxDate: current_date
                }"
                    class="form-control w-full"
                />

                <select class="form-select" v-model="form.type">
                    <option value="CI">CI</option>
                    <option value="NATIONAL">NACIONALES</option>
                    <option value="ALL">TODO</option>
                </select>

                <button class="btn btn-primary" @click="getDocuments">Consultar</button>
            </div>

            <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-secondary" @click="detail(row)">
                            <font-awesome-icon :icon="['far', 'eye']"/>
                        </button>
                    </div>
                </template>
            </v-client-table>

            <div class="box">
                <table class="table table-bordered table-sm mt-5 text-center mb-14">
                    <thead>
                    <tr class="text-center">
                        <th>BRUTO</th>
                        <th>DESCUENTO</th>
                        <th>SUBTOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="text-center">
                        <td>{{ $h.formatCurrency(bruto) }}</td>
                        <td>{{ $h.formatCurrency(discount) }}</td>
                        <td>{{ $h.formatCurrency(subtotal) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title v-if="modal_data">
                    Factura # {{ modal_data.NUMERO }}
                </template>

                <template #content v-if="modal_data">
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="flex flex-row">
                            <strong>CLIENTE:</strong> {{ modal_data.RAZONSOCIAL }}
                        </div>
                        <div class="flex flex-row">
                            <strong>NIT/CC:</strong> {{ modal_data.IDENTIFICACION }}
                        </div>
                        <div class="flex flex-row">
                            <strong>FECHA:</strong> {{ $h.formatDate(modal_data.FECHA) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENCIMIENTO:</strong> {{ $h.formatDate(modal_data.VENCIMIENTO) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENDEDOR:</strong> {{ modal_data.NOMVENDEDOR }}
                        </div>
                    </div>

                    <table class="table table-bordered table-sm mt-5 text-center">
                        <thead>
                        <tr>
                            <th>COD</th>
                            <th>DESCRIPCIÓN</th>
                            <th>ARTE</th>
                            <th>MARCA</th>
                            <th>PRECIO</th>
                            <th>CANTIDAD</th>
                            <th>TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in modal_data.details">
                            <td>{{ item.CodigoProducto }}</td>
                            <td>{{ item.DescripcionProducto }}</td>
                            <td>{{ item.ARTE }}</td>
                            <td>{{ item.Marca }}</td>
                            <td class="text-right">{{ $h.formatCurrency(item.Precio) }}</td>
                            <td class="text-right">{{ parseInt(item.Cantidad) }}</td>
                            <td class="text-right">{{ $h.formatCurrency(item.ValorMercancia) }}</td>
                        </tr>
                        </tbody>
                    </table>


                    <table class="table table-bordered table-sm mt-5 text-center">
                        <thead>
                        <tr>
                            <th>BRUTO</th>
                            <th>DESCUENTO</th>
                            <th>RTEFTE</th>
                            <th>RTEIVA</th>
                            <th>RTEICA</th>
                            <th>SUBTOTAL</th>
                            <th>IVA</th>
                            <th>TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                {{ $h.formatCurrency(modal_data.BRUTO) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.DESCUENTO) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.RTEFTE ?? 0) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.RTEIVA ?? 0) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.RTEICA ?? 0) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.SUBTOTAL) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(modal_data.IVA) }}
                            </td>
                            <td>
                                {{ $h.formatCurrency(parseFloat(modal_data.SUBTOTAL) + parseFloat(modal_data.IVA)) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props:{
        entity: String
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    data() {
        return {
            table: {
                data: [],
                columns: [
                    'NUMERO',
                    'OV',
                    'OC',
                    'RAZONSOCIAL',
                    'BRUTO',
                    'DESCUENTO',
                    'SUBTOTAL',
                    'TIPODOC',
                    'FECHA',
                    'actions'
                ],
                options: {
                    headings: {
                        NUMERO: 'NUMERO',
                        OV: 'OV',
                        OC: 'OC',
                        RAZONSOCIAL: 'CLIENTE',
                        BRUTO: 'BRUTO',
                        DESCUENTO: 'DESCUENTO',
                        SUBTOTAL: 'NETO',
                        TIPODOC: 'TIPO',
                        FECHA: 'FECHA',
                        actions: ''
                    },
                    stickyHeader: true,
                    templates: {
                        BRUTO(h, row) {
                            return row.TIPODOC === 'CR' ? `–${this.$h.formatCurrency(row.BRUTO, 0)}` : `${this.$h.formatCurrency(row.BRUTO, 0)}`
                        },
                        DESCUENTO(h, row) {
                            return row.TIPODOC === 'CR' ? `–${this.$h.formatCurrency(row.DESCUENTO, 0)}` : `${this.$h.formatCurrency(row.DESCUENTO, 0)}`
                        },
                        SUBTOTAL(h, row) {
                            return row.TIPODOC === 'CR' ? `–${this.$h.formatCurrency(row.SUBTOTAL, 0)}` : `${this.$h.formatCurrency(row.SUBTOTAL, 0)}`
                        },
                        TIPODOC(h, row) {
                            return row.TIPODOC === 'CR' ? 'Memo Credito' : 'Factura'
                        }
                    },
                    cellClasses: {
                        BRUTO: [{class: 'text-right', condition: row => row}],
                        DESCUENTO: [{class: 'text-right', condition: row => row}],
                        SUBTOTAL: [{class: 'text-right', condition: row => row}],
                        TIPODOC: [{class: 'text-center', condition: row => row}],
                    },

                    rowClassCallback(row) {
                        if (row.TIPODOC === 'CR'){
                            return 'text-danger'
                        }

                    }
                },
            },
            form: {
                date_range: '',
                type: 'ALL'
            },
            modal_data: null,
            isOpen: false,
        }
    },

    methods: {
        downloadReport() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            let fileName = "facturación_por_dia.xlsx"
            axios.post(route('report-sales.per-day-download-report',[this.entity]), this.form, {
                responseType: 'blob'
            }).then(resp => {
                this.$swal.close()
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', fileName)
                document.body.appendChild(link)
                link.click()
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

        getDocuments() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.post(route('report-sales.per-day-get-documents',[this.entity]), this.form).then(resp => {
                this.table.data = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(err)
            })

        },

        detail(row) {
            this.modal_data = row
            this.isOpen = true
        },

        closeModal() {
            this.isOpen = false
            this.modal_data = null
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        bruto() {
            return this.table.data.reduce(function (a, c) {
                return a + Number(isNaN(parseFloat(c.BRUTO)) ? 0 : c.TIPODOC === 'CU' ? parseFloat(c.BRUTO) : -Math.abs(parseFloat(c.BRUTO)) || 0)
            }, 0)
        },

        discount() {
            return this.table.data.reduce(function (a, c) {
                return a + Number(c.DESCUENTO || 0)
            }, 0)
        },

        subtotal() {
            return this.bruto - this.discount
        },

        date_range() {
            return this.form.date_range
        }
    }
}
</script>

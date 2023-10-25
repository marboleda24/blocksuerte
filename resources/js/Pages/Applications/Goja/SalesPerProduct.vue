<template>
    <div>
        <Head title="Ventas por producto"/>

        <portal to="application-title">
            Ventas por producto
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-2 gap-5">
                <Litepicker
                    :options="{
                        autoApply: false,
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
                        buttonText: {
                            apply: 'Aplicar',
                            cancel: 'Cancelar'
                        },
                        maxDate: current_date
                    }"
                    class="form-control"
                    @button:apply="search"
                />

                <button class="btn btn-primary" @click="download">
                    <font-awesome-icon icon="download" class="mr-2"/>
                    Descargar Informe
                </button>
            </div>

        </portal>

        <div>
            <div class="box">
                <div class="p-5">
                    <div class="overflow-y-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="text-center uppercase">
                                    <th colspan="2">PRODUCTO</th>
                                    <th>CANTIDAD</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="row in table.data">
                                    <td>{{ row.CodigoProducto}}</td>
                                    <td>{{ row.DescripcionProducto }}</td>
                                    <td class="text-right">{{ parseInt(row.quantity) }}</td>
                                    <td class="text-right">{{ $h.formatCurrency(row.total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";
import dayjs from "dayjs";

export default {
    components: {
        Head,
        Link
    },

    data() {
        return {
            table: {
                data: []
            },

            form: {
                startDate: '',
                endDate: ''
            }
        }
    },

    methods: {
        search(startDate, endDate) {
            this.form = {
                startDate: startDate,
                endDate: endDate
            }

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('goja.reports.sales-per-product.search'), {
                params: {
                    startDate: startDate,
                    endDate: endDate
                }
            }).then(resp => {
                this.table.data = resp.data
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        download() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('goja.reports.sales-per-product.download'), {
                params: this.form,
                responseType: 'blob'
            }).then(resp => {
                this.$swal.close()

                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))

                const link = document.createElement('a')

                link.href = url
                link.setAttribute('download', 'reporte.xlsx')
                document.body.appendChild(link)
                link.click()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
            })
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
    }
}
</script>

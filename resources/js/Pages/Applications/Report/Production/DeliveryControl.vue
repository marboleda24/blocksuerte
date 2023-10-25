<template>
    <div>
        <Head :title="`Produccion - Control entregas ${title}`" />

        <portal to="application-title">
            Produccion - Control entregas {{ title }}
        </portal>

        <portal to="actions" >
            <Link class="btn btn-secondary" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label for=""></label>
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
                    </div>

                    <button class="btn btn-primary" @click="downloadPDF">
                        Descargar PDF
                    </button>

                    <button class="btn btn-primary" @click="downloadExcel">
                        Descargar Excel
                    </button>
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

    props: {
        title: String,
        ct: String
    },

    data() {
        return {
            form: {
                date_range: ''
            }
        }
    },

    methods: {
        downloadExcel(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('delivery-control.report-pdf', [this.ct, 'xlsx', this.form]), {
                responseType: 'blob'
            }).then(resp => {
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', `${this.$h.toSnakeCase(this.title)}.xlsx`)
                document.body.appendChild(link)
                link.click()

                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        downloadPDF(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('delivery-control.report-pdf', [this.ct, 'pdf', this.form]), {
                responseType: 'blob'
            }).then(resp => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });

                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', `${this.$h.toSnakeCase(this.title)}.pdf`)
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
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

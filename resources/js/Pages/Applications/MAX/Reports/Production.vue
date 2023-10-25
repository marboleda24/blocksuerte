<template>
    <div>
        <Head :title="`Produccion - ${data.title}`"/>

        <portal to="application-title">
            Produccion - {{ data.title }}
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="downloadExcel()">
                <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2"/>
                Descargar en Excel
            </button>

            <button class="btn btn-primary ml-2" @click="downloadPDF()">
                <font-awesome-icon :icon="['fas', 'file-pdf']" class="mr-2"/>
                Descargar en PDF
            </button>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <v-client-table :data="data.data" :columns="table.columns" :options="table.options" class="overflow-y-auto"/>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";

export default {
    props: {
        data: Object
    },

    components: {
        Head, Link
    },

    data() {
        return {
            table: {
                columns: Object.keys(this.data.headings),
                options: {
                    headings: this.data.headings,
                    cellClasses: {
                        CANT_COMPLETADA: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        CANT_PENDIENTE: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        DIAS_OPERACION: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        DIAS_CT: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        DIAS_OV: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        DIAS_PLANTA: [{
                            class: 'text-right',
                            condition: row => row
                        }],


                    }
                }
            }
        }
    },

    methods: {
        downloadExcel() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('max.reports.production', [this.data.report, 'xlsx']), {
                responseType: 'blob'
            }).then(resp => {
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', `${this.$h.toSnakeCase(this.data.title)}.xlsx`)
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

        downloadPDF() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('max.reports.production', [this.data.report, 'pdf']), {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `${this.$h.toSnakeCase(this.data.title)}.pdf`);
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar',
                });
            })
        }
    }

}
</script>

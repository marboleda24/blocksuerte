<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Cargue de facturas a DMS
            </h2>
        </portal>

        <div class="mb-4 grid grid-cols-3 gap-3">
            <Tippy tag="a" content="Busque y seleccione una fecha o rango de fechas para cargar las facturas"
                   :options="{ placement: 'right' }" href="javascript:;">
                <LitePicker
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
                }"
                    class="form-control w-full" ref="datepicker"/>
            </Tippy>
            <div class="mr-auto flex flex-row">
                <button class="btn btn-primary" @click="uploadDMS(selected)">
                    Cargar Documentos
                </button>
            </div>
        </div>

            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table_documents">

            </v-client-table>
        </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import dom from "@left4code/tw-starter/dist/js/dom";
import {defineComponent} from "vue";


export default defineComponent({
    metaInfo: {
        title: 'Cargue de facturas a DMS'
    },

    data(){
        return {
            table: {
                data: [],
                columns: [
                    'number',
                    'customer',
                    'bruto',
                    'fletes',
                    'seguros',
                    'dms',
                ],
                options: {
                    headings: {
                        number: '#',
                        customer: 'RAZON SOCIAL',
                        bruto: 'BRUTO',
                        fletes: 'FLETES',
                        seguros: 'SEGUROS',
                        dms: 'ESTADO',
                    },
                    uniqueKey: "number",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['number', 'customer', 'document'],
                    selectable: {
                        mode: 'multiple', // or 'multiple'
                        only: function (row) {
                            return !row.dms
                        },
                        selectAllMode: 'all', // or 'page',
                        programmatic: false
                    },
                    templates: {
                        bruto: function (h, row) {
                            return this.$h.formatCurrency(row.bruto)
                        },
                        fletes: function (h, row) {
                            return this.$h.formatCurrency(row.seguros)
                        },
                        seguros: function (h, row) {
                            return this.$h.formatCurrency(row.seguros)
                        },
                        dms: function (h, row){
                            return row.dms
                                ? <span class="badge badge-success badge-rounded">Subida</span>
                                : <span class="badge badge-danger badge-rounded">Pendiente</span>
                        }
                    },
                    cellClasses: {
                        bruto: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        fletes: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        seguros: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        dms: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                }
            },
            count: 0,
            date_range: ''
        }
    },

    methods: {
        search_data(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Documentos…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('upload-export-invoice-dms.search-documents'), {
                params: {
                    date: this.date_range
                }
            }).then(resp => {
                this.$swal.close()
                this.table.data = resp.data
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

        uploadDMS(documents){
            if (documents.length > 0) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Enviando documentos…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('procedures.upload-documents-dms'), {
                    documents: documents,
                    date_range: this.date_range
                }).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Documentos cargados correctamente',
                        confirmButtonText: 'Aceptar'
                    });

                    this.table.data = resp.data.documents;
                    console.log(resp.data.result)

                    this.$refs.table_documents.resetSelectedRows();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err);
                })
            }else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos un documento.',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    },
    computed: {
        selected(){
            return this.$refs.table_documents.selectedRows.map(row => row.number)
        }
    },

    watch: {
        date_range: function () {
            this.count === 0 ? this.count++ : this.search_data()
        }
    },
    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
    }

})
</script>

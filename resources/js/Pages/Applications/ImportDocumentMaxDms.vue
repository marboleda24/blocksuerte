<template>
    <div>
        <Head title="Importación de facturas MAX > DMS"/>

        <portal to="application-title">
            Importación de facturas MAX > DMS
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-2 gap-3">
                <Tippy tag="a" content="Busque y seleccione una fecha o rango de fechas para cargar los documentos"
                       :options="{ placement: 'top' }" href="javascript:;">
                    <Litepicker
                        v-model="date_range"
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
                            maxDate: current_date
                        }"
                        class="form-control"
                    />
                </Tippy>

                <div class="ml-auto flex flex-row w-full">
                    <button class="btn btn-primary w-1/2"
                            @click="importDocuments(selected)">
                        Importar a DMS
                    </button>

                    <Tippy tag="div"
                           class="form-check form-switch w-1/2 ml-6 border-2 rounded-lg"
                           content="Los documentos serán importados a DMS automáticamente cada vez que se suba de manera exitosa un documento a la DIAN"
                           :options="{ placement: 'top' }"
                          >
                        <label class="form-check-label font-bold">
                            Importación automática
                        </label>
                        <input class="form-check-input mr-2"
                               type="checkbox"
                               v-model="switch_state"
                               @click="change_state">
                    </Tippy>
                </div>
            </div>
        </portal>

        <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="documents_table" />
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {Head} from '@inertiajs/vue3'

export default {
    props: {
        switch: Boolean,
        entity: String
    },

    components: {
        Head
    },

    data(){
        return {
            switch_state: this.switch,
            date_range: '',
            count: 0,
            table: {
                data: [],
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
                    'rtefte',
                    'rteiva',
                    'state'
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
                        rtefte: 'RTEFTE',
                        rteiva: 'RTEIVA',
                        state: 'ESTADO',
                    },
                    uniqueKey: 'NUMERO',
                    sortable: ['number', 'ov', 'date', 'fecha', 'term', 'customer', 'document', 'seller', 'state'],
                    selectable: {
                        mode: 'multiple', // or 'multiple'
                        only: function (row) {
                            return !row.DMS
                        },
                        selectAllMode: 'all', // or 'page',
                        programmatic: false
                    },
                    customSorting: {
                        customer: function (ascending) {
                            return function (a, b) {
                                const lastA = a.RAZONSOCIAL[0].toLowerCase();
                                const lastB = b.RAZONSOCIAL[0].toLowerCase();

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
                    templates: {
                        number: function (h, row) {
                            return row.NUMERO
                        },
                        ov: function (h, row) {
                            return row.OV
                        },
                        date: function (h, row) {
                            return this.$h.formatDate(row.FECHA, 'DD-MM-YYYY')
                        },
                        term: function (h, row) {
                            return row.DESCPLAZO.trim()
                        },
                        customer: function (h, row) {
                            return row.RAZONSOCIAL.trim()
                        },
                        document: function (h, row) {
                            return row.IDENTIFICACION.trim()
                        },
                        seller: function (h, row) {
                            return row.NOMVENDEDOR.trim()
                        },
                        bruto: function (h, row) {
                            return this.$h.formatCurrency(parseFloat(row.BRUTO))
                        },
                        discount: function (h, row) {
                            return this.$h.formatCurrency(parseFloat(row.DESCUENTO))
                        },
                        iva: function (h, row) {
                            return this.$h.formatCurrency(parseFloat(row.IVA))
                        },
                        rtefte: function (h, row) {
                            return this.$h.formatCurrency(parseFloat(row.RTEFTE))
                        },
                        rteiva: function (h, row) {
                            return this.$h.formatCurrency(parseFloat(row.RTEIVA))
                        },
                        state: function (h, row) {
                            return row.DMS
                                ? <span class="badge badge-success badge-rounded">Sincronizado</span>
                                : <span class="badge badge-warning badge-rounded">Pendiente</span>
                        }
                    },
                    childRow: function (h, row) {
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
                        if (row.CORREOFE === null || row.CORREOFE.trim() === '') {
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
                    rowClassCallback: function (row) {
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
                    },
                    cellClasses: {
                        bruto: [{class: 'text-right', condition: row => row}],
                        discount: [{class: 'text-right', condition: row => row}],
                        iva: [{class: 'text-right', condition: row => row}],
                        rtefte: [{class: 'text-right', condition: row => row}],
                        rteiva: [{class: 'text-right', condition: row => row}],
                        state: [{class: 'text-center', condition: row => row}],
                    }
                }
            }
        }
    },

    methods: {
        importDocuments(documents){
            if (documents.length > 0) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Sincronizando documentos…',
                    text: 'Este proceso puede tardar unos minutos.',
                });

                axios.post(route('import-document-max-dms.import-documents', this.entity), {
                    documents: documents,
                }).then(resp => {
                    this.$refs.documents_table.$refs.table.resetSelectedRows();
                    this.table.data = []

                    let str = `
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">Documento</th>
                                        <th class="whitespace-nowrap">Estado</th>
                                        <th class="whitespace-nowrap">Salida</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    resp.data.result.forEach(function (result){
                        str += `
                            <tr>
                                <td>${result.document}</td>
                                <td>${result.code}</td>
                                <td>${result.msg}</td>
                            </tr>
                        `;

                    });

                    str += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    this.$swal({
                        icon: 'success',
                        title: 'Proceso completado',
                        html: str,
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
            }else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos un documento.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        get_documents() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Documentos…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('import-document-max-dms.search', this.entity), {
                params: {
                    date: this.date_range
                }
            }).then(resp => {
                this.$swal.close();
                this.table.data = resp.data
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

        change_state(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Actualizando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.post(route('import-document-max-dms.activate-desactivate', this.entity), {
                state: !this.switch_state
            }).then(resp => {
                this.$swal.close()
                this.switch_state = resp.data
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
                console.log(error);
            })
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
        selected() {
            return this.$refs.documents_table.$refs.table.selectedRows.map(row => row.NUMERO)
        }
    },

    watch: {
        date_range: function () {
            this.count === 0 ? this.count++ : this.get_documents()
        },
    },

    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
        dom('.VueTables__select-row').addClass('text-center')
    }
}
</script>


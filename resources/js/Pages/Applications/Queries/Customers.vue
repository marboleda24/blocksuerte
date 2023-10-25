<template>
    <div>
        <Head title="Clientes MAX"/>

        <portal to="application-title">
            Clientes MAX
        </portal>

        <div class="post intro-y overflow-hidden box">
            <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                role="tablist">
                <li class="nav-item">
                    <Tippy
                        id="active-tab"
                        tag="button"
                        content="Activos"
                        data-tw-toggle="tab"
                        data-tw-target="#active"
                        href="javascript:;"
                        class="nav-link tooltip w-full sm:w-40 py-4 active"
                        role="tab"
                        aria-controls="content"
                        aria-selected="true"
                    >
                        Activos
                    </Tippy>
                </li>

                <li class="nav-item">
                    <Tippy
                        id="inactive-tab"
                        content="Inactivos"
                        tag="button"
                        data-tw-toggle="tab"
                        data-tw-target="#inactive"
                        href="javascript:;"
                        class="nav-link tooltip w-full sm:w-40 py-4"
                        role="tab"
                        aria-controls="content"
                        aria-selected="false"
                    >
                        Inactivos
                    </Tippy>
                </li>
            </ul>

            <div class="post__content tab-content p-5">
                <div id="active" class="tab-pane active p-2" role="tabpanel" aria-labelledby="active-tab">
                    <v-client-table :data="active_customers" :columns="columns" :options="options" ref="table1"
                                    class="overflow-y-auto">

                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <button class="btn btn-secondary" @click="inactive(row.CUSTID_23)">
                                    <font-awesome-icon icon="ban" class="mr-2"/>
                                    Desactivar
                                </button>
                            </div>
                        </template>

                    </v-client-table>
                </div>

                <div id="inactive" class="tab-pane p-2" role="tabpanel" aria-labelledby="inactive-tab">
                    <v-client-table :data="inactive_customers" :columns="columns" :options="options" ref="table1"
                                    class="overflow-y-auto">

                        <template v-slot:actions="{row}">
                            <div class="text-center">
                                <button class="btn btn-secondary" @click="active(row.CUSTID_23)">
                                    <font-awesome-icon icon="check-double" class="mr-2"/>
                                    Activar
                                </button>
                            </div>
                        </template>

                    </v-client-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import dayjs from "dayjs";
import 'dayjs/locale/es'
dayjs.locale('es')

export default{
    components: {
        Head
    },

    props: {
        customers: Array
    },

    data(){
        return {
            columns: [
                'code',
                'document',
                'name',
                'registration_date',
                'actions'
            ],
            options: {
                headings: {
                    code: 'CÓDIGO',
                    document: 'NIT/CC',
                    name: 'RAZON SOCIAL',
                    registration_date: 'FECHA REGISTRO',
                    actions: '',
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['code', 'document', 'name', 'registration_date'],
                templates: {
                    code(h, row) {
                        return row.CUSTID_23.trim()
                    },
                    document(h, row) {
                        return row.UDFKEY_23
                    },
                    name(h, row) {
                        return row.NAME_23.trim()
                    },
                    registration_date(h, row) {
                        return dayjs(new Date(row.CreationDate)).format('DD-MM-YYYY')
                    }
                },
                filterAlgorithm: {
                    code(row, query) {
                        return (row.CUSTID_23.trim().toLowerCase()).includes(query.toLowerCase())
                    },
                    document(row, query) {
                        return (row.UDFKEY_23.trim().toLowerCase()).includes(query.toLowerCase())
                    },
                    name(row, query) {
                        return (row.NAME_23.trim().toLowerCase()).includes(query.toLowerCase())
                    },
                },
                customSorting:{
                    code(ascending){
                        return function (a, b){
                            const lastA = a.CUSTID_23.trim().toLowerCase()
                            const lastB = b.CUSTID_23.trim().toLowerCase()
                            if (ascending)
                                return lastA >= lastB ? 1 : -1;
                            return  lastA <= lastB ? 1 : -1;
                        }
                    },
                    document(ascending){
                        return function (a, b){
                            const lastA = a.UDFKEY_23.trim().toLowerCase()
                            const lastB = b.UDFKEY_23.trim().toLowerCase()
                            if (ascending)
                                return lastA >= lastB ? 1 : -1;
                            return  lastA <= lastB ? 1 : -1;
                        }
                    },
                    name(ascending){
                        return function (a, b){
                            const lastA = a.NAME_23.trim().toLowerCase()
                            const lastB = b.NAME_23.trim().toLowerCase()
                            if (ascending)
                                return lastA >= lastB ? 1 : -1;
                            return  lastA <= lastB ? 1 : -1;
                        }
                    }
                }
            },
            customers_table: this.customers,
            active_customers: [],
            inactive_customers: []
        }
    },

    methods: {
        active(code){
            this.$swal({
                title: '¿Activar Cliente?',
                text: "el cliente quedara en estado activo, ¿Quiere continuar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, activar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tomar unos segundos, espere por favor…',
                    });

                    axios.post(route('queries.customers.active-customer'), {
                        code: code
                    }).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Cliente Activado!',
                            text: 'El cliente fue activado con éxito',
                            confirmButtonText: 'Aceptar',
                        });

                        this.customers_table = resp.data
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ops!',
                            text: 'Hubo un error procesando la solicitud',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err.data)
                    })
                }
            })

        },
        inactive(code){
            this.$swal({
                title: '¿Inactivar Cliente?',
                text: "el cliente quedara en estado inactivo, ¿Quiere continuar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, inactivar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tomar unos segundos, espere por favor…',
                    });

                    axios.post(route('queries.customers.inactive-customer'), {
                        code: code
                    }).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Cliente Inactivado!',
                            text: 'El cliente fue inactivado con éxito',
                            confirmButtonText: 'Aceptar',
                        });

                        this.customers_table = resp.data
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ops!',
                            text: 'Hubo un error procesando la solicitud',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err.data)
                    })
                }
            })
        },

        filter_data(){
            this.active_customers = this.customers_table.filter(customer => customer.STATUS_23.trim() === 'R');
            this.inactive_customers = this.customers_table.filter(customer => customer.STATUS_23.trim() === 'H')
        }
    },

    mounted() {
        this.filter_data()
    },

    watch: {
        customers_table(){
            this.filter_data()
        }
    }
}
</script>

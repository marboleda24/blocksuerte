<template>
    <div>
        <Head title="Ordenes de produccion para stock"/>

        <portal to="application-title">
            Ordenes de produccion para stock
        </portal>

        <portal to="actions">
            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <v-client-table :data="data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-primary" @click="view(row)">
                            <font-awesome-icon icon="eye"/>
                        </button>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="modal.open" @close="closeModal" max-width=3xl>
                <template #title>
                    {{ modal.data?.OP }}
                </template>

                <template #content>

                    <div class="overflow-x-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th colspan="7">FECHA DE LIBERACION: {{ modal.data.DATE }}</th>
                                </tr>
                                <tr class="text-center">
                                    <th colspan="4">PRODUCTO: {{ modal.data.DESCRIPTION }}</th>
                                    <th colspan="3">ESTADO: {{ modal.data.STATE_OP }}</th>
                                </tr>

                                <tr class="text-center">
                                    <th colspan="2">CENTRO DE TRABAJO</th>
                                    <th colspan="3">CANTIDADES</th>
                                    <th colspan="2">FECHAS</th>
                                </tr>
                                <tr class="text-center">
                                    <th class="whitespace-nowrap">OPERACION</th>
                                    <th class="whitespace-nowrap">PROCESO</th>
                                    <th class="whitespace-nowrap">EN PROCESO</th>
                                    <th class="whitespace-nowrap">COMPLETADA</th>
                                    <th class="whitespace-nowrap">DESECHADA</th>
                                    <th class="whitespace-nowrap">SALIDA</th>
                                    <th class="whitespace-nowrap">ENTREGA/MAX</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in modal.data.tracing" :class="{'bg-green-200 dark:bg-green-800': row.QUECDE_14 === 'Y'}">
                                    <td class="text-left">{{ row.OPRDES_14 }}</td>
                                    <td class="text-center">{{ row.WRKCTR_14 }}</td>
                                    <td class="text-right">{{ parseInt(row.QTYREM_14) }}</td>
                                    <td class="text-right">{{ parseInt(row.QTYCOM_14) }}</td>
                                    <td class="text-right">{{ parseInt(row.ASCRAP_14) }}</td>
                                    <td class="text-center">{{ row.MOVDTE_14 }}</td>
                                    <td class="text-center">{{ row.ORGCOM_14 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    components: {
        JetDialogModal,
        Head,
        Link
    },

    props: {
        data: Array
    },

    data() {
        return {
            table: {
                columns: [
                    "OP",
                    "CODE",
                    "DESCRIPTION",
                    "DATE",
                    "DESCRIPTION_OP",
                    "LOT",
                    "QUANTITY",
                    "actions"
                ],
                options: {
                    headings: {
                        OP: "OP",
                        CODE: "CODIGO",
                        DESCRIPTION: "DESCRIPCION",
                        DATE: "FECHA",
                        DESCRIPTION_OP: "DESCRIPCION OP",
                        LOT: "LOTE",
                        QUANTITY: "CANTIDAD",
                        actions: ""
                    },

                    cellClasses: {
                        QUANTITY: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                    },

                    sortable: ["OP", "CODE", "DESCRIPTION", "DATE", "DESCRIPTION_OP", "LOT", "QUANTITY"]
                }

            },

            modal: {
                open: false,
                data: {}
            }
        }
    },

    methods: {
        view(row) {
            this.modal = {
                open: true,
                data: row
            }
            this.orderTracing(row.OP)
        },

        closeModal() {
            this.modal = {
                open: false,
                data: {}
            }
        },

        orderTracing(op) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('report-production.production-order-stock-detail'), {
                params: {
                    op: op
                }
            }).then(resp => {
                this.modal.data.tracing = resp.data
                this.$swal.close()
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: error.response.data,
                    confirmButtonText: 'Aceptar',
                });
            })
        }

    }
}
</script>

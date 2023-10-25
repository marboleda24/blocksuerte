<template>
    <div>
        <Head title="Registro Materia Prima"/>

        <portal to="application-title">
            Registro Materia Prima - FPA-GCO-020
        </portal>


        <portal to="actions">
            <Link :href="route('raw-material.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Registrar
            </Link>

            <button class="btn btn-primary ml-2">
                <font-awesome-icon :icon="['far', 'file-excel']" class="mr-2"/>
                Descargar Informe
            </button>
        </portal>


        <v-client-table :data="data" :columns="columns" :options="options" class="overflow-y-auto">
            <template v-slot:actions="{row}">
                <div class="text-center">
                    <button class="btn btn-sm btn-secondary" @click="ver(row.oc)">
                        Ver
                        <font-awesome-icon :icon="['far','eye']" class="ml-2"/>
                    </button>
                </div>
            </template>
        </v-client-table>


        <jet-dialog-modal :show="isShow" @close="closeModal" max-width=3xl>
            <template #title>
                DESCRIPCIÓN
            </template>
            <template #content v-if="modalData">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ORDEN DE COMPRA</th>
                            <th>MATERIAL</th>
                            <th>CANTIDAD</th>
                            <th>DIMENSION</th>
                            <th>APARIENCIA</th>
                            <th>PESO</th>
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in modalData" v-bind:key="row.id">
                            <td>{{ row.oc }}</td>
                            <td>{{ row.material }}</td>
                            <td class="text-right">{{ row.cantidad }}</td>
                            <td>
                                <span v-if="row.dimension" class="badge badge-success">CUMPLE</span>
                                <span v-else class="badge badge-danger">NO CUMPLE</span>
                            </td>
                            <td>
                                <span v-if="row.apariencia" class="badge badge-success">CUMPLE</span>
                                <span v-else class="badge badge-danger">NO CUMPLE</span>
                            </td>
                            <td>
                                <span v-if="row.peso" class="badge badge-success">CUMPLE</span>
                                <span v-else class="badge badge-danger">NO CUMPLE</span>
                            </td>
                            <td>{{ row.observaciones }}</td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template #footer>
                <button class="btn btn-secondary"
                        @click="closeModal">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from '@/Jetstream/DialogModal.vue'

import 'dayjs/locale/es'
dayjs.locale('es');

export default{
    props: {
        data: Array
    },

    components: {
        JetDialogModal,
        Head,
        Link
    },

    data() {
        return {
            columns: [
                'oc',
                'created_at',
                'received_by',
                'actions'
            ],
            options: {
                headings: {
                    oc: 'ORDEN COMPRA',
                    created_at: 'FECHA',
                    received_by: 'RECIBIDO POR',
                    actions: '',
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                required: "autofocus",
                sortable: ['created_at'],
                templates: {
                    created_at: function (h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                    },
                    received_by: function (h, row) {
                        return row.received_by.name
                    }
                }

            },
            isShow: false,
            modalData: null,
        }
    },
    methods: {
        ver(oc) {
            axios.get(route('raw-material.detail-order'), {
                params: {
                    order: oc
                }
            }).then(resp => {
                this.modalData = resp.data.map(function (x) {
                    return {
                        oc: x.OC.substr(0, 8),
                        pieza: x.REFERENCIA,
                        item: x.ENTRADA,
                        id_entrada: x.IDEntrada,
                        fecha: x.TNXDTE_55,
                        material: x.PRODUCTO,
                        cantidad: parseFloat(x.CANTIDAD).toFixed(2),
                        dimension: x.registro !== null ? x.registro.dimension : false,
                        apariencia: x.registro !== null ? x.registro.appearance : false,
                        peso: x.registro !== null ? x.registro.weight : false,
                        observaciones: x.registro !== null ? x.registro.observation : ''

                    }
                })
                this.isShow = true
            }).catch(error => {
                alert(error.data)
            })
        },
        closeModal() {
            this.isShow = false,
                this.modalData = null
        },
    }
}

</script>

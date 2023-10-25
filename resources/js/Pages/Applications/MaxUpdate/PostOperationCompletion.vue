<template>
    <div>
        <Head title="Conclusion de operación"/>

        <portal to="application-title">
            Conclusion de operación
        </portal>

        <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table_claim"
                        class="overflow-y-auto">
            <template v-slot:actions="{row}">
                <button class="btn btn-sm btn-secondary" @click="openModal(row)">
                    Registrar
                    <font-awesome-icon icon="circle-right" class="ml-2"/>
                </button>
            </template>
        </v-client-table>

        <jet-dialog-modal :show="modal.open" max-width="2xl" @close="closeModal">
            <template #title>
                CONCLUSION DE OPERACIÓN OP: {{ modal.form.op }}
            </template>

            <template #content>
                <div class="grid grid-cols-2 gap-5">
                    <div class="box">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="flex-none max-w-full px-3">
                                    <div>
                                        <p class="mb-0 font-sans font-semibold leading-normal text-sm">
                                            OPERACIÓN DE SECUENCIA
                                        </p>
                                        <h5 class="mb-0 ">
                                            {{ modal.sequence_description }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="flex-none max-w-full px-3">
                                    <div>
                                        <p class="mb-0 font-sans font-semibold leading-normal text-sm">
                                            CENTRO DE TRABAJO
                                        </p>
                                        <h5 class="mb-0 ">
                                            {{ modal.work_center }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label>Cantidad</label>
                        <input type="number" class="form-control" v-model.number="modal.form.quantity">
                    </div>

                    <div>
                        <label>Maquina</label>
                        <TomSelect v-model="modal.form.machine" class="w-full">
                            <option value="">Seleccione…</option>
                            <option v-for="asset in assets" :value="asset.code">{{ asset.name }}</option>
                        </TomSelect>

                    </div>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button @click="store()" type="button" class="btn btn-primary">
                    Registrar conclusion
                </button>
            </template>
        </jet-dialog-modal>


    </div>
</template>

<script>
import {Head} from "@inertiajs/vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    components: {
        FontAwesomeIcon,
        Head,
        JetDialogModal
    },

    props: {
        orders: Array,
        assets: Array
    },

    data(){
        return {
            table: {
                data: this.orders,
                columns: [
                    'op',
                    'sequence',
                    'work_center',
                    'quantity',
                    'actions'
                ],
                options: {
                    headings: {
                        op: 'OP',
                        sequence: 'OPERACIÓN DE SECUENCIA',
                        work_center: 'CENTRO DE TRABAJO',
                        quantity: 'CANTIDAD',
                        actions: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['op', 'sequence', 'work_center', 'quantity'],
                    templates: {
                        op(h, row){
                            return row.ORDNUM_14
                        },
                        sequence(h, row){
                            return [row.OPRSEQ_14, row.OPRDES_14].join(' – ')
                        },
                        work_center(h, row){
                            return row.WRKCTR_14
                        },
                        quantity(h, row){
                            return row.QTYREM_14
                        },
                    },
                    cellClasses: {
                        quantity: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        actions: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    },

                    filterAlgorithm: {
                        op(row, query) {
                            return (row.ORDNUM_14.toString()).includes(query)
                        },
                        sequence(row, query) {
                            return ([row.OPRSEQ_14, row.OPRDES_14].join(' – ').toString()).includes(query)
                        },
                        work_center(row, query) {
                            return (row.WRKCTR_14.toString()).includes(query)
                        },
                        quantity(row, query) {
                            return (row.QTYREM_14.toString()).includes(query)
                        },
                    }
                }
            },

            modal: {
                open: false,
                sequence_description: '',
                work_center: '',
                form: {
                    op: '',
                    seq: '',
                    quantity: 0,
                    machine: ''
                }
            }
        }
    },

    methods: {
        store(){
            axios.post(route('post-operation-completion.update-operation'), this.modal.form).then(resp => {
                this.table.data = resp.data

                this.$swal({
                    title: '¡Éxito!',
                    text: "Conclusion de operación realizada con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                })
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        openModal(row){
            this.modal = {
                open: true,
                sequence_description: [row.OPRSEQ_14, row.OPRDES_14].join(' – '),
                work_center: row.WRKCTR_14.trim(),
                form: {
                    op: row.ORDNUM_14.trim(),
                    seq: row.OPRSEQ_14,
                    quantity: row.QTYREM_14,
                    machine: ''
                }
            }
        },

        closeModal(){
            this.modal = {
                open: false,
                sequence_description: '',
                work_center: '',
                form: {
                    op: '',
                    seq: '',
                    quantity: 0,
                    machine: ''
                }
            }
        }
    }
}
</script>

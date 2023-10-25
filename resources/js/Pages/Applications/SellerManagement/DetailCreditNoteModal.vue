<template>
    <div>
        <jet-dialog-modal :show="isOpen" max-width=xl>
            <template #title v-if="modal_data">
                Nota Credito # {{ modal_data.NUMERO }}
            </template>

            <template #content v-if="modal_data">
                <table class="table table--sm mt-5 text-center">
                    <thead>
                    <tr>
                        <th class="border border-b-2 dark:border-dark-5">
                            CÓDIGO
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            DESCRIPCIÓN
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            ARTE
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            MARCA
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            PRECIO
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            CANTIDAD
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            TOTAL
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in modal_data.details">
                        <td class="border dark:border-dark-5">{{ item.CodigoProducto }}</td>
                        <td class="border dark:border-dark-5">{{ item.DescripcionProducto }}</td>
                        <td class="border dark:border-dark-5">{{ item.ARTE }}</td>
                        <td class="border dark:border-dark-5">{{ item.Marca }}</td>
                        <td class="border dark:border-dark-5">{{ parseFloat(item.Precio) | toCurrency }}</td>
                        <td class="border dark:border-dark-5">{{ item.Cantidad }}</td>
                        <td class="border dark:border-dark-5">{{ parseFloat(item.ValorMercancia) | toCurrency }}</td>
                    </tr>
                    </tbody>
                </table>


                <table class="table table--sm mt-5 text-center">
                    <thead>
                    <tr>
                        <th class="border border-b-2 dark:border-dark-5">
                            BRUTO
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            DESCUENTO
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            RTEFTE
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            RTEIVA
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            RTEICA
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            SUBTOTAL
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            IVA
                        </th>
                        <th class="border border-b-2 dark:border-dark-5">
                            TOTAL
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.BRUTO) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.DESCUENTO) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.RTEFTE) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.RTEIVA) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.RTEICA) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.SUBTOTAL) }}</td>
                        <td class="border dark:border-dark-5">{{ $h.formatCurrency(modal_data.IVA) }}</td>
                        <td class="border dark:border-dark-5">
                            {{ $h.formatCurrency(modal_data.SUBTOTAL + parseFloat(modal_data.IVA)) }}
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
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    components: {
        JetDialogModal
    },

    props: {
        modal_data: Object,
        isOpen: {
            default: false
        }
    },
    methods: {
        closeModal() {
            this.$emit('close')
        }
    }
}
</script>

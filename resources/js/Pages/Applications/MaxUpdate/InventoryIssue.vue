<template>
    <div>
        <Head title="Salidas de inventario - Galvano"/>

        <portal to="application-title">
            Salidas de inventario - Galvano
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table_claim"
                            class="overflow-y-auto ">
                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-sm btn-secondary" @click="registryIssue(row)">
                            Registrar salida
                        </button>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="isOpen" max-width="xl">
                <template #title>
                    Registro de salida
                </template>

                <template #content>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="text-base font-bold">
                            {{ form.product }}
                        </div>

                        <div class="text-base font-bold text-right">
                            STOCK DISPONIBLE: {{ form.available }}
                        </div>
                    </div>
                    <fieldset class="border border-solid border-gray-300 p-3 rounded-lg mt-4" v-if="form.lots.length > 0">
                        <legend class="text-sm font-bold">LOTES</legend>

                        <div class="grid gap-4" :class="{'grid-cols-3' : form.lots.length > 2, 'grid-cols-2' : form.lots.length < 3}">
                            <div v-for="(lot, key) in form.lots">
                                <label class="flex flex-col sm:flex-row">
                                    {{ lot.lot }} <span class="ml-auto font-bold text-red-500">STOCK: {{ lot.stock }}</span>
                                </label>

                                <input type="number"
                                       class="form-control"
                                       :class="{ 'border-danger': v$.form.lots.$each.$response.$errors[key].qty.length > 0 }"
                                       v-model.number="lot.qty">

                                <template v-if="v$.form.lots.$each.$response.$errors[key].qty.length > 0">
                                    <ul class="mt-1">
                                        <li class="text-danger"
                                            v-for="(error, index) of v$.form.lots.$each.$response.$errors[key].qty"
                                            :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>

                            </div>
                        </div>
                    </fieldset>

                    <div class="mt-5">
                        <label class="flex flex-col sm:flex-row">
                            Concepto
                        </label>

                        <input type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.justify.$error }"
                               v-model.number="v$.form.justify.$model">

                        <template v-if="v$.form.justify.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="mt-5">
                        <label class="flex flex-col sm:flex-row">
                            Cantidad Total
                        </label>

                        <input type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.qty.$error }"
                               v-model.number="v$.form.qty.$model" :readonly="form.lots.length > 0">

                        <template v-if="v$.form.qty.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.qty.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click.prevent="save(form)" type="submit" class="btn btn-primary">
                        Registrar salida
                    </button>
                </template>

            </jet-dialog-modal>

        </div>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import 'dayjs/locale/es'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {maxValue, required} from '@/utils/i18n-validators'
import {Head} from '@inertiajs/vue3'
import {helpers} from "../../../utils/i18n-validators";

dayjs.locale('es')

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        products: Array
    },

    components: {
        JetDialogModal,
        Head
    },

    validations() {
        return {
            form: {
                qty: {
                    maxValue: maxValue(this.form.available)
                },

                justify: {
                    required
                },

                lots: {
                    $each: helpers.forEach({
                        qty: {
                            maxValue: helpers.withMessage(
                                () => `No hay suficiente stock disponible`,
                                ((qty, {stock}) => qty <= stock)
                            )
                        },
                    })
                }
            }
        }
    },

    data() {
        return {
            table: {
                data: this.products,
                columns: [
                    'PRTNUM_01',
                    'PMDES1_01',
                    'QTY',
                    'MAX',
                    'MIN',
                    'actions'
                ],
                options: {
                    headings: {
                        PRTNUM_01: 'CÓDIGO',
                        PMDES1_01: 'DESCRIPCIÓN',
                        QTY: 'STOCK DISPONIBLE',
                        MAX: 'MÁXIMO',
                        MIN: 'MINIMO',
                        actions: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['PRTNUM_01', 'PMDES1_01', 'QTY', 'MAX', 'MIN'],
                    templates: {
                        QTY: function (h, row) {
                            return parseFloat(row.QTY).toFixed(2) + ' ' + row.BOMUOM_01
                        },
                        MAX: function (h, row) {
                            return parseFloat(row.MAX).toFixed(2)
                        },
                        MIN: function (h, row) {
                            return parseFloat(row.MIN).toFixed(2)
                        },
                    },
                    cellClasses: {
                        QTY: [{class: 'text-right', condition: row => row}],
                        MAX: [{class: 'text-right', condition: row => row}],
                        MIN: [{class: 'text-right', condition: row => row}],
                    }
                }
            },

            form: {
                product: '',
                product_code: '',
                qty: this.total,
                available: 0,
                lots: []
            },
            isOpen: false
        }
    },

    methods: {
        closeModal() {
            this.isOpen = false
            this.v$.form.$reset();
            this.form = {
                product: '',
                product_code: '',
                qty: this.total,
                available: 0,
                lots: []
            }
        },

        save(form) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups..',
                    text: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                const sum_lots = form.lots.reduce(function (a, c) {
                    return a + Number((c.qty) || 0)
                }, 0);

                if (sum_lots === form.qty || form.qty > 0) {
                    axios.post(route('inventory-issue.store'), form).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Salida realizada con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                        this.$inertia.visit(route('inventory-issue.index'));
                        console.log(resp.data.result)

                        this.closeModal()
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Las cantidades de los lotes no coinciden con la cantidad total registrada.',
                        confirmButtonText: 'Aceptar',
                    });
                }
            }
        },

        registryIssue(row) {
            if (parseInt(row.QTY) > 0) {
                this.form = {
                    product: `${row.PRTNUM_01} - ${row.PMDES1_01}`,
                    product_code: row.PRTNUM_01,
                    qty: this.total,
                    available: parseInt(row.QTY),
                    lots: []
                }

                axios.get(route('max.inventory-issue.get-lots', row.PRTNUM_01.trim())).then(resp => {
                    this.form.lots = resp.data
                    this.isOpen = true
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err);
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'No hay stock disponible de este producto.',
                    confirmButtonText: 'Aceptar',
                });
            }
        }
    },


    watch: {

        'form.lots': {
            immediate: true,
            handler: function(value) {
                this.form.qty = value.reduce(function (a, c) {
                    return a + c.qty
                }, 0)
            },
            deep: true

        }

    }
}
</script>

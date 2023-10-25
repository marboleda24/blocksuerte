<template>
    <div>
        <Head title="Editar Recibo de Caja"/>

        <portal to="application-title">
            Editar Recibo de Caja {{ cash_receipt.consecutive }}
        </portal>

        <portal to="actions">
            <Link :href="route('cash-register-receipts.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="p-5">
                    <div class="grid grid-cols-5 gap-4">
                        <div class="mt-3 col-span-5 text-2xl">
                            {{ cash_receipt.customer.RAZON_SOCIAL }}
                        </div>

                        <div>
                            <label>TIPO RC</label>
                            <select class="form-select" v-model="form.type">
                                <option value="" selected disabled>Seleccione…</option>
                                <option value="national">Nacional</option>
                                <option value="export">Exportación</option>
                            </select>
                        </div>


                        <div>
                            <label>FECHA DE PAGO</label>

                            <Litepicker
                                v-model="form.payment_date"
                                :options="{
                                    autoApply: true,
                                    singleMode: true,
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

                            <template v-if="v$.form.payment_date.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.payment_date.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div>
                            <label>VALOR PAGADO</label>
                            <input type="number" class="form-control"
                                   :class="{ 'border-danger': v$.form.paid_value.$error }" v-model="form.paid_value">

                            <template v-if="v$.form.paid_value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.paid_value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div>
                            <label>CUENTA</label>
                            <select class="form-select" :class="{ 'border-danger': v$.form.account.$error }"
                                    v-model="form.account">
                                <option value="" selected disabled>Seleccione</option>
                                <option value="11200505">BANCOLOMBIA - xxxxxxx1953</option>
                                <option value="11200510">BANCOLOMBIA - xxxxxxx9471</option>
                                <option value="11200515">BANCOLOMBIA - xxxxxxx3587</option>
                                <option value="11100505">BANCOLOMBIA - xxxxxxx1701</option>
                                <option value="11100506">BANCOLOMBIA - xxxxxxx2074</option>
                                <option value="11100506">BANCO OCCIDENTE - xxxxxxx3489</option>
                                <option value="11101005" :disabled="form.type === 'national'">
                                    BANCOLOMBIA CAYMAN - xxxxxxx2643
                                </option>
                            </select>
                            <template v-if="v$.form.account.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.account.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div>
                            <label>FORMA DE PAGO</label>

                            <select class="form-select" :class="{ 'border-danger': v$.form.payment_method.$error }"
                                    v-model="form.payment_method">
                                <option value="" selected disabled>Seleccione</option>
                                <option value="1">Cheque</option>
                                <option value="7">Consignación / Efectivo</option>
                            </select>

                            <template v-if="v$.form.payment_method.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.payment_method.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="col-span-5">
                            <label>Comentarios</label>
                            <textarea v-model="form.comments" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-5">
                        <template v-if="form.type === 'export'">
                            <div class="grid grid-cols-2">
                                <div class="text-danger font-bold text-lg">
                                    TRM FECHA PAGO: {{ currentTRM }}
                                </div>
                            </div>
                        </template>

                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr class="uppercase">
                                    <th>
                                        <input type="checkbox" class="form-check-input" @click="checkAll()"
                                               v-model="isCheckAll"/>
                                    </th>
                                    <th>NUMERO</th>
                                    <th v-if="form.type === 'export'">TRM</th>
                                    <th>VALOR NETO</th>
                                    <th>SALDO</th>
                                    <th>DESCUENTO</th>
                                    <th v-if="form.type === 'export'">GASTOS FINANCIEROS</th>
                                    <template v-if="form.type === 'national'">
                                        <th>RETENCION</th>
                                        <th>RETEIVA</th>
                                        <th>RETEICA</th>
                                    </template>
                                    <th>OTRAS DEDUCCIONES</th>
                                    <th>OTROS INGRESOS</th>
                                    <th>TOTAL</th>
                                    <th v-if="form.type === 'export'">DIFERENCIA EN CAMBIO COP</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(invoice, index) in invoices_list" v-bind:key="invoice.numero">
                                <td>
                                    <input type="checkbox" class="form-check-input" v-model="form.invoices"
                                           :value="invoice" @change="updateCheckall()"/>
                                </td>
                                <td>{{ invoice.invoice }}</td>
                                <template v-if="form.type === 'export'">
                                    <td class="text-right">{{ invoice.trm }}</td>
                                </template>
                                <td class="text-right">
                                    {{ $h.formatCurrency(invoice.bruto_it) }}
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-right"
                                           :class="{ 'border-danger':  v$.form.invoices.$each[index] && v$.form.invoices.$each[index].bruto.$error }"
                                           v-model.number="invoice.bruto"
                                           @change="invoice.bruto = Math.max(invoice.bruto, 0)"
                                           @focus="$event.target.select()"/>
                                </td>
                                <td>
                                    <div class="relative flex w-full flex-wrap items-stretch">
                                        <button @click="calculate_discount(index)"
                                                class="z-10 h-full leading-snug font-normal text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-2 py-1">
                                            <font-awesome-icon icon="calculator"/>
                                        </button>
                                        <input type="number"
                                               class="form-control form-control-sm text-right pl-10"
                                               v-model.number="invoice.discount" min="0"
                                               @change="invoice.discount = Math.max(invoice.discount, 0)"
                                               @focus="$event.target.select()"/>
                                    </div>
                                </td>

                                <td v-if="form.type === 'export'">
                                    <input type="number"
                                           class="form-control form-control-sm text-right pl-10"
                                           v-model.number="invoice.financial_expenses" min="0"
                                           @focus="$event.target.select()"/>
                                </td>

                                <template v-if="form.type === 'national'">
                                    <td>
                                        <div class="relative flex w-full flex-wrap items-stretch">
                                            <button @click="calculate_retention(index)"
                                                    class="z-10 h-full leading-snug font-normal text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-2 py-1"
                                                    :disabled="invoice.original_retention > 0">
                                                <font-awesome-icon icon="calculator"/>
                                            </button>
                                            <input type="number"
                                                   class="form-control form-control-sm text-right pl-10"
                                                   v-model.number="invoice.retention" min="0"
                                                   @change="invoice.retention = Math.max(invoice.retention, 0)"
                                                   @focus="$event.target.select()"
                                                   :disabled="invoice.original_retention > 0"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="relative flex w-full flex-wrap items-stretch">
                                            <button @click="calculate_reteiva(index)"
                                                    class="z-10 h-full leading-snug font-normal text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-2 py-1">
                                                <font-awesome-icon icon="calculator"/>
                                            </button>
                                            <input type="number"
                                                   class="form-control form-control-sm text-right pl-10"
                                                   v-model.number="invoice.reteiva" min="0"
                                                   @change="invoice.reteiva = Math.max(invoice.reteiva, 0)"
                                                   @focus="$event.target.select()"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="relative flex w-full flex-wrap items-stretch">
                                            <button @click="calculate_reteica(index)"
                                                    class="z-10 h-full leading-snug font-normal text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-2 py-1">
                                                <font-awesome-icon icon="calculator"/>
                                            </button>
                                            <input type="number"
                                                   class="form-control form-control-sm text-right pl-10"
                                                   v-model.number="invoice.reteica" min="0"
                                                   @change="invoice.reteica = Math.max(invoice.reteica, 0)"
                                                   @focus="$event.target.select()"/>
                                        </div>
                                    </td>
                                </template>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-right"
                                           v-model.number="invoice.other_deductions" min="0"
                                           @change="invoice.other_deductions = Math.max(invoice.other_deductions, 0)"
                                           @focus="$event.target.select()"/>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-right"
                                           v-model.number="invoice.other_income" min="0"
                                           @change="invoice.other_income = Math.max(invoice.other_income, 0)"
                                           @focus="$event.target.select()"/>
                                </td>
                                <td class="text-right">
                                    {{
                                        $h.formatCurrency(invoice.total = (invoice.bruto + invoice.other_income + invoice.positive_balance) - (invoice.discount + invoice.retention + invoice.reteiva + invoice.reteica + invoice.other_deductions + invoice.financial_expenses))                                    }}
                                </td>

                                <template v-if="form.type === 'export'">
                                    <td class="text-right">
                                        {{ $h.formatCurrency(getDifference(invoice.trm, invoice.bruto, currentTRM)) }}
                                    </td>
                                </template>

                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm"
                                            @click="getInvoiceInfo(invoice.invoice)">
                                        <font-awesome-icon icon="info-circle"/>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <template v-if="v$.form.invoices.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.invoices.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>

                    <div class="overflow-x-auto mt-2 mb-10">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="uppercase">
                                <th>
                                    TOTAL CARTERA:
                                </th>
                                <th>
                                    {{ form.type === 'export' ? 'USD ' : 'COP ' }} {{ $h.formatCurrency(total_due) }}
                                </th>
                                <th>
                                    TOTAL LIQUIDADO:
                                </th>
                                <th>
                                    {{ form.type === 'export' ? 'USD ' : 'COP ' }} {{
                                        $h.formatCurrency(total_settled)
                                    }}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col items-center p-5 border-t sm:flex-row border-slate-200/60">
                    <button @click.prevent="update(form)" :disabled="form.processing"
                            type="submit" class="btn btn-primary uppercase">
                        <font-awesome-icon icon="save" class="mr-2"/>
                        Actualizar RC
                    </button>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title v-if="infoModal">
                    FACTURA # {{ infoModal.numero }}
                </template>

                <template #content v-if="infoModal">
                    <div class="px-0 sm:px-0 py-2 sm:py-2">
                        <div class="overflow-x-auto">
                            <table class="table table--sm">
                                <tbody class="uppercase">
                                <tr>
                                    <th class="border" scope="row">Fecha factura</th>
                                    <td class="border text-right">
                                        {{ $h.formatDate(infoModal.fecha) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Plazo</th>
                                    <td class="border text-right">
                                        {{ infoModal.descripcion }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Fecha vencimiento</th>
                                    <td class="border text-right">
                                        {{ $h.formatDate(infoModal.vencimiento) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Bruto</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.valor_mercancia) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Descuento (-)</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.descuento_pie) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">IVA (+)</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.iva) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Retencion (-)</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.retencion) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Abono (-)</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.valor_aplicado) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Total a pagar</th>
                                    <td class="border text-right">
                                        {{ $h.formatCurrency(infoModal.ValorTotal) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border" scope="row">Vendedor</th>
                                    <td class="border text-right">{{ infoModal.NombreVendedor }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
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
import useVuelidate from '@vuelidate/core'
import {helpers, minLength, minValue, required, sameAs} from '@/utils/i18n-validators'
import dayjs from "dayjs";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        cash_receipt: Object,
        invoices: Array
    },

    components: {
        JetDialogModal,
        Head,
        Link
    },

    validations() {
        return {
            form: {
                type: {required},
                payment_date: {required},
                paid_value: {required, sameAs: sameAs(this.total_settled)},
                account: {required},
                payment_method: {required},
                invoices: {
                    required,
                    minLength: minLength(1),
                    $each: helpers.forEach({
                        bruto_it: {
                            required
                        },
                        bruto: {
                            required,
                            minValue: minValue(0),
                            maxValue: (bruto, {bruto_it}) => bruto <= bruto_it
                        }
                    })
                },
                total_settled: {required},
            }
        }
    },

    data() {
        return {
            form: {
                id: this.cash_receipt.id,
                type: this.cash_receipt.type,
                payment_date: this.cash_receipt.payment_date,
                comments: this.cash_receipt.comments,
                paid_value: String(this.cash_receipt.total_paid),
                account: this.cash_receipt.payment_account,
                payment_method: this.cash_receipt.payment_method,
                invoices: [],
                processing: false,
                total_settled: null
            },
            invoices_list: this.invoices,
            isCheckAll: false,
            infoModal: null,
            isOpen: false,
            currentTRM: 0
        }
    },

    methods: {
        checkAll () {
            this.isCheckAll = !this.isCheckAll;
            this.form.invoices = [];
            if (this.isCheckAll) { // Check all
                for (const key in this.invoices_list) {
                    this.form.invoices.push(this.invoices_list[key]);
                }
            }
        },

        updateCheckall () {
            this.isCheckAll = this.form.invoices === this.invoices_list;
        },

        getInvoiceInfo (invoice, nit) {
            this.loading(true);
            axios.get(route('cash-register-receipts.search-document'), {
                params: {
                    invoice: invoice,
                    nit: this.form.customer_nit
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModal();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifique que toda la información sea correcta, si el problema persiste comuníquese con sistemas',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error.data);
            })
        },

        closeModal() {
            this.isOpen = false;
            this.infoModal = null;
        },

        openModal() {
            this.isOpen = true;
        },

        loading(bool) {
            if (bool) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información...',
                    text: 'Este proceso puede tardar algunos segundos.',
                });
            } else {
                this.$swal.close()
            }
        },

        calculate_discount(index) {
            this.$swal({
                title: 'Calcular descuento',
                text: 'Escriba el porcentaje de descuento que quiere calcular',
                icon: 'info',
                input: 'number',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Calcular',
                inputValidator: (inputValue) => {
                    return !inputValue && 'Escriba un valor entre 0 y 100'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.invoices_list[index].discount = this.$h.numberRound((inputValue.value * this.invoices_list[index].merchandise_value) / 100);
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        },

        calculate_retention(index) {
            axios.get(route('cash-register-receipts.consult-sales-type'), {
                params: {
                    invoice: `00${this.invoices_list[index].invoice}`
                }
            }).then(resp => {
                if (resp.data === '24') {
                    this.invoices_list[index].retention = this.$h.numberRound(((this.invoices_list[index].merchandise_value - this.invoices_list[index].discount_foot) * 4) / 100)
                } else {
                    this.invoices_list[index].retention = this.$h.numberRound(((this.invoices_list[index].merchandise_value - this.invoices_list[index].discount_foot) * 2.5) / 100)
                }
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error.data);
            });
        },

        calculate_reteiva(index) {
            this.invoices_list[index].reteiva = this.$h.numberRound((this.invoices_list[index].iva * 15) / 100);
        },

        calculate_reteica(index) {
            this.invoices_list[index].reteica = this.$h.numberRound(((this.invoices_list[index].merchandise_value - this.invoices_list[index].discount_foot) * 2) / 1000)
        },

        update(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                this.form.processing = true;
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Creando recibo de caja...',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.put(route('cash-register-receipts.update', this.form.id), data).then(res => {
                    this.errors = {};

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Recibo de caja creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    this.$inertia.visit(route('cash-register-receipts.index'));
                    this.form.processing = false;

                }).catch(err => {
                    this.form.processing = false;
                    this.errors = err.response.data.errors;
                    console.log(err);
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                });
                this.form.processing = false;
            }
        },
        getDifference(invoiceTRM, bruto, currentTRM){
            let invoice = bruto * invoiceTRM
            let currentPrice = bruto * currentTRM

            return (currentPrice - invoice).toFixed(2)
        }
    },
    computed: {
        total_due() {
            return this.invoices_list.reduce(function (a, c) {
                return a + Number(c.bruto_it || 0)
            }, 0)
        },

        total_settled() {
            return this.form.invoices.reduce(function (a, c) {
                return a + Number(c.total || 0)
            }, 0)
        },

        current_date() {
            return dayjs()
        }
    },

    watch: {
        total_settled() {
            this.form.total_settled = this.total_settled === 0 ? 0 : String(this.total_settled.toFixed(2));
        },

        'form.payment_date': function (value) {
            console.log(value)
            axios.get(route('cash-register-receipts.get-trm'), {
                params: {
                    date: value
                }
            }).then(resp => {
                this.currentTRM = resp.data.factor
            }).catch(err => {
                this.currentTRM = 0
            })
        }
    },

    mounted() {
        for (const idx in this.cash_receipt.details) {
            for (const idx2 in this.invoices) {
                if (parseInt(this.cash_receipt.details[idx].invoice) === parseInt(this.invoices[idx2].invoice)) {
                    console.log(this.cash_receipt.details[idx].total)

                    let value = this.invoices[idx2] = {
                        bruto: this.invoices[idx2].bruto,
                        bruto_it: this.invoices[idx2].bruto_it,
                        discount: this.cash_receipt.details[idx].discount,
                        discount_foot: this.invoices[idx2].discount_foot,
                        invoice: this.cash_receipt.details[idx].invoice,
                        iva: this.invoices[idx2].iva,
                        merchandise_value: this.invoices[idx2].merchandise_value,
                        other_deductions: this.cash_receipt.details[idx].other_deductions,
                        other_income: this.cash_receipt.details[idx].other_income,
                        reteica: this.cash_receipt.details[idx].reteica,
                        reteiva: this.cash_receipt.details[idx].reteiva,
                        retention: this.cash_receipt.details[idx].retention,
                        total: this.cash_receipt.details[idx].total,
                        trm: this.cash_receipt.details[idx].trm,
                        financial_expenses: this.cash_receipt.details[idx].financial_expenses,
                        positive_balance: this.cash_receipt.details[idx].positive_balance,
                    }
                    this.form.invoices.push(value)
                    break
                }
            }
        }
    }
}
</script>

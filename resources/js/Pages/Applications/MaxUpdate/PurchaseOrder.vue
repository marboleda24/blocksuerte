<template>
    <div>
        <Head title="Ordenes de compra"/>

        <portal to="application-title">
            Ordenes de compra
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-secondary" @click="getOrder(row.OC)">Registrar Compra</button>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=5xl>
                <template #title>
                    {{ title }}
                </template>

                <template #content>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="text-center uppercase">
                                <th></th>
                                <th>Linea</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Lotes</th>
                                <th>Cantidad recibida</th>
                                <th>Factura</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in orderList">
                            <td>
                                <input type="checkbox" class="form-check-input" v-model="row.selected_row">
                            </td>
                            <td class="text-center">
                                {{ [row.line, row.item].join('') }}
                            </td>
                            <td>
                                {{ [row.reference, row.product].join(' – ') }}
                            </td>
                            <td>
                                <div class="grid grid-cols-2">
                                    <div class="text-sm font-bold">
                                        Solicitada:
                                    </div>
                                    <div class="text-sm text-right">
                                        {{ row.quantity }}
                                    </div>

                                    <div class="text-sm font-bold">
                                        Pendiente:
                                    </div>
                                    <div class="text-sm text-right">
                                        {{ row.pending }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                {{ $h.formatCurrency(row.price ) }}
                            </td>
                            <td class="text-right">
                                {{ $h.formatCurrency(row.total) }}
                            </td>
                            <td class="text-center">
                                <Tippy tag="button"
                                       content="Si el producto no necesita lote no es necesario llenar el formulario de lotes"
                                       class="btn btn-sm btn-primary"
                                       :options="{ placement: 'top' }"
                                       @click.n.native="editLots(index)"
                                       :disabled="row.LOTTRK_01['0'].LOTTRK_01 !== 'Y'"
                                >
                                    <font-awesome-icon :icon="['far', 'list-alt']"/>
                                </Tippy>
                            </td>
                            <td>
                                <input type="number" class="form-control"
                                       v-model.number="row.registry_quantity">
                            </td>

                            <td>
                                <input type="text" class="form-control"
                                       v-model="row.invoice_reference">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="registerPurchase()" type="button" class="btn btn-primary">
                        Registrar compra
                    </button>
                </template>
            </jet-dialog-modal>


            <jet-dialog-modal :show="registryLots.isOpen" max-width="3xl">
                <template #title v-if="registryLots.isOpen">
                    REGISTRO DE LOTES: {{ orderList[registryLots.idx].product }}
                </template>

                <template #content v-if="registryLots.isOpen">
                    <div class="my-4 grid grid-cols-3 gap-5">
                        <div>
                            <label>
                                Lote:
                                <span >selecione para crear un nuevo lote </span>
                                <input type="checkbox" class="form-check-input ml-2" v-model="lotForm.manual">
                            </label>

                            <input v-if="lotForm.manual" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.lotForm.lot.$error }" v-model="lotForm.lot">

                            <select v-else class="form-select" :class="{ 'border-danger': v$.lotForm.lot.$error }"
                                    v-model="lotForm.lot"
                                    v-if="orderList[registryLots.idx].available_lots.length > 0">
                                <option value="">seleccione…</option>
                                <option v-for="lot in orderList[registryLots.idx].available_lots"
                                        :value="lot.LOTNUM_68">
                                    {{ lot.LOTNUM_68 }}
                                </option>
                            </select>

                            <template v-if="v$.lotForm.lot.$error">
                                <div v-if="!v$.lotForm.lot.required" class="text-theme-6 mt-2">
                                    Campo obligatorio
                                </div>
                            </template>
                        </div>

                        <div>
                            <label>Cantidad</label>
                            <input type="number" class="form-control"
                                   :class="{ 'border-danger': v$.lotForm.quantity.$error }" v-model.number="lotForm.quantity">

                            <template v-if="v$.lotForm.quantity.$error">
                                <div v-if="!v$.lotForm.quantity.required" class="text-theme-6 mt-2">
                                    Campo obligatorio
                                </div>
                            </template>
                        </div>

                        <button class="btn btn-secondary"
                                @click="addLot(lotForm, registryLots.idx)">
                            <font-awesome-icon icon="plus" class="mr-2"/>
                            Agregar lote
                        </button>
                    </div>

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="text-center uppercase">
                                <th>Lote</th>
                                <th>Cantidad</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in orderList[registryLots.idx].lots">
                                <td>{{ row.lot }}</td>
                                <td class="text-right">{{ row.quantity }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-secondary" @click="removeLot(registryLots.idx, index)">
                                        <font-awesome-icon :icon="['far', 'trash-alt']"/>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeEditLots()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>

    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import {Head} from '@inertiajs/vue3'


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Head
    },

    props: {
        orders: Array
    },

    validations() {
        return {
            lotForm: {
                lot: {required},
                quantity: {required}
            }
        }
    },

    data() {
        return {
            table: {
                data: this.orders,
                columns: [
                    'OC',
                    'PROVEEDOR',
                    'actions'
                ],
                options: {
                    headings: {
                        OC: 'OC',
                        PROVEEDOR: 'PROVEEDOR',
                        actions: 'ACCIONES',
                    },
                    clientSorting: false,
                    sortable: ['OC', 'PROVEEDOR'],
                },
            },
            orderList: [],
            isOpen: false,
            title: '',
            registryLots: {
                isOpen: false,
                idx: null
            },
            lotForm: {
                lot: '',
                quantity: '',
                manual: false
            }
        }
    },

    methods: {
        getOrder(oc) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('max-update.list-order', oc)).then(resp => {
                this.orderList = resp.data

                this.$swal.close()
                this.openModal(oc)
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err);
            })
        },

        registerPurchase() {
            const found = this.orderList.find(row => {
                return row.selected_row === true && row.registry_quantity > 0
            })
            const lot =this.orderList.find(row => {
                return row.selected_row === true && row.LOTTRK_01['0'].LOTTRK_01 ==='Y' ? row.lots['0']?.quantity > 0:  row.lots == 0
            })

            if (found === undefined) {
                this.$swal({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Debes seleccionar al menos un producto, y la cantidad debe ser  mayor a 0',
                    confirmButtonText: 'Aceptar'
                });

            } else if (lot  === undefined) {
                this.$swal({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Debes seleccionar o crear al menos un lote ',
                    confirmButtonText: 'Aceptar'
                });

            }else{
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando orden de compra…',
                    text: 'Este proceso puede tardar unos segundos.',
                });
                axios.post(route('max-update.receipt-purchase-order'), {
                    orderList: this.orderList
                }).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Proceso exitoso',
                        confirmButtonText: 'Aceptar'
                    });

                    this.table.data = resp.data.table
                    this.closeModal()
                    console.log(resp.data.result)
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err);
                })
            }
        },

      closeModal() {
        this.orderList = []
        this.isOpen = false
        this.title = ''
      },

      openModal(title) {
        this.isOpen = true
        this.title = title
      },

      editLots(idx) {
        this.registryLots = {
            isOpen: true,
            idx: idx
        }
      },

      closeEditLots() {
        this.updateLot()
        this.registryLots = {
            isOpen: false,
            idx: null
        }
      },

      addLot(form, idx) {
        this.v$.lotForm.$touch();
        if (this.v$.lotForm.$invalid) {
            this.$swal({
                icon: 'error',
                title: 'Ups.. Verifica que toda la información sea correcta',
                timerProgressBar: true,
                showConfirmButton: true,
                timer: 6000,
            });
        } else {
            this.orderList[idx].lots.push(form)
            this.resetLotForm()
            this.v$.lotForm.$reset()
        }

      },

      resetLotForm() {
        this.lotForm = {
            lot: '',
            quantity: '',
            manual: false
        }
      },

      sumLots(idx) {
        return idx
            ? this.orderList[idx].quantity - this.orderList[idx].registry_quantity
            : 100
      },

      updateLot() {
        this.orderList[this.registryLots.idx].registry_quantity = this.orderList[this.registryLots.idx].lots.reduce(function (a, c) {
            return a + c.quantity
        }, 0)
      },

      removeLot(idx, row) {
        this.orderList[idx].lots.splice(row, 1)
        this.updateLot()
      }
    }
}
</script>


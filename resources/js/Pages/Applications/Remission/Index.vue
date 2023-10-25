<template>
    <div>
        <Head title="Remisiones"/>

        <portal to="application-title">
            Remisiones
        </portal>

        <portal to="actions">
            <button @click="openModal" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Registrar Remisión
            </button>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table_remissions">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="editRemission(row)"
                                   v-if="row.state === '1'"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="closeRemission(row.id)"
                                   class="dropdown-item"
                                   v-if="row.state !== '2'">
                                    <font-awesome-icon :icon="['fas', 'times']" class="mr-1"/>
                                    Cerrar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="printRemission(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'print']" class="mr-1"/>
                                    Imprimir
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=5xl>
                <template #title>
                    Registrar remisión
                </template>

                <template #content>
                    <div class="grid grid-cols-4 md:grid-cols-4 gap-5">
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Tipo de remisión
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select" :class="{ 'border-danger': v$.form.type_id.$error }"
                                    v-model="v$.form.type_id.$model" @change="reset">
                                <option value="" selected disabled>seleccione...</option>
                                <option v-for="type in remission_types" :value="type.id" :disabled="type.id === 1">
                                    {{ type.description }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <autocomplete
                                url="/orders/search-customer"
                                anchor="name"
                                label=""
                                ref="customer_autocomplete"
                                @selected-value="customerInfo"
                                :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item', ulist: 'h-32 overflow-y-auto' }"
                                placeholder="Razon Social / NIT">
                            </autocomplete>
                        </div>

                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Orden Compra (OC)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input type="text" class="form-control" v-model="form.oc">
                        </div>

                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Tipo de venta
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select" :class="{ 'border-danger': v$.form.type_sale.$error }"
                                    v-model="v$.form.type_sale.$model">
                                <option value="" selected disabled>seleccione...</option>
                                <option value="sale">Venta</option>
                                <option value="service">Servicio</option>
                            </select>
                        </div>

                        <template v-if="form.type_id === 2 || form.type_id === 3 || form.type_id === 4">
                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Orden de venta
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>

                                <div class="input-group">
                                    <input type="number" class="form-control" placeholder="orden de venta"
                                           v-model="orderForm.ov"/>

                                    <Tippy tag="div" content="Obtener información de la order de venta"
                                           class="input-group-text cursor-pointer"
                                           :options="{ placement: 'right' }"
                                           @click.native="searchOrder(orderForm.ov)">
                                        <font-awesome-icon icon="search"/>
                                    </Tippy>
                                </div>
                            </div>
                        </template>

                        <div class="col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Notas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <textarea class="form-control" cols="30" rows="3" v-model="form.notes"></textarea>
                        </div>
                    </div>

                    <template v-if="Object.keys(orderForm.data).length > 0">
                        <div class="p-5 border-4 border-red-300 rounded-lg h-96 overflow-y-auto">
                            <div class="text-center font-medium text-base text-red-600">
                                Por favor, seleccione la entrega a la que requiere realizar remisión
                            </div>
                            <div v-for="(value, name, index) in orderForm.data"
                                 class="mt-5 border rounded-t-lg overflow-y-auto">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr class="uppercase">
                                        <th class="border-b-2 dark:border-dark-5 col-span-9" colspan="9">
                                            <div class="flex justify-center content-center items-center">
                                                <input class="form-check-input" type="checkbox" :value="value"
                                                       v-model="checkbox"/>
                                                <label class="ml-1">ENTREGA {{ name }}</label>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr class="uppercase text-center">
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Arte</th>
                                        <th>Arte 2</th>
                                        <th>Marca</th>
                                        <th>Notas</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in value" v-bind:key="index">
                                        <td class="text-center">{{ item.code }}</td>
                                        <td>{{ item.description }}</td>
                                        <td class="text-center">{{ item.art }}</td>
                                        <td class="text-center">{{ item.art2 }}</td>
                                        <td class="text-center">{{ item.brand }}</td>
                                        <td>{{ item.notes }}</td>
                                        <td class="text-right">
                                            {{ $h.formatCurrency(item.price) }}
                                        </td>
                                        <td class="text-right">{{ item.quantity }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <template
                        v-if="form.type_id === 1 || form.type_id === 5 || form.type_id === 6 || form.type_id === 7">
                        <div
                            class="grid grid-cols-4 gap-4 mt-5 border-2 border-dashed rounded-lg dark:border-gray-600 p-3">
                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Producto
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        STOCK: {{ productForm.stock }}
                                    </span>
                                </label>
                                <autocomplete
                                    url="/orders/search-products"
                                    show-field="Descripcion"
                                    ref="product_item"
                                    @selected-value="productDetail"
                                    :classes="{ wrapper: 'form-wrapper', input: 'form-control' , list: 'data-list', item: 'data-list-item' }"
                                    placeholder="Producto">
                                </autocomplete>
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Cantidad
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>
                                <input type="number" class="form-control"
                                       v-model.number="v$.productForm.quantity.$model"
                                       :class="{ 'border-danger': v$.productForm.quantity.$error }"
                                       @focus="$event.target.select()" placeholder="Cantidad" min="0">
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Precio
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>
                                <input type="number" class="form-control" v-model.number="v$.productForm.price.$model"
                                       :class="{ 'border-danger': v$.productForm.price.$error }" placeholder="Precio"
                                       @focus="$event.target.select()" min="0">
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Arte 1
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <autocomplete
                                    url="/orders/search-arts"
                                    show-field="value"
                                    ref="product_art"
                                    @selected-value="artDetail"
                                    :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
                                    placeholder="Arte 1">
                                </autocomplete>
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Arte 2
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <autocomplete
                                    url="/orders/search-arts"
                                    show-field="value"
                                    ref="product_art2"
                                    @selected-value="art2Detail"
                                    :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
                                    placeholder="Arte 2">
                                </autocomplete>
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Marca
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>
                                <autocomplete
                                    url="/orders/search-brands"
                                    show-field="value"
                                    ref="product_brand"
                                    @selected-value="brandDetail"
                                    :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
                                    placeholder="Marca">
                                </autocomplete>
                            </div>

                            <div>
                                <label class="flex flex-col sm:flex-row">
                                    Notas
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <textarea name="notes" cols="30" rows="1" class="form-control"
                                          v-model="productForm.notes" placeholder="Notas"></textarea>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary uppercase w-full h-full" @click="addProduct">
                                    <font-awesome-icon icon="cart-plus" class="mr-2"/>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="mt-10 overflow-y-auto">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr class="uppercase text-center">
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Arte</th>
                                <th>Arte 2</th>
                                <th>Marca</th>
                                <th>Notas</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th v-if="form.type_id === 1 || form.type_id === 2 || form.type_id === 5 || form.type_id === 6 || form.type_id === 7"></th>
                            </tr>
                            </thead>
                            <tbody v-if="form.detail.length > 0">
                            <tr v-for="(item, index) in form.detail" v-bind:key="index">
                                <td class="text-center">{{ item.code }}</td>
                                <td>{{ item.description }}</td>
                                <td class="text-center">{{ item.art }}</td>
                                <td class="text-center">{{ item.art2 }}</td>
                                <td class="text-center">{{ item.brand }}</td>
                                <td>{{ item.notes }}</td>
                                <td class="text-right">{{ $h.formatCurrency(item.price) }}</td>
                                <td class="text-right">{{ item.quantity }}</td>
                                <td v-if="form.type_id === 1 || form.type_id === 2 || form.type_id === 5 || form.type_id === 6 || form.type_id === 7">
                                    <div class="dropdown text-center">
                                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                                data-tw-toggle="dropdown">
                                            <font-awesome-icon icon="bars"/>
                                        </button>
                                        <div class="dropdown-menu w-40">
                                            <div class="dropdown-content">
                                                <a href="javascript:void(0)"
                                                   @click="editRow(index)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon icon="edit" class="mr-1"/>
                                                    Editar
                                                </a>

                                                <a href="javascript:void(0)"
                                                   @click="deleteRow(index)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon icon="trash-can" class="mr-1"/>
                                                    Eliminar
                                                </a>

                                                <a href="javascript:void(0)"
                                                   @click="cloneRow(index)"
                                                   class="dropdown-item">
                                                    <font-awesome-icon icon="clone" class="mr-1"/>
                                                    Clonar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td class="text-center text-red-600" colspan="9">
                                    <strong>No has agregado ningún producto</strong>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click.prevent="saveRemission(form)" type="submit" class="btn btn-primary">
                        Generar Remisión
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="editForm.isOpen" max-width="5xl">
                <template #title>
                    {{ `Remisión # ${editForm.data.consecutive}` }}
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="building-user" size="3x"/>
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0"><a
                                    href="javascript:void(0)" class="font-medium">Cliente</a>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ `${editForm.data.customer.CODIGO_CLIENTE} - ${editForm.data.customer.RAZON_SOCIAL}` }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="user" size="3x"/>
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0"><a
                                    href="javascript:void(0)" class="font-medium">Vendedor(a)</a>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ editForm.data.seller.name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="clock" size="3x"/>
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0"><a
                                    href="javascript:void(0)" class="font-medium">Condición de pago</a>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ editForm.data.customer.PLAZO}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="circle-info" size="3x"/>
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0"><a
                                    href="javascript:void(0)" class="font-medium">Tipo</a>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ editForm.data.type.description }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="building-user" size="3x"/>
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0"><a
                                    href="javascript:void(0)" class="font-medium">OV</a>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ editForm.data.order_number}}</div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mt-5 overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">Codigo</th>
                                <th class="whitespace-nowrap">Descripcion</th>
                                <th class="whitespace-nowrap">Cantidad</th>
                                <th class="whitespace-nowrap">Precio</th>
                                <th class="whitespace-nowrap">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in editForm.data.detail">
                                <td>{{ row.product }}</td>
                                <td>{{ row.info.Descripcion }}</td>
                                <td class="text-right">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           v-model="row.quantity"
                                           min="1"
                                           required>
                                </td>
                                <td class="text-right">{{ $h.formatCurrency(row.price) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.quantity * row.price) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click.prevent="updateRemission(editForm)" type="submit" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import useVuelidate from '@vuelidate/core'
import {minLength, minValue, numeric, required} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Autocomplete,
        Head
    },

    props: {
        remissions: Array,
        remission_types: Array
    },

    validations() {
        return {
            form: {
                customer_code: {required},
                bruto: {required, numeric},
                subtotal: {required, numeric},
                taxes: {required, numeric},
                discount: {required, numeric},
                total: {required, numeric},
                type_id: {required},
                type_sale: {required},
                detail: {required, minLength: minLength(1)}
            },
            productForm: {
                code: {required},
                quantity: {required, numeric, minValue: minValue(1)},
                price: {required, minValue: minValue(0)},
                art: {required},
                brand: {required}
            },
        }
    },

    data() {
        return {
            table: {
                data: this.remissions,
                columns: [
                    'consecutive',
                    'oc',
                    'customer_name',
                    'type',
                    'discount',
                    'subtotal',
                    'taxes',
                    'state',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        oc: 'OC',
                        customer_name: 'RAZON SOCIAL',
                        type: 'TIPO',
                        subtotal: 'SUBTOTAL',
                        discount: 'DESCUENTO',
                        taxes: 'IVA',
                        state: 'ESTADO',
                        created_at: 'CREADO',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['id', 'oc', 'order_max', 'type', 'customer_name', 'created_at'],
                    templates: {
                        oc(h, row) {
                            return row.oc
                                ? <span class="badge badge-success">{row.oc}</span>
                                : <span class="badge badge-danger">N/A</span>
                        },
                        subtotal(h, row) {
                            return this.$h.formatCurrency(row.subtotal)
                        },
                        taxes(h, row) {
                            return this.$h.formatCurrency(row.taxes)
                        },
                        discount(h, row) {
                            return this.$h.formatCurrency(row.discount)
                        },
                        type(h, row) {
                            return row.type.description
                        },
                        created_at(h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                        },
                        customer_name(h, row) {
                            return row.customer.CODIGO_CLIENTE + ' - ' + row.customer.RAZON_SOCIAL
                        },
                        state(h, row) {
                            switch (row.state) {
                                case "0":
                                    return <span class="badge badge-danger badge-rounded">anulado</span>
                                case "1":
                                    return <span class="badge badge-warning badge-rounded">abierto</span>
                                case "2":
                                    return <span class="badge badge-success badge-rounded">cerrado</span>
                            }
                        }
                    },
                    cellClasses: {
                        oc: [{class: 'text-center', condition: row => row}],
                        subtotal: [{class: 'text-right', condition: row => row}],
                        taxes: [{class: 'text-right', condition: row => row}],
                        discount: [{class: 'text-right', condition: row => row}],
                        state: [{class: 'text-center', condition: row => row}],
                    }
                }
            },
            form: {
                customer_code: null,
                notes: null,
                bruto: 0,
                subtotal: 0,
                taxes: 0,
                discount: 0,
                total: 0,
                oc: null,
                type_id: '',
                order_number: null,
                order_max: null,
                type_sale: '',
                detail: []
            },
            productForm: {
                stock: 0,
                code: '',
                product: '',
                quantity: 0,
                price: 0,
                art: '',
                art2: '',
                brand: '',
                notes: ''
            },

            orderForm: {
                ov: null,
                data: {}
            },

            editForm: {
                isOpen: false,
                data: {}
            },

            isOpen: false,
            checkbox: []
        }
    },

    methods: {
        openModal() {
            this.isOpen = true
        },

        closeModal() {
            this.resetForm()
            this.isOpen = false
            this.$refs.customer_autocomplete?.showInput(true)

            this.editForm = {
                isOpen: false,
                data: {}
            }
        },

        resetForm() {
            this.form = {
                customer_code: null,
                notes: null,
                bruto: 0,
                subtotal: 0,
                taxes: 0,
                discount: 0,
                total: 0,
                oc: null,
                currency: null,
                type_id: null,
                order_number: null,
                order_max: null,
                type_sale: null,
                detail: []
            }
        },

        resetProductForm() {
            this.productForm = {
                stock: 0,
                code: '',
                product: '',
                quantity: 0,
                price: 0,
                art: '',
                art2: '',
                brand: ''
            }
            this.$refs.product_item.showInput(true)
            this.$refs.product_art.showInput(true)
            this.$refs.product_art2.showInput(true)
            this.$refs.product_brand.showInput(true)
        },

        saveRemission(form) {
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Por favor, verifica que toda la information sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                axios.post(route('remission.store'), form).then(resp => {
                    this.table.data = resp.data
                    this.closeModal();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err.data)
                })
            }
        },

        updateRemission(form){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Actualizando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.put(route('remission.update', form.data.id), form.data).then(resp => {
                this.table.data = resp.data
                this.closeModal()
                this.$swal({
                    title: '¡Éxito!',
                    text: "Remisión actualizada con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        customerInfo(obj) {
            this.form.customer_code = obj.code
        },

        orderDetails(order) {
            axios.get(route('remission.order-detail', order)).then(resp => {
                if (resp.data === null) {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: `No se encontró ningún pedido con el número ${order}`,
                        confirmButtonText: 'Aceptar',
                    });
                } else {
                    this.form.detail = resp.data
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err.data)
            })
        },

        productDetail(obj) {
            this.productForm.stock = obj.Cant
            this.productForm.code = obj.Pieza
            this.productForm.product = obj.Descripcion
            this.productForm.description = obj.Descripcion
        },

        artDetail(obj) {
            this.productForm.art = obj.value
        },

        art2Detail(obj) {
            this.productForm.art2 = obj.value
        },

        brandDetail(obj) {
            this.productForm.brand = obj.value
        },

        addProduct() {
            this.v$.productForm.$touch();

            if (this.v$.productForm.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Recuerda buscar y seleccionar el producto, el arte y la marca. Recuerda que estos son obligatorios',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                this.form.detail.push(this.productForm);
                this.resetProductForm();
                this.v$.productForm.$reset()
            }
        },

        deleteRow(idx) {
            this.form.detail.splice(idx, 1);
        },

        cloneRow(idx) {
            const objCopy = this._.cloneDeep(this.form.detail[idx]);
            this.form.detail.push(objCopy);
        },

        searchOrder(ov) {
            axios.get(route('remission.order-detail', ov)).then(resp => {
                this.orderForm.data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err.data)
            })
        },

        reset() {
            this.orderForm = {
                ov: null,
                data: []
            }
            this.form.detail = []
        },

        editRemission(row) {
            this.editForm = {
                isOpen: true,
                data: row
            }
        },

        closeRemission(id) {
            this.$swal({
                title: '¿Cerrar remisión?',
                text: 'Por favor, escribe el documento de soporte para continuar',
                icon: 'info',
                input: 'number',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Cerrar remisión',
                inputValidator: (inputValue) => {
                    return !inputValue && 'El documento de soporte es obligatorio'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Cerrando remisión…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('remission.close'), {
                        id: id,
                        document_support: inputValue.value
                    }).then(resp => {
                        this.table.data = resp.data
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Remisión cerrada con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud. Error: '.err.data,
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        },

        printRemission(id) {
            let url = route('remission.print-pdf', id);
            window.open(url, '_blank').focus();
        }
    },

    computed: {
        bruto() {
            return this.form.detail.reduce(function (a, c) {
                return a + Number((c.price * c.quantity) || 0)
            }, 0)
        },

        discount() {
            return Number((this.form.discount * this.bruto) / 100 || 0)
        },

        subtotal() {
            return Number(this.bruto - ((this.form.discount * this.bruto) / 100) || 0)
        },

        tax(){
            return this.form.tax === '1' ? Number((this.subtotal * 19) / 100 || 0) : 0
        },

        total() {
            return Number(this.subtotal + this.tax || 0)
        },
    },

    watch: {
        bruto() {
            this.form.bruto = this.bruto;
        },

        discount() {
            this.form.discount = this.discount;
        },

        subtotal() {
            this.form.subtotal = this.subtotal;
        },

        tax() {
            this.form.taxes = this.tax;
        },

        total() {
            this.form.total = this.total;
        },

        checkbox() {
            if (this.checkbox.length > 0) {
                this.form.detail = []
                this.checkbox.map(obj => this.form.detail.push(obj))
            } else {
                this.form.detail = []
            }
        }
    },
}
</script>

<template>
    <div>
        <Head title="Empaque de exportaciones"/>

        <portal to="application-title">
            Empaque de exportaciones
        </portal>

        <portal to="actions">
            <button class="btn btn-secondary" @click="generatePackList(selected)">
                <font-awesome-icon icon="boxes-packing" class="mr-2"/>
                Generar lista de empaque
            </button>

            <Link :href="route('packing-order-exportation.list')" class="btn btn-secondary ml-2">
                <font-awesome-icon icon="list" class="mr-2"/>
                Listas generadas
            </Link>

            <Link :href="route('packing-order-exportation.index')" class="btn btn-secondary ml-2">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:child_row="props">
                    <div class="overflow-x-auto p-2 bg-white dark:bg-darkmode-700">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>OP</th>
                                <th>PRODUCTO</th>
                                <th>CANT. ACTUAL</th>
                                <th>FECHA OV</th>
                                <th>FECHA OP</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="element in props.row.items">
                                    <td>
                                        <button class="btn btn-sm btn-secondary" @click="showOP(element.op)">
                                            {{ element.op }}
                                        </button>
                                    </td>
                                    <td>{{ element.product }}</td>
                                    <td class="text-right">{{ element.quantity }}</td>
                                    <td>{{ element.date_ov }}</td>
                                    <td>{{ element.date_op }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="modal.open" max-width="5xl" @close="closeModal">
                <template #title>
                    Lista de empaque
                </template>

                <template #content>
                    <template v-for="(order, key) in modal.data">
                        <div class="overflow-x-auto" :class="{'mt-5 pt-5 border-t-2 border-primary': key > 0}">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                <tr>
                                    <td class="w-1/2 font-bold">CLIENTE</td>
                                    <td class="w-1/2">{{ order.customer }}</td>
                                </tr>
                                <tr>
                                    <td class="w-1/2 font-bold">COMERCIAL</td>
                                    <td class="w-1/2">{{ order.seller }}</td>
                                </tr>
                                <tr>
                                    <td class="w-1/2 font-bold">PEDIDO</td>
                                    <td class="w-1/2">{{ order.order }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th></th>
                                        <th>PRODUCTO</th>
                                        <th>NOTAS</th>
                                        <th>MARCA</th>
                                        <th>ARTE</th>
                                        <th>ARTE 2</th>
                                        <th>UM</th>
                                        <th>CANT</th>
                                        <th>PESO POR MILLAR</th>
                                        <th>PESO TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in order.items">
                                        <td>{{ idx+1 }}</td>
                                        <td>{{ item.product }}</td>
                                        <td>{{ item.notes }}</td>
                                        <td>{{ item.brand }}</td>
                                        <td>{{ item.art }}</td>
                                        <td>{{ item.art2 }}</td>
                                        <td>{{ item.um }}</td>
                                        <td class="text-right">
                                            <input type="number"
                                                   class="form-control form-control-sm"
                                                   :class="{ 'border-danger': v$.modal.data.$each.$response.$data[key]?.items.$each.$data[idx]?.quantity.$error }"
                                                   v-model="item.quantity">
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control form-control-sm"
                                                   :class="{ 'border-danger': v$.modal.data.$each.$response.$data[key]?.items.$each.$data[idx]?.weight.$error }"
                                                   v-model="item.weight">
                                        </td>
                                        <td class="text-right">{{ ((item.quantity / 1000) * item.weight).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <button class="btn btn-primary mt-10" @click="generatePacking">
                        Previsualizar lista de empaque
                    </button>

                    <div class="mt-10 pt-10 border-t-2 border-danger">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th colspan="5">LISTA DE EMPAQUE</th>
                                </tr>
                                <tr>
                                    <th>CAJA</th>
                                    <th>PRODUCTOS</th>
                                    <th>PESO TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(box, key) in modal.boxList">
                                    <td class="text-center">{{ key+1 }}</td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr v-for="item in removeDuplicates(box.products)">
                                                    <td class="w-2/3">{{ item.name }}</td>
                                                    <td class="w-1/3 text-right">{{ item.units }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td class="text-right">{{ box.totalWeight.toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="store()" type="button" class="btn btn-primary">
                        Generar Lista de empaque
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="showOrderModal.open" max-width="5xl" @close="closeModal">
                <template #title>
                    Visualizar orden de produccion
                </template>

                <template #content>
                    <div class="overflow-x-auto" v-for="(order, key) in showOrderModal.data" :class="{'mt-5' : key > 0}">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <th colspan="4" class="whitespace-nowrap">ORDEN DE MANUFACTURA No. {{ order.ORDNUM_10 }}</th>
                                <th colspan="3" class="whitespace-nowrap">FECHA DE LIBERACION: </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="whitespace-nowrap">PRODUCTO MANUFACTURADO: {{ `${order.PRTNUM_10} - ${order.PMDES1_01}` }}</th>
                                <th colspan="3" class="whitespace-nowrap">
                                    ESTADO DE LA ORDEN:
                                    <span class="badge-sm badge-rounded"
                                          :class="{'badge-danger': order.STATUS_10 === '3', 'badge-success': order.STATUS_10 === '4'}">
                                        {{ order.STATUS_10 === '3' ? 'Abierta' : 'Cerrada' }}
                                    </span>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <th colspan="2" class="whitespace-nowrap">CENTO DE TRABAJO</th>
                                <th colspan="2" class="whitespace-nowrap">CANTIDADES</th>
                                <th colspan="3" class="whitespace-nowrap">FECHAS</th>
                            </tr>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">OPERACIÓN</th>
                                <th class="whitespace-nowrap">PROCESO</th>
                                <th class="whitespace-nowrap">EN PROCESO</th>
                                <th class="whitespace-nowrap">COMPLETADA</th>
                                <th class="whitespace-nowrap">DESECHADA</th>
                                <th class="whitespace-nowrap">SALIDA</th>
                                <th class="whitespace-nowrap">ENTREGA/MAX</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in order.detail" :class="{'bg-green-200': item.QUECDE_14 === 'Y'}">
                                <td class="text-center">{{ item.OPRDES_14 }}</td>
                                <td class="text-center">{{ item.WRKCTR_14}}</td>
                                <td class="text-right">{{ parseInt(item.QTYREM_14) }}</td>
                                <td class="text-right">{{ parseInt(item.QTYCOM_14) }}</td>
                                <td class="text-right">{{ parseInt(item.ASCRAP_14) }}</td>
                                <td class="text-center">{{ item.MOVDTE_14 ? $h.formatDate(item.MOVDTE_14, 'YYYY-MM-DD') : '–' }}</td>
                                <td class="text-center">{{ item.ORGCOM_14 ? $h.formatDate(item.ORGCOM_14, 'YYYY-MM-DD') : '–'}}</td>
                            </tr>
                            </tbody>
                        </table>
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

import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head, Link} from "@inertiajs/vue3";
import dom from "@left4code/tw-starter/dist/js/dom";
import useVuelidate from '@vuelidate/core'
import {required, minValue, helpers, numeric} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        orders: Array
    },

    components: {
        Link,
        Head,
        JetDialogModal
    },

    validations(){
        return {
            modal: {
                data: {
                    $each: helpers.forEach({
                        items: {
                            $each: helpers.forEach({
                                quantity: {
                                    required,
                                    numeric,
                                    minValue: minValue(1),
                                },
                                weight: {
                                    required,
                                    numeric,
                                    minValue: minValue(1)
                                }
                            })
                        }
                    })
                }
            }
        }
    },

    data() {
        return {
            table: {
                data: this.orders,
                columns: [
                    'ov',
                    'customer',
                    'order',
                ],
                options: {
                    headings: {
                        ov: 'OV',
                        customer: 'CLIENTE',
                        order: 'PEDIDO',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    uniqueKey: 'ov',
                    selectable: {
                        mode: 'multiple', // or 'multiple'
                        selectAllMode: 'page', // or 'page',
                        programmatic: false
                    },
                    sortable: [
                        'ov', 'customer', 'order',
                    ],
                }
            },
            modal: {
                open: false,
                data: {},
                boxList: []
            },

            showOrderModal: {
                open: false,
                data: {}
            }
        }
    },

    methods: {
        generatePackList(products){
            if (products.length === 0){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos un registro',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.modal = {
                    open: true,
                    data: products,
                    boxList: {}
                }
            }
        },

        closeModal(){
            this.modal = {
                open: false,
                data: {},
                boxList: []
            }

            this.showOrderModal = {
                open: false,
                data: {}
            }

            this.v$.modal.$reset()
        },

        packingProducts(products) {
            const maxBoxWeight = 24;
            let boxes = [];
            let currentBox = {
                products: [],
                size: '',
                totalWeight: 0
            };

            function addProduct(product, quantity) {
                const productWeight = (product.weight / 1000) * product.quantity;
                const packWeight = product.weight;
                const completePackages = Math.floor(quantity / 1000);
                const remainingUnits = quantity % 1000;

                for (let i = 0; i < completePackages; i++) {
                    if (currentBox.totalWeight + packWeight > maxBoxWeight) {
                        boxes.push(currentBox);
                        currentBox = {
                            products: [],
                            size: '',
                            totalWeight: 0
                        };
                    }
                    currentBox.products.push({ name: product.product, units: 1000 });
                    currentBox.totalWeight += packWeight;
                }

                if (remainingUnits > 0) {
                    if (currentBox.totalWeight + ((packWeight * remainingUnits) / 1000) > maxBoxWeight) {
                        boxes.push(currentBox);
                        currentBox = {
                            products: [],
                            size: '',
                            totalWeight: 0
                        };
                    }
                    currentBox.products.push({ name: product.product, units: remainingUnits });
                    currentBox.totalWeight += (packWeight * remainingUnits) / 1000
                }
            }

            products.forEach(product => {
                addProduct(product, product.quantity);
            });

            if (currentBox.products.length > 0) {
                boxes.push(currentBox);
            }

            return boxes;
        },

        generatePacking(){
            this.v$.modal.data.$touch();
            if (this.v$.modal.data.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.modal.boxList = this.packingProducts([].concat.apply([], this.modal.data.map(row => row.items)))
            }
        },

        removeDuplicates(array){
            let result = [];

             array.forEach(function (a) {
                if (!this[a.name]) {
                    this[a.name] = { name: a.name, units: 0 };
                    result.push(this[a.name]);
                }
                this[a.name].units += a.units;
            }, Object.create(null));

             return result
        },

        store(){
            this.v$.modal.data.$touch();

            if (this.modal.boxList.length === 0){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Debes de previsualizar la lista de empaque antes de continuar',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('packing-order-exportation.store'), {
                    data: this.modal.data,
                    box_list: this.modal.boxList
                }).then(resp => {
                    this.closeModal()
                    this.$swal.close()
                    this.$inertia.visit(route('packing-order-exportation.list'));
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        },

        showOP(op){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('packing-order-exportation.show-op'), {
                params: {
                    op: op
                }
            }).then(resp => {
                this.$swal.close()

                this.showOrderModal = {
                    open: true,
                    data: resp.data
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar'
                });
            })
        }
    },

    computed: {
        selected() {
            return this.$refs.table1.$refs.table.selectedRows
        }
    },

    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
    }

}
</script>

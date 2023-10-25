1
<template>
    <div>
        <Head title="Precios cliente"/>

        <portal to="application-title">
            Precios cliente
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-2 gap-5">
                <autocomplete
                    :url="route('electronic-billing.documents.search-customer', 'CIEV')"
                    show-field="customer"
                    @selected-value="getCustomerData"
                />

                <button class="btn btn-primary" @click="get_info(customer_code)">
                    Consultar
                </button>
            </div>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-secondary btn-sm" @click="openModal(row)">
                            <font-awesome-icon icon="edit"/>
                        </button>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    Editar Producto
                </template>

                <template #content>
                    <div class="p-2">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Deshabilitado
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   :value="`${form.customer_code} - ${form.customer_name}`" disabled>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Producto
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Deshabilitado
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   :value="`${form.product_code} - ${form.product_description}`" disabled>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Código del producto del cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input type="text" class="form-control" v-model="form.customer_product_code">
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Precio
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="form.price">
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Estado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select class="form-select" v-model="form.state">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Notas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea class="form-control" cols="30" rows="5" v-model="form.notes"></textarea>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                    <button @click="update(form)" type="button" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import dayjs from "dayjs";
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head, Link} from "@inertiajs/vue3";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    components: {
        JetDialogModal,
        Autocomplete,
        Head, Link
    },

    data() {
        return {
            table: {
                data: [],
                columns: [
                    'customer',
                    'product_code',
                    'customer_product_code',
                    'product_description',
                    'price',
                    'approved_by',
                    'notes',
                    'state',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        customer: 'CLIENTE',
                        product_code: 'CÓDIGO',
                        customer_product_code: 'COD. PROD. CLIENTE',
                        product_description: 'DESCRIPCIÓN',
                        price: 'PRECIO',
                        approved_by: 'APROBO',
                        notes: 'NOTAS',
                        state: 'ESTADO',
                        created_at: 'CREADO',
                        updated_at: 'ACTUALIZADO',
                        actions: '',
                    },
                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['customer', 'product_code', 'product_description', 'price', 'aproved_by', 'state'],
                    templates: {
                        customer(h, row) {
                            return row.customer?.RAZON_SOCIAL
                        },
                        product_code(h, row) {
                            return row.product
                        },
                        product_description(h, row) {
                            return row.product_info?.Descripcion
                        },
                        price(h, row) {
                            return this.$h.formatCurrency(row.price)
                        },
                        approved_by(h, row) {
                            return row.approved.name
                        },
                        state(h, row) {
                            if (row.state) {
                                return <span class="badge badge-success badge-rounded"> Activo </span>
                            } else {
                                return <span class="badge badge-danger badge-rounded"> Inactivo </span>
                            }
                        },
                        created_at(h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                        },
                        updated_at(h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY');
                        },
                    },
                    cellClasses: {
                        price: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        state: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        created_at: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        updated_at: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                },
            },

            isOpen: false,
            form: {
                id: '',
                product_code: '',
                product_description: '',
                customer_product_code: '',
                customer_code: '',
                customer_name: '',
                price: '',
                state: '',
                notes: '',
            },
            customer_code: ''
        }
    },

    methods: {
        openModal(row) {
            this.form = {
                id: row.id,
                product_code: row.product,
                product_description: row.product_info.Descripcion,
                customer_product_code: row.customer_product_code,
                customer_code: row.customer_code,
                customer_name: row.customer.RAZON_SOCIAL,
                price: row.price,
                state: row.state,
                notes: row.notes
            }
            this.isOpen = true;
        },

        closeModal() {
            this.form = {
                id: '',
                product_code: '',
                product_description: '',
                customer_product_code: '',
                customer_code: '',
                customer_name: '',
                price: '',
                state: '',
                notes: ''
            }
            this.isOpen = false;
        },

        update(data) {
            axios.put(route('order.customer-product-prices.update', data.id), data).then(resp => {
                this.closeModal();
                this.$swal({
                    icon: 'success',
                    title: 'Información Actualizada',
                    text: 'Se actualizaron los datos satisfactoriamente',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                this.table.data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. ',
                    text: 'Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(err.data);
            })
        },

        get_info(code) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.get(route('order.customer-product-prices.get-info'), {
                params: {
                    code: code
                }
            }).then(resp => {
                this.table.data = resp.data
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. ',
                    text: 'Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(err.data);
            })
        },

        getCustomerData(obj) {
            this.customer_code = obj.code
        }
    }
}
</script>


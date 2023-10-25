<template>
    <div>
        <Head title="Lista de ordenes de empaque generadas"/>

        <portal to="application-title">
            Lista de ordenes de empaque generadas
        </portal>

        <portal to="actions">
            <Link :href="route('packing-order-exportation.pack-list')" class="btn btn-secondary">
                <font-awesome-icon icon="boxes-packing" class="mr-2"/>
                Empaque
            </Link>

            <Link :href="route('packing-order-exportation.index')" class="btn btn-secondary ml-2">
                <font-awesome-icon icon="list" class="mr-2"/>
                Ordenes de exportación
            </Link>
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                            class="overflow-y-auto">

                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu w-50">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="edit(row)"
                                   class="dropdown-item" v-if="row.state === 'pending'">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Registrar cajas
                                </a>

                                <a href="javascript:void(0)"
                                   @click="print(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'print']" class="mr-1"/>
                                    Imprimir
                                </a>

                                <a href="javascript:void(0)"
                                   @click="changeState('cancel', row.id)"
                                   class="dropdown-item"
                                   v-if="row.state === 'pending'">
                                    <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                    Anular
                                </a>

                                <a href="javascript:void(0)"
                                   @click="changeState('close', row.id)"
                                   class="dropdown-item"
                                   v-if="row.state === 'pending'">
                                    <font-awesome-icon :icon="['far', 'circle-check']" class="mr-1"/>
                                    Finalizar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="modal.open" max-width="5xl" @close="closeModal">
                <template #title>
                    Lista de empaque
                </template>

                <template #content>
                    <template v-for="(order, key) in modal.data.data">
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
                                    <td class="text-right">{{ item.quantity }}</td>
                                    <td class="text-right">{{ item.weight }}</td>
                                    <td class="text-right">{{ ((item.quantity / 1000) * item.weight).toFixed(2) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <div class="mt-10 pt-10 border-t-2 border-danger">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr class="text-center">
                                <th colspan="5">LISTA DE EMPAQUE</th>
                            </tr>
                            <tr>
                                <th>CAJA</th>
                                <th>TAMAÑO CAJA</th>
                                <th>PRODUCTOS</th>
                                <th>PESO TOTAL</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(box, key) in modal.data.box_list">
                                <td class="text-center">{{ key+1 }}</td>
                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.modal.data.box_list.$each.$response.$data[key]?.size.$error }"
                                           v-model="box.size">
                                </td>
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

                    <button @click="update(modal.data.id, modal.data.box_list)" type="button" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {helpers, required} from "@/utils/i18n-validators";
import useVuelidate from "@vuelidate/core";
import {Link, Head} from "@inertiajs/vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    components: {
        JetDialogModal,
        FontAwesomeIcon,
        Link,
        Head
    },

    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        data: Array
    },

    validations(){
        return {
            modal: {
                data: {
                    box_list: {
                        $each: helpers.forEach({
                            size: {
                                required,
                            }
                        })
                    }
                }

            }
        }
    },

    data(){
        return {
            table: {
                data: this.data,
                columns: [
                    'consecutive',
                    'user',
                    'state_name',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: '#',
                        user: 'CREADO POR',
                        state_name: 'ESTADO',
                        created_at: 'CREADO EL',
                        updated_at: 'ACTUALIZADO EL',
                        actions: ''
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: [
                        'consecutive', 'user', 'state', 'created_at', 'updated_at'
                    ],
                    templates: {
                        user(h, row){
                            return row.user_created.name
                        },
                    }
                }
            },

            modal: {
                open: false,
                data: {}
            }
        }
    },

    methods: {
        edit(row){
            this.modal = {
                open: true,
                data: row
            }
        },

        print(row){
            this.downloadLabels(row)
            this.downloadPackingList(row)
        },

        changeState(state, id) {
            this.$swal({
                icon: 'question',
                title: `¿${state === 'cancel' ? 'Anular' : 'Finalizar'} lista de empaque?`,
                text: "Solo podras imprimir los documentos, pero no sera posible modificar las cajas. Esta accion solo es reversible por un administrador",
                showCancelButton: true,
                confirmButtonText: `¡Si, ${state === 'cancel' ? 'anular' : 'finalizar'}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(route('packing-order-exportation.change-state'), {
                        id: id,
                        state: state
                    }).then(resp => {
                        this.table.data = resp.data

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Registro actualizado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        })
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: err?.response?.data.message,
                            confirmButtonText: 'Aceptar',
                        });
                    })
                }
            })
        },



        update(id, box_list){
            this.v$.modal.data.box_list.$touch();

            if (this.v$.modal.data.box_list.$invalid){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
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

                axios.put(route('packing-order-exportation.update', id), {
                    box_list: box_list
                }).then(resp => {
                    this.closeModal()
                    this.table.data = resp.data

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Registro actualizado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err?.response?.data.message,
                        confirmButtonText: 'Aceptar',
                    });
                })
            }
        },

        closeModal(){
            this.modal = {
                open: false,
                data: {}
            }
        },

        downloadLabels(row){
            axios.post(route('packing-order-exportation.labels-pdf'), {
                customerData: row.data,
                boxList: row.box_list
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');

                link.href = url;
                link.setAttribute('download', 'etiquetas.pdf');

                document.body.appendChild(link);
                link.click();

                return true;
            }).catch(err => {
                return false;
            })
        },

        downloadPackingList(row){
            axios.post(route('packing-order-exportation.packing-pdf'), {
                customerData: row.data,
                boxList: row.box_list
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');

                link.href = url;
                link.setAttribute('download', 'lista-de-empaque.pdf');

                document.body.appendChild(link);
                link.click();

                return true;
            }).catch(err => {
                return false
            })
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
    }
}
</script>

<template>
    <div>
        <Head title="Inventario de productos"/>

        <portal to="application-title">
            Inventario de productos
        </portal>

        <div>
            <div class="alert alert-secondary show mb-2 shadow-sm" role="alert">
                <div class="flex items-center">
                    <div class="font-medium text-lg uppercase">Instrucciones</div>
                    <div class="badge badge-danger badge-rounded ml-auto">Importante</div>
                </div>
                <div class="mt-3 font-medium">
                    En este formulario podrá consultar 2 tipos de información de productos:
                </div>
                <ul class="list-disc ml-6">
                    <li>Productos con inventario y su discriminación (Botón "Consulta de inventario").</li>
                    <li>Todos los productos registrados en la base de datos independiente de si tienen o no inventario y con información básica. tambien se puede habilitar el producto si es encuentra en estado Obsoleto (Botón "Consulta de productos")</li>
                </ul>
            </div>

            <div class="grid grid-flow-col grid-cols-5 gap-4 py-5">
                <input type="text" class="form-control col-span-3" placeholder="Descripción o referencia" v-model="query">
                <button class="btn btn-primary"  @click="show('inventory')">
                    Consulta de inventarios
                    <font-awesome-icon icon="angle-right" class="ml-2"/>
                </button>
                <button class="btn btn-primary" @click="show('products')">
                    Consulta de productos
                    <font-awesome-icon icon="angle-right" class="ml-2 text"/>
                </button>
            </div>

            <template v-if="show_data === 'inventory'">
                <v-client-table :data="table_data" :columns="table_inventory.columns" :options="table_inventory.options" ref="inventory_1"
                        class="overflow-y-auto">

                    <template v-slot:actions="{row}">
                        <div class="text-center">
                            <button class="btn btn-sm btn-secondary" @click="lot_detail(row.PRTNUM_01.trim())" v-if="row.LOTTRK_01 === 'Y' ">
                                Detalle por lote <font-awesome-icon icon="angle-right" class="ml-2"/>
                            </button>
                        </div>
                    </template>

                    <template v-slot:CANT_CP="{row}">
                        <div class="text-right">
                            <button class="btn btn-sm btn-secondary" @click="committed_amount(row.PRTNUM_01.trim())">
                                {{ row.part_sales ? parseFloat(row.part_sales.QTYCOM_29).toFixed(2) : 0.0 }}
                            </button>
                        </div>
                    </template>

                    <template v-slot:DISPONIBLE="{row}">
                        <div class="text-right">
                            <button class="btn btn-sm btn-secondary" @click="available_amount(row.PRTNUM_01.trim())">
                                {{ row.part_sales ? (parseFloat(row.ONHAND_01) + parseFloat(row.NONNET_01)) - parseFloat(row.part_sales.QTYCOM_29) : 0.0  }}
                            </button>
                        </div>
                    </template>
                </v-client-table>

            </template>

            <template v-if="show_data === 'products'">
                <div class="intro-y box grid ">
                    <div class=" p-5">
                        <v-client-table :data="table_data" :columns="table_products.columns" :options="table_products.options" ref="inventory_1"
                                class="overflow-y-auto">

                            <template v-slot:state="{row}">
                                <div class="text-center">

                                    <Tippy
                                        tag="a"
                                        content="Click para habilitar este producto"
                                        class="badge badge-danger badge-rounded cursor-pointer"
                                        :options="{
                                                placement: 'top'
                                            }"
                                        href="javascript:void(0)"
                                        v-if="row.STAENG_01.trim() === '5'"
                                        @click.native="active_product(row.PRTNUM_01.trim())"
                                    >
                                        Obsoleto
                                    </Tippy>

                                    <span class="badge badge-success badge-rounded" v-else>
                                    Activo
                                </span>
                                </div>
                            </template>

                            <template v-slot:actions="{row}">
                                <div class="text-center">
                                    <button class="btn btn-sm btn-secondary" @click="view_table_data(row)">
                                        Ver <font-awesome-icon icon="angle-right" class="ml-2"/>
                                    </button>
                                </div>
                            </template>

                        </v-client-table>
                    </div>
                </div>
            </template>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title >
                    {{ modal_data.title }}
                </template>

                <template #content v-if="modal_data.data">
                    <template v-if="modal_data.type === 'lot_detail'">
                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="table table--sm">
                                    <thead>
                                        <tr>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> LOTE </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> BODEGA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANTIDAD </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in modal_data.data" v-bind:key="row.Lote">
                                            <td class="border-b dark:border-dark-5">
                                                <span v-if="row.Lote">{{ row.Lote }}</span>
                                                <span v-else class="text-xs font-semibold inline-block py-1 px-2 rounded text-pink-600 bg-pink-200 uppercase last:mr-0 mr-1">N/A</span>
                                            </td>
                                            <td class="border-b dark:border-dark-5">{{ row.Bodega }}</td>
                                            <td class="border-b dark:border-dark-5">
                                                <span v-if="row.CantLote">{{ row.CantLote }}</span>
                                                <span v-else>0</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                    <template v-else-if="modal_data.type === 'committed_amount'">
                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="table table--sm">
                                    <thead>
                                        <tr>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> ORDEN DE VENTA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CLIENTE </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT PEDIDA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT ENVIADA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT FACTURADA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANT PENDIENTE </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in modal_data.data" v-bind:key="row.OV">
                                            <td class="border-b dark:border-dark-5">{{ row.OV }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.RAZON_SOCIAL }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.CANT_ACTUAL }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.CANT_DESPACHADA }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.CANT_FACTURADA }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.CANT_PENDIENTE }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <template v-else-if="modal_data.type === 'available_amount'">
                        <div class="py-2 sm:py-2 p-2">
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="table table--sm">
                                    <thead>
                                        <tr>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> BODEGA </th>
                                            <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> CANTIDAD </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in modal_data.data" v-bind:key="row.PRTNUM_06">
                                            <td class="border-b dark:border-dark-5">{{ row.STK_06 }}</td>
                                            <td class="border-b dark:border-dark-5">{{ row.QTYOH_06 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
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
    import {Head} from '@inertiajs/vue3'

    export default {
        components: {
            JetDialogModal,
            Head
        },

        data(){
            return {
                show_data: null,
                query: null,
                table_data: [],
                table_inventory : {
                    columns: [
                        'PRTNUM_01',
                        'PMDES1_01',
                        'TOTAL',
                        'CANT_CP',
                        'DISPONIBLE',
                        'actions'
                    ],
                    options: {
                        headings: {
                            PRTNUM_01: 'REFERENCIA',
                            PMDES1_01: 'DESCRIPCIÓN',
                            TOTAL: 'TOTAL',
                            CANT_CP: 'CANT COMPROMETIDA',
                            DISPONIBLE: 'CANT DISPONIBLE',
                            actions: ''
                        },
                        perPageValues: [10, 25, 50, 100, 250],
                        clientSorting: false,
                        sortable: ['PRTNUM_01','PMDES1_01'],
                        templates: {
                            TOTAL: function (h, row) {
                                return <div class="text-right">{ this.$h.numberTwoPlaces(parseFloat(row.ONHAND_01) + parseFloat(row.NONNET_01)) }</div>
                            }
                        }
                    },
                },
                table_products: {
                    columns: [
                        'code',
                        'description',
                        'fabrication',
                        'purchases',
                        'critic',
                        'state'
                    ],
                    options: {
                        headings: {
                            code: 'CÓDIGO',
                            description: 'DESCRIPCIÓN',
                            fabrication: 'FABRICACION',
                            purchases: 'COMPRAS',
                            critic: 'CRITICO',
                            state: 'ESTADO'
                        },
                        perPageValues: [10, 25, 50, 100, 250],
                        clientSorting: false,
                        sortable: ['code', 'description'],
                        templates: {
                            code: function (h, row) {
                                return row.PRTNUM_01
                            },
                            description: function (h, row) {
                                return row.PMDES1_01
                            },
                            fabrication: function (h, row) {
                                return row.MFGOPR_01
                            },
                            purchases: function (h, row) {
                                return row.PURLT_01
                            },
                            critic: function (h, row) {
                                return row.CRPHLT_01
                            }

                        }
                    },
                },
                modal_data: {
                    type: null,
                    title: '',
                    data: null
                },
                isOpen: false
            }

        },

        methods: {
            loading(bool){
                if(bool === true){
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Cargando información...',
                        text: 'Este proceso puede tardar algunos segundos.',
                    });
                }else{
                    this.$swal.close()
                }
            },
            closeModal(){
                this.isOpen = false;
                this.modal_data = {
                    type: null,
                    title: '',
                    data: null
                }
            },
            show(value){
                if(this.query){
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Cargando información...',
                        text: 'Este proceso puede tardar un momento...',
                    });
                    axios.get(route('query-inventory'),{
                        params: {
                            q: this.query,
                            type: value
                        }
                    }).then(resp => {
                        this.show_data = value;
                        this.table_data = resp.data

                        this.$swal.close()
                    }).catch(error => {
                        alert('hubo un error');
                        console.log(error.data);
                    })

                }else{
                    this.$swal({
                        icon: 'error',
                        title: 'Ups... Debes escribir el código o descripción para comenzar la busqueda',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    })
                }
            },
            lot_detail(reference){
                axios.get(route('forecasts.get-lots-detail'), {
                    params: {
                        reference: reference
                    }
                }).then(resp => {
                    this.modal_data = {
                        type: 'lot_detail',
                        title: 'Detalle por lote',
                        data: resp.data
                    }
                    this.loading(false);
                    this.isOpen = true;
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error);
                });
            },
            committed_amount(reference){
                axios.get(route('forecasts.get-committed-amount'), {
                    params: {
                        reference: reference
                    }
                }).then(resp => {
                    if (!resp.data.length > 0){
                        this.$swal({
                            icon: 'info',
                            title: 'Sin cantidades comprometidas',
                            text: 'Esta referencia no tiene cantidades comprometidas.',
                            confirmButtonText: 'Aceptar',
                            timerProgressBar: true,
                            timer: 6000,
                        });
                    }else{
                        this.modal_data = {
                            type: 'committed_amount',
                            title: 'Cantidades Comprometidas',
                            data: resp.data
                        }
                        this.loading(false);
                        this.isOpen = true;
                    }
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: 'Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error);
                });
            },
            available_amount(reference){
                axios.get(route('inventory.available_amount', reference)).then(resp => {
                    if (!resp.data.length > 0){
                        this.$swal({
                            icon: 'info',
                            title: 'Sin stock disponible',
                            text: 'Esta referencia no tiene stock disponible.',
                            confirmButtonText: 'Aceptar',
                            timerProgressBar: true,
                            timer: 6000,
                        });
                    }else{
                        this.modal_data = {
                            type: 'available_amount',
                            title: 'Stock Disponible',
                            data: resp.data
                        }
                        this.loading(false);
                        this.isOpen = true;
                    }
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: 'Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error);
                });
            },

            active_product(reference){
                this.$swal({
                    title: '¿Habilitar Producto?',
                    text: "¡Esta acción solo es reversible por un administrador!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Si, Habilitar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(route('inventory.enable-product'), {
                            reference: reference
                        }).then(resp => {
                            this.$swal({
                                title: '¡Producto Habilitado!',
                                text: "Producto habilitado con éxito, por favor consulte nuevamente para visualizar los cambios!",
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                            })
                            console.log(resp.data);
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: 'Ups!',
                                text: 'Hubo un error procesando la información.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err.data);
                        })
                    }
                })
            }
        }
    }
</script>


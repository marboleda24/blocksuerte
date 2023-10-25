<template>
    <div>
        <Head title="Remisiones de puntos de venta"/>

        <portal to="application-title">
            Remisiones de puntos de venta
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="view(row.consecutive)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </a>

                                <a :href="route('point-of-sale-remission.print', row.consecutive)" target="_blank"
                                      class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'print']" class="mr-1"/>
                                    Imprimir
                                </a>

                                <a href="javascript:void(0)"
                                   @click="transit(row.consecutive)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'truck']" class="mr-1"/>
                                    En transito
                                </a>

                                <a href="javascript:void(0)"
                                   @click="receipt(row.consecutive)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'dolly']" class="mr-1"/>
                                    Recibir
                                </a>

                                <Link :href="route('point-of-sale-remission.edit', row.consecutive)"
                                      class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </Link>

                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>


            <jet-dialog-modal :show="viewModal.isOpen" max-width="5xl" @close="closeModal()">
                <template #title>
                    Remisión # {{viewModal.data?.consecutive}}
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-5">
                        <div class="box p-5">
                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                                <div>
                                    <div class="text-slate-500">Consecutivo</div>
                                    <div class="mt-1">{{ viewModal.data.consecutive }}</div>
                                </div>
                                <font-awesome-icon icon="hashtag" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>
                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                                <div>
                                    <div class="text-slate-500">Pedido de origen</div>
                                    <div class="mt-1">{{ viewModal.data.order.consecutive }}</div>
                                </div>
                                <font-awesome-icon icon="hashtag" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>
                        </div>

                        <div class="box p-5">
                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                                <div>
                                    <div class="text-slate-500">Punto de venta</div>
                                    <div class="mt-1">{{ viewModal.data.location }}</div>
                                </div>
                                <font-awesome-icon icon="location-dot" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>
                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                                <div>
                                    <div class="text-slate-500">Estado</div>
                                    <div class="mt-1">
                                        <span class="text-red-600 font-bold"
                                              v-if="viewModal.data.state === 'pending'">
                                            Pendiente
                                        </span>
                                        <span class="text-yellow-600 font-bold"
                                              v-if="viewModal.data.state === 'transit'">
                                            En transito
                                        </span>
                                        <span class="text-green-600 font-bold"
                                              v-if="viewModal.data.state === 'finish'">
                                            Mercancía recibida
                                        </span>
                                    </div>
                                </div>
                                <font-awesome-icon icon="truck" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>
                        </div>

                        <div class="box p-5">
                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                                <div>
                                    <div class="text-slate-500">Creado por</div>
                                    <div class="mt-1">{{ viewModal.data.user.name }}</div>
                                </div>
                                <font-awesome-icon icon="user" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>

                            <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                                <div>
                                    <div class="text-slate-500">Creado el</div>
                                    <div class="mt-1">{{ viewModal.data.created_at }}</div>
                                </div>
                                <font-awesome-icon icon="clock" class="w-4 h-4 text-slate-500 ml-auto"/>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <table class="table table-bordered table-sm mt-5">
                            <thead>
                            <tr>
                                <th class="whitespace-nowrap">PRODUCTO</th>
                                <th class="whitespace-nowrap">NOTAS</th>
                                <th class="whitespace-nowrap">BODEGA DE ORIGEN</th>
                                <th class="whitespace-nowrap">UNIDAD DE MEDIDA</th>
                                <th class="whitespace-nowrap">LOTES</th>
                                <th class="whitespace-nowrap">CANTIDAD</th>
                                <th class="whitespace-nowrap">PRECIO</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in viewModal.data.detail">
                                <td>{{ `${item.product} - ${item.description.trim()}` }}</td>
                                <td>{{ item.notes }}</td>
                                <td>{{ item.warehouse ?? '–' }}</td>
                                <td>{{ item.unit_measurement }}</td>
                                <td class="text-center">

                                </td>
                                <td>{{ item.quantity }}</td>
                                <td class="text-right">{{ $h.formatCurrency(item.price) }}</td>
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
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props: {
        remissions: Array
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    data(){
        return {
            table: {
                data: this.remissions,
                columns: [
                    'consecutive',
                    'order_consecutive',
                    'location',
                    'state_name',
                    'user',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: "#",
                        order_consecutive: "PEDIDO #",
                        location: "ALMACÉN",
                        state_name: "ESTADO",
                        user: "CREADO POR",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    sortable: ['consecutive', 'order_consecutive', 'location', 'state', 'user', 'created_at'],
                    templates: {
                        order_consecutive(h, row){
                            return row.order.consecutive
                        },
                        user(h, row){
                            return row.user.name
                        }

                    }
                }
            },
            viewModal: {
                isOpen: false,
                data: {},
            }
        }
    },

    methods: {
        closeModal(){
            this.viewModal = {
                isOpen: false,
                data: {},
            }
        },

        view(consecutive){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('point-of-sale-remission.view', consecutive)).then(resp => {
                this.viewModal = {
                    isOpen: true,
                    data: resp.data,
                }
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        transit(consecutive){
            if (this.check_remission(consecutive)){

            }else {

            }
        },

        check_remission(consecutive){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.post(route('point-of-sale-remission.check-remission'), {
                consecutive: consecutive
            }).then(resp => {
                this.$swal.close()
                return true;
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar',
                });
                return false
            })

            return false
        },

        receipt(consecutive){

        }

    }
}
</script>

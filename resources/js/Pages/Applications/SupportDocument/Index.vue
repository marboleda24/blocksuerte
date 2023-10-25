<template>
    <div>
        <Head :title="`Documentos Soporte - ${entity === 'CIEV' ? 'Estrada Velasquez' : 'Plásticos Goja'}`"/>

        <portal to="application-title">
            Documentos Soporte - {{ entity === 'CIEV' ? 'Estrada Velasquez' : 'Plásticos Goja' }}
        </portal>

        <portal to="actions">
            <Link :href="route('support-document.create', entity)" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Documento Soporte
            </Link>
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            PENDIENTES {{ `(${pendingRows.length})` }}
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="finish-tab"
                            tag="button"
                            content="Finalizados"
                            data-tw-toggle="tab"
                            data-tw-target="#finish"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            FINALIZADOS {{ `(${finishRows.length})` }}
                        </Tippy>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="pending" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="pendingRows"
                                        :columns="table.columns"
                                        :options="table.options"
                                        ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>
                                            <a href="javascript:void(0)"
                                               @click="send(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'paper-plane']" class="mr-1"/>
                                                Enviar DIAN
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </v-client-table>
                    </div>

                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finishRows"
                                        :columns="table.columns"
                                        :options="table.options"
                                        ref="table1"
                                        class="overflow-y-auto">

                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-40">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.id)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Ver
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="download(row.consecutive)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'download']" class="mr-1"/>
                                                Descargar PDF
                                            </a>

                                            <a href="javascript:void(0)"
                                               v-if="row.state === 'success'"
                                               @click="creditNote(row)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'file-invoice']" class="mr-1"/>
                                                Nota de ajuste
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="view_modal.open" @close="closeModal()" max-width=5xl>
                <template #title>
                    {{ `Documento soporte ${view_modal.data.consecutive}` }}
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center uppercase">
                                <th class="whitespace-nowrap">Consecutivo</th>
                                <th class="whitespace-nowrap">Proveedor</th>
                                <th class="whitespace-nowrap">Fecha Documento</th>
                                <th class="whitespace-nowrap">Fecha Creación</th>
                                <th class="whitespace-nowrap">Creado por</th>
                                <th class="whitespace-nowrap">Logs</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <td>{{ view_modal.data.consecutive }}</td>
                                <td>{{ view_modal.data.provider_name }}</td>
                                <td>{{ view_modal.data.transaction_date }}</td>
                                <td>{{ view_modal.data.created_at }}</td>
                                <td>{{ view_modal.data.created_by.name }}</td>
                                <td>
                                    <button class="btn btn-primary" @click="openLogModal = true">
                                        Ver
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="overflow-x-auto mt-2">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center uppercase">
                                <th class="whitespace-nowrap">Producto</th>
                                <th class="whitespace-nowrap">Tipo</th>
                                <th class="whitespace-nowrap">Cantidad</th>
                                <th class="whitespace-nowrap">Precio</th>
                                <th class="whitespace-nowrap">Retencion</th>
                                <th class="whitespace-nowrap">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in  view_modal.data.details">
                                <td>{{ row.product.description }}</td>
                                <td>{{ row.type === 'service' ? 'Servicio' : 'Producto' }}</td>
                                <td class="text-right">{{ row.quantity }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.price) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.retention) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(row.price * row.quantity) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="w-1/4 ml-auto mt-2">
                        <table class="table table-bordered table-sm">
                            <tbody>
                            <tr>
                                <td class="font-bold">SUBTOTAL</td>
                                <td class="text-right">{{ $h.formatCurrency(view_modal.data.bruto) }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">RETENCIONES</td>
                                <td class="text-right">{{ $h.formatCurrency(view_modal.data.retention) }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">TOTAL</td>
                                <td class="text-right">
                                    {{ $h.formatCurrency(view_modal.data.bruto - view_modal.data.retention) }}
                                </td>
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

            <jet-dialog-modal :show="openLogModal" @close="openLogModal = false" max-width=5xl>
                <template #title>
                    {{ `Logs de documento soporte ${view_modal.data.consecutive}` }}
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="whitespace-nowrap">Descripción</th>
                                <th class="whitespace-nowrap">Usuario</th>
                                <th class="whitespace-nowrap">Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="log in view_modal.data.logs">
                                <td>{{ log.description }}</td>
                                <td>{{ log.user.name }}</td>
                                <td>{{ log.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="openLogModal = false" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script>
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props: {
        support_documents: Array,
        entity: String
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    data() {
        return {
            table: {
                data: this.support_documents,
                columns: [
                    'consecutive',
                    'provider_name',
                    'transaction_date',
                    'user_name',
                    'payment_form',
                    'notes',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: 'CONSECUTIVO',
                        provider_name: 'PROVEEDOR',
                        transaction_date: 'FECHA DOCUMENTO',
                        user_name: 'CREADO POR',
                        payment_form: 'MEDIO PAGO',
                        notes: 'NOTAS',
                        created_at: 'CREADO EL',
                        actions: ''
                    },
                    clientSorting: false,
                    sortable: ['consecutive', 'provider_name', 'transaction_date', 'created_by', 'payment_form', 'created_at'],
                    templates: {
                        transaction_date: function (h, row) {
                            return this.$h.formatDate(row.transaction_date, 'YYYY-MM-DD')
                        },
                        payment_form: function (h, row) {
                            switch (row.payment_form) {
                                case "1":
                                    return 'Contado'
                                case "2":
                                    return 'Crédito'
                            }
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    }
                }
            },
            view_modal: {
                data: null,
                open: false
            },
            openLogModal: false
        }
    },

    methods: {
        view(id) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('support-document.view', id)).then(resp => {
                this.view_modal = {
                    data: resp.data,
                    open: true
                }
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        print(id) {
            let url = route('support-document.print', id);
            window.open(url, '_blank').focus();
        },

        send(id) {
            this.$swal({
                title: '¿Enviar documento a la DIAN?',
                text: "¡Esta acción no es reversible!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, enviar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });
                    axios.post(route('support-document.send-dian', this.entity), {
                        id: id
                    }).then(resp => {
                        this.table.data = resp.data.support_documents
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Documento enviado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        let result = this.msg_string(err.response.data)

                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            html: result,
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                }
            })
        },

        creditNote(row) {
            this.$swal({
                title: '¿Generar documento de ajuste?',
                text: `Esta acción no es reversible; el Documento ${row.consecutive} sera anulado, ¿Esta seguro de continuar?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('support-document.adjust-note', this.entity), {
                        entity: this.entity,
                        consecutive: row.consecutive
                    }).then(resp => {
                        this.table.data = resp.data.support_documents
                        this.$swal({
                            title: '¡Éxito!',
                            text: `¡Documento de ajuste ${resp.data.document} creado y enviado correctamente a la DIAN!`,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        let result = this.msg_string(err.response.data)

                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            html: result,
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                }
            })
        },

        closeModal() {
            this.view_modal = {
                data: null,
                open: false
            }
        },

        download(consecutive) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Documento…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            let prefix = this.entity === 'CIEV' ? 'DSS-DSEV' : 'DSS-DSE';
            let company = this.entity === 'CIEV' ? '890926617' : '900349726'

            axios.get(route('download-invoice-dian', [company, prefix, consecutive, this.entity])).then(resp => {
                const link = document.createElement('a');

                link.download = `${prefix}-${consecutive}.pdf`;
                link.href = 'data:application/pdf;base64,' + resp.data;
                link.click();

                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err)
            })
        },

        msg_string(msg) {
            let array = msg.split('|')

            let str = '<div class="text-justify">';

            for (let i = 0; i < array.length; i++) {
                str += `<p class="mb-2"> ${array[i]} </p>`
            }

            str += '</div>'

            return str;
        }
    },

    computed: {
        pendingRows() {
            return this.table.data.filter(row => row.state === 'pending').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        },

        finishRows() {
            return this.table.data.filter(row => row.state === 'success' || row.state === 'cancel-credit-note').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        }
    }
}
</script>

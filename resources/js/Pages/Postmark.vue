<template>
    <div>
        <Head title="Postmark – Logs de correo electrónico"/>

        <portal to="application-title">
            Postmark – Logs de correo electrónico
        </portal>

        <div>
            <div class="box">
                <div class="p-5">
                    <div class="alert alert-secondary show mb-2 shadow-sm mr-auto w-full"
                         role="alert">
                        <div class="flex items-center">
                            <div class="font-medium text-lg uppercase">Información Importante</div>
                        </div>
                        <div class="mt-0">
                            Recuerde que aqui solo se veran los logs de correos anteriores a 45 dias, para ver logs mas
                            actuales, por favor ingrese a Postmark. <br>
                            La recoleccion de correos se realiza diariamente a las 12:00 PM (GMT-5) <br>
                            <span class="font-bold text-danger">Ultima fecha de recolectada: {{ lastDate }}</span>
                        </div>
                    </div>


                    <v-client-table :data="mails" :columns="table.columns" :options="table.options" ref="table1"
                                    class="overflow-y-auto">

                        <template v-slot:MessageEvents="{row}">
                            <button class="btn btn-sm btn-primary" @click="openModal(row.MessageEvents)">
                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                ({{ row.MessageEvents.length }})
                            </button>
                        </template>
                    </v-client-table>
                </div>
            </div>

            <jet-dialog-modal :show="logModal.open" @close="closeModal" max-width="3xl">
                <template #title>
                    Logs de correo electronico
                </template>

                <template #content>
                    <div class="overflow-y-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>RECIBIDO EL</th>
                                    <th>RECEPTOR</th>
                                    <th>EVENTO</th>
                                    <th>DETALLES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in logModal.data">
                                    <td>{{$h.formatDate(log.ReceivedAt, 'YYYY-MM-DD hh:mm a') }}</td>
                                    <td>{{ log.Recipient }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-warning" v-if="log.Type === 'Delivered'">
                                            Entregado
                                        </span>

                                        <span class="badge badge-success" v-else-if="log.Type === 'Opened'">
                                            Abierto
                                        </span>
                                    </td>
                                    <td v-if="log.Type === 'Delivered'">{{ log.Details.DestinationServer }}</td>
                                    <td v-else-if="log.Type === 'Opened'">{{ log.Details.Summary }}</td>
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
import {Head} from '@inertiajs/vue3'
import dayjs from "dayjs";

export default {
    components: {
        JetDialogModal,
        Head
    },

    props: {
        mails: Array
    },

    data(){
        return {
            table: {
                columns: [
                    'Server',
                    'MessageID',
                    'ReceivedAt',
                    'Recipients',
                    'Cc',
                    'Bcc',
                    'Subject',
                    'Attachments',
                    'Status',
                    'MessageEvents'
                ],
                options: {
                    headings: {
                        Server: 'SERVIDOR',
                        MessageID: 'UUID',
                        ReceivedAt: 'FECHA ENVIO',
                        Recipients: 'DESTINATARIO(S)',
                        Cc: 'CORREO COPIA',
                        Bcc: 'COPIA OCULTA',
                        Subject: 'ASUNTO',
                        Attachments: 'ARCHIVOS',
                        Status: 'ESTADO',
                        MessageEvents: 'EVENTOS'
                    },
                    clientSorting: false,
                    templates: {
                        Cc(h, row) {
                            return row.Cc.map(row => row.Email).join(', ')
                        },

                        Bcc(h, row) {
                            return row.Cc.map(row => row.Email).join(', ')
                        },

                        Recipients(h, row){
                            return row.Recipients.join(', ')
                        },

                        Status(h, row){
                            switch (row.Status) {
                                case 'Sent':
                                    return <span class="badge badge-success">Enviado</span>
                            }
                        },
                    },

                    cellClasses: {
                        Status: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        MessageEvents: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }

                }
            },

            logModal: {
                open: false,
                data: []
            }
        }
    },

    methods: {
        openModal(logs){
            this.logModal = {
                open: true,
                data: logs
            }
        },

        closeModal(){
            this.logModal = {
                open: false,
                data: []
            }
        }
    },

    computed: {
        lastDate(){
            return dayjs().subtract(45, 'days').format('YYYY-MM-DD')
        }
    }
}
</script>

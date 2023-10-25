<template>
    <div>
        <Head title="Artes" />

        <portal to="application-title">
            Artes
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                <template v-slot:arte="{row}">
                    <div class="text-center">
                        <button class="btn btn-sm btn-secondary" @click="openModal(row.arte)">
                            {{ row.arte }}
                        </button>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title>
                    Visualizar arte
                </template>

                <template #content>
                    <template v-if="artUrl">
                        <embed class="pdfobject" :src="artUrl" type="application/pdf" style="overflow: auto; width: 100%; height: 600px;">
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

    props: {
        arts: Array,
    },

    data() {
        return {
            table: {
                data: this.arts,
                columns: [
                    'requerimiento',
                    'arte',
                    'producto',
                    'material',
                    'marca',
                    'vendedor',
                    'disenador',
                    'solicitado',
                    'creado',
                ],
                options: {
                    headings: {
                        requerimiento: 'REQUERIMIENTO',
                        arte: 'ARTE',
                        producto: 'PRODUCTO',
                        material: 'MATERIAL',
                        marca: 'MARCA',
                        vendedor: 'VENDEDOR',
                        disenador: 'DISEÑADOR',
                        solicitado: 'SOLICITADO',
                        creado: 'CREADO'
                    },
                    sortable: ['requerimiento', 'arte', 'producto', 'material', 'marca', 'vendedor', 'disenador', 'solicitado', 'creado'],
                },
            },
            isOpen: false,
            artUrl: null,
        }
    },

    methods: {
        openModal: function (art) {
            this.artUrl = `http://192.168.1.12/intranet_ci/assets/Artes/${art}.pdf`;
            this.isOpen = true;
        },

        closeModal: function () {
            this.isOpen = false;
            this.artUrl = null;
        }
    }
}
</script>

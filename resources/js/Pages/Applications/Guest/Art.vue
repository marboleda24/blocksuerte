<template>
    <div>
        <Head title="Artes"/>

        <div class="box h-full min-h-screen	">
            <div class="p-5">
                <Link :href="route('pre-login')" class="btn btn-primary">
                    <font-awesome-icon icon="house" class="mr-2"/>
                    Volver al Inicio
                </Link>
            </div>

            <div class="p-5">
                <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                    <template v-slot:arte="{row}">
                        <div class="text-center">
                            <button class="btn btn-sm btn-secondary" @click="openModal(row.arte)">
                                {{ row.arte }}
                            </button>
                        </div>
                    </template>
                </v-client-table>
            </div>
        </div>

        <jet-dialog-modal :show="isOpen" max-width=5xl>
            <template #title>
                Visualizar arte
            </template>

            <template #content>
                <template v-if="artUrl">
                    <embed class="pdfobject" :src="artUrl" type="application/pdf" style="overflow: auto; width: 100%; height: 600px;">
                </template>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>

    </div>
</template>

<script lang="jsx">
import Empty from "@/Layouts/Empty.vue";
import {Head, Link} from "@inertiajs/vue3";
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    layout: Empty,

    props: {
        arts: Array
    },

    components: {
        Empty,
        Head,
        Link,
        JetDialogModal
    },

    data(){
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
    },

    mounted() {
        dom("body").removeClass("login")
    },

}
</script>


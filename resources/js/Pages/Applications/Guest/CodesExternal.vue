<template>
    <div>
        <Head title="Lista de productos codificados"/>

        <div class="box h-full min-h-screen	">
            <div class="p-5">
                <Link :href="route('pre-login')" class="btn btn-primary">
                    <font-awesome-icon icon="house" class="mr-2"/>
                    Volver al Inicio
                </Link>
            </div>

            <div class="p-5">
                <v-client-table :data="codes" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                    <template v-slot:art="{row}">
                        <div class="text-center">
                            <button class="btn btn-secondary btn-sm"
                                    v-if="row.art"
                                    @click="openModal(row.art)">
                                {{ row.art }}
                            </button>
                            <span v-else>–</span>
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
        codes: Array
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
                columns: [
                    "code",
                    "description",
                    "product_type",
                    "line",
                    "subline",
                    "feature",
                    "material",
                    "measurement_id",
                    "galvanic_finish",
                    "decorative_option",
                    "art",
                    "username",
                    "created_at",
                    "updated_at",
                ],
                options:{
                    headings: {
                        code: "CODIGO",
                        description: "DESCRIPCION",
                        product_type: "TIPO DE PRODUCTO",
                        line: "LINEA",
                        subline: "SUBLINEA",
                        feature: "CARACTERISTICA",
                        material: "MATERIAL",
                        measurement_id: "",
                        galvanic_finish: "ACABADO GALVANICO",
                        decorative_option: "OPCION DECORATIVA",
                        art: "ARTE",
                        username: "CREADO POR",
                        created_at: "CREADO EL",
                        updated_at: "ULTIMA ACTUALIZACION",

                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    responsive: true,
                    sortable: [
                        "code", "description", "product_type", "line", "subline", "feature",
                        "material", "galvanic_finish", "decorative_option", "art", "username"
                    ],
                }
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


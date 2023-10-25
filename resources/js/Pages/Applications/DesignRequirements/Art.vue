<template>
    <div>
        <Head title="Artes de diseño grafico"/>

        <portal to="application-title">
            Artes de diseño grafico
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
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'diagram-project']" class="mr-1"/>
                                    Crear Producto
                                </a>
                                <a href="javascript:void(0)"
                                   class="dropdown-item"
                                   @click="print(row.id)">
                                    <font-awesome-icon :icon="['fas', 'print']" class="mr-1"/>
                                    Imprimir
                                </a>
                                <a href="javascript:void(0)"
                                   @click="newVersion(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'code-fork']" class="mr-1"/>
                                    Neva Version
                                </a>
                                <a href="javascript:void(0)"
                                   @click="changeVersion(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'code-compare']" class="mr-1"/>
                                    Cambiar Version
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3"

export default {
    props: {
        arts: Array,
    },

    components: {
        Head,
        Link
    },

    data(){
        return {
            table: {
                data: this.arts,
                columns: [
                    'code',
                    'requirement',
                    'proposal',
                    'product',
                    'designer',
                    'seller',
                    'version',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        code: "CÓDIGO",
                        requirement: "REQUERIMIENTO",
                        proposal: "PROPUESTA",
                        product: "PRODUCTO",
                        designer: "DISEÑADOR",
                        seller: "VENDEDOR",
                        version: "VERSION",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    clientSorting: false,
                    sortable: ['id', 'customer', 'seller', 'designer', 'product', 'measure', 'created_at'],
                    templates: {
                        requirement: function(h, row){
                            return row.design_requirement.consecutive
                        },
                        product: function(h, row){
                            return row.current.product
                        },
                        proposal: function (h, row) {
                            return row.proposal.consecutive
                        },
                        version: function (h, row) {
                            return `v${row.current.version}.0`
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        },
                        designer: function (h, row) {
                            return row.current.designer.name
                        },
                        seller: function (h, row) {
                            return row.current.seller.name
                        },
                    }
                }
            },
            versions: [],
            openModalChangeVersion: false,
            openModalNewVersion: false,
            newVersionForm: {

            }
        }
    },

    methods: {
        print(id){
            let url = route('arts.print', id);
            window.open(url, '_blank').focus();
        },

        newVersion(row){

        },

        changeVersion(id){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('arts.check-versions', id)).then(resp => {
                if (resp.data.length > 1){
                    this.versions = resp.data
                    this.openModalChangeVersion = true
                }else {
                    this.$swal({
                        icon: 'error',
                        title: 'No hay versiones disponibles',
                        text: 'La version actual del arte es la única disponible y no puede ser cambiada',
                        confirmButtonText: 'Aceptar',
                    });
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err)
            })
        }
    }
}
</script>

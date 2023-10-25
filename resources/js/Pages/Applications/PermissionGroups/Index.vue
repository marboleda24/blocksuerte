<template>
    <div>
        <Head title="Grupos de permisos"/>

        <portal to="application-title">
            Grupos de permisos
        </portal>

        <portal to="actions">
            <Link :href="route('permission-groups.create')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Crear grupo de permisos
            </Link>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <Link :href="route('permission-groups.show', row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </Link>

                                <Link :href="route('permission-groups.edit', row.id)"
                                      class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'edit']" class="mr-1"/>
                                    Editar
                                </Link>

                                <a href="javascript:void(0)"
                                   @click="deleteRow(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'trash-can']" class="mr-1"/>
                                    Eliminar
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
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";

export default {
    props: {
        data: Array,
    },

    components: {
        Head,
        Link
    },

    data() {
        return {
            columns: [
                'id',
                'name',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    id: '#',
                    name: 'IDENTIFICADOR',
                    created_at: 'CREADO EN',
                    updated_at: 'ACTUALIZADO EN',
                    actions: 'ACCIONES',
                },
                clientSorting: false,
                sortable: ['id', 'name', 'created_at', 'updated_at'],
                templates: {
                    created_at(h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                    },
                    updated_at(h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY')
                    },
                }
            },
            tableData: this.data,
        }
    },

    methods: {
        deleteRow: function (row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('permission-groups.destroy', row)).then(resp => {
                        this.tableData = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Registro eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })

                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error eliminando el registro.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },
    }
}
</script>

<template>
    <div>
        <Head title="Permisos"/>

        <portal to="application-title">
            Permisos
        </portal>

        <portal to="actions">
            <Link :href="route('permissions.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Crear Permiso
            </Link>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1" class="overflow-y-auto">
                <template v-slot:name="{row}">
                    <div class="inline-flex">
                        <div v-if="row.protected">
                            <font-awesome-icon icon="lock" class="text-danger mr-1"/>
                            {{ row.name }}
                        </div>
                        <div v-else>
                            {{ row.name }}
                        </div>
                    </div>
                </template>

                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <Link :href="route('permissions.show', row.id)" class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </Link>

                                <Link :href="route('permissions.edit', row.id)" class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </Link>

                                <a href="javascript:void(0)"
                                   @click="deleteRow(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
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
    import {Head, Link} from "@inertiajs/vue3";

    export default {
        components: {
            Head,
            Link
        },

        props: {
            data: Array,
        },

        data(){
            return {
                columns: [
                    'name',
                    'description',
                    'guard_name',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        name: 'IDENTIFICADOR',
                        description: 'DESCRIPCIÓN',
                        guard_name: 'TIPO',
                        created_at: 'CREADO EN',
                        updated_at: 'ACTUALIZADO EN',
                        actions: 'ACCIONES',
                    },
                    clientSorting : false,
                    sortable: ['name', 'description', 'guard_name', 'created_at', 'updated_at'],
                },
                tableData: this.data,
            }
        },

        methods: {
            deleteRow: function(row){
                this.$swal({
                    icon: 'question',
                    title: '¿Eliminar registro?',
                    text: "¡Esta acción no es reversible!",
                    showCancelButton: true,
                    confirmButtonText: '¡Si, eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(route('permissions.destroy', row)).then(resp => {
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
                                title: 'Ups!',
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

<template>
    <div>
        <Head title="Roles"/>

        <portal to="application-title">
            Roles
        </portal>

        <portal to="actions">
            <Link :href="route('roles.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Rol
            </Link>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1" class="overflow-y-auto">
                <template v-slot:name="{row}">
                    <div class="inline-flex" >
                        <div v-if="row.protected">
                            <span class="badge badge-danger badge-rounded">
                                <font-awesome-icon icon="lock"/>
                            </span>
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
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <Link :href="route('roles.show', row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </Link>
                                <Link :href="route('roles.edit', row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </Link>
                                <a href="javascript:void(0)"
                                   @click="deleteRow(row.code)"
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
    import dayjs from "dayjs";
    import {Head, Link} from '@inertiajs/vue3'

    import 'dayjs/locale/es'
    dayjs.locale('es')

    export default{
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
                    'id',
                    'name',
                    'description',
                    'guard_name',
                    'created_at',
                    'updated_at',
                    'actions'
                ],
                options: {
                    headings: {
                        id: '#',
                        name: 'IDENTIFICADOR',
                        description: 'DESCRIPCIÓN',
                        guard_name: 'TIPO',
                        created_at: 'CREADO EN',
                        updated_at: 'ACTUALIZADO EN',
                        actions: '',
                    },
                    clientSorting : false,
                    sortable: ['id', 'name', 'description', 'guard_name', 'created_at', 'updated_at'],
                    templates: {
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                        },
                        updated_at: function (h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY')
                        },
                    }
                },
                tableData: this.data,
            }
        },

        methods: {
            deleteRow: function(row){
                this.$swal({
                    title: '¿Eliminar registro?',
                    text: "¡Esta acción no es reversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Si, eliminar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(route('roles.destroy', row)).then(resp => {
                            this.tableData = resp.data;
                            this.$swal({
                                title: '¡Éxito!',
                                text: "Registro eliminado con éxito!",
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                            })
                            this.tableData = resp.data
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

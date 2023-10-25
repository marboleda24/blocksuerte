<template>
    <div>
        <Head title="Usuarios"/>
        <div class="col-lg-2">
            <portal to="application-title">
                Usuarios
            </portal>

            <portal to="actions">
                <Link :href="route('users.create')" class="btn btn-primary">
                    <font-awesome-icon icon="plus" class="mr-2"/>
                    Nuevo Usuario
                </Link>
            </portal>

        </div>
        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:type="{row}">
                    <div class="text-center">
                        <span class="badge badge-success badge-rounded" v-if="row.domain">
                            dominio
                        </span>

                        <span class="badge badge-purple badge-rounded" v-else>
                            local
                        </span>
                    </div>
                </template>

                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <Link :href="route('users.show', row.id)" class="btn btn-secondary btn-sm mr-2">
                            <font-awesome-icon icon="eye"/>
                        </Link>

                        <Link :href="route('users.edit', row.id)" class="btn btn-secondary btn-sm">
                            <font-awesome-icon icon="edit"/>
                        </Link>
                    </div>
                </template>

            </v-client-table>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'

export default {
    components: {
        Head,
        Link
    },

    props: {
        data: Array,
        test: Array
    },

    data() {
        return {
            columns: [
                'id',
                'name',
                'email',
                'username',
                'type',
                'actions'
            ],

            options: {
                headings: {
                    id: '#',
                    name: 'NOMBRE',
                    email: 'CORREO ELECTRÓNICO',
                    username: 'USUARIO',
                    type: 'TIPO DE USUARIO',
                    actions: '',
                },
                clientSorting: false,
                sortable: ['id', 'name', 'email', 'username'],
            },
            tableData: this.data,
        }
    },
}
</script>

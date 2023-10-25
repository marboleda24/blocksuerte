<template>
    <div>
        <Head title="Backups del Sistema"/>

        <portal to="application-title">
            Backups del Sistema
        </portal>

        <portal to="actions">
            <Link :href="route('backup.create')" method="GET" class="btn btn-primary">
                <font-awesome-icon icon="database" class="mr-2"/>
                Crear Backup
            </Link>
        </portal>

        <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1" class="overflow-y-auto">

            <template v-slot:actions="{row}">
                <div class="dropdown text-center">
                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                            data-tw-toggle="dropdown">
                        <font-awesome-icon icon="bars"/>
                    </button>
                    <div class="dropdown-menu">
                        <div class="dropdown-content">
                            <Link :href="route('backup.download', row.file_name)"
                                  class="dropdown-item">
                                <font-awesome-icon :icon="['fas', 'download']" class="mr-1"/>
                                Descargar
                            </Link>
                            <Link :href="route('backup.delete', row.file_name)"
                                  class="dropdown-item">
                                <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                Eliminar
                            </Link>
                        </div>
                    </div>
                </div>
            </template>

        </v-client-table>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'

export default {
    components: {
        Head,
        Link
    },

    props: ['data'],

    data() {
        return {
            columns: [
                'file_name',
                'file_size',
                'last_modified',
                'actions'
            ],
            options: {
                headings: {
                    file_name: 'ARCHIVO',
                    file_size: 'TAMAÑO',
                    last_modified: 'CREADO',
                    actions: 'ACCIONES'
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['file_name', 'file_size', 'last_modified'],
            },
            tableData: this.data
        }
    }
}
</script>

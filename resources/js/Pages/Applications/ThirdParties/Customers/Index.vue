<template>
    <div>
        <Head title="Clientes"/>

        <portal to="application-title">
           Clientes
        </portal>

        <portal to="actions">
            <Link :href="route('customers.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo Cliente
            </Link>
        </portal>

        <div>
            <v-client-table :data="customers" :columns="columns" :options="options" ref="table1" class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="text-center ">
                        <Link :href="route('customers.show', row.CodigoMAX.trim())"
                              class="btn btn-secondary">
                            <font-awesome-icon :icon="['far', 'eye']"/>
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
        customers: Array
    },

    data(){
        return {
            columns: [
                'CodigoMAX',
                'NITMAX',
                'NombreMAX',
                'EstadoMAX',
                'actions'
            ],
            options: {
                headings: {
                    CodigoMAX: 'CÓDIGO',
                    NITMAX: 'NIT / CC',
                    NombreMAX: 'RAZON SOCIAL',
                    EstadoMAX: 'ESTADO',
                    actions: '',
                },
                clientSorting : false,
                sortable: ['CodigoMAX', 'NombreMAX', 'NITMAX', 'EstadoMAX'],
                templates: {
                    EstadoMAX: function (h, row) {
                        return row.EstadoMAX === 'R'
                            ?   <div class="text-center"><span class="badge badge-success">Liberado</span></div>
                            :   <div class="text-center"><span class="badge badge-danger">Retenido</span></div>
                    }
                }
            },
        }
    },
}
</script>


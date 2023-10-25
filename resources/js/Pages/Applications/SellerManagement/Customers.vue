<template>
    <div>
        <Head title="Clientes"/>

        <portal to="application-title">
            Clientes
        </portal>

        <portal to="actions">
            <Link :href="route('customers.create')" class="btn btn-primary">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Nuevo cliente
            </Link>
        </portal>


        <div>
            <v-client-table :data="customers" :columns="columns" :options="options" ref="table1" class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <Link :href="route('seller-management.customers.customer', props.row.CodigoMAX.trim())"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>

    </div>
</template>

<script lang="jsx">
import {Link, Head} from '@inertiajs/vue3'

export default {
    components: {
        Link,
        Head
    },

    props: {
        customers: Array,
        reason_reprocessing: Array,
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


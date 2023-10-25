<template>
    <div>
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                        ref="table_data" class="select-list">

        </v-client-table>
    </div>

</template>
<script >
export default {
    props: {
        op: {
            Array


        },
    },
    data() {
        return {
            table: {
                data: this.op,
                columns: [
                    'MES',
                    'AÑO',
                    'OV',
                    'NOMVENDEDOR',
                    'RAZON_SOCIAL',

                ],
                options: {
                    headings: {
                        MES: "MES",
                        AÑO: "AÑO",
                        OV: "OV",
                        NOMVENDEDOR: "VENDEDOR",
                        RAZON_SOCIAL: "CLIENTE",
                    }
                }

            }
        }
    }
}

</script>

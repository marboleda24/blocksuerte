<template>
    <div>
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                        ref="table_data" class="select-list">

        </v-client-table>
    </div>

</template>
<script lang="jsx">

export default{
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
                            'OV',
                            'op',
                            'CT',
                            'OPRSEQ_14',
                            'OPRDES_14',
                            'OPRID_14',
                            'QTYCOM_14',
                            'RLSDTE_14',
                            'MOVDTE_14',
                            'CURCOM_14',
                            'QUECDE_14',
                            'OPRTYP_14',
                            'REVDTE_14',
                            'MOVDAY_14',
                            'EstadoOP',
                            'CURQTY_10',
                            'CUSORD_10',
                            'CREDTE_10',
                            'CURSRT_14',
                            'CTActual',
                            'REFERENCIA',
                            'PRODUCTO',
                            'Dias de programación '
                        ],
                        options: {
                            headings:{
                                OV:"OV",
                                OP:"OP",
                                CY:"CT",
                                OPRSEQ_14:"OPRSEQ_14",
                                OPRDES_14:"OPRDES_14",
                                OPRID_14:"OPRID_14",
                                QTYCOM_14:"QTYCOM_14",
                                RLSDTE_14:"RLSDTE_14",
                                MOVDTE_14:"MOVDTE_14",
                                CURCOM_14:"CURCOM_14",
                                QUECDE_14:"QUECDE_14",
                                OPRTYP_14:"OPRTYP_14",
                                REVDTE_14:"REVDTE_14",
                                MOVDAY_14:"MOVDAY_14",
                                EstadoOP:"Estado OP",
                                CURQTY_10:"CURQTY_10",
                                CUSORD_10:"CUSORD_10",
                                CREDTE_10:"CREDTE_10",
                                CURSRT_14:"CURSRT_14",
                                CTActual:"Cantidad Actual",
                                REFERENCI:"REFERENCIA",
                                PRODUCTOQTYCOM_14 :"PRODUCTOQTYCOM_14"
                            }

                        }
            }
        }

    }

}
</script>

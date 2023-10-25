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
        ov: {
            Array
        },
        op:{
          Array
        },
    },
    data() {
        return {
            table: {
                data:this.ov,
                columns: [
                   'OV',
                   'OC',
                   'OP',
                   'FECHA_OV',
                   'CANT_ACTUAL',
                   'OPRID_14',
                   'RAZON_SOCIAL',
                   'PRODUCTO',
                   'NOMVENDEDOR',
                   'ORDENCT',
                   'ESTADO',
                   'NOTAS',
                   'ARTE',
                   'Marca',
                    'DIAS_DIFERENCIA',
                    'URGENCIA'
                ],
                options: {
                    headings:{
                        OV:"OV",
                        OC:"OC",
                        FECHA_OV:"FECHA OV",
                        CANT_ACTUAL:"CANT_ACTUAL",
                        OPRID_14:"CANT PENDIENTE",
                        RAZON_SOCIAL:"RAZON_SOCIAL",
                        PRODUCTO:"PRODUCTO",
                        NOMVENDEDOR:"NOMVENDEDOR",
                        ESTADO:"ESTADO",
                        NOTAS:"NOTAS",
                        ARTE:"ARTE",
                        Marca:"Marca",
                        DIAS_DIFERENCIA:'DIAS DIFERENCIA',
                        URGENCIA:'URGENCIA',
                        templates: {
                            URGENCIA: function (h, ov){
                                if (ov.URGENCIA >25) {
                                    return <span >red</span>

                                }
                            }

                        }

                    }



                }
            }
        }

    }

}
</script>

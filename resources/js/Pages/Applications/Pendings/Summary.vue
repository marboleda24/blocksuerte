<template>
    <div>
        <Head title="RESUMEN DE VENTAS" />

        <portal to="application-title">
            RESUMEN DE VENTAS
        </portal>

        <div class="p-5">
            <div class="box p-4 te" >
                <table class="table table-bordered table-sm ">
                    <thead>
                    <th colspan="1" class="text-center">VENDEDOR</th>
                    <th colspan="1">TOTAL</th>
                    </thead>
                    <tbody>
                    <tr v-for="(item, key) in OV">
                        <td > {{key}}</td>
                        <td class="text-right"> total:{{item.TOTAL}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>
<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    components: {
        Head
    },
    props: {

        OV:{
            Object
        },





}



}
</script>

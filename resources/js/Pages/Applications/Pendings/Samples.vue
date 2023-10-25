<template>
    <div>
        <Head title="MUESTRAS" />

        <portal to="application-title">
            MUESTRAS
        </portal>
        <div class="p-5">
            <div class="box p-4 te" >
                <table  class="table table-bordered table-sm ">
                    <thead  >
                    <tr>
                        <th  v-for="(value, key)  in Object.keys(OV[0]).slice().reverse()" class="uppercase ">{{value}}</th>
                        <th>TOTAL</th>
                    </tr>
                    </thead>
                    <tbody >
                    <tr  v-for="(value, key)  in OV">
                        <td v-for="item in reverseList(value)">{{ value[item] }}</td>
                        <td >{{ Object.values(value).reduce((a, b) => a + (isNaN(b) ? 0 : b), 0) }}</td>
                    </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from "@inertiajs/vue3";

export default {
    components: {
        Head
    },

    props: {
        OV: Array
    },


    methods:{
        reverseList(object){
            return Object.keys(object).reverse()
        },

    },

}
</script>

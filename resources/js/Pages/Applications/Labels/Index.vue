<template>
    <div>
        <Head title="Etiquetado"/>

        <portal to="application-title">
            Etiquetado
        </portal>

        <div>
            <div class="grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4">
                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="image-fit lg:mr-1">
                           <font-awesome-icon icon="tags" size="4x"/>
                        </div>

                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <span class="font-medium uppercase">
                                Etiqueta Detalle
                            </span>
                            <div class="text-slate-500 text-xs mt-0.5">
                                Esta aplicación permite generar los Códigos de Barras para marcar la unidad mínima de
                                empaque de los productos de Estrada Velasquez, por medio de una Orden de Producción.
                            </div>

                            <input type="text" class="form-control mt-2" v-model="op">
                        </div>
                        <div class="flex mt-4 lg:mt-0">
                            <button class="btn btn-primary ml-2" @click="search">
                                Generar
                                <ArrowRightIcon class="w-5 h-5 ml-1"/>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="image-fit lg:mr-1">
                            <font-awesome-icon icon="tags" size="4x"/>
                        </div>

                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <span class="font-medium uppercase">
                                Etiqueta Manual
                            </span>
                            <div class="text-slate-500 text-xs mt-0.5">
                                Esta aplicación permite generar los Códigos de Barras para marcar la unidad mínima de
                                empaque de los productos de Estrada Velasquez, de manera manual.
                            </div>
                        </div>
                        <div class="flex mt-4 lg:mt-0">
                            <button class="btn btn-primary ml-2" @click="manual_label_open = true">
                                Generar
                                <ArrowRightIcon class="w-5 h-5 ml-1"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <label-detail :data="label_detail_data" :packers="packers" :reviewers="reviewers"/>

            <label-manual :open="manual_label_open" :packers="packers" :reviewers="reviewers"/>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import LabelDetail from "@/Pages/Applications/Labels/Components/LabelDetail.vue";
import LabelManual from "@/Pages/Applications/Labels/Components/LabelManual.vue";

export default {
    props: {
        packers: Array,
        reviewers: Array
    },

    components: {
        LabelManual,
        Head,
        Link,
        LabelDetail
    },

    data(){
        return {
            op: '',
            label_detail_data: {},
            manual_label_open: false
        }
    },

    methods: {
        search(){
            axios.get(route('label.search-production-order', this.op)).then(resp => {
                this.label_detail_data = resp.data
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

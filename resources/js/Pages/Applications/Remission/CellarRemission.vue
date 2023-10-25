<template>
    <div>
        <Head title="Remisiones bodega"/>

        <portal to="application-title">
            Remisiones bodega
        </portal>

        <div class="grid grid-cols-5 gap-5">
            <div class="form-group">
                <label for="">OV Enviada</label>
                <input type="number" class="form-control" v-model="form.ov">
            </div>
            <div class="form-group">
                <label for="">Fecha Envio</label>
                <Litepicker
                    v-model="form.date"
                    :options="{
                    autoApply: true,
                    singleMode: true,
                    numberOfColumns: 1,
                    numberOfMonths: 1,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2022,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                }"
                    class="form-control"
                />
            </div>

            <div class="form-group">
                <label for="">Hora Inicial</label>
                <input type="time" class="form-control" v-model="form.start_time">
            </div>

            <div class="form-group">
                <label for="">Hora Final</label>
                <input type="time" class="form-control" v-model="form.end_time">
            </div>

            <button class="btn btn-primary" @click="previewRemission">Previsualizar remision</button>
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
    data(){
        return {
            form: {
                ov: '',
                date: '',
                start_time: '',
                end_time: '',
            },
            remission: {}
        }
    },

    methods: {
        previewRemission(){
            axios.get(route('')).then(resp => {
                this.remission = resp.data
            }).catch(err => {

            })
        }
    }
}
</script>


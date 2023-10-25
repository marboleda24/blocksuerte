<template>
    <div>
        <Head title="Produccion - Ensamble Externo" />

        <portal to="application-title">
            Produccion - Ensamble Externo
        </portal>

        <portal to="actions" >
            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="grid grid-cols-2 w-1/2 gap-5">
            <Litepicker
                v-model="form.date_range"
                :options="{
                    autoApply: true,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2022,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                    maxDate: current_date
                }"
                class="form-control w-full"
            />

            <a class="btn btn-primary" :href="route('report-production.ensamble-externo-pdf', form)" target="_blank">Consultar</a>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";

export default {
    props: {
        data: Array,
    },

    components: {
        Head, Link
    },

    data(){
        return {
            form: {
                date_range: ''
            }
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
    }
}
</script>

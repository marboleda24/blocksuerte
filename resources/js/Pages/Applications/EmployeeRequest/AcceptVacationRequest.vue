<template>
    <div>
        <div class="alert alert-secondary show mb-2" role="alert">
            <div class="flex items-center">
                <div class="font-medium text-lg">Solicitud de vacaciones aprobada</div>
                <div class="text-xs bg-gray-600 px-1 rounded-md text-white ml-auto">info</div>
            </div>
            <div class="mt-3">
                Usted ha aceptado la solicitud de vacaciones del empleado {{ employee.nombres }} entre el {{ data.start_date }} y el {{ data.end_date }}, esta solicitud
                sera enviada a recursos humanos para una ultima validation
            </div>
            <button type="button" class="btn btn-primary ml-4" onclick="window.close()">Cerrar</button>
        </div>
    </div>
</template>

<script lang="jsx">
import Empty from '@/Layouts/Empty.vue'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    components : {
        Empty
    },

    layout: Empty,

    props: {
        data: Object,
        employee: Object
    },

    options:{
        templates: {
            start_date: function (h, row) {
                return dayjs(new Date(row.start_date)).format('DD MMMM YYYY');
            },
            end_date: function (h, row) {
                return dayjs(new Date(row.end_date)).format('DD MMMM YYYY');
            }

        }
    }
}
</script>


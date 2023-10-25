<template>
    <div>
        <Head title="SST – Ausentismos"/>

        <portal to="application-title">
            SST – Ausentismos
        </portal>

        <portal to="actions">
            <Link :href="route('security-health-work.sociodemographic')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y box px-5 pt-5 mt-5">
                <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
                    <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                            <img alt="Midone - HTML Admin Template" class="rounded-full" :src="'https://ui-avatars.com/api/?name='+encodeURI(employee.NOMBRES_APELLIDOS)+'&color=7F9CF5&background=EBF4FF'">
                        </div>
                        <div class="ml-5">
                            <div class="sm:whitespace-normal font-medium text-lg">{{ employee.NOMBRES_APELLIDOS }}</div>
                            <div class="text-slate-500">{{ employee.IDENTIFICACION }}</div>
                            <span class="badge badge-success" v-if="employee.ESTADO === 'A'">Activo</span>
                            <span class="badge badge-danger" v-else>Retirado</span>
                        </div>
                    </div>
                    <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                        <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center">
                                <font-awesome-icon :icon="['fas', 'cake-candles']" class="mr-2"/>
                                <label class="font-bold uppercase">Fecha de nacimiento:</label>&nbsp; {{ employee.FECHA_NACIMIENTO }} ({{ employee.EDAD }})
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'calendar-days']" class="mr-2"/>
                                <label class="font-bold uppercase">Fecha de ingreso:</label>&nbsp; {{ employee.FECHA_INGRESO }} ({{ employee.ANTIGUEDAD }})
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'map-pin']" class="mr-2"/>
                                <label class="font-bold uppercase">Area:</label>&nbsp; {{ employee.AREA }}
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'user-tag']" class="mr-2"/>
                                <label class="font-bold uppercase">Cargo:</label>&nbsp; {{ employee.CARGO }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                        <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center">
                                <font-awesome-icon :icon="['fas', 'cake-candles']" class="mr-2"/>
                                <label class="font-bold uppercase">EPS:</label>&nbsp; {{ employee.EPS }}
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'calendar-days']" class="mr-2"/>
                                <label class="font-bold uppercase">Fondo de pension:</label>&nbsp; {{ employee.PENSION }}
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'map-pin']" class="mr-2"/>
                                <label class="font-bold uppercase">Tipo de contrato:</label>&nbsp; <div v-html="contract_type"/>
                            </div>

                            <div class="truncate sm:whitespace-normal flex items-center justify-items-center mt-3">
                                <font-awesome-icon :icon="['fas', 'user-tag']" class="mr-2"/>
                                <label class="font-bold uppercase">Estado Civil:</label>&nbsp; <div v-html="marital_status"/>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center" role="tablist">
                    <li id="disabilities-tab" class="nav-item" role="presentation">
                        <a href="javascript:;" class="nav-link py-4 active" data-tw-target="#disabilities" aria-controls="disabilities" aria-selected="true" role="tab">
                            Incapacidades ({{ disabilities.length }})
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="disabilities-tab" class="tab-pane leading-relaxed p-5 active" role="tabpanel" aria-labelledby="disabilities-tab">
                        <div class="overflow-x-auto" v-if="disabilities.length > 0">
                            <table class="table table-bordered">
                                <thead>
                                <tr class="uppercase">
                                    <th class="whitespace-nowrap"># Incapacidad</th>
                                    <th class="whitespace-nowrap">Concepto</th>
                                    <th class="whitespace-nowrap">Diagnostico</th>
                                    <th class="whitespace-nowrap">SVE de interes</th>
                                    <th class="whitespace-nowrap">Inicio</th>
                                    <th class="whitespace-nowrap">Fin</th>
                                    <th class="whitespace-nowrap">Dias</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="disability in disabilities">
                                        <td>{{ disability.NumIncapacidad }}</td>
                                        <td>{{ disability.ConceptoIncapacidad }}</td>
                                        <td>{{ disability.DescripcionDiagnostico }}</td>
                                        <td>{{ disability.SVE }} – {{ disability.SVE_INTERES }}</td>
                                        <td>{{ $h.formatDate(disability.fecha_inicial, 'YYYY-MM-DD') }}</td>
                                        <td>{{ $h.formatDate(disability.fecha_final, 'YYYY-MM-DD') }}</td>
                                        <td>{{ disability.dias_incap }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else class="alert alert-success-soft show flex items-center mb-2" role="alert">
                            <font-awesome-icon :icon="['far', 'circle-check']" size="2x" class="mr-4"/>
                            ESTE EMPLEADO NO TIENE REGISTRO DE INCAPACIDADES
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";

export default {
    components: {
        Head,
        Link
    },

    props: {
        disabilities: Array,
        employee: Object
    },

    computed: {
        marital_status(){
            switch (this.employee.ESTADO_CIVIL){
                case "U":
                    return `<span class="badge badge-sm badge-primary">Union libre</span>`
                case "C":
                    return `<span class="badge badge-sm badge-warning">Casad${this.employee.GENERO === 'F' ? 'a' : 'o'}</span>`
                case "S":
                    return `<span class="badge badge-sm badge-pink">Solter${this.employee.GENERO === 'F' ? 'a' : 'o'}</span>`
                case "V":
                    return `<span class="badge badge-sm badge-purple">Viud${this.employee.GENERO === 'F' ? 'a' : 'o'}</span>`
                case "E":
                    return `<span class="badge badge-sm badge-danger">Separad${this.employee.GENERO === 'F' ? 'a' : 'o'}</span>`
            }
        },

        contract_type(){
            if (this.employee.CONTRATO === 'I') {
                return '<span class="badge badge-sm badge-success">Indefinido</span>'
            } else {
                return '<span class="badge badge-sm badge-warning">Aprendiz</span>'
            }
        }
    }
}
</script>

<template>
    <div>
        <Head title="SST – Socio-demográfico"/>

        <portal to="application-title">
            SST – Socio-demográfico
        </portal>

        <portal to="actions">
            <div class="flex flex-col sm:flex-row items-center border rounded-lg p-2 bg-gray-50">
                <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                    <label class="form-check-label text-base font-bold ml-0" for="only-active">SOLO EMPLEADOS
                        ACTIVOS</label>
                    <input id="only-active" class="show-code form-check-input mr-0 ml-3" type="checkbox"
                           v-model="onlyActive">
                </div>
            </div>
        </portal>

        <div>
            <v-client-table :data="table.data"
                            :columns="table.columns"
                            :options="table.options"
                            class="overflow-y-auto">

                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <Link :href="route('security-health-work.work-absenteeism', row.IDENTIFICACION)" class="btn btn-sm btn-secondary">
                            <font-awesome-icon :icon="['far', 'eye']"/>
                        </Link>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
        Head,
        Link
    },

    props: {
        employees: Array
    },

    data() {
        return {
            table: {
                data: this.employees,
                columns: [
                    'IDENTIFICACION',
                    'NOMBRES_APELLIDOS',
                    'GENERO',
                    'FECHA_NACIMIENTO',
                    'EDAD',
                    'FECHA_INGRESO',
                    'ANTIGUEDAD',
                    'AREA',
                    'CARGO',
                    'CONTRATO',
                    'EPS',
                    'PENSION',
                    'ESTADO',
                    'ESTADO_CIVIL',
                    'actions'
                ],
                options: {
                    headings: {
                        NOMBRES_APELLIDOS: 'NOMBRES',
                        FECHA_NACIMIENTO: 'FECHA NACIMIENTO',
                        FECHA_INGRESO: 'FECHA INGRESO',
                        ESTADO_CIVIL: 'ESTADO CIVIL',
                        actions: ''
                    },
                    sortable: [
                        'IDENTIFICACION', 'NOMBRES_APELLIDOS', 'GENERO', 'FECHA_NACIMIENTO', 'EDAD', 'FECHA_INGRESO',
                        'ANTIGUEDAD', 'AREA', 'CARGO', 'CONTRATO', 'EPS', 'PENSION', 'ESTADO', 'ESTADO_CIVIL'
                    ],
                    templates: {
                        GENERO(h, row) {
                            if (row.GENERO === 'F') {
                                return <span class="badge badge-pink">F</span>
                            } else {
                                return <span class="badge badge-primary">M</span>
                            }
                        },
                        CONTRATO(h, row) {
                            if (row.CONTRATO === 'I') {
                                return <span class="badge badge-success">Indefinido</span>
                            } else {
                                return <span class="badge badge-warning">Aprendiz</span>
                            }
                        },
                        ESTADO(h, row) {
                            if (row.ESTADO === 'R') {
                                return <span class="badge badge-danger">Retirado</span>
                            } else {
                                return <span class="badge badge-success">Activo</span>
                            }
                        },
                        ESTADO_CIVIL(h, row) {
                            switch (row.ESTADO_CIVIL) {
                                case "U":
                                    return <span class="badge badge-primary">Union libre</span>
                                case "C":
                                    return <span
                                        class="badge badge-warning">Casad{row.GENERO === 'F' ? 'a' : 'o'}</span>
                                case "S":
                                    return <span class="badge badge-pink">Solter{row.GENERO === 'F' ? 'a' : 'o'}</span>
                                case "V":
                                    return <span class="badge badge-purple">Viud{row.GENERO === 'F' ? 'a' : 'o'}</span>
                                case "E":
                                    return <span
                                        class="badge badge-danger">Separad{row.GENERO === 'F' ? 'a' : 'o'}</span>
                            }
                        }
                    },

                    cellClasses: {
                        GENERO: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        CONTRATO: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        ESTADO: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                        ESTADO_CIVIL: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                },
            },
            onlyActive: false
        }
    },

    watch: {
        onlyActive: function (filter) {
            console.log(filter)
            if (filter) {
                this.table.data = this.employees.filter(row => row.ESTADO === 'A')
            }else {
                this.table.data = this.employees
            }
        }
    }
}
</script>

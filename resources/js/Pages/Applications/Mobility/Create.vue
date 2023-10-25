<template>
    <div>
        <Head title="INSPECCIÓN MOVILIDAD (PESV)"/>

        <portal to="application-title">
            INSPECCIÓN MOVILIDAD (PESV)
        </portal>

        <portal to="actions">
            <Link :href="route('check-mobility.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Placa
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.plate.$error }"
                               v-model="form.plate">

                        <template v-if="v$.form.plate.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.plate.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Kilometraje
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.mileage.$error }"
                               v-model="form.mileage">

                        <template v-if="v$.form.mileage.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.mileage.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Fecha
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

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
                                        minYear: 2020,
                                        maxYear: 2035,
                                        months: true,
                                        years: true
                                    }
                                }"
                            class="form-control w-full"
                        />

                        <template v-if="v$.form.date.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.date.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Ciudad
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <TomSelect v-model="form.city"
                                   class="w-full"
                                   :class="{ 'border-danger': v$.form.city.$error }">
                            <option value="">Seleccione…</option>
                            <option v-for="city in cities" :value="city.descripcion">{{ city.descripcion }}</option>
                        </TomSelect>

                        <template v-if="v$.form.city.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.city.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Conductor
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.driver.$error }"
                               v-model="form.driver">

                        <template v-if="v$.form.driver.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.driver.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Jefe inmediato
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.boss.$error }"
                               v-model="form.boss">

                        <template v-if="v$.form.boss.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.boss.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                </div>
            </div>
        </div>

        <div class="box mt-5">
            <div class="p-5">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th class="font-bold text-center" colspan="2">
                            REVISIÓN DE DOCUMENTOS
                        </th>
                    </tr>
                    <tr>
                        <th>DOCUMENTO</th>
                        <th>FECHA DE VIGENCIA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>SOAT</td>
                        <td>
                            <Litepicker
                                v-model="form.documents.soat"
                                :options="{
                                    autoApply: true,
                                    singleMode: true,
                                    numberOfColumns: 1,
                                    numberOfMonths: 1,
                                    showWeekNumbers: true,
                                    format: 'DD-MM-YYYY',
                                    lang: 'es-ES',
                                    dropdowns: {
                                        minYear: 2020,
                                        maxYear: 2035,
                                        months: true,
                                        years: true
                                    }
                                }"
                                class="form-control form-control-sm w-full"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>REVISIÓN TECNOMECÁNICA</td>
                        <td>
                            <Litepicker
                                v-model="form.documents.technomechanical_review"
                                :options="{
                                    autoApply: true,
                                    singleMode: true,
                                    numberOfColumns: 1,
                                    numberOfMonths: 1,
                                    showWeekNumbers: true,
                                    format: 'DD-MM-YYYY',
                                    lang: 'es-ES',
                                    dropdowns: {
                                        minYear: 2020,
                                        maxYear: 2035,
                                        months: true,
                                        years: true
                                    }
                                }"
                                class="form-control form-control-sm w-full"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>LICENCIA DE CONDUCIR</td>
                        <td>
                            <Litepicker
                                v-model="form.documents.driver_license"
                                :options="{
                                    autoApply: true,
                                    singleMode: true,
                                    numberOfColumns: 1,
                                    numberOfMonths: 1,
                                    showWeekNumbers: true,
                                    format: 'DD-MM-YYYY',
                                    lang: 'es-ES',
                                    dropdowns: {
                                        minYear: 2020,
                                        maxYear: 2035,
                                        months: true,
                                        years: true
                                    }
                                }"
                                class="form-control form-control-sm w-full"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box mt-5">
            <div class="p-5">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th class="font-bold text-center" colspan="3">
                            INSPECCION
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2" class="text-center">CRITERIO</th>
                        <th colspan="2" class="text-center">ESTADO</th>
                    </tr>

                    <tr>
                        <th class="text-center">BUENO</th>
                        <th class="text-center">MALO</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>
                            ESTADO GENERAL ORDEN Y ASEO
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins1" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins1" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            PLUMILLAS LIMPIA VIDRIOS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins2" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins2" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            VIDRIOS, PARABRISAS Y LATERALES
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins3" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins3" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            ESPEJOS LATERALES / RETROVISORES
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins4" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins4" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL DE AGUA RADIADOR
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins5" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins5" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL ACEITE MOTOR
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins6" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins6" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL LIQUIDO FRENOS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins7" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins7" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LUCES ESTACIONARIAS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins8" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins8" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LUCES ALTAS / BAJAS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins9" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins9" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LLANTAS ESTADO / PRESIÓN
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins10" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins10" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FRENO DE EMERGENCIA
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins11" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins11" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            INDICADOR NIVEL DE COMBUSTIBLE
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins12" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins12" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            INDICADOR TEMPERATURA MOTOR
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins13" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins13" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            CINTURONES DE SEGURIDAD
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins14" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins14" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            ESTADO PITO / BOCINA
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins15" :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.inspection.ins15" :value="false">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FUGAS FLUIDOS
                        </td>
                        <td class="text-center" colspan="2">
                            <input type="text" class="form-control form-control-sm" v-model="form.inspection.ins16">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FALLAS A REPORTAR
                        </td>
                        <td class="text-center" colspan="2">
                            <input type="text" class="form-control form-control-sm" v-model="form.inspection.ins17">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box mt-5">
            <div class="p-5">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th class="font-bold text-center" colspan="4">
                            KIT DE CARRETERA
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2" class="text-center">
                            CRITERIO
                        </th>
                        <th colspan="2" class="text-center">
                            ESTADO
                        </th>
                        <th rowspan="2" class="text-center">
                            OBSERVACIONES
                        </th>
                    </tr>

                    <tr>
                        <th class="text-center">BUENO</th>
                        <th class="text-center">MALO</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            GATO
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk1.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk1.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk1.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            CRUCETA
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk2.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk2.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk2.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            SEÑALES REFLECTIVAS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk3.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk3.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk3.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            BOTIQUÍN DE PRIMEROS AUXILIOS
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk4.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk4.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk4.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            EXTINTOR (INCLUYE CARGA)
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk5.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk5.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk5.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            TACOS PARA BLOQUEAR VEHICULO
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk6.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk6.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk6.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            CAJA DE HERRAMIENTA BÁSICA
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk7.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk7.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk7.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LLANTA DE REPUESTO
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk8.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk8.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk8.observation">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LINTERNA
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk9.state"
                                   :value="true">
                        </td>
                        <td class="text-center">
                            <input type="radio" class="form-check-input" v-model="form.road_kit.rk9.state"
                                   :value="false">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.road_kit.rk9.observation">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-primary mr-auto mt-8 w-96" @click="store">
            GUARDAR REGISTRO
        </button>
    </div>
</template>

<script>

import useVuelidate from '@vuelidate/core';
import {Head, Link} from '@inertiajs/vue3';
import {helpers, numeric, required} from '@/utils/i18n-validators'

const validPlate = helpers.regex(/^[A-Z]{3}\d{3}$/i)

const uppercase = {
    beforeUpdate(el) {
        el.value = el.value.toUpperCase()
    },
}
export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Link,
        Head
    },

    directives: {
        uppercase,
    },

    props: {
        cities: Array
    },

    validations() {
        return {
            form: {
                date: {required},
                city: {required},
                driver: {required},
                boss: {required},
                mileage: {required, numeric},
                plate: {
                    required,
                    validPlate: helpers.withMessage("La placa debe de tener el formato ABC123", validPlate)
                },
                documents: {
                    soat: {required},
                    technomechanical_review: {required},
                    driver_license: {required},
                },
                inspection: {
                    ins1: {required},
                    ins2: {required},
                    ins3: {required},
                    ins4: {required},
                    ins5: {required},
                    ins6: {required},
                    ins7: {required},
                    ins8: {required},
                    ins9: {required},
                    ins10: {required},
                    ins11: {required},
                    ins12: {required},
                    ins13: {required},
                    ins14: {required},
                    ins15: {required},
                },
                road_kit: {
                    rk1: {
                        state: {required},
                        observation: ''
                    },
                    rk2: {
                        state: {required},
                        observation: ''
                    },
                    rk3: {
                        state: {required},
                        observation: ''
                    },
                    rk4: {
                        state: {required},
                        observation: ''
                    },
                    rk5: {
                        state: {required},
                        observation: ''
                    },
                    rk6: {
                        state: {required},
                        observation: ''
                    },
                    rk7: {
                        state: {required},
                        observation: ''
                    },
                    rk8: {
                        state: {required},
                        observation: ''
                    },
                    rk9: {
                        state: {required},
                        observation: ''
                    },
                }
            }
        }
    },

    data() {
        return {
            form: {
                documents: {
                    soat: '',
                    technomechanical_review: '',
                    driver_license: ''
                },
                inspection: {
                    ins1: '',
                    ins2: '',
                    ins3: '',
                    ins4: '',
                    ins5: '',
                    ins6: '',
                    ins7: '',
                    ins8: '',
                    ins9: '',
                    ins10: '',
                    ins11: '',
                    ins12: '',
                    ins13: '',
                    ins14: '',
                    ins15: '',
                    ins16: '',
                    ins17: '',
                },
                road_kit: {
                    rk1: {
                        state: '',
                        observation: ''
                    },
                    rk2: {
                        state: '',
                        observation: ''
                    },
                    rk3: {
                        state: '',
                        observation: ''
                    },
                    rk4: {
                        state: '',
                        observation: ''
                    },
                    rk5: {
                        state: '',
                        observation: ''
                    },
                    rk6: {
                        state: '',
                        observation: ''
                    },
                    rk7: {
                        state: '',
                        observation: ''
                    },
                    rk8: {
                        state: '',
                        observation: ''
                    },
                    rk9: {
                        state: '',
                        observation: ''
                    },
                }
            }
        }
    },

    methods: {
        store() {
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.post(route('check-mobility.store'), this.form).then(resp => {

                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        }
    }
}

</script>

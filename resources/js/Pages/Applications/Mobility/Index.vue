<template>
    <div>
        <Head title="Listas de movilidad (PESV)"/>

        <portal to="application-title">
            Listas de movilidad (PESV)
        </portal>

        <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                        class="overflow-y-auto" ref="table1">
            <template v-slot:actions="{row}">
                <div class="text-center">
                    <button class="btn btn-sm btn-primary" @click="show(row)">
                        Ver
                    </button>
                </div>
            </template>
        </v-client-table>

        <jet-dialog-modal :show="modal.open" @close="closeModal">
            <template #title>
                Visualizar registro
            </template>

            <template #content>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Placa
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.plate" disabled>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Kilometraje
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.mileage" disabled>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Fecha
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.date" disabled>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Ciudad
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.city" disabled>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Conductor
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.driver" disabled>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Jefe inmediato
                        </label>

                        <input type="text" class="form-control form-control-sm"
                               :value="modal.data.boss" disabled>
                    </div>
                </div>

                <table class="table table-sm table-bordered mt-5">
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
                        <td>{{ modal.data.documents.soat }}</td>
                    </tr>
                    <tr>
                        <td>REVISIÓN TECNOMECÁNICA</td>
                        <td>{{ modal.data.documents.technomechanical_review }}</td>
                    </tr>
                    <tr>
                        <td>LICENCIA DE CONDUCIR</td>
                        <td>{{ modal.data.documents.driver_license }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-sm table-bordered mt-5">
                    <thead>
                    <tr>
                        <th class="font-bold text-center" colspan="2">
                            INSPECCION
                        </th>
                    </tr>
                    <tr>
                        <th class="text-center">CRITERIO</th>
                        <th class="text-center">BUENO / MALO</th>
                    </tr>

                    </thead>

                    <tbody>
                    <tr>
                        <td>
                            ESTADO GENERAL ORDEN Y ASEO
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins1"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            PLUMILLAS LIMPIA VIDRIOS
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins2"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            VIDRIOS, PARABRISAS Y LATERALES
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins3"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            ESPEJOS LATERALES / RETROVISORES
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins4"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL DE AGUA RADIADOR
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins5"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL ACEITE MOTOR
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins6"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            NIVEL LIQUIDO FRENOS
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins7"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LUCES ESTACIONARIAS
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins8"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LUCES ALTAS / BAJAS
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins9"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            LLANTAS ESTADO / PRESIÓN
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins10"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FRENO DE EMERGENCIA
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins11"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            INDICADOR NIVEL DE COMBUSTIBLE
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins12"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            INDICADOR TEMPERATURA MOTOR
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins13"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            CINTURONES DE SEGURIDAD
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins14"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            ESTADO PITO / BOCINA
                        </td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.inspection.ins15"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FUGAS FLUIDOS
                        </td>
                        <td class="text-center">
                            {{ modal.data.inspection.ins16 }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FALLAS A REPORTAR
                        </td>
                        <td class="text-center">
                            {{ modal.data.inspection.ins17 }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-sm table-bordered mt-5">
                    <thead>
                        <tr>
                            <th class="font-bold text-center" colspan="4">
                                KIT DE CARRETERA
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center">CRITERIO</th>
                            <th class="text-center">BUENO / MALO</th>
                            <th class="text-center">OBSERVACIONES</th>
                        </tr>

                    </thead>
                    <tbody>
                    <tr>
                        <td>GATO</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk1.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk1.observation }}</td>
                    </tr>
                    <tr>
                        <td>CRUCETA</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk2.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk2.observation }}</td>
                    </tr>
                    <tr>
                        <td>SEÑALES REFLECTIVAS</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk3.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk3.observation }}</td>
                    </tr>
                    <tr>
                        <td>BOTIQUÍN DE PRIMEROS AUXILIOS</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk4.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk4.observation }}</td>
                    </tr>
                    <tr>
                        <td>EXTINTOR (INCLUYE CARGA)</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk5.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk5.observation }}</td>
                    </tr>
                    <tr>
                        <td>TACOS PARA BLOQUEAR VEHICULO</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk6.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk6.observation }}</td>
                    </tr>
                    <tr>
                        <td>CAJA DE HERRAMIENTA BÁSICA</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk7.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk7.observation }}</td>
                    </tr>
                    <tr>
                        <td>LLANTA DE REPUESTO</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk8.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk8.observation }}</td>
                    </tr>
                    <tr>
                        <td>LINTERNA</td>
                        <td class="text-center">
                            <font-awesome-icon icon="circle-check" size="lg" class="text-emerald-600" v-if="modal.data.road_kit.rk9.state"/>
                            <font-awesome-icon icon="circle-xmark" size="lg" class="text-red-600" v-else/>
                        </td>
                        <td>{{ modal.data.road_kit.rk9.observation }}</td>
                    </tr>
                    </tbody>
                </table>
            </template>

            <template #footer>
                <button class="btn btn-secondary" @click="closeModal">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
        JetDialogModal,
        Head
    },

    props: {
        check_mobilities: Array
    },

    data() {
        return {
            table: {
                data: this.check_mobilities,
                columns: [
                    'date',
                    'city',
                    'driver',
                    'boss',
                    'mileage',
                    'plate',
                    'actions',
                ],
                options: {
                    headings: {
                        date: "FECHA",
                        city: "CIUDAD",
                        driver: "CONDUCTOR",
                        boss: "JEFE",
                        mileage: "KILOMETRAJE",
                        plate: "PLACA",
                        actions: ''
                    }
                }
            },

            modal: {
                open: false,
                data: {}
            }
        }
    },
    methods: {
        closeModal() {
            this.modal = {
                open: false,
                data: {}
            }
        },

        show(row) {
            this.modal = {
                open: true,
                data: row
            }
        }
    }


}

</script>

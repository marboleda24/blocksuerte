<template>
    <div>
        <Head title="Reportes"/>

        <portal to="application-title">
            Reportes
        </portal>

        <div>
            <div class="flex items-center h-10 border-b-2">
                <h2 class="text-lg font-base">
                    Reportes de Produccion
                </h2>
            </div>

            <div class="p-5">
                <div class="flex items-center h-10 border-b-2 mt-5">
                    <h2 class="text-lg font-base">
                        Pendientes
                    </h2>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <Link v-permission="'reports.production.galvano-1'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.galvano-1')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Galvano 1</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes Galvano 1</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.galvano-2'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.galvano-2')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Galvano 2</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes Galvano 2</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.pulido'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.pulido')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Pulido</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes Pulido</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.estatica'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.estatica')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Estatica</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes Estatica</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.cnc'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.cnc')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">CNC</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes CNC</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.zamac'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.zamac')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Inyeccion</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes Inyeccion</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.uv'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.uv')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">UV</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes UV</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.pendientes-automaticas'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.pendientes-automaticas')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Automaticas</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes automaticas</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.others'"
                          v-for="report in other_reports"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('max.reports.production', report.report)">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">{{ report.title }}</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Pendientes {{ report.title }}</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.plant-transaction'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('plant-transaction.index')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Transacciones de planta</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Transacciones de planta</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'report-production.monitoring-injection'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.monitoring-injection')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Seguimiento Inyeccion</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Seguimiento Inyeccion</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>
                </div>

                <div class="flex items-center h-10 border-b-2 mt-10">
                    <h2 class="text-lg font-base">
                        Control entregas
                    </h2>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <Link v-permission="'reports.production.control-entrega-pulido'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.control-entrega-pulido')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Entregas pulido</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control entregas pulido</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.control-entrega-inyeccion'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.control-entrega-inyeccion')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Control entregas inyección</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control entregas inyección</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.control-entrega-troquelado'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.control-entrega-troquelado')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Control entregas troquelado</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control entregas troquelado</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.control-entrega-automaticas'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.control-entrega-automaticas')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Control entregas automaticas</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control entregas automaticas</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.ensamble-externo'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.ensamble-externo')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Control ensamble externo</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control ensamble externo</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.production-order-stock'"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('report-production.production-order-stock')">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Ordenes de produccion para stock</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Ordenes de produccion para stock</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>

                    <Link v-permission="'reports.production.control-entregas-other'"
                          v-for="(value, index) in delivery_control"
                          class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                          :href="route('delivery-control.report', index)">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="icon" src="/dist/images/report.png">
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <span class="font-medium">Control entregas {{ value }}</span>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Control entregas {{ value }}</div>
                        </div>
                        <div class="ml-auto">
                            <font-awesome-icon icon="arrow-right" size="2x"/>
                        </div>
                    </Link>
                </div>
            </div>

            <div class="flex items-center h-10 border-b-2 mt-5">
                <h2 class="text-lg font-base">
                    Reportes de Ventas
                </h2>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-5">
                <Link v-permission="'reports.sales.per-day'"
                      class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                      :href="route('report-sales.per-day', 'CIEV')">
                    <div class="w-12 h-12 flex-none image-fit mr-1">
                        <img alt="icon" src="/dist/images/report.png">
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="flex items-center">
                            <span class="font-medium">Facturación por dia</span>
                        </div>
                        <div class="w-full truncate text-slate-500 mt-0.5">CI / Nacionales</div>
                    </div>
                    <div class="ml-auto">
                        <font-awesome-icon icon="arrow-right" size="2x"/>
                    </div>
                </Link>


                <Link v-permission="'reports.sales.account-receivable'"
                      class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                      :href="route('account-receivable.index')">
                    <div class="w-12 h-12 flex-none image-fit mr-1">
                        <img alt="icon" src="/dist/images/report.png">
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="flex items-center">
                            <span class="font-medium">Cartera por vendedor</span>
                        </div>
                        <div class="w-full truncate text-slate-500 mt-0.5">Análisis de cuentas por cobrar detallado</div>
                    </div>
                    <div class="ml-auto">
                        <font-awesome-icon icon="arrow-right" size="2x"/>
                    </div>
                </Link>

                <Link v-permission="'reports.sales.ov-pending-per-product'"
                      class="cursor-pointer box relative flex items-center p-5 col-span-12 sm:col-span-6 xl:col-span-3"
                      :href="route('report-sales.ov-pending-per-product.index', 'CIEV')">
                    <div class="w-12 h-12 flex-none image-fit mr-1">
                        <img alt="icon" src="/dist/images/report.png">
                    </div>
                    <div class="ml-2 overflow-hidden">
                        <div class="flex items-center">
                            <span class="font-medium">OV pendientes por producto</span>
                        </div>
                        <div class="w-full truncate text-slate-500 mt-0.5">OV pendientes por producto</div>
                    </div>
                    <div class="ml-auto">
                        <font-awesome-icon icon="arrow-right" size="2x"/>
                    </div>
                </Link>
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
        other_reports: Array
    },

    data(){
        return {
            delivery_control: {
                CABEZ: "Encabezados",
                TROQL: "Broches",
                DEFOR: "Deformadora de alambre",
                PLANT: "Galvano 1",
                GALV2: "Galvano 2",
                STAT: "Estatica",
                PINT: "Pintura"
            }
        }
    }
}
</script>

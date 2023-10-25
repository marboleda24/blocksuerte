<template>
    <div>
        <Head title="Ordenes Cerradas"/>

        <portal to="application-title">
            Ordenes Cerradas
        </portal>
        <div>
             <div class="box">
                <div class="p-5" >
                    <div class="grid grid-cols-3 gap-5">
                        <div>
                            <label>Tipo de búsqueda</label>
                            <select class="form-select" v-model="form.type">
                                <option value="customer">Por cliente</option>
                                <option value="dateRange">Por fecha</option>
                            </select>
                        </div>

                        <div v-if ="form.type === 'customer'">
                            <label>Cliente</label>
                            <autocomplete
                                :url="route('electronic-billing.documents.search-customer', 'CIEV')"
                                show-field="customer"
                                @selected-value="selectCustomer"

                            />
                        </div>
                        <div v-else>
                            <label>Fecha</label>
                            <litepicker
                                v-model="form.dateRange"
                                :options="{
                                    autoApply: true,
                                    singleMode: false,
                                    numberOfColumns: 2,
                                    numberOfMonths: 2,
                                    showWeekNumbers: true,
                                    format: 'DD-MM-YYYY',
                                    lang: 'es-ES',
                                    dropdowns: {
                                        minYear: 2021,
                                        maxYear: null,
                                        months: true,
                                        years: true
                                    },
                                    maxDate: current_date
                                }"
                                class="form-control w-full"
                            />

                        </div>
                        <button class="btn btn-primary" @click="search">
                            Obtener infomacion
                        </button>
                    </div>
                </div>
            </div>

            <div class="box mt-10" v-if="Object.keys(table.data).length > 0">
                <div class="p-5">
                    <div class="overflow-x-auto" v-for="(row, index) in table.data">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th colspan="5">{{ index }} – {{ row[0].NOMVENDEDOR }}</th>
                                    <th colspan="2">FECHAS</th>
                                    <th colspan="4">CANTIDADES</th>
                                    <th colspan="4"></th>
                                </tr>

                                <tr>
                                    <th>OV</th>
                                    <th>LINEA</th>
                                    <th>REFERENCIA</th>
                                    <th>PRODUCTO</th>
                                    <th>PRECIO</th>
                                    <th>OV</th>
                                    <th>DESPACHO</th>
                                    <th>COMPROMISO </th>
                                    <th>ACTUAL</th>
                                    <th>DESPACHADA </th>
                                    <th>FACTURADA</th>
                                    <th>PENDIENTE</th>
                                    <th>MOTIVO</th>
                                    <th>NOTAS </th>
                                    <th>ARTE </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="element in row">
                                    <td>{{ element.OV }}</td>
                                    <td>{{ `${element.LINEA}${element.ITEM}` }}</td>
                                    <td>{{ element.REFERENCIA }}</td>
                                    <td>{{ element.PRODUCTO }}</td>
                                    <td class="text-right">{{ parseFloat(element.PRECIO).toFixed(2) }}</td>
                                    <td class="text-center">{{ $h.formatDate(element.FECHA_OV, 'DD-MMM-YYYY') }}</td>
                                    <td class="text-center">{{ element.FECHA_DESPACHO ? $h.formatDate(element.FECHA_DESPACHO, 'DD-MMM-YYYY') : '–' }}</td>
                                    <td class="text-center">{{ $h.formatDate(element.FechaCompromiso, 'DD-MMM-YYYY') }}</td>
                                    <td class="text-right">{{ parseInt(element.CANT_ACTUAL) }}</td>
                                    <td class="text-right">{{ parseInt(element.CANT_DESPACHADA) }}</td>
                                    <td class="text-right">{{ parseInt(element.CANT_FACTURADA) }}</td>
                                    <td class="text-right">{{ parseInt(element.CANT_PENDIENTE) }}</td>
                                    <td class="text-right">{{ element.MOTIVO }}</td>
                                    <td>{{ element.NOTAS }}</td>
                                    <td>{{ element.ARTE }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">

import dayjs from "dayjs";
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import {Head} from "@inertiajs/vue3";

export default{
    components:{
        Autocomplete,
        Head
    },

    data(){
        return{
            form: {
                type: 'customer',
                customer: null,
                dateRange: ''
            },
            table: {
                data: {}
            }
        }
    },

    methods:{
        selectCustomer(obj){
            this.form.customer = obj.code
        },

        search() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('queries.close-sell-order.search'), {
                params: {
                    type: this.form.type,
                    customer:this.form.customer,
                    dateRange: this.form.dateRange
                }
            }).then(resp => {
                this.$swal.close()
                this.table.data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar',
                });

            })
        },

    },

    computed:{
        current_date(){
            return dayjs()
        }
    }




}


</script>

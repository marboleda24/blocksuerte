<template>
    <div>
        <Head title="Cronograma de preventivos"/>

        <portal to="application-title">
            Cronograma de preventivos
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-3 gap-3">
                <Litepicker
                    v-model="date_range"
                    :options="{
                        autoApply: true,
                        singleMode: false,
                        numberOfColumns: 2,
                        numberOfMonths: 2,
                        showWeekNumbers: true,
                        format: 'DD-MM-YYYY',
                        lang: 'es-ES',
                        dropdowns: {
                            minYear: 2023,
                            maxYear: 2035,
                            months: true,
                            years: true
                        }
                    }"
                    class="form-control w-72"
                />

                <button class="btn btn-primary" @click="search(date_range)">
                    <font-awesome-icon icon="search" class="mr-1"/>
                    Buscar
                </button>

                <button class="btn btn-primary" @click="pdf(date_range)">
                    <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                    PDF
                </button>
            </div>
        </portal>

        <div>
            <template v-if="Object.keys(table.data).length > 0">
                <div class="box">
                    <div class="p-5">
                        <table class="table table-bordered table-sm mt-5 text-center">
                            <thead>
                            <tr>
                                <th colspan="2">SEMANA </th>
                            </tr>
                            </thead>
                            <tbody  v-for="(item, key) in table.data">
                            <tr>
                                <td>{{key}}</td>
                                <td>
                                    <ul>
                                        <li v-for="value in item"  >
                                            <div class="grid grid-cols-2 gap-5 ">
                                                <div>{{value.code}} - {{value.name}}</div>
                                                <div>PROXIMO MANTENIMIENTO : {{value.next}}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template v-else>
                <div class="box p-5 text-danger text-center">
                    NO SE ENCONTRARON REGISTROS EN ESTE RANGO DE FECHAS
                </div>
            </template>
        </div>
    </div>
</template>

<script >
import {Head} from "@inertiajs/vue3";

export default {
    components: {
        Head
    },

    data() {
        return {
            date_range: "",
            table: {
                data: [],
            }
        }
    },

    methods:{
        search(date_range){
            if (date_range.length > 0) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Enviando…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('maintenance_schedule_search'), {
                    date: this.date_range
                }).then(resp => {
                    this.table.data = resp.data

                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: "Cronograma creado con éxito!",
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar'
                    });
                }).catch(() => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos una fecha',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        pdf(date_range){
            if(date_range.length > 0){
                axios.get(route('maintenance.pdf_preventive', date_range), {
                    data: this.date_range,
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');

                    link.href = url;
                    link.setAttribute('download', 'cronograma-mantenimientos-preventivos.pdf');

                    document.body.appendChild(link);
                    link.click();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Selecciona al menos una fecha',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    }
}
</script>

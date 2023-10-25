<template>
    <div>
        <Head title="OV pendientes por producto"/>

        <portal to="application-title">
            OV pendientes por producto
        </portal>

        <portal to="actions">
            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label for="">BUSQUE EL PRODUCTO A CONSULTAR CODIGO/DESCRIPCION</label>
                        <autocomplete
                            ref="product_item"
                            :url="route('order.search-products')"
                            class="w-full"
                            show-field="field"
                            @selected-value="getProductInfo"
                        />
                    </div>

                    <button class="btn btn-primary" @click="search">
                        Consultar
                    </button>
                </div>
            </div>
        </div>

        <div class="box mt-5">
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th colspan="2"></th>
                                <th colspan="3">FECHAS</th>
                                <th colspan="4">CANTIDADES</th>
                                <th colspan="4"></th>
                            </tr>
                        <tr>
                            <th>OV</th>
                            <th>OC</th>
                            <th>MOVIMIENTO</th>
                            <th>COMPROMISO</th>
                            <th>DESPACHO</th>
                            <th>ACTUAL</th>
                            <th>DESPACHADA</th>
                            <th>FACTURADA</th>
                            <th>PENDIENTE</th>
                            <th>CLIENTE</th>
                            <th>ARTE</th>
                            <th>MARCA</th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr v-for="elem in data">
                                <td>{{ `${elem.OV}${elem.ITEM}${elem.LINEA}` }}</td>
                                <td>{{ elem.OC }}</td>
                                <td>{{ $h.formatDate(elem.FECHA_OV, 'YYYY-MM-DD') }}</td>
                                <td class="text-right">{{ elem.CANT_ACTUAL }}</td>
                                <td>{{ $h.formatDate(elem.FECHA_DESPACHO, 'YYYY-MM-DD') }}</td>
                                <td class="text-right">{{ elem.CANT_DESPACHADA }}</td>
                                <td class="text-right">{{ elem.CANT_FACTURADA }}</td>
                                <td class="text-right">{{ elem.CANT_PENDIENTE }}</td>
                                <td>{{ elem.RAZON_SOCIAL }}</td>
                                <td>{{ elem.ARTE }}</td>
                                <td>{{ elem.Marca }}</td>
                                <td>{{ $h.formatDate(elem.FechaCompromiso, 'YYYY-MM-DD') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>
</template>

<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import useVuelidate from "@vuelidate/core";
import {required} from "@/utils/i18n-validators";
import {Head, Link} from "@inertiajs/vue3";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Autocomplete,
        Head, Link
    },

    validations(){
        return {
            form: {
                code: {
                    required
                }
            }
        }
    },

    data() {
        return {
            form: {
                code: '',
            },
            data: []
        }
    },

    methods: {
        search(){
            this.v$.form.$touch()

            if (this.v$.form.$invalid){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.get(route('report-sales.ov-pending-per-product.search', 'CIEV'), {
                    params: {
                        code: this.form.code
                    }
                }).then(resp => {
                    this.data = resp.data
                    this.$swal.close()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        },

        getProductInfo(obj){
            this.form.code = obj.code
        }
    }
}

</script>

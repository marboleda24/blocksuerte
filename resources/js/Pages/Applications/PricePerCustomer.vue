<template>
    <div>
        <Head title=" Precios por cliente" />

        <portal to="application-title">
            Precios por cliente
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-4 gap-5">
                <input type="number" class="form-control" v-model="form.increase_percent">

                <autocomplete
                    :url="route('electronic-billing.documents.search-customer', 'CIEV')"
                    show-field="customer"
                    @selected-value="getCustomerData"
                />

                <button class="btn btn-primary" @click="getData(form.customer_code)">
                    <font-awesome-icon :icon="['fas', 'search']" class="mr-1"/>
                    Consultar
                </button>

                <button class="btn btn-primary" @click="download(selected)">
                    <font-awesome-icon :icon="['fas', 'download']" class="mr-1"/>
                    Descargar
                </button>
            </div>
        </portal>
        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                            class="overflow-y-auto">
            </v-client-table>
        </div>

    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head, Link} from "@inertiajs/vue3";
import useVuelidate from "@vuelidate/core";
import dom from "@left4code/tw-starter/dist/js/dom";
import {isNumber} from "lodash";


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Autocomplete,
        Head,
        Link
    },

    data() {
        let self = this

        return {
            form: {
                increase_percent: 0,
                customer_code: ''
            },

            table: {
                data: [],
                columns: [
                    'product',
                    'customer_product_code',
                    'price',
                    'new_price'
                ],
                options: {
                    headings: {
                        product: 'PRODUCTO',
                        customer_product_code: 'COD. PROD. CLIENTE',
                        price: 'PRECIO',
                        new_price: 'NUEVO PRECIO'
                    },
                    uniqueKey: 'customer_product_code',
                    selectable: {
                        mode: 'multiple',
                        selectAllMode: 'all',
                        programmatic: false
                    },
                    templates: {
                        price(h, row) {
                            return this.$h.formatCurrency(row.price)
                        },
                        new_price(h, row){
                            let result = 0
                            if (isNaN(self.form.increase_percent) || !isNumber(self.form.increase_percent)){
                                result = this.$h.formatCurrency(row.price)
                            }

                            result = this.$h.formatCurrency((((row.price * self.form.increase_percent) / 100) + Number(row.price)))
                            row.new_price = ((row.price * self.form.increase_percent) / 100) + Number(row.price)

                            return result;
                        }
                    },
                    cellClasses: {
                        price: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        new_price: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                    }
                },

            },

        }
    },
    methods: {
        getCustomerData(obj) {
            this.form.customer_code = obj.code
        },


        getData(code) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });


            axios.get(route('price-per-customer.get-data'), {
                params: {
                    code: code
                }

            }).then(resp => {
                this.table.data = resp.data
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. ',
                    text: 'Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(err.data);
            })
        },

        download(){
            if (this.selected.length > 0){
                axios.post(route('price-per-customer.download'), {
                    selected: this.selected
                },{
                    responseType: 'blob'
                }).then(resp => {
                    const url = URL.createObjectURL(new Blob([resp.data], {
                        type: 'application/vnd.ms-excel'
                    }))

                    const link = document.createElement('a')

                    link.href = url
                    link.setAttribute('download', 'report.xlsx')
                    document.body.appendChild(link)
                    link.click()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err.data);
                })
            }else{
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Por favor, selecciona al menos un registro',
                    showConfirmButton: true,
                });
            }


        }
    },

    computed: {
        selected() {
            return this.$refs.table1.$refs.table.selectedRows
        }
    },

    mounted() {
        dom('#vt-toggle-all').addClass('form-check-input')
    }
}


</script>

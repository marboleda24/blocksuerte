<template>
    <div>
        <Head title="Nuevo Anticipo"/>

        <portal to="application-title">
            Nuevo Anticipo
        </portal>

        <portal to="actions">
            <Link :href="route('advances.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>


        <div>
            <div class="grid grid-cols-4 gap-4">
                <div class="mt-1">
                    <label for="">CLIENTE</label>
                    <autocomplete
                        url="/orders/search-customer"
                        show-field="name"
                        @selected-value="get_client_data"
                        class="w-full"
                    />

                    <jet-input-error v-if="errors.customer_code" :message="errors.customer_code[0]"
                                     class="mt-2"/>
                </div>
                <div class="mt-1">
                    <label for="">FECHA DE PAGO</label>

                    <Litepicker
                        v-model="form.payment_date"
                        :options="{
                            autoApply: true,
                            singleMode: true,
                            numberOfColumns: 1,
                            numberOfMonths: 1,
                            showWeekNumbers: true,
                            format: 'DD-MM-YYYY',
                            lang: 'es-ES',
                            dropdowns: {
                                minYear: 2021,
                                maxYear: null,
                                months: true,
                                years: true
                            },
                            minDate: startDateFrom,
                            maxDate: current_date
                        }"
                        class="form-control"
                    />

                    <jet-input-error v-if="errors.payment_date" :message="errors.payment_date[0]"
                                     class="mt-2"/>
                </div>

                <div class="mt-1">
                    <label for="">CUENTA</label>
                    <select class="form-select" v-model="form.bank_account">
                        <option value="" selected disabled>Seleccione</option>
                        <option value="11200505">BANCOLOMBIA - xxxxxxx1953</option>
                        <option value="11200510">BANCOLOMBIA - xxxxxxx9471</option>
                        <option value="11200515">BANCOLOMBIA - xxxxxxx3587</option>
                        <option value="11100505">BANCOLOMBIA - xxxxxxx1701</option>
                        <option value="11100506">BANCOLOMBIA - xxxxxxx2074</option>
                        <option value="11100506">BANCO OCCIDENTE - xxxxxxx3489</option>
                    </select>
                    <jet-input-error v-if="errors.bank_account" :message="errors.bank_account[0]"
                                     class="mt-2"/>
                </div>

                <div class="mt-1">
                    <label for="">VALOR PAGADO</label>
                    <input type="number" class="form-control" v-model.number="form.total_paid">
                    <jet-input-error v-if="errors.total_paid" :message="errors.total_paid[0]" class="mt-2"/>
                </div>


                <div class="mt-1 col-span-4">
                    <label for="">OBSERVACIONES</label>
                    <textarea class="form-control" v-model="form.details"></textarea>
                </div>
            </div>

            <div class="row justify-content-left mt-4">
                <button @click.prevent="save(form)" :disabled="form.processing"
                        type="submit" class="btn btn-primary uppercase">
                    <font-awesome-icon icon="save" class="mr-2"/>
                    Guardar recibo de caja
                </button>
            </div>
        </div>

    </div>

</template>
<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import {Head, Link} from '@inertiajs/vue3'
import JetInputError from "@/Jetstream/InputError.vue";
import dayjs from "dayjs";


export default {
    components: {
        Autocomplete,
        JetInputError,
        Head,
        Link
    },

    data() {
        return {
            form: {
                customer_code: null,
                customer_nit: null,
                payment_date: '',
                total_paid: null,
                bank_account: "",
                details: null,
                processing: false,
            },
            errors: {},
            processing: false
        }
    },

    methods: {
        get_client_data(obj) {
            this.form.customer_nit = obj.nit,
                this.form.customer_code = obj.code
        },

        save: function (data) {
            this.form.processing = true;
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Creando anticipo...',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('advances.store'), data).then(res => {
                this.errors = {};

                this.$swal({
                    title: '¡Éxito!',
                    text: "Anticipo creado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                this.$inertia.visit(route('advances.index'));
                this.form.processing = false;

            }).catch(err => {
                this.form.processing = false;
                this.errors = err.response.data.errors;
                console.log(err);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            });
            this.form.processing = false;
        }
    },

    computed: {
        startDateFrom(){
            const currentTime = new Date();
            return new Date(currentTime.getFullYear(),currentTime.getMonth(),1)
        },

        current_date() {
            return dayjs()
        }
    }
}
</script>

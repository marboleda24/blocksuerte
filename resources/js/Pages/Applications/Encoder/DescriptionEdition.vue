<template>
    <div>
        <Head title="Edicion de descripciones"/>

        <portal to="application-title">
            Edicion de descripciones
        </portal>

        <div>
            <div class="grid grid-cols-1 gap-5">
                <autocomplete
                    :url="route('description-edition.search-product')"
                    show-field="description"
                    @selected-value="getProduct"
                />
            </div>

            <div class="box mt-5">
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-5">
                        <div>
                            <label class="form-label">Codigo</label>
                            <input type="text" class="form-control" :value="product_information.code" disabled>
                        </div>

                        <div>
                            <label class="form-label">Descripcion</label>
                            <input type="text" class="form-control border-warning" :value="product_information.description" disabled>
                        </div>

                        <div>
                            <label class="form-label">Nueva Descripcion</label>
                            <input type="text" class="form-control border-success" :value="new_description" disabled>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-5 mt-5">
                        <div>
                            <label class="form-label">Tipo de producto</label>
                            <input type="text" class="form-control" :value="product_information.product_type?.name" disabled>
                        </div>

                        <div>
                            <label class="form-label">Linea</label>
                            <input type="text" class="form-control" :value="product_information.line?.name" disabled>
                        </div>

                        <div>
                            <label class="form-label">Sublinea</label>
                            <input type="text" class="form-control" :value="product_information.subline?.name" disabled>
                        </div>

                        <div>
                            <label class="form-label">Caracteristica</label>
                            <select class="form-control" v-model="form.feature_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="feature in features" :value="feature.code">{{ feature.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Material</label>
                            <input type="text" class="form-control" :value="product_information.material?.material.name" disabled>
                        </div>

                        <div>
                            <label class="form-label">Medida</label>
                            <select class="form-control" v-model="form.measurement_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="measurement in measurements" :value="measurement.id">{{ measurement.measurement }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Acabado Galvanico</label>
                            <select class="form-select" v-model="form.galvanic_finish_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="gf in galvanic_finish" :value="gf.code">{{ gf.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Opcion decorativa</label>
                            <select class="form-select" v-model="form.decorative_option_code">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="decorative_option in decorative_options" :value="decorative_option.code">{{ decorative_option.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Arte</label>
                            <input type="text" class="form-control" :value="product_information.art_code" disabled>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <button class="btn btn-primary">Actualizar descripcion</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";

export default {
    components: {
        Head,
        Autocomplete
    },

    props: {
        decorative_options: Array,
        galvanic_finish: Array
    },

    data(){
        return {
            measurements: [],
            features: [],
            product_information: {},
            form: {},
            new_description: ''
        }
    },

    methods: {
        update(){
            this.$swal({
                icon: 'question',
                title: '¿Actualizar producto?',
                text: "¡Esta acción no es reversible!",
                showCancelButton: true,
                confirmButtonText: '¡Si, actualizar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tomar unos segundos, espere por favor…',
                    });

                    axios.post(route('description-edition.store'), this.form).then(resp => {

                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: error.response.data,
                            confirmButtonText: 'Aceptar',
                        });
                    })
                }
            })
        },

        getProduct(obj){
            this.product_information = obj
        },

        getMeasurementsFeatures(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('description-edition.get-measurements-features'), {
                params: {
                    line_code: this.product_information.line_code,
                    subline_code: this.product_information.subline_code
                }
            }).then(resp => {
                this.$swal.close()
                this.measurements = resp.data.measurements
                this.features = resp.data.features

                return true
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: error.response.data,
                    confirmButtonText: 'Aceptar',
                });

                return false
            })

            return false
        },

        generateDescription(){
            let feature = this.features.find(row => row.code === this.form.feature_code)

            console.log(this.form.feature_code, this.features)

            let description = `${this.product_information.product_type_code}-${this.product_information.line?.abbreviation} ${this.product_information.line?.abbreviation} ${feature.abbreviation}`

            return description.trim().replace(/ +(?= )/g,'');
        }

    },

    watch: {
        'product_information': function () {
            const result = this.getMeasurementsFeatures();

            if (result){
                this.form = {
                    feature_code: this.product_information?.feature_code,
                    measurement_id: this.product_information?.measurement_id,
                    galvanic_finish_code: this.product_information?.galvanic_finish_code,
                    decorative_option_code: this.product_information?.decorative_option_code,
                }
            }

        },

        'form': function (){
            this.new_description = this.generateDescription()
        }
    },



}
</script>

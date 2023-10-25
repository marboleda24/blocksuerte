<template>
    <dialog-modal :show="showModal" @close="close">
        <template #title>
            Crear producto
        </template>

        <template #content>
            <div class="alert alert-warning show mb-2 shadow-sm"
                 role="alert">
                <div class="flex items-center">
                    <div class="font-medium text-lg uppercase">Información importante</div>
                </div>
                <div class="mt-0">
                    Por favor, seleccione el acabado galvánico y la opción decorativa para este producto
                </div>
            </div>

            <div class="mt-5">
                <label class="form-label">Arte</label>
                <TomSelect class="w-full" v-model="form.art_code">
                    <option selected value="">Seleccione…</option>
                    <option v-for="art in arts" :value="art">{{ art }}</option>
                </TomSelect>
            </div>

            <template v-if="form.art_code">
                <div class="grid grid-cols-3 gap-5 mt-5">
                    <div>
                        <label class="form-label">Tipo de producto</label>
                        <input type="text" class="form-control"
                               :value="`${product_data.product_type?.code} – ${product_data.product_type?.name}`" disabled>
                    </div>
                    <div>
                        <label class="form-label">Linea</label>
                        <input type="text" class="form-control"
                               :value="`${product_data.line?.code} – ${product_data.line?.name}`" disabled>
                    </div>
                    <div>
                        <label class="form-label">Sublinea</label>
                        <input type="text" class="form-control"
                               :value="`${product_data.subline?.code} – ${product_data.subline?.name}`" disabled>
                    </div>
                    <div>
                        <label class="form-label">Caracteristica</label>
                        <input type="text" class="form-control"
                               :value="`${product_data.feature?.code} – ${product_data.feature?.name}`" disabled>
                    </div>
                    <div>
                        <label class="form-label">Material</label>
                        <input type="text" class="form-control"
                               :value="`${product_data.material?.material?.code} – ${product_data.material?.material?.name}`"
                               disabled>
                    </div>
                    <div>
                        <label class="form-label">Medida</label>
                        <input type="text" class="form-control" :value="product_data.measurement?.measurement" disabled>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="form-label">Acabado galvánico</label>
                        <TomSelect class="w-full" v-model="form.galvanic_finish_code">
                            <option selected value="">Seleccione…</option>
                            <option v-for="galvanic_finish in galvanic_finishes"
                                    v-bind:key="galvanic_finish.code" :value="galvanic_finish.code">
                                {{ galvanic_finish.name }}
                            </option>
                        </TomSelect>
                    </div>
                    <div>
                        <label class="form-label">Opción Decorativa</label>
                        <TomSelect class="w-full" v-model="form.decorative_option_code">
                            <option selected value="">Seleccione…</option>
                            <option v-for="decorative_option in decorative_options"
                                    v-bind:key="decorative_option.code" :value="decorative_option.code">
                                {{ decorative_option.name }}
                            </option>
                        </TomSelect>
                    </div>
                </div>
            </template>

        </template>

        <template #footer>
            <button @click="storeProduct" type="button" class="btn btn-primary mr-2">
                Crear producto
            </button>
            <button @click="close" type="button" class="btn btn-secondary">
                Cancelar
            </button>
        </template>
    </dialog-modal>
</template>

<script lang="jsx">
import DialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props: {
        productDescription: {
            type: String,
            default: ''
        },
        showModal: {
            type: Boolean,
            default: false
        }
    },

    components: {
        DialogModal
    },

    data() {
        return {
            arts: [],
            product_data: {},
            form: {
                product_description: '',
                galvanic_finish_code: '',
                decorative_option_code: '',
                art_code: ''
            },
            galvanic_finishes: [],
            decorative_options: [],
        }
    },

    methods: {
        storeProduct() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('design-requirements.new-product.store-product-v2'), this.form).then(resp => {
                this.$swal({
                    icon: 'info',
                    title: 'Solicitud procesada correctamente',
                    text: 'Producto creado con éxito, el area encargada codificara este producto en MAX y el producto generado sera actualizado de este pedido',
                    confirmButtonText: 'Aceptar'
                });

                this.$emit('success', resp.data, 'success')
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response?.data,
                    confirmButtonText: 'Aceptar'
                });
                this.$emit('success', null, 'fail')
            })
        },

        close() {
            this.product_data = {}
            this.form = {
                product_description: '',
                galvanic_finish_code: '',
                decorative_option_code: '',
                art_code: ''
            }
            this.galvanic_finishes = []
            this.decorative_options = []
            this.$emit('close')
        },

        loadProduct() {
            axios.get(route('design-requirements.new-product.product'), {
                params: {
                    product_description: this.productDescription
                }
            }).then(resp => {
                this.product_data = resp.data.product
                this.form.product_description = resp.data.product.description
                this.arts = resp.data.arts
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        loadFields() {
            axios.get(route('design-requirements.new-product.fields')).then(resp => {
                this.galvanic_finishes = resp.data.galvanic_finishes
                this.decorative_options = resp.data.decorative_options
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },
    },

    watch: {
        showModal: function () {
            if (this.showModal) {
                this.loadFields()
                this.loadProduct()
            }
        },

        'form.art_code': function (art){
            axios.get(route('design-requirements.new-product.get-measurement-by-art'), {
                params: {
                    art: art
                }
            }).then(resp => {
                this.product_data.measurement_id = resp.data.measurement_id
                this.product_data.measurement = resp.data.measurement
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar'
                });
            })
        }
    },
}
</script>

<template>
    <div>
        <Head title="Nuevo Requerimiento"/>

        <portal to="application-title">
            Nuevo Requerimiento
        </portal>

        <portal to="actions">
            <Link :href="route('design-requirements.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="p-5">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Asesor comercial
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.seller_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.seller_id.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="seller in sellers" :value="seller.id">{{ seller.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.seller_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.seller_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <autocomplete
                                url="/orders/search-customer"
                                show-field="name"
                                @selected-value="customerInfo">
                            </autocomplete>

                            <template v-if="v$.form.customer_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.customer_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Marca
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <div class="input-group">
                                <TomSelect v-model="form.brand_id"
                                           class="w-full"
                                           :class="{ 'border-danger': v$.form.brand_id.$error }">
                                    <option value="">Seleccione…</option>
                                    <option :value="brand.id" v-for="brand in brands">{{ brand.name }}</option>
                                </TomSelect>
                                <div class="input-group-text cursor-pointer" @click="openBrandModal = true">
                                    <font-awesome-icon icon="plus" class="text-primary"/>
                                </div>
                            </div>

                            <template v-if="v$.form.brand_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.brand_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Parámetro
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.parameter"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.parameter.$error }">
                                <option value="">Seleccione…</option>
                                <option value="no">Sin Parámetros</option>
                                <option value="yes">Oro o Terminado ESP</option>
                            </TomSelect>

                            <template v-if="v$.form.parameter.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.parameter.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Tipo de producto
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.product_type_code"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.product_type_code.$error }"
                                       @change="getLines">
                                <option value="">Seleccione…</option>
                                <option v-for="product_type in product_types" :value="product_type.code">{{ product_type.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.product_type_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.product_type_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Linea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <div class="input-group">
                                <TomSelect v-model="form.line_code"
                                           class="w-full"
                                           :class="{ 'border-danger': v$.form.line_code.$error }"
                                           @change="getSublines">
                                    <option value="">Seleccione…</option>
                                    <option v-for="line in lines" :value="line.code">{{ line.name }}</option>
                                </TomSelect>
                                <div class="input-group-text cursor-pointer" @click="openSearchModal = true">
                                    <font-awesome-icon icon="search" class="text-primary"/>
                                </div>
                            </div>

                            <template v-if="v$.form.line_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.line_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Sublinea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.subline_code"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.subline_code.$error }"
                                       @change="getAnotherInputs">
                                <option value="">Seleccione…</option>
                                <option v-for="subline in sublines" :value="subline.code">{{ subline.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.subline_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.subline_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Característica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.feature_code"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.feature_code.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="feature in features" :value="feature.code">{{ feature.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.feature_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.feature_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Material
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.material_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.material_id.$error }" ref="materialTom">
                                <option value="">Seleccione…</option>
                                <option v-for="material in materials" :value="material.id">{{ material.material.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.material_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.material_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Medida Sugerida
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.measurement_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.measurement_id.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="measurement in measurements" v-bind:key="measurement.id"
                                        :value="measurement.id">
                                    {{ measurement.measurement }}
                                </option>
                            </TomSelect>

                            <template v-if="v$.form.measurement_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.measurement_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form" v-if="showBase">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Requiere base
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Opcional
                                </span>
                            </label>

                            <select class="form-select form-control"
                                    v-model="form.base"
                                    @change="baseMsg">
                                <option value="">Seleccione…</option>
                                <option value="yes">Si</option>
                                <option value="no">No</option>
                            </select>
                        </div>

                        <div class="col-span-2 flex flex-row">
                            <div class="input-form w-2/3">
                                <label class="form-label w-full flex flex-col sm:flex-row">
                                    Archivos de Soporte
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Opcional (imagenes, .pdf, .csv, .xlsx, .txt)
                                </span>
                                </label>
                                <input type="file"
                                       ref="file_upload"
                                       accept="image/*,.pdf,.csv,.xlsx,.xls,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword"
                                       class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                                       multiple/>
                            </div>

                            <div class="input-form w-1/3 ml-4">
                                <label class="form-label w-full flex flex-col sm:flex-row">
                                    ¿Renderizar?
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Opcional
                                </span>
                                </label>

                                <div class="flex flex-col sm:flex-row mt-4">
                                    <div class="form-check mr-2">
                                        <input class="form-check-input" type="radio" value="yes" v-model="form.render" :disabled="required_render"/>
                                        <label class="form-check-label">SI</label>
                                    </div>
                                    <div class="form-check mr-2">
                                        <input class="form-check-input" type="radio" value="no" v-model="form.render" :disabled="required_render"/>
                                        <label class="form-check-label">NO</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-form col-span-4">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Detalles adicionales
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control resize-none"
                                      v-model="form.details"
                                      :class="{ 'border-danger': v$.form.details.$error }" cols="30"
                                      rows="5"></textarea>

                            <template v-if="v$.form.details.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.details.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <button class="btn btn-primary" @click="save(form)">
                        <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-2"/>
                        Solicitar Requerimiento
                    </button>
                </div>
            </div>

            <jet-dialog-modal :show="openBrandModal" @close="closeBrandModal" max-width=xl>
                <template #title>
                    Registrar Marca
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <autocomplete
                                url="/orders/search-customer"
                                ref="brand_customer"
                                show-field="name"
                                @selected-value="getCustomerBrand">
                            </autocomplete>

                            <template v-if="v$.brand_form.customer_code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.brand_form.customer_code.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Tipo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select form-control"
                                    :class="{ 'border-danger': v$.brand_form.type.$error }"
                                    v-model="brand_form.type">
                                <option value="">Seleccione…</option>
                                <option value="G">Genérica</option>
                                <option value="MP">Marca Propia</option>
                            </select>

                            <template v-if="v$.brand_form.type.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.brand_form.type.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Nombre
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.brand_form.name.$error }"
                                   v-model="brand_form.name" v-uppercase>

                            <template v-if="v$.brand_form.name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.brand_form.name.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="input-form col-span-3">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Información Adicional
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Opcional
                                </span>
                            </label>
                            <textarea class="form-control resize-none" cols="30" rows="3"></textarea>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-secondary mr-2"
                            @click="closeBrandModal">
                        Cancelar
                    </button>

                    <button class="btn btn-primary"
                            @click="saveBrand(brand_form)">
                        Guardar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="openSearchModal" @close="closeSearchModal" max-width=xl>
                <template #title>
                    Búsqueda de Productos
                </template>

                <template #content>
                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Producto
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Busque y seleccione un producto para autocompletar
                            </span>
                        </label>

                        <autocomplete
                            :url="route('design-requirements.search-product')"
                            ref="search_product"
                            show-field="description"
                            @selected-value="getProductInfo">
                        </autocomplete>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-secondary mr-2"
                            @click="closeSearchModal">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {helpers, minLength, required, requiredIf} from '@/utils/i18n-validators'

const validName = helpers.regex(/^([A-Z]{1,2})/i)
const { withAsync } = helpers

const CancelToken = axios.CancelToken;
let source;

const uppercase = {
    beforeUpdate(el) {
        el.value = el.value.toUpperCase()
    },
}

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    directives: {
        uppercase,
    },

    props: {
        sellers: Array,
        product_types: Array
    },

    components: {
        Autocomplete,
        JetDialogModal,
        Head,
        Link
    },

    validations() {
        return {
            form: {
                seller_id: {required},
                customer_code: {required},
                brand_id: {required},
                parameter: {required},
                product_type_code: {required},
                line_code: {required},
                subline_code: {required},
                feature_code: {required},
                material_id: {required},
                measurement_id: {required},
                details: {required}
            },

            brand_form: {
                type: {required},
                name: {
                    required,
                    validName: helpers.withMessage('Nombre invalido, debe comenzar con una letra', validName),
                    minLength: minLength(1),
                    isUnique: helpers.withMessage('Esta marca ya se encuentra registrada', withAsync(async (value) => {

                        if(value) {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            try {
                                return await axios.get(route('design-requirements.verify-brand', value), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                if (axios.isCancel(error)) {
                                    console.log('Operation canceled by the user.');
                                    return true;
                                }
                                return false
                            }
                        }
                        else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                customer_code: {
                    required: requiredIf(function () {
                        return this.brand_form.type === 'MP'
                    })
                }
            },
        }
    },

    data() {
        return {
            brands: [],
            lines: [],
            sublines: [],
            features: [],
            materials: [],
            measurements: [],
            form: {
                seller_id: '',
                customer_code: '',
                brand_id: '',
                parameter: '',
                product_type_code: '',
                line_code: '',
                subline_code: '',
                feature_code: '',
                material_id: '',
                measurement_id: '',
                base: 'no',
                render: 'no',
                details: ''
            },
            brand_form: {
                name: '',
                customer_code: '',
                details: '',
                type: ''
            },
            openBrandModal: false,
            openSearchModal: false,
            showBase: false,
            required_render: false
        }
    },

    methods: {
        closeBrandModal() {
            this.brand_form = {
                name: '',
                customer_code: '',
                details: '',
                type: ''
            }
            this.$refs.brand_customer.showInput(true)
            this.v$.brand_form.$reset();
            this.openBrandModal = false
        },

        closeSearchModal(){
            this.$refs.search_product.showInput(true)
            this.openSearchModal = false
        },

        save(form) {
            this.v$.form.$touch()
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Por favor, verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                const formData = new FormData();
                Object.keys(form).forEach(key => formData.append(key, form[key]));

                for (let i = 0; i < this.$refs.file_upload.files.length; i++) {
                    let file = this.$refs.file_upload.files[i];
                    formData.append('files[]', file);
                }
                axios.post(route('design-requirements.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Requerimiento creado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })

                    this.$inertia.visit(route('design-requirements.index'));
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                })


            }
        },

        saveBrand(form){
            this.v$.brand_form.$touch()
            if (this.v$.brand_form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando Marca…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.post(route('brands.store'), form).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Marca creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.LoadBrands()
                    this.closeBrandModal()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                })
            }
        },

        getLines(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-lines'), {
                params: {
                    product_type: this.form.product_type_code
                }
            }).then(resp => {
                this.lines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getSublines(){
            let selected_line = this.lines.filter(elem => elem.code === this.form.line_code)[0].types.filter(elem => elem === 'S')
            this.showBase = selected_line.length > 0

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-sublines'), {
                params: {
                    line_code: this.form.line_code
                }
            }).then(resp => {
                this.sublines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getAnotherInputs(){
            this.reset_onchange_subline()

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-other-inputs'), {
                params: {
                    line_code: this.form.line_code,
                    subline_code: this.form.subline_code
                }
            }).then(resp => {
                this.features = resp.data.features
                this.materials = resp.data.materials
                this.measurements = resp.data.measurements
                this.$swal.close();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        reset_onchange_subline() {
            this.form.feature_code = ''
            this.form.material_id = ''
            this.form.measurement_id = ''
            this.features = []
            this.materials = []
            this.measurements = []
        },

        getCustomerBrand(obj){
            this.brand_form.customer_code = obj.code
        },

        customerInfo(obj){
            this.form.customer_code = obj.code
            this.LoadBrands()
        },

        getProductInfo(obj){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('design-requirement.product-info', obj.code)).then(resp => {
                this.form.line_code = resp.data.line_code
                this.getSublines()
                this.form.subline_code = resp.data.subline_code
                this.getAnotherInputs()
                this.form.feature_code = resp.data.feature_code
                this.form.material_id = resp.data.material_id
                this.form.measurement_id = resp.data.measurement_id
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        LoadBrands(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('design-requirements.get-brands'), {
                params: {
                    customer_code: this.form.customer_code
                }
            }).then(resp => {
                this.brands = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        baseMsg(){
            if (this.form.base === 'yes'){
                this.$swal({
                    icon: 'info',
                    title: 'Información importante',
                    html: `Recuerda colocar en el campo <span class="text-danger"> detalles adicionales </span> el producto base `,
                    confirmButtonText: 'Aceptar',
                });
            }

        }
    },
    mounted() {
        this.LoadBrands();
    },

    watch: {
        'form.material_id': function (value) {
            let result = this.materials.find(elem => elem.id === parseInt(value))?.material?.name === 'ZAMAK'
            this.form.render = result ? 'yes' : 'no';
            this.required_render = result;
        }
    }
}
</script>


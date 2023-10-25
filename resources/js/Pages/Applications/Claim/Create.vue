<template>
    <div>
        <Head title="Nueva Reclamación"/>

        <portal to="application-title">
            Nueva Reclamación
        </portal>

        <portal to="actions">
            <Link :href="route('claim.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="intro-y box">
            <div class="p-5">
                <div class="grid grid-cols-4 gap-5">
                    <div>
                        <label class="form-label">
                            Documento
                        </label>

                        <div class="input-group">
                            <input type="number"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.form.document.$error }"
                                   v-model="form.document"
                                   placeholder="Documento"/>
                            <div class="input-group-text cursor-pointer" @click="getDocument(form.document)">
                                <font-awesome-icon icon="search" class="text-primary"/>
                            </div>
                        </div>

                        <template v-if="v$.form.document.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.document.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="form-label">
                            Destino
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.form.destiny.$error }"
                                v-model="form.destiny" @change="getCauses">
                            <option value="">Seleccione…</option>
                            <option value="cellar">Bodega</option>
                            <option value="quality">Calidad</option>
                        </select>

                        <template v-if="v$.form.destiny.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.destiny.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="form-label">
                            Acción
                        </label>

                        <select v-model="form.action"
                                :class="{ 'border-danger': v$.form.action.$error }"
                                class="form-select">
                            <option value="">Seleccione…</option>
                            <template v-if="form.destiny === 'cellar'">
                                <option value="credit-note">Nota Crédito</option>
                                <option value="change-reposition">Cambio - Reposiciones</option>
                            </template>

                            <template v-if="form.destiny === 'quality'">
                                <option value="manufacturing">Fabricación</option>
                                <option value="reprocess">Reprocesos</option>
                                <option value="return">Devolución</option>
                            </template>
                        </select>

                        <template v-if="v$.form.action.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.action.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="form-label">
                            Motivo
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.form.reason.$error }"
                                @change="updateForm"
                                v-model="form.reason">
                            <option value="">Seleccione…</option>
                            <template v-if="form.action === 'change-reposition'">
                                <option value="change">Cambio mano a mano</option>
                                <option value="reposition">Reposición de producto</option>
                            </template>

                            <template v-if="form.action === 'credit-note'">
                                <option value="customer-change">Cambio razón social</option>
                                <option value="product-change">Cambio de producto</option>
                                <option value="quantity">Cantidad</option>
                                <option value="quantity-new-invoice">Cantidad - Repetir factura</option>
                                <option value="discount">Descuento</option>
                                <option value="date">Fecha</option>
                                <option value="major-value">Mayor valor cobrado</option>
                                <option value="price">Precio</option>
                            </template>

                            <template v-if="form.action === 'manufacturing' || form.action === 'reprocess' || form.action === 'return'">
                                <option value="NA">NO APLICA</option>
                                <option value="quality-r1">Pedido mal programado desde ventas</option>
                                <option value="quality-r2">Pedido mal programado de parte del cliente</option>
                                <option value="quality-r3">Pedido mal despachado desde el área de bodega</option>
                                <option value="quality-r4">Factura mal generada desde el área de sistemas</option>
                                <option value="quality-r5">Mal revisado por calidad</option>
                                <option value="quality-r6">Pedido mal programado por producción</option>
                            </template>
                        </select>

                        <template v-if="v$.form.reason.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.reason.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>


                    <div v-if="form.reason === 'customer-change'">
                        <label class="form-label">
                            Cliente a facturar
                        </label>

                        <autocomplete
                            :url="route('electronic-billing.documents.search-customer', 'CIEV')"
                            show-field="customer"
                            @selected-value="getCustomerData"
                        />

                        <template v-if="v$.form.new_customer_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.new_customer_code.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>


                    <div v-if="form.reason === 'discount'">
                        <label class="form-label">
                            Descuento
                        </label>

                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.discount.$error }"
                               v-model="form.discount">

                        <template v-if="v$.form.discount.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.discount.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div v-if="form.reason === 'major-value'">
                        <label class="form-label">
                            Mayor valor cobrado
                        </label>

                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.major_value.$error }"
                               v-model="form.major_value">

                        <template v-if="v$.form.major_value.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.major_value.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>


                    <div>
                        <label class="form-label">
                            Causa(s)
                        </label>

                        <TomSelect v-model="form.causes"
                                   class="w-full"
                                   :class="{ 'border-danger': v$.form.causes.$error }"
                                   :options="{
                                       maxItems: 2,
                                       placeholder: 'seleccione o busque una causa (max 2)'
                                   }"
                                   multiple>
                            <option v-for="cause in causes" :value="cause.id">{{ cause.name }}</option>
                        </TomSelect>

                        <template v-if="v$.form.causes.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.causes.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="form-label">
                            Archivos de soporte
                        </label>

                        <input type="file"
                               accept="image/*,.pdf,.csv,.xlsx,.xls,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword"
                               class="form-control file:mr-4 file:btn file:btn-primary border"
                               :class="{ 'border-danger': v$.form.files.$error }"
                               ref="files"
                               @change="filesChange($event)"
                               multiple/>

                        <template v-if="v$.form.files.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.files.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="col-span-4">
                        <label class="form-label">
                            Información adicional
                        </label>

                        <textarea cols="30" rows="5"
                                  class="form-control resize-none"
                                  :class="{ 'border-danger': v$.form.notes.$error }"
                                  v-model="form.notes"></textarea>

                        <template v-if="v$.form.notes.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.notes.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>

                <div class="mt-10 border-t" v-if="items.length > 0">
                    <div class="alert show mb-2 shadow-sm mt-10 mr-auto sm:w-full md:w-2/3 lg:w-2/3 xl:w-1/3"
                         :class="{ 'alert-danger': v$.form.items.$error, 'alert-secondary': !v$.form.items.$error }"
                         role="alert">
                        <div class="flex items-center">
                            <div class="font-medium text-lg uppercase">Información Importante</div>
                        </div>
                        <div class="mt-0">
                            Por favor, seleccione uno o varios items que quiere afectar en esta reclamación, dependiendo
                            del tipo de reclamo debera proporcionar nueva cantidad, precio o producto
                        </div>
                    </div>

                    <div class="mt-5">
                        <table class="table table-bordered">
                            <thead>
                            <tr class="text-center uppercase">
                                <th>#</th>
                                <th>Producto</th>
                                <th>Notas</th>
                                <th v-if="form.reason === 'change' || form.reason === 'product-change'">
                                    Nuevo Producto
                                </th>
                                <th>Arte</th>
                                <th>Marca</th>
                                <th>Precio</th>
                                <th v-if="form.reason === 'price'">
                                    Nuevo Precio
                                </th>
                                <th>Cantidad</th>
                                <th v-if="form.reason === 'quantity' || form.reason === 'change' || form.reason === 'quantity-new-invoice' || form.reason === 'product-change'" >
                                    Cantidad NC
                                </th>
                                <th v-if="form.reason === 'quantity-new-invoice'">
                                    Cantidad Facturar
                                </th>
                                <th v-if="form.reason === 'reposition'">
                                    Cantidad a reponer
                                </th>
                                <th v-if="form.action === 'manufacturing' || form.action === 'reprocess'">
                                    Cantidad a reprocesar
                                </th>
                                <th>
                                    <input type="checkbox"
                                           class="form-check-input"
                                           v-model="selectAll"
                                           :disabled="form.reason === 'discount' || form.reason === 'date'">
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, key) in items" v-bind:key="key">
                                <td>{{ item.item }}</td>
                                <td>{{ `${item.code} - ${item.description}` }}</td>
                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           v-model="item.notes">
                                </td>
                                <td v-if="form.reason === 'change' || form.reason === 'product-change'">
                                    <autocomplete
                                        :ref="`prd_atc_${key}`"
                                        url="/orders/search-products"
                                        class="w-full"
                                        custom-class="form-control-sm"
                                        :iterate-key="key"
                                        show-field="description"
                                        @selected-value="getDataProduct"
                                    />
                                </td>
                                <td class="text-center">
                                    {{ item.art }}
                                </td>
                                <td class="text-center">
                                    {{ item.brand }}
                                </td>
                                <td class="text-right">
                                    {{ item.price }}
                                </td>
                                <td class="text-right" v-if="form.reason === 'price'">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.form.items.$each.$response.$data[key]?.new_price.$error }"
                                           v-model="item.new_price">
                                </td>
                                <td class="text-right">
                                    {{ item.quantity }}
                                </td>
                                <td class="text-right" v-if="form.reason === 'quantity' || form.reason === 'change' || form.reason === 'quantity-new-invoice' || form.reason === 'product-change'">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.form.items.$each.$response.$data[key]?.credit_note_quantity.$error }"
                                           v-model="item.credit_note_quantity">
                                </td>

                                 <td class="text-right" v-if="form.reason === 'quantity-new-invoice'">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.form.items.$each.$response.$data[key]?.new_quantity.$error }"
                                           v-model="item.new_quantity">
                                </td>

                                <td class="text-right" v-if="form.reason === 'reposition'">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.form.items.$each.$response.$data[key]?.reposition_quantity.$error }"
                                           v-model="item.reposition_quantity">
                                </td>
                                <td class="text-right" v-if="form.action === 'manufacturing' || form.action === 'reprocess'">
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.form.items.$each.$response.$data[key]?.delivered_quantity.$error }"
                                           v-model="item.delivered_quantity">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           :value="item"
                                           v-model="form.items"
                                           :disabled="form.reason === 'discount' || form.reason === 'date'">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                <button class="btn btn-primary" @click="store(form)">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {helpers, maxLength, minLength, minValue, numeric, required, requiredIf} from '@/utils/i18n-validators'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Link,
        Autocomplete
    },

    validations() {
        return {
            form: {
                destiny: {required},
                action: {required},
                reason: {required},
                document: {
                    required,
                    minLength: minLength(6),
                    maxLength: maxLength(6),
                    numeric
                },
                discount: {
                    required: requiredIf(function () {
                        return this.form.action === 'discount'
                    })
                },
                major_value: {
                    required: requiredIf(function () {
                        return this.form.action === 'major-value'
                    })
                },
                causes: {
                    required,
                    minLength: minLength(1),
                    maxLength: maxLength(2)
                },
                files: {
                    required: requiredIf(function () {
                        return this.form.action === 'credit-note'
                    })
                },
                notes: {
                    required
                },
                new_customer_code: {
                    required: requiredIf(function () {
                        return this.form.action.action === 'customer-change'
                    })
                },
                items: {
                    required,
                    minLength: minLength(1),
                    $each: helpers.forEach({
                        new_product: {
                            required: requiredIf(function () {
                                return this.form.reason === 'change'
                            }),
                        },
                        new_price: {
                            required: requiredIf(function () {
                                return this.form.reason === 'price'
                            }),
                            minValue: minValue(0)
                        },
                        credit_note_quantity: {
                            required: requiredIf(function () {
                                return this.form.reason === 'quantity' || this.form.reason === 'change' || this.form.reason === 'quantity-new-invoice'
                            }),
                            minValue: minValue(1)
                        },
                        new_quantity: {
                            required: requiredIf(function () {
                                return this.form.reason === 'quantity-new-invoice'
                            }),

                        },
                        reposition_quantity: {
                            required: requiredIf(function () {
                                return this.form.reason === 'reposition'
                            }),
                            minValue: minValue(1)
                        },
                        delivered_quantity: {
                            required: requiredIf(function () {
                                return this.form.reason === 'NA' && this.form.action === 'manufacturing'
                                    || this.form.reason === 'NA' && this.form.action === 'reprocess'
                            }),
                            minValue: minValue(1)
                        }
                    })
                }
            },
        }
    },

    data() {
        return {
            form: {
                destiny: '',
                action: '',
                reason: '',
                causes: [],
                document: '',
                items: [],
                notes: '',
                discount: '',
                major_value: '',
                new_customer_code: ''
            },
            items: [],
            selectAll: false,
            causes: [],
            invoice_date : ''
        }
    },

    methods: {
        getDocument(document) {
            if (document.toString().length === 6 && !isNaN(document)) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.get(route('claim.document-data', document)).then(resp => {
                    this.items = resp.data
                    this.$swal.close()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(err);
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Numero de documento no valido',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        getCauses() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            this.form.causes = [];

            axios.get(route('claim.get-causes', this.form.destiny)).then(resp => {
                this.causes = resp.data
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        filesChange(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.files = ''
            }
            this.form.files = files[0];
        },

        store(form) {
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                const formData = new FormData();

                Object.keys(form).forEach(key => formData.append(key, key === 'items' ? JSON.stringify(form[key]) : form[key]));


                formData.delete('files');

                for (let i = 0; i < this.$refs.files.files.length; i++) {
                    let file = this.$refs.files.files[i];
                    formData.append('files[]', file);
                }

                axios.post(route('claim.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "¡Reclamo creado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    this.$inertia.visit(route('claim.index'));

                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });

                    console.log(err);
                })
            }
        },

        updateForm() {
            for (const key in this.items) {
                this.items[key].new_product = ''
                this.items[key].new_price = null
                this.items[key].new_quantity = null
                this.items[key].credit_note_quantity = null
                this.items[key].reposition_quantity = null
                this.items[key].delivered_quantity = null
                this.$refs['prd_atc_'.key]?.showInput(true)
            }

            this.form.discount = ''
            this.form.major_value = ''
            this.form.new_customer_code = ''

        },

        getDataProduct(obj, key) {
            this.items[key].new_product = obj.code
        },

        getCustomerData(obj){
            this.form.new_customer_code = obj.code
        }
    },

    watch: {
        selectAll() {
            if (this.selectAll) {
                this.form.items = this.items
            } else if (!this.selectAll && this.form.items.length === this.items.length) {
                this.form.items = []
            }
        },

        'form.items': function () {
            if (this.form.items.length < this.items.length) {
                this.selectAll = false
            } else if (this.form.items.length === this.items.length) {
                this.selectAll = true
            }
        },

        'form.reason': function () {
            if (this.form.reason === 'discount' || this.form.reason === 'date') {
                this.selectAll = true
                this.form.items = this.items
            }
        }
    }
}
</script>


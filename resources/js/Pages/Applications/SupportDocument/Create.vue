<template>
    <Head title="Nuevo Documento Soporte"/>

    <portal to="application-title">
        Nuevo Documento Soporte
    </portal>

    <portal to="actions">
        <Link :href="route('support-document.index', entity)" class="btn btn-primary">
            <font-awesome-icon icon="arrow-left" class="mr-2"/>
            Atras
        </Link>

        <button class="btn btn-secondary ml2" @click="validateNIT">Validar NIT</button>
    </portal>

    <div>
        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-4">
                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Proveedor
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <div class="input-group">
                            <autocomplete
                                :url="route('support-document.search-provider', entity)"
                                show-field="nombres"
                                class="w-full"
                                @selected-value="provider_info">
                            </autocomplete>

                            <Tippy tag="button"
                                   content="Crear un nuevo proveedor"
                                   class="btn btn-secondary ml-2 w-56"
                                   :options="{ placement: 'top' }"
                                   @click.native="provider_modal = true"
                            >
                                <font-awesome-icon icon="plus" class="mr-2"/>
                                Crear Proveedor
                            </Tippy>
                        </div>

                        <template v-if="v$.form.provider.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.provider.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Fecha Documento
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <Litepicker
                            v-model="form.date"
                            :options="{
                            autoApply: true,
                            singleMode: true,
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
                            maxDate: current_date,
                            minDate: current_date.subtract(10, 'day')
                        }"
                            class="form-control"
                        />

                        <template v-if="v$.form.date.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.date.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Metodo de pago
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.form.payment_form.$error }"
                                v-model="form.payment_form">
                            <option value="" selected disabled>Seleccione…</option>
                            <option value="1">Contado</option>
                            <option value="2">Credito</option>
                        </select>

                        <template v-if="v$.form.payment_form.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.payment_form.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form col-span-3">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Notas
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Opcional
                            </span>
                        </label>

                        <textarea class="form-control resize-none"
                                  v-model="form.notes"
                                  cols="30" rows="2"></textarea>

                    </div>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-4 gap-4 border border-2 p-2 mb-2">
                        <div class="input-group">
                            <autocomplete
                                :url="route('support-document.search-product', entity)"
                                show-field="description"
                                class="w-full"
                                @selected-value="product_info">
                            </autocomplete>
                            <Tippy tag="button"
                                   content="Crear un nuevo producto o servicio"
                                   class="btn btn-secondary ml-2 w-56"
                                   :options="{ placement: 'top' }"
                                   @click.native="new_product_form.open = true"
                            >
                                <font-awesome-icon icon="plus" class="mr-2"/>
                                Crear Producto
                            </Tippy>
                        </div>

                        <div class="input-group">
                            <div class="input-group-text">Tipo</div>
                            <select class="form-control form-select"
                                    :class="{ 'border-danger': v$.product_form.type.$error }"
                                    v-model="product_form.type">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="product">Producto</option>
                                <option value="service">Servicio</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <div class="input-group-text">Transmisión</div>
                            <select class="form-control form-select"
                                    :class="{ 'border-danger': v$.product_form.type_generation_transmition_id.$error }"
                                    v-model="product_form.type_generation_transmition_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option value="1">Por operación</option>
                                <option value="2">Acumulado semanal</option>
                            </select>
                        </div>

                        <div class="input-group" v-if="product_form.type_generation_transmition_id === '2'">
                            <div class="input-group-text">Fecha</div>
                            <Litepicker
                                v-model="product_form.transaction_date"
                                :options="{
                            autoApply: true,
                            singleMode: true,
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
                            maxDate: current_date,
                            minDate: current_date.subtract(8, 'day')
                        }"
                                class="form-control"
                            />
                        </div>

                        <div class="input-group">
                            <div class="input-group-text">Cantidad</div>
                            <input type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.product_form.quantity.$error }"
                                   v-model.number="product_form.quantity"/>
                        </div>

                        <div class="input-group">
                            <div class="input-group-text">Precio</div>
                            <input type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.product_form.price.$error }"
                                   v-model.number="product_form.price"
                            />
                        </div>

                        <div class="input-group">
                            <div class="input-group-text">Retencion</div>
                            <input type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.product_form.retention.$error }"
                                   v-model.number="product_form.retention"
                            />

                            <Tippy tag="div"
                                   content="calcular retencion"
                                   class="input-group-text cursor-pointer"
                                   :options="{ placement: 'top' }"
                            >
                                <font-awesome-icon icon="square-root-variable"/>
                            </Tippy>

                        </div>

                        <div class="input-group">
                            <div class="input-group-text">UM</div>
                            <select class="form-control form-select"
                                    :class="{ 'border-danger': v$.product_form.measurement.$error }"
                                    v-model="product_form.measurement">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="A76">Galones</option>
                                <option value="GGR">Gramos</option>
                                <option value="KGM">Kilogramo</option>
                                <option value="LTR">Litros</option>
                                <option value="MTR">Metros</option>
                                <option value="T4">Millar</option>
                                <option value="94">Unidad</option>
                            </select>
                        </div>


                        <button class="btn btn-primary" @click="add_item(product_form)">
                            <font-awesome-icon icon="plus" class="mr-2"/>
                            Agregar Item
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <th class="whitespace-nowrap text-center">PRODUCTO</th>
                                <th class="whitespace-nowrap text-center">TIPO</th>
                                <th class="whitespace-nowrap text-center">TRANSMISIÓN</th>
                                <th class="whitespace-nowrap text-center">CANTIDAD</th>
                                <th class="whitespace-nowrap text-center">PRECIO</th>
                                <th class="whitespace-nowrap text-center">RETENCION</th>
                                <th class="whitespace-nowrap text-center">TOTAL ITEM</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="form.items.length > 0"
                                v-for="(item, key) in form.items" v-bind:key="key">
                                <td>{{ item.description }}</td>
                                <td>{{ item.type }}</td>
                                <td>
                                    {{
                                        item.type_generation_transmition_id === "1" ? 'Por operación' : 'Acumulado semanal'
                                    }}
                                </td>
                                <td class="text-right">{{ item.quantity }}</td>
                                <td class="text-right">{{ $h.formatCurrency(item.price) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(item.retention) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(item.price * item.quantity) }}</td>
                            </tr>
                            <tr v-else>
                                <td colspan="7" class="text-danger text-center font-bold">Aun no se agregan productos
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="w-1/4 ml-auto mt-2">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td class="font-bold">SUBTOTAL</td>
                                <td class="text-right">{{ $h.formatCurrency(bruto) }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">RETENCIONES</td>
                                <td class="text-right">{{ $h.formatCurrency(retention) }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">TOTAL</td>
                                <td class="text-right">{{ $h.formatCurrency(bruto - retention) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60 dark:border-dark-5">
                <button class="btn btn-primary mr-auto" @click="store(form)">
                    Crear Documento Soporte
                </button>
            </div>
        </div>

        <jet-dialog-modal :show="new_product_form.open" @close="close_new_product_modal" max-width=lg>
            <template #title>
                Registro de producto
            </template>

            <template #content>
                <div class="input-form mt-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Descripción
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.product_form.description.$error }"
                           v-model="new_product_form.description">

                    <template v-if="v$.new_product_form.description.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.new_product_form.description.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>
            </template>

            <template #footer>
                <button class="btn btn-secondary mr-2"
                        @click="close_new_product_modal">
                    Cancelar
                </button>
                <button class="btn btn-primary" @click="save_product(new_product_form)">
                    Guardar
                </button>
            </template>
        </jet-dialog-modal>


        <CreateProviderModal :open-modal="provider_modal" @close-modal="provider_modal = false"/>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";
import useVuelidate from '@vuelidate/core'
import {required, minValue, minLength, helpers} from '@/utils/i18n-validators'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import CreateProviderModal from "@/Pages/Applications/Components/CreateProviderModal.vue";

const {withAsync} = helpers
const CancelToken = axios.CancelToken;
let source;

export default {
    props: {
        entity: String
    },

    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        Link,
        Autocomplete,
        JetDialogModal,
        CreateProviderModal
    },

    validations() {
        return {
            form: {
                provider: {required},
                date: {required},
                payment_form: {required},
                items: {required, minLength: minLength(1)}
            },
            product_form: {
                id: {required},
                description: {required},
                price: {required, minValue: minValue(0)},
                quantity: {required, minValue: minValue(0)},
                retention: {required, minValue: minValue(0)},
                measurement: {required},
                type: {required},
                type_generation_transmition_id: {required}
            },
            new_product_form: {
                description: {
                    required,
                    isUnique: helpers.withMessage('Esta descripción de producto ya se encuentra registrada', withAsync(async (value) => {

                        if (value) {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            try {
                                return await axios.get(route('support-document.product.validate-description', [this.entity, value]), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                }).catch(err => {
                                    console.log(err)
                                })
                            } catch (error) {
                                if (axios.isCancel(error)) {
                                    console.log('Operation canceled by the user.');
                                    return true;
                                }
                                return false
                            }
                        } else {
                            return false
                        }
                    })),
                    $autoDirty: true
                }
            }
        }
    },

    data() {
        return {
            form: {
                provider: '',
                date: '',
                payment_form: '',
                notes: '',
                items: []
            },
            product_form: {
                id: '',
                description: '',
                price: 0,
                quantity: 0,
                retention: 0,
                measurement: '',
                type: '',
                type_generation_transmition_id: '',
                transaction_date: '',
            },
            new_product_form: {
                open: false,
                description: ''
            },

            provider_modal: false
        }
    },

    methods: {
        provider_info(obj) {
            this.form.provider = obj.nit
        },
        product_info(obj) {
            this.product_form.id = obj.id
            this.product_form.description = obj.description
        },
        add_item(form) {
            this.v$.product_form.$touch();
            if (!this.v$.product_form.$invalid) {
                this.form.items.push(form)
                this.product_form = {
                    id: '',
                    description: '',
                    price: 0,
                    quantity: 0,
                    retention: 0,
                    measurement: '',
                    type: '',
                    type_generation_transmition_id: '',
                    transaction_date: '',
                }
                this.v$.product_form.$reset()
            }
        },
        close_new_product_modal() {
            this.new_product_form = {
                open: false,
                code: '',
                description: ''
            }
            this.v$.new_product_form.$reset()
        },

        save_product(form) {
            this.v$.new_product_form.$touch();
            if (!this.v$.new_product_form.$invalid) {
                axios.post(route('support-document.product.store', this.entity), form).then(resp => {
                    this.close_new_product_modal()
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Producto creado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },
        store(form) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                axios.post(route('support-document.store', this.entity), form).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Documento creado con éxito, recuerde subirlo a la DIAN",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.$inertia.visit(route('support-document.index', this.entity));
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        validateNIT() {
            window.open("https://muisca.dian.gov.co/WebRutMuisca/DefConsultaEstadoRUT.faces", "ventana1", "width=975,height=475,scrollbars=NO,resizable=NO");
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        bruto() {
            return this.form.items.reduce(function (a, c) {
                return a + Number((c.price * c.quantity) || 0)
            }, 0)
        },

        retention() {
            return this.form.items.reduce(function (a, c) {
                return a + Number((c.retention) || 0)
            }, 0)
        }
    }
}
</script>

<template>
    <jet-dialog-modal :show="openModal" @close="closeModal" max-width=5xl>
        <template #title>
            Registro de proveedor
        </template>

        <template #content>
            <div class="grid grid-cols-3 gap-4">
                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Tipo de documento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.document_type.$error }"
                            v-model="form.document_type">
                        <option value="">Seleccione…</option>
                        <option value="C">Cédula de ciudadanía</option>
                        <option value="E">Cédula de extranjería</option>
                        <option value="N">NIT</option>
                    </select>

                    <template v-if="v$.form.document_type.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.document_type.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        NIT / CC
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <div class="input-group mt-2">
                        <input type="number"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.nit.$error }"
                               v-model="form.nit">
                        <div id="input-group-price" class="input-group-text">{{ dv }}</div>
                    </div>

                    <template v-if="v$.form.nit.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.nit.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Tipo de regimen
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.regimen.$error }"
                            v-model="form.regimen">
                        <option value="">Seleccione…</option>
                        <option value="N">Persona natural</option>
                        <option value="C">Regimen común</option>
                        <option value="S">Regimen simplificado</option>
                    </select>

                    <template v-if="v$.form.regimen.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.regimen.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Gran contribuyente
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.great_contributor.$error }"
                            v-model="form.great_contributor">
                        <option value="">Seleccione…</option>
                        <option :value="true">SI</option>
                        <option :value="false">NO</option>
                    </select>

                    <template v-if="v$.form.great_contributor.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.great_contributor.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Autorretenedor
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.autoretenedor.$error }"
                            v-model="form.autoretenedor">
                        <option value="">Seleccione…</option>
                        <option :value="true">SI</option>
                        <option :value="false">NO</option>
                    </select>

                    <template v-if="v$.form.autoretenedor.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.autoretenedor.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Nombres
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.names.$error }"
                           v-model="form.names"
                           v-uppercase>

                    <template v-if="v$.form.names.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.names.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Apellidos
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.last_names.$error }"
                           v-model="form.last_names"
                           v-uppercase>

                    <template v-if="v$.form.last_names.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.last_names.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Pais
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.country.$error }"
                            v-model="form.country"
                            @change="getDepartments(form.country)">
                        <option value="">Seleccione…</option>
                        <option v-for="country in countries" :value="country.pais">{{ country.descripcion }}</option>
                    </select>

                    <template v-if="v$.form.country.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.country.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Departamento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.department.$error }"
                            v-model="form.department"
                            @change="getCities(form.country, form.department)">
                        <option value="">Seleccione…</option>
                        <option v-for="department in departments" :value="department.departamento">{{ department.descripcion }}</option>
                    </select>

                    <template v-if="v$.form.department.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.department.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Ciudad
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-control"
                            :class="{ 'border-danger': v$.form.city.$error }"
                            v-model="form.city">
                        <option value="">Seleccione…</option>
                        <option v-for="city in cities" :value="city.ciudad">{{ city.descripcion }}</option>
                    </select>

                    <template v-if="v$.form.city.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.city.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Dirección
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.address.$error }"
                           v-model="form.address"
                           v-uppercase>

                    <template v-if="v$.form.address.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.address.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Teléfono 1
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="number"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.phone_1.$error }"
                           v-model="form.phone_1">

                    <template v-if="v$.form.phone_1.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.phone_1.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Teléfono 2
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="number"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.phone_2.$error }"
                           v-model="form.phone_2">

                    <template v-if="v$.form.phone_2.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.phone_2.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Celular
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="number"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.cellphone.$error }"
                           v-model="form.cellphone">

                    <template v-if="v$.form.cellphone.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.cellphone.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Correo electrónico
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="email"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.mail.$error }"
                           v-model="form.mail"
                           v-lowercase>

                    <template v-if="v$.form.mail.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.mail.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="input-form">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Persona de contacto
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.contact.$error }"
                           v-model="form.contact"
                           v-uppercase>

                    <template v-if="v$.form.contact.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.contact.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>

            </div>
        </template>

        <template #footer>
            <button class="btn btn-secondary mr-2"
                    @click="closeModal">
                Cancelar
            </button>
            <button class="btn btn-primary" @click="save(form)">
                Guardar
            </button>
        </template>
    </jet-dialog-modal>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {email, helpers, numeric, required} from '@/utils/i18n-validators'

const { withAsync } = helpers
const CancelToken = axios.CancelToken;
let source;

const uppercase = {
    beforeUpdate(el) {
        el.value = el.value.toUpperCase()
    },
}

const lowercase = {
    beforeUpdate(el) {
        el.value = el.value.toLowerCase()
    },
}
export default {
    directives: {
        uppercase,
        lowercase
    },

    props: {
        openModal: {
            type: Boolean,
            default: false
        }
    },

    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            form: {
                document_type: {
                    required
                },
                nit: {
                    required,
                    numeric,
                    isUnique: helpers.withMessage('Este proveedor ya esta registrado', withAsync(async (value) => {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if(value) {
                            try {
                                return await axios.get(route('custom.global.validate-provider-dms', value), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                return false
                            }
                        }
                        else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                great_contributor: {
                    required
                },
                autoretenedor: {
                    required
                },
                regimen: {
                    required
                },
                seller: {
                    required
                },
                names: {
                    required
                },
                last_names: {
                    required
                },
                country: {
                    required
                },
                department: {
                    required
                },
                city: {
                    required
                },
                address: {
                    required
                },
                phone_1: {
                    required,
                    numeric
                },
                phone_2: {
                    required,
                    numeric
                },
                cellphone: {
                    required,
                    numeric
                },
                mail: {
                    required,
                    email
                },
                contact: {
                    required
                },
                condicion: {
                    required
                }
            },
        }
    },

    data() {
        return {
            form: {
                nit: '',
                names: '',
                last_names: '',
                address: '',
                city: '',
                phone_1: '',
                phone_2: '',
                cellphone: '',
                document_type: '',
                country: '',
                great_contributor: '',
                autoretenedor: '',
                notes: '',
                mail: '',
                regimen: '',
                contact: '',
            },
            countries: [],
            departments: [],
            cities: []
        }
    },

    methods: {
        save(form) {
            this.v$.form.$touch()
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('custom.global.save-provider-dms'), form).then(() => {
                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Proveedor creado con éxito.',
                        confirmButtonText: 'Aceptar',
                    });
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error recuperando la información.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        closeModal() {
            this.$emit('closeModal')
            this.v$.form.$reset()
        },

        resetForm() {

        },

        getCountries(){
            axios.get(route('custom.global.get-countries-dms')).then(resp => {
                this.countries = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err)
            })
        },

        getDepartments(country){
            this.departments = []
            this.form.department = ''

            axios.get(route('custom.global.get-departments-dms', country)).then(resp => {
                this.departments = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err)
            })
        },

        getCities(country, department){
            this.cities = []
            this.form.city = ''

            axios.get(route('custom.global.get-cities-dms', [country, department])).then(resp => {
                this.cities = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err)
            })
        }
    },

    mounted() {
        this.getCountries()
    },

    computed: {
        dv() {
            return this.form.nit ? this.$h.calculeDV(this.form.nit) : 0;
        },
    },

}
</script>

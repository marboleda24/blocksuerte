<template>
    <div>
        <div class="box">
            <div class="flex flex-col lg:flex-row items-center p-5">
                <div class="lg:mr-1 items-center">
                    <font-awesome-icon :icon="['fas', 'map-location-dot']" size="3x"/>
                </div>
                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                    <a href="" class="font-medium">
                        Ubicación
                    </a>
                    <div class="text-slate-500 text-xs mt-0.5">
                        {{ `${country} - ${department} - ${city}` }}
                    </div>
                </div>
                <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                    <button class="btn btn-secondary" @click="isOpen = true">
                        <EditIcon class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="isOpen" @close="closeModal" max-width=lg>
            <template #title>
                Actualizar Ubicación
            </template>

            <template #content>
                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Pais
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.country.$error }"
                            v-model="form.country"
                            @change="departments_list">
                        <option :value="country.descripcion" v-for="country in countries">{{ country.descripcion }}</option>
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

                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Departamento
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.department.$error }"
                            v-model="form.department" @change="cities_list">
                        <option :value="department.descripcion" v-for="department in departments">{{ department.descripcion }}</option>
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

                <div class="input-form mb-4">
                    <label class="form-label w-full flex flex-col sm:flex-row">
                        Ciudad
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.city.$error }"
                            v-model="form.city">
                        <option :value="city.descripcion" v-for="city in cities">{{ city.descripcion }}</option>
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
                        Justificación
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                            Obligatorio
                        </span>
                    </label>

                    <textarea class="form-control resize-none"
                              :class="{ 'border-danger': v$.form.justify.$error }"
                              v-model="form.justify"
                              cols="30" rows="5"></textarea>

                    <template v-if="v$.form.justify.$error">
                        <ul class="mt-1">
                            <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                :key="index">
                                {{ error.$message }}
                            </li>
                        </ul>
                    </template>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button  @click.prevent="update(form)" type="submit"
                         class="btn btn-primary">
                    Actualizar
                </button>
            </template>
        </jet-dialog-modal>

    </div>

</template>

<script lang="jsx">
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import useVuelidate from '@vuelidate/core'
import {maxLength, minLength, required} from "@/utils/i18n-validators";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        city: String,
        department: String,
        country: String,
        customer_code: String
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            form: {
                country: {
                    required,
                },
                department: {
                    required
                },
                city: {
                    required
                },
                justify: {
                    required,
                    minLength: minLength(5),
                    maxLength: maxLength(255)
                }
            }
        }
    },

    data(){
        return {
            form: {
                customer_code: this.customer_code,
                country: this.country,
                country_code: '',
                department: this.department,
                department_code: '',
                city: this.city,
                city_code: '',
                justify: ''
            },
            isOpen: false,
            countries: [],
            departments: [],
            cities: []
        }
    },

    methods: {
        closeModal() {
            this.isOpen = false
            this.form = {
                customer_code: this.customer_code,
                country: this.country,
                department: this.department,
                city: this.city,
                justify: ''
            }
            this.v$.form.$reset()
        },

        update(form) {
            this.v$.form.$touch()
            if (!this.v$.form.$invalid) {
                axios.post(route('customer.update-information.location'), form).then(resp => {
                    this.$emit('success', {
                        country: resp.data.country,
                        department: resp.data.department,
                        city: resp.data.city
                    })

                    this.$swal({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Ubicación actualizada con éxito.',
                        confirmButtonText: 'Aceptar',
                        timerProgressBar: true,
                        timer: 6000
                    });

                    this.closeModal()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                        timerProgressBar: true,
                        timer: 6000
                    });
                    console.log(err)
                })
            }
        },

        countries_list(){
            axios.get(route('customer-transaction.global-data.countries')).then(resp => {
                this.countries = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 6000
                });
                console.log(err)
            })
        },

        departments_list(){
            this.cities = [];
            this.departments = [];
            axios.get(route('customer-transaction.global-data.departments', this.form.country)).then(resp => {
                this.departments = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 6000
                });
                console.log(err)
            })
        },

        cities_list(){
            this.cities = [];
            axios.get(route('customer-transaction.global-data.cities', [this.form.country, this.form.department])).then(resp => {
                this.cities = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 6000
                });
                console.log(err)
            })
        },

        location_codes(){
            this.form.country_code = this.countries.find(elem => elem.descripcion === this.country)?.pais
            this.form.department_code = this.departments.find(elem => elem.descripcion === this.department)?.departamento
            this.form.city_code = this.cities.find(elem => elem.descripcion === this.city)?.ciudad
        }
    },

    mounted() {
        this.countries_list()
        this.departments_list()
        this.cities_list()
        this.location_codes()
    },

    watch: {
        'form.country': function () {
            this.form.country_code = this.countries.find(elem => elem.descripcion === this.form.country).pais
        },

        'form.department': function () {
            this.form.department_code = this.departments.find(elem => elem.descripcion === this.form.department).departamento
        },

        'form.city': function () {
            this.form.city_code = this.cities.find(elem => elem.descripcion === this.form.city).ciudad
        }
    }
}
</script>

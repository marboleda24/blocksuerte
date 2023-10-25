<template>
    <div>
        <Head title="Relación OP OV"/>

        <portal to="application-title">
            Relación OP OV
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label for="">Orden de venta inicial</label>
                        <input type="number" class="form-control" v-model="form.start">
                    </div>

                    <div>
                        <label for="">Orden de venta final</label>
                        <input type="number" class="form-control" v-model="form.end">
                    </div>

                    <button class="btn btn-primary" @click="search">
                        Consultar
                    </button>
                </div>
            </div>
        </div>

        <div class="box mt-5" v-if="Object.keys(form.items).length > 0">
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm" v-for="(elem, key) in form.items">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    OV: {{ key }}
                                </th>

                                <th colspan="4">
                                    CLIENTE: {{ elem[0].customer }}
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>PRODUCTO</th>
                                <th>ITEM</th>
                                <th>COMENTARIOS</th>
                                <th>OC</th>
                                <th>CANTIDAD</th>
                                <th>STOCK</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in elem">
                                <td class="w-10">
                                    <input type="checkbox" class="form-check-input" :value="item" v-model="selected">
                                </td>
                                <td class="w-2/6">
                                    {{ [item.code, item.description].join(' – ') }}
                                </td>
                                <td class="text-center w-1/6">
                                    {{ item.item }}
                                </td>
                                <td class="w-1/6">
                                    {{ item.comment }}
                                </td>
                                <td class="w-1/6">
                                    {{ item.oc }}
                                </td>
                                <td class="text-right">{{ item.quantity }}</td>
                                <td class="text-right">{{ item.stock }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex flex-col items-center p-5 border-t sm:flex-row border-slate-200/60">
                <button class="btn btn-primary" @click="groupBy(selected)">
                    Agrupar productos
                </button>
            </div>
        </div>

        <div class="box mt-5">
            <div class="flex flex-col items-center p-5 border-b sm:flex-row border-slate-200/60">
                <h2 class="mr-auto text-base font-medium">Grupos</h2>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm" v-for="(elem, key) in groups" v-if="groups.length > 0">
                        <thead>
                            <tr class="bg-green-200 dark:bg-green-800">
                                <th colspan="3">
                                    <button class="btn btn-danger btn-sm mr-2" @click="removeGroup(key)">
                                        <font-awesome-icon icon="trash-can"/>
                                    </button>
                                    {{ `GRUPO ${key+1}` }}
                                </th>
                                <th colspan="3">{{ `OP ${elem.op}` }}</th>
                            </tr>
                            <tr>
                                <th>PRODUCTO</th>
                                <th>ITEM</th>
                                <th>COMENTARIOS</th>
                                <th>OC</th>
                                <th>CANTIDAD</th>
                                <th>STOCK</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in elem.items">
                                <td class="w-2/6">
                                    {{ [item.code, item.description].join(' – ') }}
                                </td>
                                <td class="text-center w-1/6">
                                    {{ item.item }}
                                </td>
                                <td class="w-1/6">
                                    {{ item.comment }}
                                </td>
                                <td class="w-1/6">
                                    {{ item.oc }}
                                </td>
                                <td class="text-right">{{ item.quantity }}</td>
                                <td class="text-right">{{ item.stock }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-danger-soft show flex items-center" v-else
                         role="alert">
                        <AlertTriangleIcon class="w-6 h-6 mr-2"/>
                        Aun no se agrega ningún grupo…
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center p-5 border-t sm:flex-row border-slate-200/60">
                <button class="btn btn-primary" @click="save">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {helpers, maxValue, minLength, minValue, numeric, required} from "@/utils/i18n-validators";
import {Head} from "@inertiajs/vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
        Head
    },

    setup() {
        return {v$: useVuelidate()}
    },

    validations() {
        return {
            form: {
                start: {
                    required,
                    numeric,
                    maxValue: maxValue(this.form.end)
                },
                end: {
                    required,
                    numeric,
                    minValue: minValue(this.form.start)
                },
                items: {
                    required,
                    minLength: minLength(1),
                    $each: helpers.forEach({
                        ov: {required},
                        item: {required},
                    })
                }
            },
            groups: {
                required,
                minLength: minLength(1)
            }
        }
    },

    data() {
        return {
            form: {
                start: '',
                end: '',
                items: []
            },

            selected: [],
            groups: [],
        }
    },

    methods: {
        save() {
            this.v$.groups.$touch();
            if (this.v$.groups.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
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

                axios.post(route('op-ov-relationship.store'), this.groups).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Registros guardados con éxito',
                        confirmButtonText: 'Aceptar'
                    });

                    this.resetForm()
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

        search() {
            this.v$.form.start.$touch();
            if (this.v$.form.start.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada',
                    confirmButtonText: 'Aceptar'
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

                axios.get(route('op-ov-relationship.search'), {
                    params: {
                        start: this.form.start,
                        end: this.form.end,
                    }
                }).then(resp => {
                    this.form.items = resp.data
                    this.$swal.close()
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

        groupBy(group) {
            if (group.length === 0){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Debes seleccionar al menos un item',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                this.$swal({
                    icon: 'info',
                    title: 'Ingresa la(s) orden(es) de compra para estos items',
                    text: `Por favor, ingrese la(s) orden(es) de compra para los items seleccionados`,
                    input: 'number',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Agrupar',
                    inputValidator: (inputValue) => {
                        const regex = new RegExp(/^[0-9]{8}$/)
                        if (!inputValue){
                            return 'Este campo es obligatorio'
                        }else if (!regex.test(inputValue)){
                            return 'La OP debe de ser un numero de 8 caracteres'
                        }
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        let currentGroup = {
                            items: group,
                            op: inputValue.value
                        }
                        this.groups.push(currentGroup)
                        this.selected = []
                    }
                })
            }
        },

        removeGroup(key){
            this.groups.splice(key, 1);
        },

        resetForm(){
            this.form = {
                start: '',
                end: '',
                items: []
            }

            this.selected = []
            this.groups = []

            this.v$.form.$reset()
            this.v$.groups.$reset()
        }
    }
}
</script>

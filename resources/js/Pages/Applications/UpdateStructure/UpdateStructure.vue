<template>
    <div>
        <Head title="Actualizacion masiva de estructuras"/>

        <portal to="application-title">
            Actualizacion masiva de estructuras
        </portal>

        <div class="box mt-5">
            <div class="grid grid-cols-2 gap-5 border-b p-5">
                <div>
                    <label for="">Búsqueda</label>
                    <input type="text" class="form-control" v-model="search.query">
                </div>

                <button class="btn btn-primary  h-full w-full " @click="searchProducts">
                    Buscar
                </button>
            </div>

            <div class="p-5 max-h-96 overflow-y-auto">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">
                            <input type="checkbox" class="form-check-input" v-model="selectAll">
                        </th>
                        <th>REFERENCIA</th>
                        <th>PRODUCTO</th>
                        <th colspan="2">COMPONENTE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(element, key) in search.result">
                        <td class="text-center">
                            <input type="checkbox"
                                   v-model="edit.selected"
                                   :value="key"
                                   class="form-check-input">
                        </td>
                        <td>
                            {{ key }}
                        </td>
                        <td>
                            {{ element[0].DESCRIPTION }}
                        </td>
                        <td>
                            <table class="table table-sm table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="2">COMPONENTE</th>
                                    <th>CANTIDAD</th>
                                    <th>FECHA EFECTIVIDAD</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in element">
                                    <td>{{ item.COMPONENT }}</td>
                                    <td>{{ item.COMPONENT_DESCRIPTION }}</td>
                                    <td>{{ parseInt(item.QUANTITY) }}</td>
                                    <td>{{ item.DATE }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box mt-5">
            <div class="flex flex-col items-right p-5 border-b border-slate-200/60">
                <div class="grid grid-cols-4 gap-5 w-3/5 ml-auto">
                    <div>
                        <autocomplete
                            :url="route('update-structure.search', 'component')"
                            show-field="COMPONENT_DESCRIPTION"
                            ref="product_input"
                            @selected-value="selectedProduct"
                        />

                        <template v-if="v$.form.component.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.component.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <input type="number" min="1" max="10"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.quantity.$error }"
                               v-model="form.quantity">

                        <template v-if="v$.form.quantity.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.quantity.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <select class="form-select"
                                :class="{ 'border-danger': v$.form.type.$error }"
                                v-model="form.type">
                            <option value="">Seleccione…</option>
                            <option value="add">Agregar</option>
                            <option value="remove">Quitar</option>
                        </select>

                        <template v-if="v$.form.type.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.type.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <button class="btn btn-primary" @click="addItem(form)">
                        Agregar
                    </button>
                </div>
            </div>
            <div class="p-5">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>COMPONENTE</th>
                        <th>CANTIDAD</th>
                        <th>OPERACION</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in edit.components">
                        <td>{{ `${row.component.COMPONENT} – ${row.component.COMPONENT_DESCRIPTION}` }}</td>
                        <td>{{ row.quantity }}</td>
                        <td class="text-center">
                            <span class="badge badge-rounded"
                                  :class="{'badge-success' : row.type === 'add', 'badge-danger' : row.type === 'remove'}">
                                {{ row.type === 'add' ? 'Agregar' : 'Quitar' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" @click="removeItem(index)">
                                <font-awesome-icon icon="trash-can" class="mr-2"/>
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-left p-5 border-t border-slate-200/60">
                <button class="btn btn-primary w-1/3 mr-auto" @click="store(edit)">
                    Editar Productos
                </button>
            </div>
        </div>

    </div>
</template>
<script lang="jsx">
import {Link, Head} from "@inertiajs/vue3";
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import useVuelidate from '@vuelidate/core'
import {required, numeric, maxValue, minValue, minLength} from '@/utils/i18n-validators'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        FontAwesomeIcon,
        Link,
        Head,
        Autocomplete,
    },

    validations() {
        return {
            form: {
                component: {required},
                type: {required},
                quantity: {
                    required,
                    numeric,
                    minValue: minValue(1),
                    maxValue: maxValue(10)
                }
            },

            edit: {
                selected: {
                    required,
                    minLength: minLength(1)
                },
                components: {
                    minLength: minLength(1)
                }
            }
        }
    },

    data() {
        return {
            search: {
                query: "",
                result: [],
            },
            edit: {
                selected: [],
                components: [],
            },
            form: {
                component: {},
                type: '',
                quantity: 0
            },
            selectAll: false
        }
    },
    methods: {
        searchProducts() {
            if (this.search.query) {
                axios.get(route('update-structure.search'), {
                    params: {
                        q: this.search.query
                    }
                }).then(resp => {
                    this.search.result = resp.data
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data.message,
                        confirmButtonText: 'Aceptar',
                    })
                })

            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'la consulta no debe estar vacía ',
                    confirmButtonText: 'Aceptar',
                })
            }
        },

        selectedProduct(obj) {
            this.form.component = obj
        },

        addItem(form) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    confirmButtonText: 'Aceptar',
                })
            } else {
                this.edit.components.push(form)
                this.resetForm()
            }
        },

        removeItem(idx) {
            this.edit.components.splice(idx, 1);
        },

        resetForm() {
            this.form = {
                component: {},
                type: '',
                quantity: 0
            }
            this.$refs.product_input?.showInput(true)
            this.v$.form.$reset()
        },

        store(edit) {
            this.v$.edit.$touch();
            if (this.v$.edit.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    confirmButtonText: 'Aceptar',

                })
            } else {
                this.$swal({
                    icon: 'warning',
                    title: '¿Esta seguro que desea continuar?',
                    text: "¡Esta apunto de reemplazar información, esta acción no es reversible!",
                    showCancelButton: true,
                    confirmButtonText: '¡Si, continuar!'
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

                        axios.post(route('update-structure.update'), edit).then(resp => {
                            this.edit = {
                                selected: [],
                                components: [],
                            }
                            this.v$.edit.$reset()

                            this.$swal({
                                title: '¡Éxito!',
                                text: "¡Estructuras actualizadas con éxito!",
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            })
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: err.response.data,
                                confirmButtonText: 'Aceptar',
                            })
                        })
                    }
                })

            }
        }
    },

    watch: {
        selectAll() {
            if (this.selectAll) {
                this.edit.selected = Object.keys(this.search.result)
            } else if (!this.selectAll && this.edit.selected.length === Object.keys(this.search.result).length) {
                this.edit.selected = []
            }
        },
    }
}

</script>

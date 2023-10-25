<template>
    <div>
        <Head title="Nuevo Registro Materia Prima"/>

        <portal to="application-title">
            Nuevo Registro Materia Prima
        </portal>

        <portal to="actions">
            <Link :href="route('raw-material.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>


        <div class="intro-y box">
            <div class="p-5">
                <div class="grid grid-cols-4 gap-3">
                    <div class="mt-2">
                        <label>
                            <span class="text-justify-center">ORDEN DE COMPRA</span>
                        </label>

                        <autocomplete
                            url="/raw/search-order"
                            show-field="ORDNUM_16"
                            @selected-value="select_item"
                        />
                    </div>
                    <div class="mt-2">
                        <label>
                            <span>NOMBRE DE PROVEEDOR</span>
                        </label>
                        <input type="text" v-model="form.nombre_prov" disabled class="form-control">
                    </div>
                    <div class="mt-2">
                        <label>
                            <span>RECIBE</span>
                        </label>
                        <select v-model="form.received_by" class="form-select"
                                :class="{ 'border-danger': v$.form.received_by.$error }">
                            <option value=""> Seleccione...</option>
                            <option v-for="user in users" v-bind:key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>

                        <template v-if="v$.form.received_by.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.received_by.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
                <table class="table table-bordered mt-5">
                    <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="">PIEZA</th>
                        <th class="">ITEM</th>
                        <th class="">ID ENTRADA</th>
                        <th class="">FECHA</th>
                        <th class="">MATERIAL</th>
                        <th class="">CANTIDAD</th>
                        <th class="">DIMENSION</th>
                        <th class="">APARIENCIA</th>
                        <th class="">PESO</th>
                        <th class="">OBSERVACIONES/NUMERO ROLLOS</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in form.items" v-bind:key="item.IDEntrada">
                        <td class="border-b dark:border-dark-5">{{ item.pieza }}</td>
                        <td class="border-b dark:border-dark-5">{{ item.item }}</td>
                        <td class="border-b dark:border-dark-5">{{ item.id_entrada }}</td>
                        <td class="border-b dark:border-dark-5">{{ item.fecha }}</td>
                        <td class="border-b dark:border-dark-5">{{ item.material }}</td>
                        <td class="border-b dark:border-dark-5">{{ parseFloat(item.cantidad).toFixed(2) }}</td>
                        <td class="border-b dark:border-dark-5">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" class="form-check-input" v-model="item.dimension">
                            </div>
                        </td>
                        <td class="border-b dark:border-dark-5">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" class="form-check-input" v-model="item.apariencia">
                            </div>
                        </td>
                        <td class="border-b dark:border-dark-5">
                            <div class="form-check form-switch flex justify-center">
                                <input type="checkbox" class="form-check-input" v-model="item.peso">
                            </div>
                        </td>
                        <td class="border-b dark:border-dark-5">
                            <input type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.items.$each.$response.$errors[index].length }"
                                   v-model="item.observaciones">

                            <template v-if="v$.form.items.$each.$response.$errors[index].length">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.items.$each.$response.$errors[index].observaciones"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="items-center text-center p-5 border-t border-gray-200 dark:border-dark-5">
                <button class="btn btn-primary uppercase" @click="save(form)">
                    <font-awesome-icon icon="copy" class="mr-2"/>
                    Guardar / Editar
                </button>
            </div>
        </div>
    </div>
</template>
<script lang="jsx">
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, maxLength, helpers} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        users: Array,
    },

    components: {
        Autocomplete,
        Head,
        Link
    },

    data() {
        return {
            form: {
                orden_compra: null,
                fecha: null,
                nombre_prov: null,
                received_by: null,
                items: [],
            }
        }
    },

    validations() {
        return {
            form: {
                received_by: {
                    required
                },
                items: {
                    $each: helpers.forEach({
                        observaciones: {
                            required,
                            maxLength: maxLength(250)
                        }
                    })
                }
            }
        }
    },

    methods: {
        select_item(obj) {
            this.form.orden_compra = obj.ORDNUM_16.trim();
            this.form.nombre_prov = obj.vendor.COMNAM_08;

            axios.get(route('raw-material.detail-order'), {
                params: {
                    order: this.form.orden_compra
                }
            }).then(resp => {
                this.form.items = resp.data.map(function (x) {
                    return {
                        oc: x.OC,
                        pieza: x.REFERENCIA,
                        item: x.ENTRADA,
                        id_entrada: x.IDEntrada,
                        lote: x.LOTE,
                        fecha: x.TNXDTE_55,
                        material: x.PRODUCTO,
                        cantidad: x.CANTIDAD,
                        dimension: x.registro !== null && x.registro.dimension === '1' ,
                        apariencia: x.registro !== null && x.registro.appearance === '1',
                        peso: x.registro !== null && x.registro.weight === '1',
                        observaciones: x.registro !== null ? x.registro.observation : ''
                    }
                })
                this.form.received_by = resp.data[0].registro !== null ? resp.data[0].registro.received_by : ''
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(error.data);
            })
        },

        save(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                axios.post(route('raw-material.store'), data).then(resp => {
                    this.$swal({
                        title: '¡Orden Guardada!',
                        text: "Guardado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                    console.log(resp.data);
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error guardando la información.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(error.data);
                })
            }
        }
    },

}
</script>

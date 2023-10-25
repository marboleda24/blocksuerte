<template>
    <div>
        <Head :title="`Editar remisión ${remission.consecutive}`"/>

        <portal to="application-title">
            Editar remisión {{ remission.consecutive }}
        </portal>

        <portal to="actions">
            <Link :href="route('point-of-sale-remission.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="grid grid-cols-3 gap-5">
                <div class="box p-5">
                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                        <div>
                            <div class="text-slate-500">Consecutivo</div>
                            <div class="mt-1">{{ remission.consecutive }}</div>
                        </div>
                        <font-awesome-icon icon="hashtag" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>
                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                        <div>
                            <div class="text-slate-500">Pedido de origen</div>
                            <div class="mt-1">{{ remission.order.consecutive }}</div>
                        </div>
                        <font-awesome-icon icon="hashtag" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>
                </div>

                <div class="box p-5">
                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                        <div>
                            <div class="text-slate-500">Punto de venta</div>
                            <div class="mt-1">{{ remission.location }}</div>
                        </div>
                        <font-awesome-icon icon="location-dot" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>
                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                        <div>
                            <div class="text-slate-500">Estado</div>
                            <div class="mt-1">
                                <span class="text-red-600 font-bold"
                                      v-if="remission.state === 'pending'">
                                    Pendiente
                                </span>
                                <span class="text-yellow-600 font-bold"
                                      v-if="remission.state === 'transit'">
                                    En transito
                                </span>
                                <span class="text-green-600 font-bold"
                                      v-if="remission.state === 'finish'">
                                    Mercancía recibida
                                </span>
                            </div>
                        </div>
                        <font-awesome-icon icon="truck" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>
                </div>

                <div class="box p-5">
                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                        <div>
                            <div class="text-slate-500">Creado por</div>
                            <div class="mt-1">{{ remission.user.name }}</div>
                        </div>
                        <font-awesome-icon icon="user" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>

                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 py-5">
                        <div>
                            <div class="text-slate-500">Creado el</div>
                            <div class="mt-1">{{ remission.created_at }}</div>
                        </div>
                        <font-awesome-icon icon="clock" class="w-4 h-4 text-slate-500 ml-auto"/>
                    </div>
                </div>
            </div>

            <div class="box mt-5">
                <div class="p-5">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">PRODUCTO</th>
                            <th class="whitespace-nowrap">NOTAS</th>
                            <th class="whitespace-nowrap">BODEGA DE ORIGEN</th>
                            <th class="whitespace-nowrap">UNIDAD DE MEDIDA</th>
                            <th class="whitespace-nowrap">LOTES</th>
                            <th class="whitespace-nowrap">CANTIDAD</th>
                            <th class="whitespace-nowrap">PRECIO</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) in remission.detail">
                            <td>{{ `${item.product} - ${item.description.trim()}` }}</td>
                            <td>{{ item.notes }}</td>
                            <td>
                                <TomSelect class="w-full"
                                           v-model.trim="item.warehouse"
                                           :class="{ 'border-danger': v$.form.detail.$each.$response.$data[key].warehouse.$error }">
                                    <option value="">Seleccione…</option>
                                    <option v-for="warehouse in warehouses" :value="warehouse.STK_05.trim()">
                                        {{
                                            `${warehouse.STK_05.trim()} ${warehouse.DESC_05.trim().length > 0 ? ' - ' + warehouse.DESC_05.trim() : ''}`
                                        }}
                                    </option>
                                </TomSelect>
                            </td>
                            <td>
                                <select class="form-select"
                                        v-model="item.unit_measurement" disabled>
                                    <option value="units">Unidad</option>
                                    <option value="thousands">Millar</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary"
                                        :disabled="!item.required_lot"
                                        @click="registryLots(key)">
                                    Registrar ({{ item.lots.length }})
                                </button>
                            </td>
                            <td>
                                <input type="number"
                                       class="form-control"
                                       :class="{ 'border-danger': v$.form.detail.$each.$response.$data[key].quantity.$error }"
                                       v-model.number="item.quantity">
                            </td>
                            <td class="text-right">{{ $h.formatCurrency(item.price) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <button class="btn btn-primary mt-5 mr-auto" @click="save(form, 'transit')">
                Actualizar
            </button>

            <jet-dialog-modal :show="lotForm.isOpen" max-width="3xl">
                <template #title v-if="lotForm.isOpen">
                    Registro de lotes {{ form.detail[lotForm.idx].description }}
                </template>

                <template #content v-if="lotForm.isOpen">
                    <div class="my-4 grid grid-cols-3 gap-4">
                        <div>
                            <label>Lote</label>
                            <select class="form-select"
                                    :class="{ 'border-danger': v$.lotForm.lot.name.$error }"
                                    v-model="lotForm.lot.name">
                                <option value="">seleccione…</option>
                                <option v-for="lot in lots"
                                        :value="lot.LOTNUM_68">
                                    {{ `${lot.LOTNUM_68} – CANT:${lot.QTYOH_68}` }}
                                </option>
                            </select>

                            <template v-if="v$.lotForm.lot.name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.lotForm.lot.name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div>
                            <label>Cantidad</label>
                            <input type="number"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.lotForm.lot.quantity.$error }"
                                   v-model.number="lotForm.lot.quantity">

                            <template v-if="v$.lotForm.lot.quantity.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.lotForm.lot.quantity.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <button class="btn btn-secondary"
                                @click="pushLot(lotForm.lot, lotForm.idx)">
                            <font-awesome-icon icon="plus" class="mr-2"/>
                            Agregar lote
                        </button>
                    </div>

                    <table class="table table-sm shadow-lg overflow-hidden border-b border-gray-400 rounded-lg">
                        <thead class="table-dark">
                        <tr>
                            <th class="border px-4 py-2">Lote</th>
                            <th class="border px-4 py-2">Cantidad</th>
                            <th class="border px-4 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(row, index) in form.detail[lotForm.idx]?.lots">
                            <td class="border px-4 py-2">{{ row.name }}</td>
                            <td class="border px-4 py-2">{{ row.quantity }}</td>
                            <td class="border px-4 py-2 text-center">
                                <button class="btn btn-secondary" @click="removeLot(lotForm.idx, index)">
                                    <font-awesome-icon :icon="['far', 'trash-alt']"/>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeRegistryLots()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {minValue, numeric, required, helpers, requiredIf} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        remission: Object,
        warehouses: Array
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    validations() {
        return {
            form: {
                detail: {
                    $each: helpers.forEach({
                        warehouse: {
                            required
                        },
                        quantity: {
                            required,
                            numeric,
                            minValue: minValue(1)
                        },
                    })
                }
            },
            lotForm: {
                lot: {
                    name: {required},
                    quantity: {
                        required,
                        numeric,
                        minValue: minValue(1)
                    }
                }
            }
        }
    },

    data() {
        return {
            form: this.remission,
            lotForm: {
                isOpen: false,
                idx: null,
                lot: {
                    name: '',
                    quantity: ''
                }
            },
            lots: [],
        }
    },

    methods: {
        registryLots(idx){
            if (!this.form.detail[idx].warehouse){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Debes seleccionar la bodega de origen para poder registrar lotes',
                    confirmButtonText: 'Cerrar'
                });
                return;
            }

            if (this.lots === undefined){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'No hay mercancía disponible en la bodega seleccionada',
                    confirmButtonText: 'Cerrar'
                });
                return;
            }

            this.lotForm = {
                isOpen: true,
                idx: idx,
                lot: {
                    name: '',
                    quantity: ''
                }
            }
        },

        closeRegistryLots(){
            this.lotForm = {
                isOpen: false,
                idx: null,
                lot: {
                    name: '',
                    quantity: ''
                }
            }
            this.v$.lotForm.lot.$reset();
        },

        removeLot(idx, i){
            this.form.detail[idx].lots.splice(i, 1)
            //this.updateLot()
        },

        updateLot() {
            this.form.detail[this.lotForm.idx].quantity = this.form.detail[this.lotForm.idx].lots.reduce(function (a, c) {
                return a + c.quantity
            }, 0)
        },

        resetLotForm() {
            this.lotForm.lot = {
                name: '',
                quantity: ''
            }
        },

        pushLot(lot, idx){
            this.v$.lotForm.lot.$touch();
            if (this.v$.lotForm.lot.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {
                this.form.detail[idx].lots.push(lot)
                this.resetLotForm()
                this.v$.lotForm.lot.$reset()
            }
        },
        save(form) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información sea correcta',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {

                this.$swal({
                    icon: 'question',
                    title: '¿Actualizar Información?',
                    text: "La mercancía sera enviada a una bodega intermedia, una vez recibida en el punto de venta se actualizara la bodega de destino",
                    showCancelButton: true,
                    confirmButtonText: '¡Si, Enviar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Procesando solicitud…',
                            text: 'Este proceso puede tardar unos segundos.',
                        });

                        axios.put(route('point-of-sale-remission.update'), form).then(resp => {
                            this.$swal({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Remisión actualizada con éxito',
                                confirmButtonText: 'Cerrar'
                            });

                            this.$inertia.visit(route('point-of-sale-remission.index'));
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Cerrar'
                            });
                        })
                    }
                })
            }
        }
    },

    watch: {
        'lotForm.idx': function (){
            this.lots = this.form.detail[this.lotForm.idx]?.available_lots.filter(row => row.STK_68.trim() === this.form.detail[this.lotForm.idx].warehouse)
        }
    }
}
</script>

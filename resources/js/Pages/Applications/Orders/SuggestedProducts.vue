<template>
    <div>
        <Tippy
            tag="button"
            content="Productos sugeridos"
            class="btn btn-primary ml-2"
            @click.native="load()"
            :disabled="!customer_code"
        >
            <font-awesome-icon icon="cubes-stacked"/>
        </Tippy>

        <jet-dialog-modal :show="isOpen" @close="closeModal" max-width=5xl>
            <template #title>
                Productos sugeridos
            </template>

            <template #content>
                <div class="overflow-x-auto">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="uppercase">
                                <th class="whitespace-nowrap">Producto</th>
                                <th class="whitespace-nowrap">CPC</th>
                                <th class="whitespace-nowrap">Destino</th>
                                <th class="whitespace-nowrap">U/M</th>
                                <th class="whitespace-nowrap">Cantidad</th>
                                <th class="whitespace-nowrap">Precio</th>
                                <th class="whitespace-nowrap">Arte</th>
                                <th class="whitespace-nowrap">Marca</th>
                                <th class="whitespace-nowrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, key) in suggested" v-bind:key="key">
                                <td>{{ `${item.product} - ${item.description_product}` }}</td>
                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           v-model="item.customer_product_code">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" v-model="item.destiny">
                                        <option value="C">Bodega</option>
                                        <option value="P">Producción</option>
                                        <option value="D">Troqueles</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" v-model="item.unit_measurement">
                                        <option value="units">Unidad</option>
                                        <option value="thousands">Millar</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.suggested.$each.$response.$data[key].quantity.$error }"
                                           v-model.number="item.quantity">
                                </td>
                                <td>
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           :class="{ 'border-danger': v$.suggested.$each.$response.$data[key].price.$error }"
                                           v-model.number="item.price">
                                </td>
                                <td>{{ item.art }}</td>
                                <td>{{ item.brand }}</td>
                                <td class="text-center">
                                    <button class="btn btn-secondary btn-sm"
                                            @click="flushItem(key, item)">
                                        <font-awesome-icon icon="circle-plus"/>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import useVuelidate from '@vuelidate/core'
import {minValue, numeric, required, helpers} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        customer_code: String
    },

    components: {
        JetDialogModal
    },

    validations() {
        return {
            suggested: {
                $each: helpers.forEach({
                    product: {required},
                    destiny: {required},
                    type: {required},
                    unit_measurement: {required},
                    price: {
                        required,
                        minValue: minValue(0)
                    },
                    quantity: {
                        required,
                        numeric,
                        minValue: minValue(1)
                    }
                })
            }
        }
    },

    data(){
        return {
            suggested: [],
            isOpen: false
        }
    },

    methods: {
        openModal(){
            this.isOpen = true
        },

        closeModal(){
            this.isOpen = false
            this.suggested = []
        },

        load(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('orders.suggested-products', this.customer_code)).then(resp => {
                if (resp.data.length > 0){
                    this.suggested = resp.data
                    this.openModal()
                    this.$swal.close()
                }else {
                    this.$swal({
                        icon: 'info',
                        title: '¡No hay productos sugeridos!',
                        text: 'Lo sentimos, pero en este momento no podemos sugerirte productos para este cliente',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.error(err);
            })
        },
        flushItem(key, item){
            this.v$.suggested.$touch()
            if (!this.v$.suggested.$each.$response.$data[key].$invalid){
                this.$emit('AddItem', item)
                this.$swal({
                    icon: 'success',
                    title: 'Producto Agregado',
                    text: 'Producto agregado con éxito',
                    timer: 4000,
                    timerProgressBar: true,
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    }
}
</script>


<template>
    <div>
        <Head :title="customer_data.RAZON_SOCIAL"/>

        <portal to="application-title">
            {{ customer_data.RAZON_SOCIAL }}
        </portal>

        <portal to="actions">
            <Link :href="route('customers.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="px-5 pt-5 intro-y box">
                <div class="flex flex-col pb-5 -mx-5 lg:flex-row">
                    <div class="flex flex-1 justify-center items-center px-5 lg:justify-start">
                        <div class="relative flex-none w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 image-fit">
                            <img
                                alt="photo" class="rounded-full"
                                :src="'https://ui-avatars.com/api/?name='+encodeURI(customer_data.RAZON_SOCIAL)+'&color=7F9CF5&background=EBF4FF'">
                        </div>
                        <div class="ml-5">
                            <div class="text-lg font-medium truncate sm:whitespace-normal">
                                {{ customer_data.RAZON_SOCIAL }}
                            </div>
                            <div class="text-gray-600">
                                {{ `${customer_data.CODIGO_CLIENTE} - ${customer_data.NIT}` }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex-1 px-5 pt-5 mt-6 border-t border-r border-l border-gray-200 lg:mt-0 dark:text-gray-300 dark:border-dark-5 lg:border-t-0 lg:pt-0">
                        <div class="font-medium text-center lg:text-left lg:mt-3">
                            Detalles de contacto
                        </div>
                        <div class="flex flex-col justify-center items-center mt-4 lg:items-start">
                            <div class="flex items-center truncate sm:whitespace-normal">
                                <font-awesome-icon :icon="['far', 'envelope']" class="mr-2"/>
                                {{ customer_data.CORREO_CONTACTO }}
                            </div>
                            <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                                <font-awesome-icon icon="map-marker-alt" class="mr-2"/>
                                {{ customer_data.DIRECCION }}
                            </div>
                            <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                                <font-awesome-icon icon="phone" class="mr-2"/>
                                {{ customer_data.TEL1 }}
                            </div>
                            <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                                <font-awesome-icon :icon="['far', 'question-circle']" class="mr-2"/>

                                <div v-if="$gates.hasPermission('customer.change_state_customer')">
                                    <Tippy tag="span" content="Click para inactivar este cliente"
                                           class="cursor-pointer badge badge-success badge-rounded"
                                           :options="{ placement: 'right' }"
                                           @click.native="change_state_customer('inactive', customer_data.CODIGO_CLIENTE)"
                                           v-if="customer_data.ACTIVO === 'R'">
                                        ACTIVO
                                    </Tippy>

                                    <Tippy tag="span" content="Click para activar este cliente"
                                           class="cursor-pointer badge badge-danger badge-rounded"
                                           :options="{ placement: 'right' }"
                                           @click.native="change_state_customer('active', customer_data.CODIGO_CLIENTE)"
                                           v-else>
                                        INACTIVO
                                    </Tippy>
                                </div>
                                <div v-else>
                                    <span class="badge badge-success badge-rounded" v-if="customer_data.ACTIVO === 'R'">ACTIVO</span>
                                    <span class="badge badge-danger badge-rounded" v-else>ACTIVO</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex-1 px-5 pt-5 mt-6 border-t border-gray-200 lg:mt-0 lg:border-0 dark:border-dark-5 lg:pt-0">
                        <div class="font-medium text-center lg:text-left lg:mt-5"> Estadísticas</div>
                        <div class="flex justify-center items-center mt-2 lg:justify-start">
                            <div class="flex mr-2 w-20"> Pedidos EVPIU:
                                <span class="ml-3 font-medium text-gray-600">{{ orders_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t tab-content">
                    <div id="update" class="tab-pane active" role="tabpanel" aria-labelledby="update-tab">
                        <div class="grid grid-cols-4 gap-6 my-4">
                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Contacto</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.CONTACTO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('contact', customer_data.CONTACTO, 'Contacto')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Ciudad - Departamento - País</div>
                                    <div class="mt-1 text-gray-600">
                                        {{
                                            `${customer_data.CIUDAD} - ${customer_data.ESTADO} - ${customer_data.PAIS}`
                                        }}
                                        <font-awesome-icon :icon="['far', 'edit']" class="hidden ml-2 text-center"/>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono 1</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.TEL1 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone1', customer_data.TEL1, 'Teléfono 1')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono 2</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.TEL2 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone2', customer_data.TEL2, 'Teléfono 2')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Celular</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.CELULAR }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('cellphone', customer_data.CELULAR, 'Celular')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">E-mail Contacto</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.CORREO_CONTACTO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('contact_email', customer_data.CORREO_CONTACTO, 'E-mail Contacto')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">E-mail Facturación</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.CORREO_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('billing_email', customer_data.CORREO_FE, 'E-mail Facturación')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Correos Copia</div>
                                    <div class="mt-1 text-gray-600 flex flex-row">
                                        <div class="w-72 truncate">
                                            {{ customer_data.CORREOS_COPIA }}
                                        </div>

                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('copy_emails', customer_data.CORREOS_COPIA, 'Correos Copia')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Dirección</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.DIRECCION }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('address1', customer_data.DIRECCION, 'Dirección')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Dirección 2</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.DIRECCION2 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('address2', customer_data.DIRECCION2, 'Dirección 2')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Moneda</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.MONEDA }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('currency', customer_data.MONEDA, 'Moneda')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Tipo Cliente</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.TIPO_CLIENTE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('customer_type', customer_data.TIPO_CLIENTE, 'Tipo Cliente')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Gravado</div>
                                    <div class="mt-1 text-gray-600">
                                        <span v-if="customer_data.GRABADO === 'Y'"
                                              class="badge badge-success badge-rounded">
                                            Si
                                        </span>
                                        <span v-else class="badge badge-danger badge-rounded">
                                            No
                                        </span>
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('gravado', customer_data.GRABADO, 'Grabado')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Plazo de Pago</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.PLAZO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('paid_term', customer_data.CODIGO_PLAZO, 'PLazo de pago')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Porcentaje de Descuento</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ `${customer_data.DESCUENTO}%` }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('discount', customer_data.DESCUENTO, 'Porcentaje de descuento')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Vendedor</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.NOMBRE_VENDEDOR }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('seller', customer_data.VENDEDOR, 'Vendedor')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Responsable Facturación electrónica</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.RESPONSABLE_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('responsible_electronic_billing', customer_data.RESPONSABLE_FE, 'Responsable Facturación Electronica')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono Facturación Electrónica</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.TEL_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone_electronic_billing', customer_data.TEL_FE, 'Teléfono Facturación Electronica')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Código de Ciudad Exterior</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.CIUDAD_EXT }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('code_city_ext', customer_data.CIUDAD_EXT, 'Código ciudad exterior')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">RUT Entregado</div>
                                    <div class="mt-1 text-gray-600">
                                        <span v-if="customer_data.RUT  === '1' "
                                              class="badge badge-success badge-rounded">
                                            Si
                                        </span>
                                        <span v-else class="badge badge-danger badge-rounded">
                                            No
                                        </span>
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('rut', customer_data.RUT, 'RUT Entregado')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Gran Contribuyente</div>
                                    <div class="mt-1 text-gray-600">
                                        <span v-if="customer_data.GRAN_CONTRIBUYENTE  === '1' "
                                              class="badge badge-success badge-rounded">
                                            Si
                                        </span>
                                        <span v-else class="badge badge-danger badge-rounded">
                                            No
                                        </span>
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('great_contributor', customer_data.GRAN_CONTRIBUYENTE, 'Gran Contribuyente')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Responsable de IVA</div>
                                    <div class="mt-1 text-gray-600">
                                        <span v-if="customer_data.RESPONSABLE_IVA  === '1' "
                                              class="badge badge-success badge-rounded">
                                            Si
                                        </span>
                                        <span v-else class="badge badge-danger badge-rounded">
                                            No
                                        </span>
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('responsible_iva', customer_data.RESPONSABLE_IVA, 'Responsable de IVA')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col content-center sm:flex-row">
                                <div class="mr-auto">
                                    <div class="font-medium">Actividad Económica</div>
                                    <div class="mt-1 text-gray-600">
                                        {{ customer_data.GRUPO_ECONOMICO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('economic_group', customer_data.GRUPO_ECONOMICO, 'Actividad Económica')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="ml-2 text-center"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <update-data-modal :is-open="isOpenUpdateData" :form="form_field" :modal_data="customer" @close="closeModal"
                               @success_update=""/>
        </div>
    </div>
</template>

<script lang="jsx">

import UpdateDataModal from "@/Pages/Applications/SellerManagement/UpdateDataModal.vue";
import {Head, Link} from '@inertiajs/vue3'

export default {

    props: {
        customer: Object,
        orders_count: Number
   },

    components: {
        UpdateDataModal,
        Head,
        Link
    },

    data() {
        return {
            form: {
                invoices_date: '',
                credit_notes_date: ''
            },
            form_field: {
                field: '',
                value: '',
                justify: '',
                title: '',
                processing: false,
            },
            modal_data: null,

            isOpenUpdateData: false,

            customer_data: this.customer,

            customers_types: [],
            sellers: [],
            paid_terms: [],
            economic_groups: [],
            countries: [],
        }
    },

    methods: {
        closeModal() {
            this.isOpenUpdateData = false
            this.modal_data = null
            this.form_field = {
                field: '',
                value: '',
                justify: '',
                title: '',
                processing: false,
            }
        },

        construct_modal(field, value, title) {
            this.form_field = {
                field: field,
                value: value,
                justify: '',
                title: title,
                processing: false,
            }
            this.isOpenUpdateData = true
        },

        customer_load_data(customer) {
            this.customer_data = customer
        },

        change_state_customer(state, code) {
            this.$swal({
                title: state === 'active' ? '¿Activar Cliente?' : '¿Inactivar Cliente?',
                text: "¿Esta seguro que desea continuar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: state === 'active' ? 'Si, Activar' : 'Si, Inactivar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(route('seller-management.customers.change-state-customer'), {
                        state: state,
                        code: code
                    }).then(resp => {
                        this.customer_data = resp.data
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: 'Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                }
            });
        },

        freshInfo(obj) {
            this.customer_data = obj
        }
    },

}
</script>


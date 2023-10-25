<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                {{ customer_data.RAZON_SOCIAL }}
            </h2>
        </portal>

        <portal to="action_header">
            <inertia-link :href="route('seller-management.customers')" class="btn btn-primary ml-4">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </inertia-link>
        </portal>

        <div>
            <div class="intro-y box px-5 pt-5">
                <div class="flex flex-col lg:flex-row border-b border-gray-200 dark:border-dark-5 pb-5 -mx-5">
                    <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative"><img
                            alt="photo" class="rounded-full"
                            :src="'https://ui-avatars.com/api/?name='+encodeURI(customer_data.RAZON_SOCIAL)+'&color=7F9CF5&background=EBF4FF'">
                        </div>
                        <div class="ml-5">
                            <div class="truncate sm:whitespace-normal font-medium text-lg">
                                {{ customer_data.RAZON_SOCIAL }}
                            </div>
                            <div class="text-gray-600">
                                {{ `${customer_data.CODIGO_CLIENTE} - ${customer_data.NIT }` }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-6 lg:mt-0 flex-1 dark:text-gray-300 px-5 border-l border-r border-gray-200 dark:border-dark-5 border-t lg:border-t-0 pt-5 lg:pt-0">
                        <div class="font-medium text-center lg:text-left lg:mt-3">
                            Detalles de contacto
                        </div>
                        <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                            <div class="truncate sm:whitespace-normal flex items-center">
                                <font-awesome-icon :icon="['far', 'envelope']" class="mr-2"/>
                                {{ customer_data.CORREO_CONTACTO }}
                            </div>
                            <div class="truncate sm:whitespace-normal flex items-center mt-3">
                                <font-awesome-icon icon="map-marker-alt" class="mr-2"/>
                                {{ customer_data.DIRECCION }}
                            </div>
                            <div class="truncate sm:whitespace-normal flex items-center mt-3">
                                <font-awesome-icon icon="phone" class="mr-2"/>
                                {{ customer_data.TEL1 }}
                            </div>
                            <div class="truncate sm:whitespace-normal flex items-center mt-3">
                                <font-awesome-icon :icon="['far', 'question-circle']" class="mr-2"/>

                                <Tippy tag="span" content="Click para inactivar este cliente"
                                       class="badge badge-success badge-rounded cursor-pointer"
                                       :options="{ placement: 'right' }"
                                       @click.native="change_state_customer('inactive', customer_data.CODIGO_CLIENTE)"
                                       v-if="customer_data.ACTIVO === 'R'">
                                        ACTIVO
                                </Tippy>

                                <Tippy tag="span" content="Click para activar este cliente"
                                       class="badge badge-danger badge-rounded cursor-pointer"
                                       :options="{ placement: 'right' }"
                                       @click.native="change_state_customer('active', customer_data.CODIGO_CLIENTE)"
                                       v-else>
                                    INACTIVO
                                </Tippy>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-gray-200 dark:border-dark-5 pt-5 lg:pt-0">
                        <div class="font-medium text-center lg:text-left lg:mt-5"> Estadísticas</div>
                        <div class="flex items-center justify-center lg:justify-start mt-2">
                            <div class="mr-2 w-20 flex"> Pedidos:
                                <span class="ml-3 font-medium text-gray-600">{{ orders_count }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start">
                            <div class="mr-2 w-20 flex"> STP:
                                <span class="ml-3 font-medium text-gray-600">-2%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav nav-tabs flex-col sm:flex-row">
                    <a id="update-tab" data-toggle="tab" data-target="#update" href="javascript:;"
                       class="w-full  py-4 text-center flex justify-center items-center active" role="tab"
                       aria-controls="update" aria-selected="true">
                        Actualizacion de datos
                    </a>
                    <a id="invoices-tab" data-toggle="tab" data-target="#invoices" href="javascript:;"
                       class="w-full  py-4 text-center flex justify-center items-center" role="tab"
                       aria-controls="invoices" aria-selected="false">
                        Facturas
                    </a>
                    <a id="credit-notes-tab" data-toggle="tab" data-target="#credit-notes" href="javascript:;"
                       class="w-full  py-4 text-center flex justify-center items-center" role="tab"
                       aria-controls="credit-notes" aria-selected="false">
                        Notas Credito
                    </a>
                    <a id="open-ov-tab" data-toggle="tab" data-target="#open-ov" href="javascript:;"
                       class="w-full  py-4 text-center flex justify-center items-center" role="tab"
                       aria-controls="open-ov" aria-selected="false">
                        OV Abiertas
                    </a>

                    <a id="close-ov-tab" data-toggle="tab" data-target="#close-ov" href="javascript:;"
                       class="w-full  py-4 text-center flex justify-center items-center" role="tab"
                       aria-controls="close-ov" aria-selected="false">
                        OV Cerradas
                    </a>
                </div>
                <div class="tab-content p-5 border-t">
                    <div id="update" class="tab-pane active" role="tabpanel" aria-labelledby="update-tab">
                        <div class="grid grid-cols-4 gap-6 my-4">
                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Contacto</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CONTACTO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('contact', customer_data.CONTACTO, 'Contacto')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Ciudad - Departamento - Pais</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ `${customer_data.CIUDAD} - ${customer_data.ESTADO} - ${customer_data.PAIS}` }}
                                        <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2 hidden"/>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono 1</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.TEL1 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone1', customer_data.TEL1, 'Teléfono 1')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono 2</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.TEL2 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone2', customer_data.TEL2, 'Teléfono 2')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Celular</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CELULAR }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('cellphone', customer_data.CELULAR, 'Celular')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">E-mail Contacto</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CORREO_CONTACTO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('contact_email', customer_data.CORREO_CONTACTO, 'E-mail Contacto')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">E-mail Facturación</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CORREO_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('billing_email', customer_data.CORREO_FE, 'E-mail Facturación')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Correos Copia</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CORREOS_COPIA }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('copy_emails', customer_data.CORREOS_COPIA, 'Correos Copia')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Dirección</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.DIRECCION }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('address1', customer_data.DIRECCION, 'Dirección')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Dirección 2</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.DIRECCION2 }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('address2', customer_data.DIRECCION2, 'Dirección 2')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Moneda</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.MONEDA }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('currency', customer_data.MONEDA, 'Moneda')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Tipo Cliente</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.TIPO_CLIENTE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('customer_type', customer_data.TIPO_CLIENTE, 'Tipo Cliente')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Gravado</div>
                                    <div class="text-gray-600 mt-1">
                                        <span v-if="customer_data.GRABADO === 'Y'" class="badge badge-success badge-rounded">
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
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Plazo de Pago</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.PLAZO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('paid_term', customer_data.CODIGO_PLAZO, 'PLazo de pago')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Porcentaje de Descuento</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ `${customer_data.DESCUENTO}%` }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('discount', customer_data.DESCUENTO, 'Porcentaje de descuento')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Vendedor</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.NOMBRE_VENDEDOR }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('seller', customer_data.VENDEDOR, 'Vendedor')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Responsable Facturación Electronica</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.RESPONSABLE_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('responsible_electronic_billing', customer_data.RESPONSABLE_FE, 'Responsable Facturación Electronica')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Teléfono Facturación Electronica</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.TEL_FE }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('phone_electronic_billing', customer_data.TEL_FE, 'Teléfono Facturación Electronica')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Código de Ciudad Exterior</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.CIUDAD_EXT }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('code_city_ext', customer_data.CIUDAD_EXT, 'Código ciudad exterior')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">RUT Entregado</div>
                                    <div class="text-gray-600 mt-1">
                                        <span v-if="customer_data.RUT  === 'Y' " class="badge badge-success badge-rounded">
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
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Gran Contribuyente</div>
                                    <div class="text-gray-600 mt-1">
                                        <span v-if="customer_data.GRAN_CONTRIBUYENTE  === 'Y' " class="badge badge-success badge-rounded">
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
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Responsable de IVA</div>
                                    <div class="text-gray-600 mt-1">
                                        <span v-if="customer_data.RESPONSABLE_IVA  === 'Y' " class="badge badge-success badge-rounded">
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
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row content-center">
                                <div class="mr-auto">
                                    <div class="font-medium">Grupo Economico</div>
                                    <div class="text-gray-600 mt-1">
                                        {{ customer_data.GRUPO_ECONOMICO }}
                                        <Tippy tag="a" content="click para actualizar"
                                               class="cursor-pointer"
                                               :options="{ placement: 'right' }"
                                               :href="void(0)"
                                               @click.native="construct_modal('economic_group', customer_data.GRUPO_ECONOMICO, 'Grupo economico')">
                                            <font-awesome-icon :icon="['far', 'edit']" class="text-center ml-2"/>
                                        </Tippy>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="invoices" class="tab-pane" role="tabpanel" aria-labelledby="invoices-tab">
                        <div class="my-4">
                            <div class="flex flex-row">
                                <Tippy tag="a" content="Busque y seleccione una fecha o rango de fechas para cargar las facturas"
                                       class="w-1/3 ml-auto"
                                       :options="{ placement: 'left' }" href="javascript:;">
                                    <LitePicker
                                        v-model="form.invoices_date"
                                        :options="{
                                            autoApply: true,
                                            singleMode: false,
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
                                            maxDate: current_date
                                        }"
                                        class="form-control w-full"
                                    />
                                </Tippy>
                            </div>
                            <v-client-table :data="invoice_table.data" :columns="invoice_table.columns" :options="invoice_table.options" ref="invoices_table"
                                            class="overflow-y-auto">
                                <div class="text-center" slot="actions" slot-scope="props">
                                    <Tippy tag="button" content="Clic para ver el detalle de la factura"
                                           class="btn btn-secondary"
                                           @click.native="detail_invoice(props.row)"
                                           :options="{ placement: 'top' }" href="javascript:;">
                                        <font-awesome-icon icon="eye"/>
                                    </Tippy>
                                </div>
                            </v-client-table>
                        </div>
                    </div>

                    <div id="credit-notes" class="tab-pane" role="tabpanel" aria-labelledby="credit-notes-tab">
                        <div class="my-4">
                            <div class="flex flex-row">
                                <Tippy tag="a" content="Busque y seleccione una fecha o rango de fechas para cargar las notas credito"
                                       class="w-1/3 ml-auto"
                                       :options="{ placement: 'left' }" href="javascript:;">
                                    <LitePicker
                                        v-model="form.credit_notes_date"
                                        :options="{
                                                autoApply: true,
                                                singleMode: false,
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
                                                maxDate: current_date
                                            }"
                                        class="form-control w-full"
                                    />
                                </Tippy>
                            </div>
                            <v-client-table :data="credit_note_table.data" :columns="credit_note_table.columns" :options="credit_note_table.options" ref="credit_note_table"
                                            class="overflow-y-auto">
                                <div class="text-center" slot="actions" slot-scope="props">
                                    <Tippy tag="button" content="Clic para ver el detalle de la nota credito"
                                           class="btn btn-secondary w-1/3 ml-auto"
                                           @click.native="detail_credit_note(props.row)"
                                           :options="{ placement: 'top' }" href="javascript:;">
                                        <font-awesome-icon icon="eye"/>
                                    </Tippy>
                                </div>
                            </v-client-table>
                        </div>
                    </div>

                    <div id="open-ov" class="tab-pane" role="tabpanel" aria-labelledby="open-ov-tab">
                        <div class="my-4">
                            <div class="w-1/3 ml-auto">
                                <Tippy tag="button" content="Clic para cargar las ordenes abiertas"
                                       class="btn btn-secondary w-full"
                                       @click.native="load_open_ov()"
                                       :options="{ placement: 'left' }" href="javascript:;">
                                    <font-awesome-icon icon="spinner" class="mr-2"/>
                                    Cargar Datos
                                </Tippy>
                            </div>

                            <v-client-table :data="open_ov_table.data" :columns="open_ov_table.columns" :options="open_ov_table.options" ref="open_ov_table"
                                            class="overflow-y-auto">
                                <div class="text-center" slot="actions" slot-scope="props">
                                    <Tippy tag="button" content="Clic para ver el detalle"
                                           class="btn btn-secondary w-1/3 ml-auto"
                                           @click.native="detail_ov(props.row.OV)"
                                           :options="{ placement: 'top' }" href="javascript:;">
                                        <font-awesome-icon icon="eye"/>
                                    </Tippy>
                                </div>
                            </v-client-table>
                        </div>
                    </div>

                    <div id="close-ov" class="tab-pane" role="tabpanel" aria-labelledby="close-ov-tab">
                        OV cerradas
                    </div>
                </div>
            </div>

            <detail-invoice-modal :modal_data="modal_data" :is-open="isOpen" @close="closeModal"/>
            <detail-credit-note-modal :modal_data="modal_data" :is-open="isOpenCreditNote" @close="closeModal"/>
            <update-data-modal :is-open="isOpenUpdateData" :form="form_field" @close="closeModal" @success_update=""/>

        </div>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {defineComponent} from "vue";
import DetailInvoiceModal from "@/Pages/Applications/SellerManagement/DetailInvoiceModal.vue";
import DetailCreditNoteModal from "@/Pages/Applications/SellerManagement/DetailCreditNoteModal.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import UpdateDataModal from "@/Pages/Applications/SellerManagement/UpdateDataModal.vue";

export default defineComponent({
    metaInfo() {
        return {
            title: this.customer_data.RAZON_SOCIAL,
        };
    },

    props: {
        customer: Object,
        orders_count: Number,
        reason_reprocessing: Array,
    },

    components: {
        UpdateDataModal,
        DetailCreditNoteModal,
        DetailInvoiceModal,
        JetDialogModal
    },

    data(){
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
            invoice_table: {
                data: [],
                columns: [
                    'NUMERO',
                    'OC',
                    'BRUTO',
                    'DESCUENTO',
                    'RTEFTE',
                    'RTEIVA',
                    'RTEICA',
                    'SUBTOTAL',
                    'IVA',
                    'actions'
                ],
                options: {
                    headings: {
                        NUMERO: '#',
                        OC: 'OC',
                        BRUTO: 'BRUTO',
                        DESCUENTO: 'DESCUENTO',
                        RTEFTE: 'RTEFTE',
                        RTEIVA: 'RTEIVA',
                        RTEICA: 'RTEICA',
                        SUBTOTAL: 'SUBTOTAL',
                        IVA: 'IVA',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['NUMERO', 'OC', 'BRUTO', 'DESC', 'SUBTOTAL'],
                    templates: {
                        BRUTO: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.BRUTO)}
                            </div>
                        },
                        DESCUENTO: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.DESCUENTO)}
                            </div>
                        },
                        RTEFTE: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEFTE ?? 0 )}
                            </div>
                        },
                        RTEIVA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEIVA ?? 0)}
                            </div>
                        },
                        RTEICA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEICA ?? 0)}
                            </div>
                        },
                        SUBTOTAL: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.SUBTOTAL ?? 0)}
                            </div>
                        },
                        IVA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.IVA ?? 0)}
                            </div>
                        },
                    }
                },
            },
            credit_note_table: {
                data: [],
                columns: [
                    'NUMERO',
                    'OC',
                    'BRUTO',
                    'DESCUENTO',
                    'RTEFTE',
                    'RTEIVA',
                    'RTEICA',
                    'SUBTOTAL',
                    'IVA',
                    'actions'
                ],
                options: {
                    headings: {
                        NUMERO: '#',
                        OC: 'OC',
                        BRUTO: 'BRUTO',
                        DESCUENTO: 'DESCUENTO',
                        RTEFTE: 'RTEFTE',
                        RTEIVA: 'RTEIVA',
                        RTEICA: 'RTEICA',
                        SUBTOTAL: 'SUBTOTAL',
                        IVA: 'IVA',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['NUMERO', 'OC', 'BRUTO', 'DESC', 'SUBTOTAL'],
                    templates: {
                        BRUTO: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.BRUTO)}
                            </div>
                        },
                        DESCUENTO: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.DESCUENTO)}
                            </div>
                        },
                        RTEFTE: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEFTE ?? 0 )}
                            </div>
                        },
                        RTEIVA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEIVA ?? 0)}
                            </div>
                        },
                        RTEICA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.RTEICA ?? 0)}
                            </div>
                        },
                        SUBTOTAL: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.SUBTOTAL ?? 0)}
                            </div>
                        },
                        IVA: function (h, row) {
                            return <div class="text-right">
                                {this.$h.formatCurrency(row.IVA ?? 0)}
                            </div>
                        },
                    }
                },
            },
            open_ov_table: {
                data: [],
                columns: [
                    'OV',
                    'OC',
                    'FECHA_OV',
                    'actions'
                ],
                options: {
                    headings: {
                        OV: 'ORDEN DE VENTA',
                        OC: 'ORDEN DE COMPRA',
                        FECHA_OV: 'FECHA',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['OV', 'OC', 'FECHA_OV'],
                }


            },
            close_ov_table: {
                data: [],
                columns: [
                    'OV',
                    'OC',
                    'FECHA_OV',
                    'actions'
                ],
                options: {
                    headings: {
                        OV: 'ORDEN DE VENTA',
                        OC: 'ORDEN DE COMPRA',
                        FECHA_OV: 'FECHA',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['OV', 'OC', 'FECHA_OV'],
                }


            },
            count_inv: 0,
            count_cn: 0,
            modal_data: null,

            isOpen: false,
            isOpenCreditNote: false,
            isOpenUpdateData: false,

            customer_data: this.customer,

            customers_types: [],
            sellers: [],
            paid_terms: [],
            economic_groups: [],
            invoices: [],
            credit_notes: [],
            countries: [],
        }
    },

    methods: {
        get_invoices(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('seller-management.customers.customer.invoices'), {
                date: this.form.invoices_date,
                customer: this.customer_data.CODIGO_CLIENTE.trim()
            }).then(resp => {
                this.$swal.close()
                this.invoice_table.data = resp.data
                this.count_inv++;
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        get_credit_notes(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('seller-management.customers.customer.credit-notes'), {
                date: this.form.credit_notes_date,
                customer: this.customer_data.CODIGO_CLIENTE.trim()
            }).then(resp => {
                this.$swal.close()
                this.credit_note_table.data = resp.data
                this.count_cn++;
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        load_open_ov(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('seller-management.customers.customer.open-ov'), {
                customer: this.customer_data.CODIGO_CLIENTE.trim()
            }).then(resp => {
                this.$swal.close()
                this.open_ov_table.data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        detail_invoice(row){
            this.modal_data = row
            this.isOpen = true
        },

        detail_credit_note(row){
            this.modal_data = row
            this.isOpenCreditNote = true;
        },

        closeModal(){
            this.isOpen = false
            this.isOpenCreditNote = false;
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

        detail_ov(ov){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.post('', {
                ov: ov
            }).then(resp => {
                this.modal_data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        construct_modal(field, value, title){
            this.form_field = {
                field: field,
                value: value,
                justify: '',
                title: title,
                processing: false,
            }
            this.isOpenUpdateData = true
        },

        customer_load_data(customer){
            this.customer_data = customer
        },

        change_state_customer(state, code){
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

        }
    },

    computed: {
        current_date() {
            return dayjs()
        },
        invoices_date: function () {
            return this.form.invoices_date
        },
        credit_note_date: function (){
            return this.form.credit_notes_date
        }
    },

    watch: {
        invoices_date: function (){
            this.count_inv === 0 ? this.count_inv++ : this.get_invoices()
        },
        credit_note_date: function (){
            this.count_cn === 0 ? this.count_cn++ : this.get_credit_notes()
        },
    },
})
</script>


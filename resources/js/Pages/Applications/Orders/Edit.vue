<template>
    <div>
        <Head :title="`Pedido ` + order.consecutive"/>

        <portal to="application-title">
            Pedido {{ order.consecutive }}
        </portal>

        <portal to="actions">
            <Link :href="route('orders.index')" class="btn btn-primary">
                <font-awesome-icon icon="rotate-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>
        <div>
            <div class="box p-2 grid grid-cols-4 gap-1">
                <div class="m-2">
                    <label>
                        <span>Tipo de pedido</span>
                    </label>
                    <select class="form-select" v-model="form.type"
                            :disabled="form.code_customer === '01000' || form.code_customer === '01001' || form.code_customer === '01002' || form.code_customer === '01003' || form.code_customer === '01004' || form.code_customer === '01005'">
                        <option value="" selected>Seleccione...</option>
                        <option value="national">Nacional</option>
                        <option v-permission:has.disabled="'orders.national-usd'" value="nationalUSD">Nacional USD</option>
                        <option value="export">Exportación</option>
                        <option value="forecast">Pronostico</option>
                        <option value="samples">Muestras</option>
                        <option value="point_of_sale" disabled>Punto de venta</option>
                        <option value="services">Servicios</option>
                        <option value="delivered_merchandise">Mercancía Entregada</option>
                        <option value="recycling">Recuperación – Reciclaje</option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        <span>Vendedor</span>
                    </label>
                    <select class="form-select" disabled>
                        <option :value="order.seller.id" selected>{{ order.seller.name }}</option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        <span>Cliente</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.RAZON_SOCIAL" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Código Cliente</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.CODIGO_CLIENTE" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Orden de compra</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.oc.$error }"
                           v-model="form.oc">
                </div>

                <div class="m-2">
                    <label>
                        <span>Dirección</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.DIRECCION" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Ciudad</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.CIUDAD" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Teléfono</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.TEL1" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Condición pago</span>
                    </label>
                    <input type="text" class="form-control" :value="order.customer.PLAZO" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Descuento</span>
                    </label>
                    <input type="number" class="form-control" v-model="form.discount">
                </div>

                <div class="m-2">
                    <label>
                        <span>IVA</span>
                    </label>
                    <select class="form-select" v-model.number="form.tax">
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="1">SI</option>
                        <option value="0">NO</option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        <span>Notas</span>
                    </label>
                    <textarea name="notes" cols="30" rows="1" class="form-control"
                              v-model="form.notes"></textarea>
                </div>

                <div class="m-2">
                    <label>
                        <span>Moneda</span>
                    </label>
                    <select class="form-select" :value="order.currency">
                        <option value="" selected>Seleccione...</option>
                        <option value="COP">COP</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <div class="m-2" v-if="form.type === 'point_of_sale'">
                    <label for="tax">
                        <span>Punto de venta</span>
                    </label>

                    <select class="form-select" :class="{ 'border-danger': v$.form.cellar.$error }"
                            v-model="form.cellar">
                        <option value="" selected>Seleccione...</option>
                        <option value="BOGOTA">Bogota</option>
                        <option value="CALI">Cali</option>
                        <option value="ITAGUI">Itagüí</option>
                        <option value="PEREIRA">Dos Quebradas</option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        <span>Destino</span>
                    </label>
                    <template v-if="form.type === 'delivered_merchandise'">
                        <select class="form-select" :class="{ 'border-danger': v$.form.destiny.$error }"
                                v-model="form.destiny">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option value="C">Bodega</option>
                        </select>
                    </template>

                    <template v-else>
                        <select class="form-select" :class="{ 'border-danger': v$.form.destiny.$error }"
                                v-model="form.destiny">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option value="C" :disabled="form.type === 'forecast'">Bodega</option>
                            <option value="P" :disabled="form.type === 'recycling'">Producción</option>
                            <option value="D" :disabled="form.type === 'forecast' || form.type === 'recycling'">Troqueles</option>
                        </select>
                    </template>
                </div>


                <div class="m-2">
                    <label>
                        <span>Estado cliente</span>
                    </label>
                    <br>

                    <span
                        class="text-xs font-semibold inline-block py-1 px-2 rounded text-green-600 bg-green-200 uppercase last:mr-0 mr-1 mt-2"
                        v-if="order.customer.ACTIVO === 'R'">
                                Liberado
                            </span>

                    <span
                        class="text-xs font-semibold inline-block py-1 px-2 rounded text-red-600 bg-red-200 uppercase last:mr-0 mr-1 mt-2"
                        v-else-if="order.customer.ACTIVO === 'H'">
                                Retenido
                            </span>
                </div>
            </div>


            <div class="box mt-5">
                <div class="p-2">
                    <div class="grid grid-cols-7 gap-2">
                        <div class="mx-2 mt-3 col-span-2">
                            <label class="flex flex-col sm:flex-row ml-6">
                                Producto
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                STOCK: {{ item_form.stock }}
                            </span>
                            </label>
                            <div class="flex flex-row items-center">
                                <Tippy
                                    tag="div"
                                    content="Buscar por código de producto del cliente"
                                >
                                    <input type="checkbox" v-model="search_by_customer_code" class="form-check-input mr-2">
                                </Tippy>

                                <template v-if="search_by_customer_code">
                                    <autocomplete
                                        ref="product_item"
                                        :url="route('order.product_customer', { customer: form.code_customer })"
                                        class="w-full"
                                        show-field="description"
                                        @selected-value="getDataProduct"
                                    />
                                </template>

                                <template v-else>
                                    <autocomplete
                                        ref="product_item"
                                        url="/orders/search-products"
                                        class="w-full"
                                        show-field="description"
                                        @selected-value="getDataProduct"
                                    />
                                </template>

                                <suggested-products :customer_code="order.customer_code" @add-item="addExternalItem" />
                            </div>
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                U/M
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select class="form-select"
                                    :class="{ 'border-danger': v$.item_form.unit_measurement.$error }"
                                    v-model="item_form.unit_measurement">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="units" :disabled="form.type === 'recycling'">Unidad</option>
                                <option value="thousands" :disabled="form.type === 'recycling'">Millar</option>
                                <option value="liters">Litros</option>
                                <option value="kilos">Kilos</option>
                            </select>
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Cantidad
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="item_form.quantity"
                                   :class="{ 'border-danger': v$.item_form.quantity.$error }" placeholder="Cantidad"
                                   min="0">
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Precio
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="item_form.price"
                                   :class="{ 'border-danger': v$.item_form.price.$error }" placeholder="Precio" min="0">
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Arte 1
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <autocomplete
                                ref="product_art"
                                url="/orders/search-arts"
                                show-field="value"
                                @selected-value="getDataArt"
                                v-if="form.type !== 'recycling'"
                            />
                            <input type="text" class="form-control" disabled v-else>
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Arte 2
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <autocomplete
                                ref="product_art2"
                                url="/orders/search-arts"
                                show-field="value"
                                @selected-value="getDataArt2"
                                v-if="form.type !== 'recycling'"
                            />
                            <input type="text" class="form-control" disabled v-else>
                        </div>


                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Marca
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <autocomplete
                                ref="product_brand"
                                url="/orders/search-brands"
                                show-field="value"
                                @selected-value="getDataBrand"
                                v-if="form.type !== 'recycling'"
                            />
                            <input type="text" class="form-control" disabled v-else>
                        </div>

                        <div class="mx-2 my-3 col-span-2">
                            <label class="flex flex-col sm:flex-row">
                                Cod. prod. cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model="item_form.customer_product_code"
                                   placeholder="Código prod. cliente"
                                   :disabled="form.type === 'recycling'"
                            >
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Tipo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select class="form-select" :class="{ 'border-danger': v$.item_form.type.$error }"
                                    v-model="item_form.type"
                                    :disabled="form.type === 'recycling'">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="new">Nuevo</option>
                                <option value="reprogrammed">Reprogramación</option>
                            </select>
                        </div>

                        <div class="mx-2 my-3 col-span-2">
                            <label class="flex flex-col sm:flex-row">
                                Notas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea cols="30" rows="1" class="form-control" v-model="item_form.notes"
                                      placeholder="Notas"></textarea>
                        </div>

                        <div class="mx-2 mt-2 mb-4 text-center">
                            <button class="btn btn-primary uppercase w-full h-full" @click="AddItem">
                                <font-awesome-icon icon="cart-plus" class="mr-2"/>
                                Agregar
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-10">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr class="uppercase text-center">
                                <th></th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Cod Prod. Cliente</th>
                                <th>Tipo</th>
                                <th>Arte</th>
                                <th>Arte 2</th>
                                <th>Marca</th>
                                <th>Notas</th>
                                <th>UM</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>

                            <draggable
                                tag="tbody"
                                v-model="form.items"
                                handle=".move_icon"
                                item-key="index"
                                v-if="form.items.length > 0"
                            >
                                <template #item="{ element, index }">
                                    <tr>
                                        <td class="cursor-move move_icon text-center">
                                            <font-awesome-icon icon="arrows-alt" class="mr-2"/>
                                        </td>
                                        <td>{{ element.product }}</td>
                                        <td>
                                            <template v-if="element.product_info">
                                                <span>{{ element.product_info.description }}</span>
                                            </template>
                                            <template v-else>
                                                <span>{{ element.description_product }}</span>
                                            </template>
                                        </td>
                                        <td>{{ element.customer_product_code }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-primary badge-rounded"
                                                v-if="element.type === 'new'">
                                                Nuevo
                                            </span>
                                            <span
                                                class="badge badge-purple badge-rounded"
                                                v-else>
                                                Repro
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-secondary btn-sm" v-if="element.art"
                                                    @click="viewArt(element.art)">
                                                {{ element.art }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-secondary btn-sm" v-if="element.art2"
                                                    @click="viewArt(element.art2)">
                                                {{ element.art2 }}
                                            </button>
                                        </td>
                                        <td class="text-center">{{ element.brand }}</td>
                                        <td class="text-center">{{ element.notes }}</td>
                                        <td class="text-center">
                                            <span
                                                class="text-xs font-semibold inline-block py-1 px-2 rounded text-blue-600 bg-blue-200 uppercase last:mr-0 mr-1"
                                                v-if="element.unit_measurement === 'units'">
                                                unidad
                                            </span>

                                            <span
                                                class="badge badge-pink badge-rounded"
                                                v-if="element.unit_measurement === 'kilos'">
                                                kilos
                                            </span>

                                            <span
                                                class="badge badge-warning badge-rounded"
                                                v-if="element.unit_measurement === 'liters'">
                                                litros
                                            </span>
                                            <span
                                                class="text-xs font-semibold inline-block py-1 px-2 rounded text-purple-600 bg-purple-200 uppercase last:mr-0 mr-1"
                                                v-if="element.unit_measurement === 'thousands'">
                                                millar
                                            </span>
                                        </td>
                                        <td class="text-right">{{ $h.formatCurrency(element.price) }}</td>
                                        <td class="text-right">{{ element.quantity }}</td>
                                        <td class="text-right">
                                            {{ $h.formatCurrency(element.price * element.quantity) }}
                                        </td>
                                        <td>
                                            <div class="dropdown text-center">
                                                <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                                        data-tw-toggle="dropdown">
                                                    <font-awesome-icon icon="bars"/>
                                                </button>
                                                <div class="dropdown-menu w-40">
                                                    <div class="dropdown-content">
                                                        <a href="javascript:void(0)"
                                                           @click="editRow(index)"
                                                           class="dropdown-item">
                                                            <font-awesome-icon :icon="['far', 'pen-to-square']"
                                                                               class="mr-1"/>
                                                            Editar
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                           @click="deleteRow(index)"
                                                           class="dropdown-item">
                                                            <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                                            Eliminar
                                                        </a>

                                                        <a href="javascript:void(0)"
                                                           @click="cloneRow(index)"
                                                           class="dropdown-item">
                                                            <font-awesome-icon :icon="['far', 'clone']" class="mr-1"/>
                                                            Clonar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </draggable>
                            <tbody v-else>
                                <tr>
                                    <td class="border-b dark:border-dark-5 text-center text-red-600" colspan="12"><strong>No
                                        has agregado ningún producto</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-row">
                        <div class="mt-2 w-4/5 p-5">
                            <div class="alert alert-secondary show mb-2 shadow-sm"
                                 role="alert">
                                <div class="flex items-center">
                                    <div class="font-medium text-lg uppercase">Información sobre retenciones</div>
                                </div>
                                <div class="mt-0">
                                    Las retenciones para este pedido serán re calculadas una vez guardes los cambios.
                                    Los totales están expresados SIN RETENCIONES
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto mt-2 w-1/5 ml-auto">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                <tr>
                                    <td class="whitespace-nowrap">BRUTO</td>
                                    <td class="whitespace-nowrap text-right">
                                        {{ $h.formatCurrency(bruto) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">DESCUENTO</td>
                                    <td class="whitespace-nowrap text-right text-danger">
                                        (-) {{ $h.formatCurrency(discount) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">SUBTOTAL</td>
                                    <td class="whitespace-nowrap text-right">
                                        {{ $h.formatCurrency(subtotal) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">IVA</td>
                                    <td class="whitespace-nowrap text-right text-success">
                                        (+) {{ $h.formatCurrency(tax) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">TOTAL PEDIDO</td>
                                    <td class="whitespace-nowrap text-right">
                                        {{ $h.formatCurrency(total) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200 dark:border-dark-5">
                    <button @click="update(form)" :disabled="form.processing" class="btn btn-primary uppercase">
                        <font-awesome-icon icon="save" class="mr-2"/>
                        Actualizar pedido
                    </button>

                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=xl>
                <template #title v-if="idx != null">
                    {{ `${form.items[idx].product} - ${form.items[idx].description_product}` }}
                </template>

                <template #content v-if="idx != null">
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Tipo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select class="form-select" v-model="form.items[idx].type">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="new">Nuevo</option>
                                <option value="reprogrammed">Reprogramación</option>
                            </select>
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                U/M
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select class="form-select" v-model="form.items[idx].unit_measurement" placeholder="Unidad">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="units">Unidad</option>
                                <option value="thousands">Millar</option>
                            </select>
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Cantidad
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="form.items[idx].quantity"
                                   placeholder="Cantidad" min="0">
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Precio
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="form.items[idx].price"
                                   placeholder="Precio" min="0">
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Arte 1
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearfield(0)"/>
                                </span>
                            </label>
                            <autocomplete
                                url="/orders/search-arts"
                                show-field="value"
                                :init-value="form.items[idx].art"
                                @selected-value="changeArtModal"
                            />
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Arte 2
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearfield(1)"/>
                                </span>
                            </label>
                            <autocomplete
                                url="/orders/search-arts"
                                show-field="value"
                                :init-value="form.items[idx].art2"
                                @selected-value="changeArt2Modal"
                            />
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Marca
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearfield(2)"/>
                                </span>
                            </label>
                            <autocomplete
                                url="/orders/search-brands"
                                show-field="value"
                                :init-value="form.items[idx].brand"
                                @selected-value="changeBrandModal"
                            />
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Cod. prod. cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input type="text" class="form-control" v-model="form.items[idx].customer_product_code"
                                   placeholder="Código prod. cliente">
                        </div>

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Notas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea name="notes" cols="30" rows="1" class="form-control"
                                      v-model="form.items[idx].notes" placeholder="Notas"></textarea>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-primary">
                        Finalizar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="isOpenArt" max-width=xl>
                <template #title>
                    Visualizar arte
                </template>

                <template #content>
                    <template v-if="artUrl">
                        <embed class="pdfobject" :src="artUrl" type="application/pdf"
                               style="overflow: auto; width: 100%; height: 600px;">
                    </template>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInputError from "@/Jetstream/InputError.vue"
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, numeric, minValue, minLength, maxLength, requiredIf, helpers} from '@/utils/i18n-validators'
import dayjs from "dayjs";
import draggable from 'vuedraggable'
import SuggestedProducts from "./SuggestedProducts.vue";

import 'dayjs/locale/es'
dayjs.locale('es')

const validString = helpers.regex(/([A-Za-z0-9\-\/]+)/gm)

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: ['order'],

    components: {
        Head,
        Link,
        Autocomplete,
        JetInputError,
        JetDialogModal,
        draggable,
        SuggestedProducts
    },

    validations() {
        return {
            form: {
                destiny: {
                    required
                },
                type: {
                    required
                },
                oc: {
                    maxLength: maxLength(25)
                },
                cellar: {
                    required: requiredIf(function () {
                        return this.form.type === 'point_of_sale'
                    })
                },
                items: {
                    required,
                    minLength: minLength(1),
                },
            },

            item_form: {
                product: {required},
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
                },
                art: {required},
                brand: {required},
                customer_product_code: {
                    maxLength: maxLength(20),
                    validString : helpers.withMessage("este campo no acepta caracteres especiales", validString)
                },
                notes: {
                    validString : helpers.withMessage("este campo no acepta caracteres especiales", validString)
                }
            }
        }
    },

    data() {
        return {
            form: {
                order_id: this.order.id,
                notes: this.order.notes,
                items: this.order.details,
                oc: this.order.oc,
                tax: parseInt(this.order.taxable),
                discount: Number((this.order.discount * 100) / this.order.bruto),
                destiny: this.order.destiny,
                processing: false,
                type: this.order.type
            },
            item_form: {
                product: null,
                description_product: null,
                destiny: this.order.destiny,
                stock: 0,
                type: '',
                unit_measurement: "",
                price: 0,
                quantity: 0,
                art: null,
                art2: null,
                brand: null,
                notes: null,
                customer_product_code: null
            },
            items_errors: [],
            errors: {},
            product_item: null,
            isOpen: false,
            isOpenArt: false,
            idx: null,
            artUrl: null,
        }
    },

    methods: {
        getDataArt(obj) {
            let regex = /marc/i

            if (regex.test(this.item_form.description_product) && obj.value === 'G03661'){
                this.$swal({
                    title: '¡Información Importante!',
                    text: "NO SE PERMITE USAR ARTE GENÉRICO EN UN PRODUCTO MARCADO",
                    icon: 'info',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 10000,
                });
                setTimeout(this.resetArt, 2000)

            }else {
                this.item_form.art = obj.value;
                this.verify_sold_art(obj.value, this.item_form.product)
            }
        },

        verify_sold_art(art, product) {
            let regex3 = /^820/i

            if (!regex3.test(this.item_form.product)){
                axios.get(route('order.verify-sold-art', {art, product})).then(resp => {
                    resp.data > 0
                        ? this.item_form.type = 'reprogrammed'
                        : this.item_form.type = 'new'
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                })
            }
        },

        getDataArt2(obj) {
            this.item_form.art2 = obj.value;
        },

        getDataBrand(obj) {
            this.item_form.brand = obj.value;
        },

        getDataProduct(obj) {
            let regex = /^330/i
            let regex2 = /^319/i

            if (regex.test(obj.code) || regex2.test(obj.code)) {
                this.$swal({
                    title: '¡Información Importante!',
                    text: "RECUERDE COLOCAR EL TAMAÑO DE LA ESPIGA EN LAS NOTAS (SI SE REQUIERE)",
                    icon: 'question',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 10000,
                });
            }

            let regex3 = /^820/i

            if (regex3.test(obj.code)){
                this.item_form.type = 'reprogrammed'
            }

            this.item_form.product = obj.code;
            this.item_form.description_product = obj.description;
            this.item_form.stock = obj.stock
            this.item_form.price = obj.customer_price ?? 0
            this.item_form.customer_product_code = obj.customer_product_code ?? ''
        },

        resetProductForm: function () {
            this.item_form = {
                product: null,
                description_product: null,
                stock: 0,
                destiny: this.order.destiny,
                type: "",
                unit_measurement: "units",
                price: 0,
                quantity: 0,
                art: null,
                art2: null,
                brand: null,
                notes: null,
                customer_product_code: null
            }
            this.items_errors = []
            this.$refs.product_item.showInput(true)
            this.$refs.product_art.showInput(true)
            this.$refs.product_art2.showInput(true)
            this.$refs.product_brand.showInput(true)
        },

        AddItem() {
            this.v$.item_form.$touch();
            if (this.v$.item_form.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Verifica que toda la información sea correcta, Recuerde que las Notas no deben contener  caracteres especiales ',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                this.form.items.push(this.item_form);
                this.resetProductForm();
                this.v$.item_form.$reset()
            }
        },

        addExternalItem(item){
            this.form.items.push(item);
        },

        deleteRow: function (idx) {
            this.form.items.splice(idx, 1);
        },

        cloneRow: function (i) {
            const myObjCopy = _.cloneDeep(this.form.items[i]);
            delete myObjCopy.id;
            this.form.items.push(myObjCopy);
        },

        editRow(idx) {
            console.log(this.form.items[idx])
            this.idx = idx;
            this.isOpen = true;
        },

        closeModal() {
            this.idx = null;
            this.isOpen = false;
            this.isOpenArt = false;
            this.artUrl = null;
        },

        update: function (data) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                if (this.form.detained === 'H' && this.form.type === 'delivered_merchandise'){
                    this.$swal({
                        icon: 'error',
                        title: 'El cliente se encuentra retenido y no se permite crear pedidos de mercancía entregada hasta que se encuentre liberado de cartera',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 10000,
                    })
                }else{
                    this.form.processing = true;
                    this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                        title: 'Actualizando pedido(s)...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.put(route('orders.update', data.order_id), data).then(res => {
                        this.errors = {};

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Pedido actualizado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                        this.$inertia.visit(route('orders.index'));
                        this.form.processing = false;

                    }).catch(err => {
                        this.form.processing = false;
                        this.errors = err.response.data.errors;
                        console.log(err);
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                    });
                    this.form.processing = false;
                }
            }
        },

        changeArtModal(obj) {
            this.form.items[this.idx].art = obj.value;
        },

        changeArt2Modal(obj) {
            this.form.items[this.idx].art2 = obj.value;
        },

        changeBrandModal(obj) {
            this.form.items[this.idx].brand = obj.value;
        },

        viewArt(art) {
            this.artUrl = `http://192.168.1.12/intranet_ci/assets/Artes/${art}.pdf`;
            this.isOpenArt = true;
        },

        clearfield(v) {
            if (v === 0)
                this.form.items[this.idx].art = null;
            else if (v === 1)
                this.form.items[this.idx].art2 = null;
            else if (v === 2)
                this.form.items[this.idx].brand = null;
        },

        toggleActive: function () {
            const dest = this.form.destiny;
            this.form.items.forEach(function (item) {
                console.log(item);
                item.destiny = dest;
            });
        }

    },

    computed: {
        bruto: function () {
            return this.form.items.reduce(function (a, c) {
                return a + Number((c.price * c.quantity) || 0)
            }, 0)
        },

        discount: function () {
            return Number((this.form.discount * this.bruto) / 100 || 0)
        },

        subtotal: function () {
            return Number(this.bruto - ((this.form.discount * this.bruto) / 100) || 0)
        },

        tax: function () {
            return this.form.tax === 1 ? Number((this.subtotal * 19) / 100 || 0) : 0
        },

        total: function () {
            return Number(this.subtotal + this.tax || 0)
        },


    },

    created() {
        this.form.bruto_val = this.bruto;
        this.form.discount_val = this.discount;
        this.form.subtotal_val = this.subtotal;
        this.form.tax_val = this.tax;
        this.form.total_val = this.total;
    },

    watch: {
        bruto() {
            this.form.bruto_val = this.bruto;
            this.toggleActive();
        },

        discount() {
            this.form.discount_val = this.discount;
        },

        subtotal() {
            this.form.subtotal_val = this.subtotal;
        },

        tax() {
            this.form.tax_val = this.tax;
        },

        total() {
            this.form.total_val = this.total;
        },

        'form.type': function (value) {
            if (value === 'recycling'){
                this.form.tax = 0
                this.item_form.destiny = 'C'
                this.item_form.type = 'reprogrammed'
            }
        }
    },

    mounted() {
        if (this.order.type === 'recycling'){
            this.form.tax = 0
            this.item_form.destiny = 'C'
            this.item_form.type = 'reprogrammed'
        }
    }
}
</script>

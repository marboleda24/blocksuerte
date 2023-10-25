<template>
    <div>
        <Head title="Nuevo Pedido"/>

        <portal to="application-title">
            Nuevo Pedido
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
                    <label for="tax">
                        <span>Tipo de pedido</span>
                    </label>
                    <select class="form-select" :class="{ 'border-danger': v$.form.type.$error }"
                            v-model="form.type"
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
                    <select class="form-select" required autofocus v-model="form.seller"
                            :class="{ 'border-danger': v$.form.seller.$error }">
                        <option value="" selected>Seleccione...</option>
                        <option :value="seller.id" v-for="seller in sellers" v-bind:key="seller.id">
                            {{ seller.name }}
                        </option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        Cliente
                    </label>
                    <autocomplete
                        :url="route('order.search_customer_seller', { seller: form.seller })"
                        show-field="name"
                        @selected-value="getDataCustomer"
                    />
                </div>

                <div class="m-2">
                    <label>
                        <span>Código Cliente</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.code_customer.$error }"
                           v-model="form.code_customer" disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Orden de compra</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           :class="{ 'border-danger': v$.form.oc.$error }"
                           v-model="form.oc"
                           @blur="validateOC(form.oc, form.code_customer)">
                </div>

                <div class="m-2">
                    <label>
                        <span>Dirección</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.code_customer.$error }" v-model="form.address"
                           disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Ciudad</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.code_customer.$error }" v-model="form.city"
                           disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Teléfono</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.code_customer.$error }" v-model="form.phone"
                           disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Condición pago</span>
                    </label>
                    <input type="text" class="form-control"
                           :class="{ 'border-danger': v$.form.code_customer.$error }" v-model="form.term"
                           disabled>
                </div>

                <div class="m-2">
                    <label>
                        <span>Descuento</span>
                    </label>
                    <input type="number" class="form-control"
                           :class="{ 'border-danger': v$.form.discount.$error }" v-model="form.discount"
                           min="0">
                </div>

                <div class="m-2">
                    <label for="tax">
                        <span>IVA</span>
                    </label>
                    <select class="form-select"
                            :class="{ 'border-danger': v$.form.tax.$error }"
                            v-model="form.tax">
                        <option value="" selected>Seleccione...</option>
                        <option value="1">SI</option>
                        <option value="0">NO</option>
                    </select>
                </div>

                <div class="m-2">
                    <label for="notes">
                        <span>Notas</span>
                    </label>
                    <textarea name="notes" cols="30" rows="1" class="form-control"
                              :class="{ 'border-danger': v$.form.notes.$error }"
                              v-model="form.notes"></textarea>
                </div>

                <div class="m-2">
                    <label for="tax">
                        <span>Moneda</span>
                    </label>
                    <select id="currency" class="form-select"
                            :class="{ 'border-danger': v$.form.currency.$error }" v-model="form.currency">
                        <option value="" selected>Seleccione...</option>
                        <option value="COP" :disabled="form.type === 'nationalUSD' || form.type === 'export'">COP</option>
                        <option value="USD" :disabled="form.type === 'national'">USD</option>
                    </select>
                    <jet-input-error v-if="errors.currency" :message="errors.currency[0]" class="mt-2"/>
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
                        <option value="ITAGUI">Itagüi</option>
                        <option value="PEREIRA">Dos Quebradas</option>
                    </select>
                </div>

                <div class="m-2">
                    <label>
                        <span>Estado cliente</span>
                    </label>
                    <br>

                    <span
                        class="badge badge-success badge-rounded"
                        v-if="form.detained === 'R'">
                        Liberado
                    </span>

                    <span
                        class="badge badge-danger badge-rounded"
                        v-else-if="form.detained === 'H'">
                        Retenido
                    </span>
                </div>
            </div>

            <div class="box mt-5">
                <div class="p-2">
                    <div class="grid grid-cols-7 gap-2">
                        <div class="mx-2 mt-3 col-span-2">
                            <label class="flex flex-col sm:flex-row ml-6">
                                Producto x
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    STOCK: {{ item_form.stock }}
                                </span>
                            </label>
                            <div class="flex flex-row items-center">
                                <Tippy
                                    tag="div"
                                    content="Buscar por código de producto del cliente"
                                >
                                    <input type="checkbox" v-model="search_by_customer_code"
                                           class="form-check-input mr-2">
                                </Tippy>

                                <template v-if="search_by_customer_code">
                                    <autocomplete
                                        ref="product_item"
                                        :url="route('order.product_customer', { customer: form.code_customer })"
                                        class="w-full"
                                        show-field="field"
                                        @selected-value="getDataProduct"
                                    />
                                </template>

                                <template v-else>
                                    <autocomplete
                                        ref="product_item"
                                        :url="route('order.search-products')"
                                        class="w-full"
                                        show-field="field"
                                        @selected-value="getDataProduct"
                                    />
                                </template>

                                <suggested-products :customer_code="form.code_customer" @add-item="addExternalItem"/>
                            </div>
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Destino
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>


                            <template v-if="form.type === 'delivered_merchandise'">
                                <select class="form-select" :class="{ 'border-danger': v$.item_form.destiny.$error }"
                                        v-model="item_form.destiny">
                                    <option value="" selected="selected" disabled>Seleccione...</option>
                                    <option value="C">Bodega</option>
                                </select>
                            </template>

                            <template v-else>
                                <select class="form-select" :class="{ 'border-danger': v$.item_form.destiny.$error }"
                                        v-model="item_form.destiny" :disabled="form.type === 'export'">
                                    <option value="" selected="selected" disabled>Seleccione...</option>
                                    <option value="C" :disabled="form.type === 'forecast'">Bodega</option>
                                    <option value="P" :disabled="form.type === 'recycling'">Producción</option>
                                    <option value="D" :disabled="form.type === 'forecast' || form.type === 'recycling'">Troqueles</option>
                                </select>
                            </template>
                        </div>


                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                U/M
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <select id="type" class="form-select"
                                    :class="{ 'border-danger': v$.item_form.unit_measurement.$error }"
                                    v-model="item_form.unit_measurement">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="units" :disabled="form.type === 'recycling'">Unidad</option>
                                <option value="thousands" :disabled="form.type === 'recycling' || form.type === 'national'">Millar</option>
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
                                   :class="{ 'border-danger': v$.item_form.quantity.$error }"
                                   @focus="$event.target.select()" placeholder="Cantidad" min="0">
                        </div>

                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Precio
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control" v-model.number="item_form.price"
                                   :class="{ 'border-danger': v$.item_form.price.$error }" placeholder="Precio"
                                   @focus="$event.target.select()" min="0">
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
                            <input type="text" class="form-control" v-model="item_form.customer_product_code"
                                   :class="{ 'border-danger': v$.item_form.customer_product_code.$error }"
                                   placeholder="Código prod. cliente"
                                   :disabled="form.type === 'recycling'">
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

                        <div class="mx-2 my-3">
                            <label class="flex flex-col sm:flex-row">
                                Notas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea name="notes" cols="30" rows="1" class="form-control" v-model="item_form.notes"
                                      placeholder="Notas"></textarea>
                        </div>

                        <div class="mx-2 mt-2 mb-4 text-center">
                            <button class="btn btn-primary uppercase w-full h-full" @click="addItem">
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
                                <th>Destino</th>
                                <th>Tipo</th>
                                <th>Arte</th>
                                <th>Arte 2</th>
                                <th>Marca</th>
                                <th>Notas</th>
                                <th>UM</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
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
                                        <td class="text-center cursor-move move_icon">
                                            <font-awesome-icon icon="arrows-alt" class="mr-2"/>
                                        </td>
                                        <td>{{ element.product }}</td>
                                        <td>{{ element.description_product }}</td>
                                        <td>{{ element.customer_product_code }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-success badge-rounded"
                                                v-if="element.destiny === 'C'">
                                                Bodega
                                            </span>
                                            <span
                                                class="badge badge-primary badge-rounded"
                                                v-else-if="element.destiny === 'P'">
                                                Producción
                                            </span>
                                            <span
                                                class="badge badge-yellow badge-rounded"
                                                v-else>
                                                Troqueles
                                            </span>
                                        </td>
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
                                                class="badge badge-primary badge-rounded"
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
                                        <td class="text-right">{{ element.quantity }}</td>
                                        <td class="text-right">{{ $h.formatCurrency(element.price) }}</td>
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
                                                            <font-awesome-icon :icon="['far', 'trash-can']"
                                                                               class="mr-1"/>
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
                                <td class="border-b dark:border-dark-5 text-center text-red-600" colspan="15">
                                    <strong>No has agregado ningún producto</strong>
                                </td>
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
                                    Las retenciones para este pedido serán calculadas una vez guardes el pedido.
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
                    <button @click.prevent="save(form)"
                            :disabled="form.processing"
                            class="btn btn-primary uppercase">
                        <font-awesome-icon :icon="['far', 'floppy-disk']" class="mr-2"/>
                        GUARDAR PEDIDO
                    </button>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title v-if="idx != null">
                    {{ `${form.items[idx].product} - ${form.items[idx].description_product}` }}
                </template>

                <template #content v-if="idx != null">
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                        <div class="mx-2 mt-3">
                            <label class="flex flex-col sm:flex-row">
                                Destino
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <template v-if="form.type === 'delivered_merchandise'">
                                <select class="form-select" v-model="form.items[idx].destiny">
                                    <option value="" selected="selected" disabled>Seleccione...</option>
                                    <option value="C">Bodega</option>
                                </select>
                            </template>

                            <template v-else>
                                <select class="form-select" v-model="form.items[idx].destiny">
                                    <option value="" selected="selected" disabled>Seleccione...</option>
                                    <option value="C" :disabled="form.type === 'forecast'">Bodega</option>
                                    <option value="P" :disabled="form.type === 'recycling'">Producción</option>
                                    <option value="D" :disabled="form.type === 'forecast' || form.type === 'recycling'">Troqueles</option>
                                </select>
                            </template>
                        </div>

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
                            <select class="form-select" v-model="form.items[idx].unit_measurement">
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
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearField(0)"/>
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
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearField(1)"/>
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
                                    <font-awesome-icon icon="trash" class="cursor-pointer" @click="clearField(2)"/>
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
                            <textarea name="notes" id="notes" cols="30" rows="1" class="form-control"
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

            <jet-dialog-modal :show="isOpenArt" max-width=3xl>
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

            <generate-product :show-modal="new_product_form.open" :product-description="new_product_form.product_description"
                              @close="closeModal(false)" @success="generateProductSuccess"/>
        </div>
    </div>
</template>

<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInputError from "@/Jetstream/InputError.vue"
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {maxLength, maxValue, minLength, minValue, numeric, required, requiredIf, helpers} from '@/utils/i18n-validators'
import dayjs from "dayjs";
import draggable from 'vuedraggable'
import SuggestedProducts from "./SuggestedProducts.vue";
import GenerateProduct from "@/Pages/Applications/Orders/GenerateProduct.vue";


import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

const validString = helpers.regex(/([A-Za-z0-9\-\/]+)/gm)

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        sellers: Array
    },

    components: {
        Head,
        Link,
        JetDialogModal,
        JetInputError,
        Autocomplete,
        draggable,
        SuggestedProducts,
        GenerateProduct
    },

    validations() {
        return {
            form: {
                seller: {required},
                code_customer: {required},
                address: {required},
                city: {required},
                phone: {required},
                term: {required},
                discount: {
                    required,
                    numeric,
                    minValue: minValue(0),
                    maxValue: maxValue(40),
                },
                oc: {
                    maxLength: maxLength(25)
                },
                currency: {required},
                tax: {required},
                items: {
                    required,
                    minLength: minLength(1),
                },
                type: {required},
                cellar: {
                    required: requiredIf(function () {
                        return this.form.type === 'point_of_sale'
                    })
                },
                notes: {
                    maxLength: maxLength(90)
                }
            },
            item_form: {
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
                },
                art: {
                    required: requiredIf(function () {
                        return this.form.type !== 'recycling'
                    })
                },
                brand: {
                    required: requiredIf(function () {
                        return this.form.type !== 'recycling'
                    })
                },
                customer_product_code: {
                    maxLength: maxLength(20),
                    validString: helpers.withMessage('este campo no admite caracteres especiales', validString)
                },
                notes: {
                    validString: helpers.withMessage('este campo no admite caracteres especiales', validString)
                }
            }
        }
    },

    data() {
        return {
            form: {
                seller: null,
                code_customer: null,
                oc: null,
                address: null,
                city: null,
                phone: null,
                term: null,
                discount: null,
                detained: null,
                currency: null,
                tax: null,
                notes: null,
                items: [],
                bruto_val: null,
                subtotal_val: null,
                discount_val: null,
                tax_val: null,
                total_val: null,
                type: '',
                uploadedFiles: [],
                processing: false,
                cellar: ''
            },
            item_form: {
                product: null,
                description_product: null,
                stock: 0,
                destiny: "",
                type: "",
                unit_measurement: "",
                price: 0,
                quantity: 0,
                art: null,
                art2: null,
                brand: null,
                notes: null,
                customer_product_code: null
            },
            errors: {},
            product_item: null,
            isOpen: false,
            isOpenArt: false,
            idx: null,
            artUrl: null,
            search_by_customer_code: false,

            new_product_form: {
                open: false,
                product_description: ''
            }
        }
    },

    methods: {
        getDataCustomer(obj) {
            if (obj.code === '01000' || obj.code === '01001' || obj.code === '01002' || obj.code === '01003' || obj.code === '01004' || obj.code === '01005') {
                this.form.type = 'point_of_sale'
            }

            this.form.code_customer = obj.code;
            this.form.address = obj.address;
            this.form.city = obj.city;
            this.form.phone = obj.phone;
            this.form.term = obj.term;
            this.form.discount = obj.discount;
            this.form.detained = obj.detained;
            this.form.currency = obj.currency;
            this.form.tax = obj.taxable === 'Y' ? '1' : '0';
        },

        getDataArt(obj) {
            let regex = /marc/i

            if (regex.test(this.item_form.description_product) && obj.value === 'G03661') {
                this.$swal({
                    title: '¡Información Importante!',
                    text: "NO SE PERMITE USAR ARTE GENÉRICO EN UN PRODUCTO MARCADO",
                    icon: 'info',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 10000,
                });
                setTimeout(this.resetArt, 2000)

            }else if (obj.value === null || obj.value === undefined){
                return true;
            } else {
                this.item_form.art = obj.value;
                this.verify_sold_art(obj.value, this.item_form.product)
            }
        },

        verify_sold_art(art, product) {
            if (art === undefined || product === undefined){
                return true;
            }

            let regex3 = /^820/i

            if (!regex3.test(this.item_form.product)) {
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
            if (obj.origin === 'EVPIU') {
                this.new_product_form = {
                    open: true,
                    product_description: obj.description
                }
            }

            let regex = /^330/i
            let regex2 = /^319/i
            let regex3 = /^820/i
            let regex4 = /\s[A-Za-z]\/[A-Za-z]/g

            if (regex.test(obj.code) || regex2.test(obj.code)) {
                this.$swal({
                    title: '¡Información Importante!',
                    text: "Recuerde colocar el tamaño de la espiga en las notas (si se requiere)",
                    icon: 'question',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 10000,
                });
            }

            if (regex3.test(obj.code)) {
                this.item_form.type = 'reprogrammed'
            }

            if (regex4.test(obj.description)){
                this.$swal({
                    title: '¡Información Importante!',
                    text: "Recuerde revisar inventario de productos Niquel, de no existir inventario deberá programar con el nuevo ACABADO GALVANICO",
                    icon: 'warning',
                    confirmButtonText: 'Aceptar',
                    timerProgressBar: true,
                    timer: 10000,
                });
            }

            this.item_form.product = obj.code;
            this.item_form.description_product = obj.description;
            this.item_form.stock = obj.stock
            this.item_form.price = obj.customer_price ?? 0
            this.item_form.customer_product_code = obj.customer_product_code ?? ''

            if (obj.art){
                this.item_form.art = obj.art
                this.$refs.product_art.setValue(obj.art)
            }
        },

        resetProductForm() {
            this.item_form = {
                product: null,
                description_product: null,
                stock: 0,
                destiny: this.form.type === 'recycling' ? 'C': '',
                type: this.form.type === 'recycling' ? 'reprogrammed': '',
                unit_measurement: "",
                price: 0,
                quantity: 0,
                art: null,
                art2: null,
                brand: null,
                notes: null,
                customer_product_code: null
            }
            this.items_errors = []
            this.$refs.product_item?.showInput(true)
            this.$refs.product_art?.showInput(true)
            this.$refs.product_art2?.showInput(true)
            this.$refs.product_brand?.showInput(true)
        },

        addItem() {
            this.v$.item_form.$touch();
            if (this.v$.item_form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Verifica la información',
                    text: 'Recuerda buscar y seleccionar el producto, el arte y la marca, ademas recuerda que estos son obligatorios, Recuerde que las Notas no deben contener  caracteres especiales  ',
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

        addExternalItem(item) {
            this.form.items.push(item);
        },

        deleteRow(idx) {
            this.form.items.splice(idx, 1);
        },

        cloneRow(i) {
            const myObjCopy = _.cloneDeep(this.form.items[i]);
            this.form.items.push(myObjCopy);
        },

        editRow(idx) {
            this.idx = idx;
            this.isOpen = true;
        },

        closeModal(reset = true) {
            this.idx = null;
            this.isOpen = false;
            this.isOpenArt = false;
            this.artUrl = null;
            this.new_product_form = {
                open: false,
                product_description: ''
            }

            if (reset){
                this.resetProductForm()
                console.log('reset form')
            }
        },

        save(data) {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada y hayas agregado al menos un item',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                if (this.form.detained === 'H' && this.form.type === 'delivered_merchandise') {
                    this.$swal({
                        icon: 'error',
                        title: 'El cliente se encuentra retenido y no se permite crear pedidos de mercancía entregada hasta que se encuentre liberado de cartera',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 10000,
                    })
                } else {
                    this.form.processing = true;
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Creando pedido(s)...',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('orders.store'), data).then(res => {
                        this.errors = {};

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Pedido creado con éxito!, recuerde que si agrego productos de requerimientos el pedido quedara retenido hasta que el area de produccion cree los productos en MAX",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                        this.$inertia.visit(route('orders.index'));
                        this.form.processing = false;
                    }).catch(err => {
                        this.form.processing = false;
                        this.errors = err.response.data.errors;
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

        clearField(v) {
            if (v === 0)
                this.form.items[this.idx].art = null;
            else if (v === 1)
                this.form.items[this.idx].art2 = null;
            else if (v === 2)
                this.form.items[this.idx].brand = null;
        },

        validateOC(oc, customer) {
            if (oc && customer) {
                axios.get(route('orders.validate-oc', {oc, customer})).then(resp => {
                    if (resp.data === true) {
                        this.$swal({
                            icon: 'info',
                            title: '¡Información Importante!',
                            html: '<strong class="text-red-500">¡Ya existe un pedido con esta orden de compra! ¿Está seguro de crear otro?</strong>',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                })
            }
        },

        resetArt() {
            this.$refs.product_art.showInput(true)
        },

        generateProductSuccess(product, state){
            if (state === 'success'){
                this.item_form.product = product.code
                this.item_form.description_product = product.description
                this.item_form.art = product.art_code
                this.$refs.product_item.setValue(product)
                this.$refs.product_art.setValue({label: '', value: product.art_code})
                this.getBrand(product.art_code)
                this.closeModal(false)
            }else {
                this.resetProductForm()
            }
        },

        getBrand(art){
            axios.get(route(''), {
                params: {
                    art: art
                }
            }).then(resp => {
                this.$refs.product_brand.setValue({label: '', value: resp.data})
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data,
                    confirmButtonText: 'Aceptar',
                });
            })
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
            return this.form.tax === '1' ? Number((this.subtotal * 19) / 100 || 0) : 0
        },

        total: function () {
            return Number(this.subtotal + this.tax || 0)
        },
    },

    watch: {
        bruto() {
            this.form.bruto_val = this.bruto;
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

            if (value === 'national'){
                this.form.currency = 'COP'
            }

            if (value === 'nationalUSD' || value === 'export'){
                this.form.currency = 'USD'
            }

            if (value === 'export'){
                this.item_form.destiny = 'P'
            }
        }
    },

    created() {
        this.form.bruto_val = this.bruto;
        this.form.discount_val = this.discount;
        this.form.subtotal_val = this.subtotal;
        this.form.tax_val = this.tax;
        this.form.total_val = this.total;
    },

    mounted() {
        const current_seller = this.sellers.find(element => element.id === this.$page.props.user.id);

        if (typeof current_seller !== 'undefined') {
            console.log(typeof current_seller)

            this.form.seller = this.$page.props.user.id
        }

        if (this.form.type === 'recycling'){
            this.form.tax = 0
            this.item_form.destiny = 'C'
            this.item_form.type = 'reprogrammed'
        }
    }
}
</script>

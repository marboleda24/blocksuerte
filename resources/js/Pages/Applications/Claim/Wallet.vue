<template>
    <div>
        <Head title="Gestión reclamos cartera"/>

        <portal to="application-title">
            Gestión reclamos cartera
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <Tippy
                            id="pending-tab"
                            tag="button"
                            content="Pendientes"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            PENDIENTES
                        </Tippy>
                    </li>

                    <li class="nav-item w-full">
                        <Tippy
                            id="finish-tab"
                            tag="button"
                            content="Finalizados"
                            data-tw-toggle="tab"
                            data-tw-target="#finish"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            FINALIZADOS
                        </Tippy>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="pending" class="tab-pane active p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="wallet_rows" :columns="table.columns" :options="table.options">
                            <template v-slot:actions="{row}">
                                <div class="text-center">
                                    <button class="btn btn-secondary btn-sm" @click="view(row.hash)">
                                        <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                        Gestionar
                                    </button>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                    <div id="finish" class="tab-pane p-2" role="tabpanel" aria-labelledby="finish-tab">
                        <v-client-table :data="finish_rows" :columns="table.columns" :options="table.options">
                            <template v-slot:actions="{row}">
                                <div class="text-center">
                                    <button class="btn btn-secondary btn-sm" @click="view(row.hash)">
                                        <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                        Ver
                                    </button>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="modal.isOpen" @close="closeModal" max-width=5xl>
                <template #title>
                    Reclamo {{ modal.data.consecutive }}
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="file-invoice-dollar" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Documento afectado
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.document }} - OV:{{ modal.data.invoice.OV }}
                                    </div>
                                </div>

                                <div class="flex mt-4 lg:mt-0">
                                    <button class="btn btn-secondary py-2 px-2" @click="downloadInvoice(modal.data.document)">
                                        <font-awesome-icon icon="download"/>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="building-user" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Cliente
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.invoice.RAZONSOCIAL }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="paper-plane" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Destino
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.destiny_esp }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="file-circle-question" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Accion
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.action_esp }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="circle-question" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Razon
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.reason_esp }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="user" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Creado por
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.user.name }}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="box col-span-2">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="triangle-exclamation" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Causas
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        <span v-for="cause in modal.data.causes"
                                              class="badge-sm badge-warning badge-rounded">
                                            {{ cause.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="calendar" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Fecha de creación
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ $h.formatDate(modal.data.created_at, 'YYYY-MM-DD hh:mm a') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box col-span-2" v-if="modal.data.notes">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="message" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Notas
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.notes }}
                                    </div>

                                </div>

                                <div class="flex mt-4 lg:mt-0">
                                    <button class="btn btn-secondary py-1 px-2" @click="modal.isOpenComments = true">
                                        Mostrar comentarios
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="list-check" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Logs
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        Logs del reclamo
                                    </div>
                                </div>

                                <div class="flex mt-4 lg:mt-0">
                                    <button class="btn btn-secondary py-1 px-2" @click="modal.isOpenLog = true">
                                        Mostrar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="box col-span-3" v-if="modal.data.files.length > 0">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="folder-open" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Archivos
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                         <span v-for="file in modal.data.files"
                                               class="badge badge-primary badge-rounded cursor-pointer"
                                               @click="downloadFile(file)">
                                            {{ $h.truncateString(file.name, 20) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.discount">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="dollar-sign" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Descuento
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ $h.formatCurrency(modal.data.discount) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.major_value">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="dollar-sign" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Mayor valor cobrado
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ $h.formatCurrency(modal.data.major_value) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.workplace">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="people-group" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Area responsable
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.workplace.name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.production_order">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Orden de producción
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.production_order }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.quality_observation">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="message" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Observaciones de calidad
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.quality_observation }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.credit_memo">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Memo crédito
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.credit_memo }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.sale_order">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Orden de venta
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.sale_order }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.remission_id">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Remisión
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.remission_id }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.credit_note">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Nota crédito
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.credit_note }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="modal.data.new_customer_code">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="people-arrows-left-right" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Cliente a facturar
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ modal.data.new_customer.RAZON_SOCIAL }}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="check-to-slot" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Estado contabilización
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        <span v-if="modal.data.accounted === 1"
                                              class="badge badge-success badge-rounded">
                                            Contabilizado
                                        </span>
                                        <span v-else
                                              class="badge badge-warning badge-rounded">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto box mt-5">
                        <table class="table table-bordered table-sm" v-if="modal.data.reason === 'discount' || modal.data.reason === 'major-value'">
                            <thead>
                            <tr class="text-center uppercase">
                                <th class="whitespace-nowrap">Bruto</th>
                                <th class="whitespace-nowrap">Descuento</th>
                                <th class="whitespace-nowrap">Subtotal</th>
                                <th class="whitespace-nowrap">IVA</th>
                                <th class="whitespace-nowrap">RETEFUENTE</th>
                                <th class="whitespace-nowrap">RETEIVA</th>
                                <th class="whitespace-nowrap">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-right">
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.BRUTO) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.DESCUENTO) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.SUBTOTAL) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.IVA) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.RTEFTE) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(modal.data.invoice.RTEIVA) }}</td>
                                <td class="text-right">{{ $h.formatCurrency(parseFloat(modal.data.invoice.SUBTOTAL) + parseFloat(modal.data.invoice.IVA)) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered table-sm" v-else>
                            <thead>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">Item</th>
                                <th class="whitespace-nowrap">Producto</th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'change'">Nuevo Producto</th>
                                <th class="whitespace-nowrap">Arte</th>
                                <th class="whitespace-nowrap">Marca</th>
                                <th class="whitespace-nowrap">Notas</th>
                                <th class="whitespace-nowrap">
                                    Cantidad
                                </th>
                                <th class="whitespace-nowrap"
                                    v-if="modal.data.reason === 'quantity' || modal.data.reason === 'change' || modal.data.reason === 'quantity-new-invoice' || modal.data.reason === 'product-change'">
                                    Cantidad NC
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'quantity-new-invoice'">
                                    Cantidad Facturar
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'reposition'">
                                    Cantidad Reponer
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'NA' && modal.data.action === 'manufacturing'
                                    || modal.data.action === 'reprocess'">
                                    Cantidad a reprocesar
                                </th>
                                <th class="whitespace-nowrap">Precio</th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'price'">
                                    Nuevo Precio
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in modal.data.items">
                                <td>
                                    {{ item.item }}
                                </td>
                                <td>
                                    {{ `${item.product.CodigoProducto} - ${item.product.DescripcionProducto}` }}
                                </td>
                                <td v-if="modal.data.reason === 'change'">
                                    {{ `${item.new_product.Pieza} - ${item.new_product.Descripcion}` }}
                                </td>
                                <td class="text-center">
                                    {{ item.product.ARTE }}
                                </td>
                                <td class="text-center">
                                    {{ item.product.Marca }}
                                </td>
                                <td>
                                    {{ item.notes }}
                                </td>
                                <td class="text-right">
                                    {{ item.product.Cantidad }}
                                </td>
                                <td class="text-right"
                                    v-if="modal.data.reason === 'quantity' || modal.data.reason === 'change' || modal.data.reason === 'quantity-new-invoice' || modal.data.reason === 'product-change'">
                                    {{ item.credit_note_quantity }}
                                </td>
                                <td class="text-right" v-if="modal.data.reason === 'quantity-new-invoice'">
                                    {{ item.new_quantity }}
                                </td>
                                <td class="text-right" v-if="modal.data.reason === 'reposition'">
                                    {{ item.reposition_quantity }}
                                </td>
                                <td class="text-right" v-if="modal.data.reason === 'NA' && modal.data.action === 'manufacturing'
                                    || modal.data.action === 'reprocess'">
                                    {{ item.delivered_quantity }}
                                </td>
                                <td class="text-right">
                                    {{ $h.formatCurrency(item.product.Precio) }}
                                </td>
                                <td class="text-right" v-if="modal.data.reason === 'price'">
                                    {{ $h.formatCurrency(item.new_price) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-secondary mr-auto"
                            @click="print(modal.data.hash)">
                        <font-awesome-icon icon="print"
                                           class="mr-2"/>
                        Imprimir
                    </button>

                    <button type="button"
                            class="btn btn-secondary mr-2"
                            @click="closeModal()">
                        Cerrar
                    </button>

                    <button @click="store_without_gestion()"
                            type="button"
                            class="btn btn-primary mr-2"
                            :disabled="modal.data.accounted === 1"
                            v-if="modal.data.state === 'wallet'">
                        Finalizar sin gestion
                    </button>

                    <button @click="store()"
                            type="button"
                            class="btn btn-primary"
                            :disabled="modal.data.accounted === 0"
                            v-if="modal.data.state === 'wallet'">
                        Finalizar gestión
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="modal.isOpenLog" @close="modal.isOpenLog = false" max-width=5xl>
                <template #title>
                    Logs reclamo {{ modal.data.consecutive }}
                </template>

                <template #content>
                    <div class="overflow-x-auto box">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">Descripción</th>
                                <th class="whitespace-nowrap">Usuario</th>
                                <th class="whitespace-nowrap">Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="log in modal.data.logs">
                                <tr v-if="log.type === 'log'">
                                    <td>{{ log.description }}</td>
                                    <td class="text-center">{{ log.user.name }}</td>
                                    <td class="text-center">{{
                                            $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a')
                                        }}
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="modal.isOpenLog = false" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="modal.isOpenComments" @close="modal.isOpenComments = false" max-width=5xl>
                <template #title>
                    Logs reclamo {{ modal.data.consecutive }}
                </template>

                <template #content>
                    <div class="chat__box box">
                        <div class="overflow-y-scroll scrollbar-hidden px-5 pt-5 flex-1 max-h-72 h-72">
                            <template v-if="modal.data.logs.filter(row => row.type === 'comment').length > 0"
                                      v-for="comment in modal.data.logs.filter(row => row.type === 'comment')">
                                <template v-if="comment.user.id === $page.props.user.id">
                                    <div class="chat__box__text-box flex items-end float-right mb-4">
                                        <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md">
                                            {{ comment.description }}
                                            <div class="mt-1 text-xs text-white text-opacity-80">
                                                {{ $h.formatDate(comment.created_at, 'YYYY-MM-DD hh:mm a') }}
                                            </div>
                                        </div>
                                        <div class="w-10 h-10 hidden sm:block flex-none image-fit relative ml-5">
                                            <Tippy
                                                tag="img"
                                                :content="comment.user.name"
                                                class="rounded-full"
                                                alt="user photo"
                                                :src="comment.user.profile_photo_url"
                                            />
                                        </div>
                                    </div>
                                    <div class="clear-both"></div>

                                </template>

                                <template v-else>
                                    <div class="chat__box__text-box flex items-end float-left mb-4">
                                        <div class="w-10 h-10 hidden sm:block flex-none image-fit relative mr-5">
                                            <Tippy
                                                tag="img"
                                                :content="comment.user.name"
                                                class="rounded-full"
                                                alt="user photo"
                                                :src="comment.user.profile_photo_url"
                                            />
                                        </div>
                                        <div
                                            class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md">
                                            {{ comment.description }}
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $h.formatDate(comment.created_at, 'YYYY-MM-DD hh:mm a') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear-both"></div>
                                </template>
                            </template>

                            <template v-else>
                                <div class="text-slate-400 dark:text-slate-500 text-md text-center mb-10 mt-10">
                                    Aun no se agrega ningún comentario
                                </div>
                            </template>
                        </div>
                        <div
                            class="pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400"
                            @keypress.enter.prevent="addComment(msg)">
                            <textarea
                                class="chat__box__input form-control dark:bg-darkmode-600 h-12 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                                rows="1"
                                placeholder="Escribe un comentario..."
                                v-model="msg"
                            ></textarea>
                            <a href="javascript:;"
                               class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5"
                               @click="addComment(msg)">
                                <SendIcon class="lucide w-4 h-4"/>
                            </a>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="modal.isOpenComments = false" type="button" class="btn btn-secondary">
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

export default {
    props: {
        claims: Array,
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    data() {
        return {
            table: {
                data: this.claims,
                columns: [
                    'consecutive',
                    'document',
                    'credit_note',
                    'customer',
                    'destiny_esp',
                    'action_esp',
                    'reason_esp',
                    'accounted',
                    'user',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        consecutive: "#",
                        document: "FACTURA",
                        credit_note: "NOTA CREDITO",
                        customer: "CLIENTE",
                        destiny_esp: "DESTINO",
                        action_esp: "ACCION",
                        reason_esp: "RAZON",
                        accounted: "¿CONTABILIZADO?",
                        user: "CREADO POR",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    clientSorting: false,
                    sortable: ['id', 'document', 'customer', 'destiny_esp', 'action_esp', 'reason_esp', 'user'],
                    templates: {
                        credit_note(h, row){
                            return row.credit_note ?? '–'
                        },
                        accounted: function (h, row) {
                            return row.accounted === 1
                                ? <span class="badge badge-success badge-rounded">Contabilizado</span>
                                : <span class="badge badge-warning badge-rounded">Pendiente</span>
                        },
                        user: function (h, row) {
                            return row.user.name
                        },
                        customer: function (h, row) {
                            return row.invoice?.RAZONSOCIAL
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    },
                    cellClasses: {
                        credit_note: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                }
            },
            modal: {
                isOpen: false,
                data: {}
            },
            msg: '',
        }
    },

    methods: {
        closeModal() {
            this.modal = {
                isOpen: false,
                isOpenLog: false,
                isOpenComments: false,
                data: {}
            }
        },

        view(hash) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando Información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('claim.show', hash)).then(resp => {
                this.$swal.close()
                this.modal = {
                    isOpen: true,
                    isOpenLog: false,
                    isOpenComments: false,
                    data: resp.data
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err);
            })
        },

        addComment(msg) {
            if (msg.length > 0) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });
                axios.post(route('claim.add-comment'), {
                    msg: msg,
                    hash: this.modal.data.hash
                }).then(resp => {
                    this.modal.data.logs = resp.data
                    this.msg = ''
                    this.$swal.close()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        timer: 5000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err.data);
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'El comentario no puede ir vacío.',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'Aceptar',
                });
            }
        },

        downloadFile(file) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Archivo…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('claim.download-file'), {
                id: file.id
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', file.name);
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
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

        downloadInvoice(invoice){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Documento…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('electronic-billing.documents.download', invoice)).then(resp => {
                const link = document.createElement('a');

                link.download = invoice;
                link.href = 'data:application/pdf;base64,' + resp.data;
                link.click();

                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err)
            })
        },

        store(){
            this.$swal({
                title: '¿Finalizar gestión?',
                text: "¡Esta acción no es reversible!. Recuerde que este proceso subirá el documento a la DIAN y posteriormente lo cargara a DMS",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, Finalizar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos... Espere por favor',
                    });

                    axios.post(route('claim.wallet-store', this.modal.data.hash)).then(resp => {
                        this.table.data = resp.data
                        this.closeModal()
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Reclamo finalizado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });

                        console.log(err);
                    })
                }
            })
        },

        store_without_gestion(){
            this.$swal({
                title: '¿Finalizar reclamo sin gestion?',
                text: "¡Esta acción no es reversible!. Recuerde que este proceso NO subirá el documento a la DIAN ni a DMS",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, Finalizar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos... Espere por favor',
                    });

                    axios.post(route('claim.wallet-store-without-gestion', this.modal.data.hash)).then(resp => {
                        this.table.data = resp.data
                        this.closeModal()
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Reclamo finalizado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });

                        console.log(err);
                    })
                }
            })
        },

        print(hash) {
            window.open(route('claim.print', hash), '_blank').focus();
        }
    },

    computed: {
        wallet_rows() {
            return this.table.data.filter(row => row.state === 'wallet').sort((a, b) => {
                return a.consecutive - b.consecutive
            });
        },
        finish_rows() {
            return this.table.data.filter(row => row.state === 'finish').sort((a, b) => {
                return b.consecutive - a.consecutive
            });
        },
    }
}
</script>

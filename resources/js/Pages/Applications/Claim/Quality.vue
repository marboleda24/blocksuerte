<template>
    <div>
        <Head title="Gestión reclamos calidad"/>

        <portal to="application-title">
            Gestión reclamos calidad
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
                        <v-client-table :data="quality_rows" :columns="table.columns" :options="table.options">
                            <template v-slot:actions="{row}">
                                <div class="dropdown text-center">
                                    <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                            data-tw-toggle="dropdown">
                                        <font-awesome-icon icon="bars"/>
                                    </button>
                                    <div class="dropdown-menu w-32">
                                        <div class="dropdown-content">
                                            <a href="javascript:void(0)"
                                               @click="view(row.hash)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                                Gestionar
                                            </a>

                                            <a href="javascript:void(0)"
                                               @click="refuse(row.hash)"
                                               class="dropdown-item">
                                                <font-awesome-icon :icon="['fas', 'ban']" class="mr-1"/>
                                                Rechazar
                                            </a>
                                        </div>
                                    </div>
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

                                <div class="flex mt-4 lg:mt-0">
                                    <button class="btn btn-secondary py-1 px-2" @click="modal.isOpenCauses = true">
                                        Modificar
                                    </button>
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
                                    <font-awesome-icon icon="comment" size="3x"/>
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
                    </div>

                    <div class="overflow-x-auto box mt-5">
                        <table class="table table-bordered table-sm" v-if="modal.data.reason === 'discount'">
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
                                <th class="whitespace-nowrap">
                                    Item
                                </th>
                                <th class="whitespace-nowrap">
                                    Producto
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'change'">
                                    Nuevo Producto
                                </th>
                                <th class="whitespace-nowrap">
                                    Arte
                                </th>
                                <th class="whitespace-nowrap">
                                    Marca
                                </th>
                                <th class="whitespace-nowrap">
                                    Notas
                                </th>
                                <th class="whitespace-nowrap">
                                    Cantidad
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'quantity'">
                                    Cantidad Nota
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'reposition'">
                                    Cantidad Reponer
                                </th>
                                <th class="whitespace-nowrap" v-if="modal.data.reason === 'NA' && modal.data.action === 'manufacturing'
                                    || modal.data.action === 'reprocess'">
                                    Cantidad a reprocesar
                                </th>
                                <th class="whitespace-nowrap">
                                    Precio
                                </th>
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
                                <td class="text-right" v-if="modal.data.reason === 'quantity' || modal.data.reason === 'change'">
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

                    <div class="box mt-5 p-5" v-if="modal.data.state === 'quality'">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">
                                    Area responsable
                                </label>

                                <select class="form-select form-select-sm"
                                        :class="{ 'border-danger': v$.form.workplace_id.$error }"
                                        v-model="form.workplace_id">
                                    <option value="">seleccione…</option>
                                    <option v-for="workplace in workplaces" :value="workplace.id">{{ workplace.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">
                                    Orden de producción
                                </label>

                                <input type="text"
                                       class="form-control form-control-sm"
                                       v-model="form.production_order">
                            </div>

                            <div>
                                <label class="form-label">
                                    Observación
                                </label>

                                <input type="text"
                                       class="form-control form-control-sm"
                                       :class="{ 'border-danger': v$.form.quality_observation.$error }"
                                       v-model="form.quality_observation">
                            </div>
                        </div>
                    </div>

                </template>

                <template #footer>
                    <button class="btn btn-secondary mr-auto"
                            @click="print(modal.data.hash)">
                        <font-awesome-icon icon="print"
                                           class="mr-2"/>
                        Imprimir
                    </button>

                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cerrar
                    </button>

                    <button @click="store(form)"
                            type="button"
                            class="btn btn-primary"
                            v-if="modal.data.state === 'quality'">
                        Registrar gestión
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
                    Comentarios reclamo {{ modal.data.consecutive }}
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

            <jet-dialog-modal :show="modal.isOpenCauses" @close="modal.isOpenCauses = false" max-width=3xl>
                <template #title>
                    Modificar causas {{ modal.data.consecutive }}
                </template>

                <template #content>
                    <TomSelect v-model="formCauses.causes"
                               class="w-full"
                               :class="{ 'border-danger': v$.formCauses.causes.$error }"
                               :options="{
                                   maxItems: 2,
                                   placeholder: 'seleccione o busque una causa (max 2)'
                               }"
                               multiple>
                        <option v-for="cause in causes.filter(element => element[modal.data.destiny] === 1)" :value="cause.id">{{ cause.name }}</option>
                    </TomSelect>
                </template>

                <template #footer>
                    <button @click="modal.isOpenCauses = false" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>

                    <button class="btn btn-primary ml-2" @click="updateCauses">
                        Actualizar
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
import {required, minLength, maxLength} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        claims: Array,
        workplaces: Array,
        causes: Array
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    validations() {
        return {
            form: {
                workplace_id: {required},
                quality_observation: {required}
            },
            formCauses: {
                causes: {
                    required,
                    minLength: minLength(1),
                    maxLength: maxLength(2)
                },
            }
        }
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
                isOpenLog: false,
                isOpenComments: false,
                isOpenCauses: false,
                data: {}
            },
            msg: '',
            form: {
                workplace_id: '',
                production_order: null,
                quality_observation: null
            },
            formCauses: {
                causes: []
            }
        }
    },

    methods: {
        closeModal() {
            this.modal = {
                isOpen: false,
                isOpenLog: false,
                isOpenComments: false,
                isOpenCauses: false,
                data: {}
            }
            this.form = {
                workplace_id: '',
                production_order: null,
                quality_observation: null
            }

            this.formCauses.causes = []
            this.v$.form.$reset()
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

                this.formCauses.causes = resp.data.causes.map((row) => row.id)
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

        refuse(hash) {
            this.$swal({
                title: '¿Rechazar reclamo?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, rechazar',
                inputValidator: (inputValue) => {
                    return !inputValue && 'La justificación es obligatoria'
                }
            }).then((inputValue) => {
                if (inputValue.value) {


                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('claim.quality-refuse'), {
                        hash: hash,
                        justify: inputValue.value
                    }).then(res => {
                        this.table.data = res.data;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Reclamo rechazado con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err);
                    })
                }else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
        },

        store(form) {
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('claim.quality-store', this.modal.data.hash), form).then(resp => {
                    this.table.data = resp.data
                    this.closeModal()
                    this.$swal({
                        title: '¡Éxito!',
                        text: "¡Reclamo gestionado con éxito!",
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
        },

        updateCauses(){
            this.v$.formCauses.$touch();

            if (this.v$.formCauses.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('claim.update-causes', this.modal.data.hash), this.formCauses).then(resp => {
                    this.table.data = resp.data
                    this.closeModal()
                    this.$swal({
                        title: '¡Éxito!',
                        text: "¡Causas actualizadas con éxito!",
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
                })
            }
        },

        print(id_encode) {
            window.open(route('claim.print', id_encode), '_blank').focus();
        }
    },

    computed: {
        quality_rows() {
            return this.table.data.filter(row => row.state === 'quality').sort((a, b) => {
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


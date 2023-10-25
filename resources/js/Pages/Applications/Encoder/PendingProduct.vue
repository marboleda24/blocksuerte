<template>
    <div>
        <Head title="Pendientes clonación"/>

        <portal to="application-title">
            Pendientes clonación
        </portal>

        <div>
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                            class="overflow-y-auto">

                <template v-slot:art_code="{row}">
                    <div class="text-center">
                        <a class="btn btn-sm btn-secondary"
                           :href="route('arts.print', [row.art_code, 'code'])"
                           target="_blank">
                            {{ row.art_code }}
                        </a>
                    </div>
                </template>

                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-sm btn-secondary mr-2" @click="openEditionModal(row)">
                            <font-awesome-icon :icon="['far', 'pen-to-square']"/>
                        </button>

                        <button class="btn btn-sm btn-secondary" @click="openModal(row)">
                            <font-awesome-icon :icon="['far', 'clone']"/>
                        </button>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="modal.open" max-width="3xl" @close="closeModal">
                <template #title>
                    Clonación de estructuras para pedidos
                </template>

                <template #content>
                    <div class="alert alert-warning show mb-2 shadow-sm"
                         role="alert">
                        <div class="flex items-center">
                            <div class="font-medium text-lg uppercase">
                                ¡Información importante!
                            </div>
                        </div>
                        <div class="mt-0">
                            Este proceso creara un producto en Codificador de EVPIU, en MAX y una vez terminado el
                            proceso el vendedor podrá ver el producto final en su aplicación de pedidos para continuar
                            con el proceso normal de pedidos.
                        </div>
                    </div>

                    <div class="post intro-y overflow-hidden box mt-5">
                        <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                            role="tablist">
                            <li class="nav-item w-full">
                                <button
                                    id="master-tab"
                                    data-tw-toggle="tab"
                                    data-tw-target="#master"
                                    href="javascript:;"
                                    class="nav-link tooltip w-full py-4 active"
                                    role="tab"
                                    aria-controls="content"
                                    aria-selected="true"
                                >
                                    <FileTextIcon class="w-4 h-4 mr-2"/>
                                    Maestro
                                </button>
                            </li>

                            <li class="nav-item w-full">
                                <button
                                    id="engineering-tab"
                                    data-tw-toggle="tab"
                                    data-tw-target="#engineering"
                                    href="javascript:;"
                                    class="nav-link tooltip w-full py-4"
                                    role="tab"
                                    aria-controls="content"
                                    aria-selected="false"
                                >
                                    <FileTextIcon class="w-4 h-4 mr-2"/>
                                    Ingeniería
                                </button>
                            </li>

                            <li class="nav-item w-full">
                                <button
                                    id="planner-tab"
                                    data-tw-toggle="tab"
                                    data-tw-target="#planner"
                                    href="javascript:;"
                                    class="nav-link tooltip w-full py-4"
                                    role="tab"
                                    aria-controls="content"
                                    aria-selected="false"
                                >
                                    <FileTextIcon class="w-4 h-4 mr-2"/>
                                    Planificador
                                </button>
                            </li>

                            <li class="nav-item w-full">
                                <button
                                    id="inventory-tab"
                                    data-tw-toggle="tab"
                                    data-tw-target="#inventory"
                                    href="javascript:;"
                                    class="nav-link tooltip w-full py-4"
                                    role="tab"
                                    aria-controls="content"
                                    aria-selected="true"
                                >
                                    <FileTextIcon class="w-4 h-4 mr-2"/>
                                    Inventario
                                </button>
                            </li>
                        </ul>

                        <div class="mt-2 p-5 border-b grid grid-cols-2 gap-4">
                            <input type="text" class="form-control border-success"
                                   v-model="form.master.description" disabled>

                            <autocomplete
                                url="/cloner/search-product-max"
                                show-field="PMDES1_01"
                                @selected-value="select_max_item"
                                @reset-val="resetForm"
                            />
                        </div>

                        <div class="post__content tab-content">
                            <div
                                id="master"
                                class="tab-pane p-5 active"
                                role="tabpanel"
                                aria-labelledby="master-tab"
                            >
                                <div class="grid grid-cols-3 gap-6">

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Tipo Pieza
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                                Obligatorio
                                            </span>
                                        </label>
                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.type.$error }"
                                                v-model="form.master.type">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option value="A">A - Pieza Fabricada Control MRP</option>
                                            <option value="B">B - Pieza Comprada Control MRP</option>
                                            <option value="C">C - Pieza Fabricada Control PDR</option>
                                            <option value="D">D - Pieza Comprada Control PDR</option>
                                            <option value="E">E - Objeto Mantenimiento</option>
                                            <option value="F">F - Familia de Productos</option>
                                            <option value="M">M - Pieza de Programada Maestro</option>
                                            <option value="O">O - Pieza Subcontratada</option>
                                            <option value="P">P - Ensamble Fantasma</option>
                                            <option value="R">R - Recurso</option>
                                            <option value="S">S - Seudo Pieza</option>
                                            <option value="T">T - Herramienta</option>
                                            <option value="X">X - Pieza Fabricada a Granel</option>
                                            <option value="Y">Y - Pienza Comprada a Granel</option>
                                        </select>

                                        <template v-if="v$.form.master.type.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.type.$errors" :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Comprador
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.buyer.$error }"
                                                v-model="form.master.buyer">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="buyer in buyers" :value="buyer.id">{{ buyer.name }}</option>
                                        </select>

                                        <template v-if="v$.form.master.buyer.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.buyer.$errors" :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Planificador
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.planner.$error }"
                                                v-model="form.master.planner">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="planner in planners" :value="planner.id">{{
                                                    planner.name
                                                }}
                                            </option>
                                        </select>

                                        <template v-if="v$.form.master.planner.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.planner.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Almacen preferido
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.warehouse.$error }"
                                                v-model="form.master.warehouse">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="warehouse in warehouses" :value="warehouse.id.trim()">
                                                {{ `${warehouse.id.trim()} - ${warehouse.name.trim()}` }}
                                            </option>
                                        </select>

                                        <template v-if="v$.form.master.warehouse.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.warehouse.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            UMD LDM
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.umd_ldm.$error }"
                                                v-model="form.master.umd_ldm">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="KG">KG - Kilogramos</option>
                                            <option value="UN">UN - Unidad</option>
                                        </select>

                                        <template v-if="v$.form.master.umd_ldm.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.umd_ldm.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            UMD Costo
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.udm_cost.$error }"
                                                v-model="form.master.udm_cost">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="KG">KG - Kilogramos</option>
                                            <option value="UN">UN - Unidad</option>
                                        </select>

                                        <template v-if="v$.form.master.udm_cost.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.udm_cost.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Código Clase
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.cod_class.$error }"
                                                v-model="form.master.cod_class">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="class_code in class_codes" :value="class_code.id">
                                                {{ class_code.name }}
                                            </option>
                                        </select>

                                        <template v-if="v$.form.master.cod_class.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.cod_class.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Código Comodidad
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.master.comfort_code.$error }"
                                                v-model="form.master.comfort_code">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="comfort_code in comfort_codes"
                                                    :value="comfort_code.id.trim()">
                                                {{ comfort_code.id.trim() }}
                                            </option>
                                        </select>

                                        <template v-if="v$.form.master.comfort_code.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.comfort_code.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Inventario
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.inventory"
                                               disabled>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Costo Unitario
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <input type="text" class="form-control"
                                               :class="{ 'border-danger': v$.form.master.unit_cost.$error }"
                                               v-model="form.master.unit_cost">

                                        <template v-if="v$.form.master.unit_cost.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.master.unit_cost.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Zona
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.zone">
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Nivel Revision
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.revision_level">
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            TC Compras
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.tc_shopping">
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            TC Manufactura
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.tc_manufacture">
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            CIV de CDU
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.civ_cdu" disabled>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Referencia CDU
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.master.ref_cdu" disabled>
                                    </div>
                                </div>
                            </div>

                            <div
                                id="engineering"
                                class="tab-pane p-5"
                                role="tabpanel"
                                aria-labelledby="engineering-tab"
                            >
                                <div class="grid grid-cols-3 gap-6">
                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Numero Plano
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="text" class="form-control" v-model="form.engineering.plane">
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Porcentaje de Rendimiento
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.engineering.yield.$error }"
                                               v-model="form.engineering.yield">

                                        <template v-if="v$.form.engineering.yield.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.engineering.yield.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Porcentaje de Desecho
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.engineering.waste.$error }"
                                               v-model="form.engineering.waste">

                                        <template v-if="v$.form.engineering.waste.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.engineering.waste.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Estado
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.engineering.state.$error }"
                                                v-model="form.engineering.state">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="1">1 - En Proyecto</option>
                                            <option value="2">2 - En Producción</option>
                                            <option value="3">3 - No Definido</option>
                                            <option value="4">4 - No Definido</option>
                                            <option value="5">5 - Obsoleto</option>
                                        </select>

                                        <template v-if="v$.form.engineering.state.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.engineering.state.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            CBN
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Requerido
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.engineering.cbn.$error }"
                                               v-model="form.engineering.cbn">

                                        <template v-if="v$.form.engineering.cbn.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.engineering.cbn.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Tipo Cuenta Contable
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.engineering.account_type.$error }"
                                                v-model="form.engineering.account_type">
                                            <option value="" selected="selected">Seleccione...</option>
                                            <option v-for="account_type in account_types"
                                                    :value="account_type.id.trim()">
                                                {{ `${account_type.id.trim()} - ${account_type.name.trim()}` }}
                                            </option>
                                        </select>

                                        <template v-if="v$.form.engineering.account_type.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.engineering.account_type.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div
                                id="planner"
                                class="tab-pane p-5"
                                role="tabpanel"
                                aria-labelledby="planner-tab"
                            >
                                <div class="grid grid-cols-8 gap-6">
                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Politica de Orden
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Requerido
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.planner.order_policy.$error }"
                                                v-model="form.planner.order_policy">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="D">D - Discreta</option>
                                            <option value="F">F - Fija</option>
                                            <option value="L">L - Lote por Lote</option>
                                            <option value="O">O - Orden</option>
                                            <option value="P">P - Periodo</option>
                                            <option value="R">R - Punto Reorden</option>
                                            <option value="W">W - Semanal</option>
                                        </select>

                                        <template v-if="v$.form.planner.order_policy.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.order_policy.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Programa
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <select class="form-select"
                                                :class="{ 'border-danger': v$.form.planner.program.$error }"
                                                v-model="form.planner.program">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="R">R - Ruta</option>
                                            <option value="Q">Q - Cola</option>
                                        </select>

                                        <template v-if="v$.form.planner.program.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.program.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            TC Critico
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.planner.critical_tc.$error }"
                                               v-model="form.planner.critical_tc">

                                        <template v-if="v$.form.planner.critical_tc.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.critical_tc.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            PRD
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.planner.prd.$error }"
                                               v-model="form.planner.prd">

                                        <template v-if="v$.form.planner.prd.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.prd.$errors" :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            CRD
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.planner.crd.$error }"
                                               v-model="form.planner.crd">

                                        <template v-if="v$.form.planner.crd.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.crd.$errors" :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-2">
                                        <label class="flex flex-col sm:flex-row">
                                            Inventario de Seguridad
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.planner.safety_inventory.$error }"
                                               v-model="form.planner.safety_inventory">

                                        <template v-if="v$.form.planner.safety_inventory.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.planner.safety_inventory.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 form-switch">
                                        <label class="flex flex-col sm:flex-row">
                                            Plan Firme
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="checkbox" class="form-check-input mt-2"
                                               v-model="form.planner.firm_plan">
                                    </div>

                                    <div class="mt-2 form-switch">
                                        <label class="flex flex-col sm:flex-row">
                                            NCND
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="checkbox" class="form-check-input mt-2"
                                               v-model="form.planner.ncnd">
                                    </div>

                                    <div class="mt-2 form-switch">
                                        <label class="flex flex-col sm:flex-row">
                                            RUMP
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="checkbox" class="form-check-input mt-2"
                                               v-model="form.planner.rump">
                                    </div>

                                    <div class="mt-2 form-switch">
                                        <label class="flex flex-col sm:flex-row">
                                            Pieza Critica
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="checkbox" class="form-check-input mt-2"
                                               v-model="form.planner.critical_piece">
                                    </div>
                                </div>

                                <fieldset class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 mt-4">
                                    <legend class="text-sm">Fabricacion</legend>
                                    <div class="grid grid-cols-4 gap-6">
                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Tiempo Ciclo
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.manufacturing.cycle_time.$error }"
                                                   v-model="form.planner.manufacturing.cycle_time">

                                            <template v-if="v$.form.planner.manufacturing.cycle_time.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.manufacturing.cycle_time.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Planear
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.manufacturing.plan.$error }"
                                                   v-model="form.planner.manufacturing.plan">

                                            <template v-if="v$.form.planner.manufacturing.plan.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.manufacturing.plan.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Fabricar
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.manufacturing.manufacture.$error }"
                                                   v-model="form.planner.manufacturing.manufacture">

                                            <template v-if="v$.form.planner.manufacturing.manufacture.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.manufacturing.manufacture.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Almacenar
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.manufacturing.stock.$error }"
                                                   v-model="form.planner.manufacturing.stock">

                                            <template v-if="v$.form.planner.manufacturing.stock.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.manufacturing.stock.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 mt-4">
                                    <legend class="text-sm">Compras</legend>
                                    <div class="grid grid-cols-4 gap-6">
                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Tiempo Ciclo
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.purchases.cycle_time.$error }"
                                                   v-model="form.planner.purchases.cycle_time">

                                            <template v-if="v$.form.planner.purchases.cycle_time.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.purchases.cycle_time.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Planear
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.purchases.plan.$error }"
                                                   v-model="form.planner.purchases.plan">

                                            <template v-if="v$.form.planner.purchases.plan.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.purchases.plan.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Comprar
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.purchases.purchase.$error }"
                                                   v-model="form.planner.purchases.purchase">

                                            <template v-if="v$.form.planner.purchases.purchase.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.purchases.purchase.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Almacenar
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.purchases.stock.$error }"
                                                   v-model="form.planner.purchases.stock">

                                            <template v-if="v$.form.planner.purchases.stock.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.purchases.stock.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 mt-4">
                                    <legend class="text-sm">Cantidad de Orden</legend>
                                    <div class="grid grid-cols-4 gap-6">
                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Promedio
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.order_quantity.average.$error }"
                                                   v-model="form.planner.order_quantity.average"/>

                                            <template v-if="v$.form.planner.order_quantity.average.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.order_quantity.average.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Minima
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.order_quantity.min.$error }"
                                                   v-model="form.planner.order_quantity.min">

                                            <template v-if="v$.form.planner.order_quantity.min.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.order_quantity.min.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Maxima
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.order_quantity.max.$error }"
                                                   v-model="form.planner.order_quantity.max">

                                            <template v-if="v$.form.planner.order_quantity.max.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.order_quantity.max.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Multiple
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.planner.order_quantity.multiple.$error }"
                                                   v-model="form.planner.order_quantity.multiple">

                                            <template v-if="v$.form.planner.order_quantity.multiple.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.planner.order_quantity.multiple.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div
                                id="inventory"
                                class="tab-pane p-5"
                                role="tabpanel"
                                aria-labelledby="inventory-tab"
                            >
                                <div class="grid grid-cols-11 gap-6">
                                    <div class="mt-2 col-span-2 form-switch">
                                        <label class="flex flex-col sm:flex-row">
                                            Requiere Inspeccion
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="checkbox" class="form-check-input mt-2"
                                               v-model="form.inventory.requires_inspection">
                                    </div>

                                    <div class="mt-2 col-span-3">
                                        <label class="flex flex-col sm:flex-row">
                                            Exceso Entradas
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>
                                        <input type="number" class="form-control"
                                               v-model="form.inventory.excess_tickets">
                                    </div>

                                    <div class="mt-2 col-span-3">
                                        <label class="flex flex-col sm:flex-row">
                                            Peso Promedio
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <input type="number" class="form-control"
                                               :class="{ 'border-danger': v$.form.inventory.average_weight.$error }"
                                               v-model="form.inventory.average_weight">

                                        <template v-if="v$.form.inventory.average_weight.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.inventory.average_weight.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-2 col-span-3">
                                        <label class="flex flex-col sm:flex-row">
                                            UDM Peso
                                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                        </label>

                                        <select class="form-control"
                                                :class="{ 'border-danger': v$.form.inventory.udm_weight.$error }"
                                                v-model="form.inventory.udm_weight">
                                            <option value="" selected>Seleccione...</option>
                                            <option value="UN">UN - Unidad</option>
                                            <option value="KG">KG - Kilogramo</option>
                                        </select>

                                        <template v-if="v$.form.inventory.udm_weight.$error">
                                            <ul class="mt-1">
                                                <li class="text-danger"
                                                    v-for="(error, index) of v$.form.inventory.udm_weight.$errors"
                                                    :key="index">
                                                    {{ error.$message }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>
                                </div>

                                <fieldset class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 mt-4">
                                    <legend class="text-sm">Seguimiento de Lotes / Serial</legend>
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Dias Vencimiento
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   v-model="form.inventory.expiration_days">
                                        </div>

                                        <div class="mt-2 form-switch">
                                            <label class="flex flex-col sm:flex-row">
                                                Control Lote
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="checkbox" class="form-check-input mt-2"
                                                   v-model="form.inventory.batch_control">
                                        </div>

                                        <div class="mt-2 form-switch">
                                            <label class="flex flex-col sm:flex-row">
                                                Control N/S
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="checkbox" class="form-check-input mt-2"
                                                   v-model="form.inventory.control_ns">
                                        </div>

                                        <div class="mt-2 form-switch">
                                            <label class="flex flex-col sm:flex-row">
                                                Multi Entradas
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="checkbox" class="form-check-input mt-2"
                                                   v-model="form.inventory.multi_inputs">
                                        </div>

                                        <div class="mt-2 form-switch">
                                            <label class="flex flex-col sm:flex-row">
                                                Lote CDP
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="checkbox" class="form-check-input mt-2"
                                                   v-model="form.inventory.cdp_lot">
                                        </div>

                                        <div class="mt-2 form-switch">
                                            <label class="flex flex-col sm:flex-row">
                                                N/S CDP
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <input type="checkbox" class="form-check-input mt-2"
                                                   v-model="form.inventory.ns_cdp">
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 mt-4">
                                    <legend class="text-sm">Seguimiento de Lotes / Serial</legend>
                                    <div class="grid grid-cols-3 gap-6">
                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Código
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>
                                            <select class="form-control"
                                                    :class="{ 'border-danger': v$.form.inventory.cycle_count.code.$error }"
                                                    v-model="form.inventory.cycle_count.code">
                                                <option value="" selected>Seleccione...</option>
                                                <option value="N">N - Ninguno</option>
                                                <option value="D">D - Diario</option>
                                                <option value="W">W - Semanal</option>
                                                <option value="M">M - Mensual</option>
                                                <option value="Q">Q - Trimestral</option>
                                                <option value="B">B - Semestral</option>
                                                <option value="A">A - Anual</option>
                                            </select>

                                            <template v-if="v$.form.inventory.cycle_count.code.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.inventory.cycle_count.code.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Tolerancia ($)
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Requerido
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.inventory.cycle_count.tolerance.$error }"
                                                   v-model="form.inventory.cycle_count.tolerance">

                                            <template v-if="v$.form.inventory.cycle_count.tolerance.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.inventory.cycle_count.tolerance.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>

                                        <div class="mt-2">
                                            <label class="flex flex-col sm:flex-row">
                                                Tolerancia (%)
                                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                            </label>

                                            <input type="number" class="form-control"
                                                   :class="{ 'border-danger': v$.form.inventory.cycle_count.tolerance_percentage.$error }"
                                                   v-model="form.inventory.cycle_count.tolerance_percentage">

                                            <template v-if="v$.form.inventory.cycle_count.tolerance_percentage.$error">
                                                <ul class="mt-1">
                                                    <li class="text-danger"
                                                        v-for="(error, index) of v$.form.inventory.cycle_count.tolerance_percentage.$errors"
                                                        :key="index">
                                                        {{ error.$message }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="save(form)" type="submit" class="btn btn-primary">
                        Procesar producto
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="editionModal.open" max-width="3xl" @close="closeModal">
                <template #title>
                    Modificar producto
                </template>

                <template #content>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">Codigo</label>
                            <input type="text" class="form-control" :value="editionModal.form.product.code" disabled>
                        </div>

                        <div>
                            <label class="form-label">Descripcion</label>
                            <input type="text" class="form-control" :value="editionModal.form.product.description" disabled>
                        </div>

                        <div>
                            <label class="form-label">Acabado Galvanico</label>
                            <TomSelect v-model="editionModal.form.galvanic_finish_code"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.editionModal.form.galvanic_finish_code.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="galvanic_finis in galvanic_finishes" :value="galvanic_finis.code">{{ galvanic_finis.name }}</option>
                            </TomSelect>
                        </div>

                        <div>
                            <label class="form-label">Opcion Decorativa</label>
                            <TomSelect v-model="editionModal.form.decorative_option_code"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.editionModal.form.decorative_option_code.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="decorative_option in decorative_options" :value="decorative_option.code">{{ decorative_option.name }}</option>
                            </TomSelect>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="updateProduct()" type="button" class="btn btn-primary">
                        Guardar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head, Link} from "@inertiajs/vue3";
import useVuelidate from '@vuelidate/core'
import {required, minValue, maxValue} from '@/utils/i18n-validators'
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";

const CancelToken = axios.CancelToken;
let source;
export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        data: Array,
        buyers: Array,
        planners: Array,
        warehouses: Array,
        class_codes: Array,
        comfort_codes: Array,
        account_types: Array,
        galvanic_finishes: Array,
        decorative_options: Array
    },

    components: {
        JetDialogModal,
        Head,
        Link,
        Autocomplete
    },

    validations() {
        return {
            form: {
                from_piece: {required},
                master: {
                    description: {required},
                    type: {required},
                    buyer: {required},
                    planner: {required},
                    warehouse: {required},
                    umd_ldm: {required},
                    udm_cost: {required},
                    cod_class: {required},
                    comfort_code: {required},
                    unit_cost: {required, minValue: minValue(0)}
                },
                engineering: {
                    state: {required},
                    account_type: {required},
                    yield: {required, minValue: minValue(0), maxValue: maxValue(100)},
                    waste: {required, minValue: minValue(0), maxValue: maxValue(100)},
                    cbn: {required, minValue: minValue(0)},
                },
                planner: {
                    order_policy: {required},
                    program: {required},
                    critical_tc: {required, minValue: minValue(0)},
                    prd: {required, minValue: minValue(0)},
                    crd: {required, minValue: minValue(0)},
                    safety_inventory: {required, minValue: minValue(0)},
                    manufacturing: {
                        cycle_time: {required, minValue: minValue(0)},
                        plan: {required, minValue: minValue(0)},
                        manufacture: {required, minValue: minValue(0)},
                        stock: {required, minValue: minValue(0)}
                    },
                    purchases: {
                        cycle_time: {required, minValue: minValue(0)},
                        plan: {required, minValue: minValue(0)},
                        purchase: {required, minValue: minValue(0)},
                        stock: {required, minValue: minValue(0)}
                    },
                    order_quantity: {
                        average: {required, minValue: minValue(0)},
                        min: {required, minValue: minValue(0)},
                        max: {required, minValue: minValue(0)},
                        multiple: {required, minValue: minValue(0)}
                    }
                },
                inventory: {
                    average_weight: {required, minValue: minValue(0)},
                    udm_weight: {required},
                    cycle_count: {
                        code: {required},
                        tolerance: {required, minValue: minValue(0)},
                        tolerance_percentage: {required, minValue: minValue(0), maxValue: maxValue(100)},
                    }
                }
            },
            editionModal: {
                form: {
                    product: {required},
                    galvanic_finish_code: {required},
                    decorative_option_code: {required},
                }
            }
        }

    },

    data() {
        return {
            table: {
                data: this.data,
                columns: [
                    'code',
                    'description',
                    'product_type',
                    'line',
                    'subline',
                    'feature',
                    'material',
                    'measurement',
                    'galvanic_finish',
                    'decorative_option',
                    'art_code',
                    'brand',
                    'notes',
                    'actions'
                ],
                options: {
                    headings: {
                        code: 'CÓDIGO',
                        description: 'DESCRIPCIÓN',
                        base_cost: 'COSTO BASE',
                        product_type: 'TIPO PRODUCTO',
                        line: 'LINEA',
                        subline: 'SUBLINEA',
                        feature: 'CARACTERISTICA',
                        material: 'MATERIAL',
                        measurement: 'MEDIDA',
                        galvanic_finish: 'A. GALVANICO',
                        decorative_option: 'OPC.DECORATIVA',
                        art_code: 'ARTE',
                        brand: 'MARCA',
                        notes: 'NOTAS',
                        actions: '',
                    },

                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['code', 'description', 'product_type', 'line', 'subline', 'feature',
                        'material', 'measurement', 'galvanic_finish', 'decorative_option', 'brand'
                    ],
                    templates: {
                        product_type(h, row) {
                            return row.product_type.name
                        },
                        line(h, row) {
                            return row.line.name
                        },
                        subline(h, row) {
                            return row.subline.name
                        },
                        feature(h, row) {
                            return row.feature.name
                        },
                        material(h, row) {
                            return row.material.material.name
                        },
                        measurement(h, row) {
                            return this.$h.denominationCreator(row.measurement.detail)
                        },
                        galvanic_finish(h, row) {
                            return row.galvanic_finish?.name
                        },
                        decorative_option(h, row) {
                            return row.decorative_option?.name
                        },
                        brand(h, row){
                            return row.brand.name
                        },
                        notes(h, row){
                            return row.order_notes
                        }
                    },
                    filterAlgorithm: {
                        product_type(row, query) {
                            return (row.product_type.name).includes(query)
                        },
                        line(row, query) {
                            return (row.line.name).includes(query)
                        },
                        subline(row, query) {
                            return (row.subline.name).includes(query)
                        },
                        feature(row, query) {
                            return (row.feature ? row.feature.name : '').includes(query)
                        },
                        material(row, query) {
                            return (row.material.material.name).includes(query)
                        },
                        galvanic_finish(row, query) {
                            return (row.galvanic_finish?.name)?.includes(query)
                        },
                        decorative_option(row, query) {
                            return (row.decorative_option?.name)?.includes(query)
                        },
                    }
                },

            },
            form: {
                from_piece: '',
                master: {
                    description: '',
                    type: '',
                    buyer: '',
                    planner: '',
                    warehouse: '',
                    umd_ldm: '',
                    udm_cost: '',
                    cod_class: '',
                    comfort_code: '',
                    inventory: 0,
                    unit_cost: 0,
                    zone: '',
                    revision_level: '',
                    tc_shopping: '',
                    tc_manufacture: '',
                    civ_cdu: '',
                    ref_cdu: ''
                },
                engineering: {
                    plane: '',
                    yield: 0,
                    waste: 0,
                    state: '',
                    cbn: '',
                    account_type: ''
                },
                planner: {
                    order_policy: '',
                    program: '',
                    critical_tc: '',
                    prd: '',
                    crd: '',
                    safety_inventory: '',
                    firm_plan: '',
                    ncnd: '',
                    rump: '',
                    critical_piece: '',
                    manufacturing: {
                        cycle_time: 0,
                        plan: 0,
                        manufacture: 0,
                        stock: 0
                    },
                    purchases: {
                        cycle_time: 0,
                        plan: 0,
                        purchase: 0,
                        stock: 0
                    },
                    order_quantity: {
                        average: 0,
                        min: 0,
                        max: 0,
                        multiple: 0
                    }
                },
                inventory: {
                    requires_inspection: '',
                    excess_tickets: '',
                    average_weight: '',
                    udm_weight: '',
                    expiration_days: '',
                    batch_control: '',
                    control_ns: '',
                    multi_inputs: '',
                    cdp_lot: '',
                    ns_cdp: '',
                    cycle_count: {
                        code: '',
                        tolerance: '',
                        tolerance_percentage: ''
                    }
                }
            },
            modal: {
                open: false,
            },
            editionModal: {
                open: false,
                form: {
                    product: '',
                    galvanic_finish_code: '',
                    decorative_option_code: ''
                }
            }
        }
    },

    methods: {
        openModal(row) {
            this.modal = {
                open: true,
                row: row
            }
            this.form = {
                from_piece: '',
                master: {
                    piece: row.code,
                    description: row.description,
                    type: '',
                    buyer: '',
                    planner: '',
                    warehouse: '',
                    umd_ldm: '',
                    udm_cost: '',
                    cod_class: '',
                    comfort_code: '',
                    inventory: 0,
                    unit_cost: 0,
                    zone: '',
                    revision_level: '',
                    tc_shopping: '',
                    tc_manufacture: '',
                    civ_cdu: '',
                    ref_cdu: ''
                },
                engineering: {
                    plane: '',
                    yield: 0,
                    waste: 0,
                    state: '',
                    cbn: '',
                    account_type: ''
                },
                planner: {
                    order_policy: '',
                    program: '',
                    critical_tc: '',
                    prd: '',
                    crd: '',
                    safety_inventory: '',
                    firm_plan: '',
                    ncnd: '',
                    rump: '',
                    critical_piece: '',
                    manufacturing: {
                        cycle_time: 0,
                        plan: 0,
                        manufacture: 0,
                        stock: 0
                    },
                    purchases: {
                        cycle_time: 0,
                        plan: 0,
                        purchase: 0,
                        stock: 0
                    },
                    order_quantity: {
                        average: 0,
                        min: 0,
                        max: 0,
                        multiple: 0
                    }
                },
                inventory: {
                    requires_inspection: '',
                    excess_tickets: '',
                    average_weight: '',
                    udm_weight: '',
                    expiration_days: '',
                    batch_control: '',
                    control_ns: '',
                    multi_inputs: '',
                    cdp_lot: '',
                    ns_cdp: '',
                    cycle_count: {
                        code: '',
                        tolerance: '',
                        tolerance_percentage: ''
                    }
                }
            }
        },

        openEditionModal(product){
            this.editionModal = {
                open: true,
                form: {
                    product: product,
                    galvanic_finish_code: product.galvanic_finish_code,
                    decorative_option_code: product.decorative_option_code
                }
            }
        },

        closeModal() {
            this.modal = {
                open: false,
            }
            this.form = {
                from_piece: '',
                master: {
                    piece: '',
                    description: '',
                    type: '',
                    buyer: '',
                    planner: '',
                    warehouse: '',
                    umd_ldm: '',
                    udm_cost: '',
                    cod_class: '',
                    comfort_code: '',
                    inventory: 0,
                    unit_cost: 0,
                    zone: '',
                    revision_level: '',
                    tc_shopping: '',
                    tc_manufacture: '',
                    civ_cdu: '',
                    ref_cdu: ''
                },
                engineering: {
                    plane: '',
                    yield: 0,
                    waste: 0,
                    state: '',
                    cbn: '',
                    account_type: ''
                },
                planner: {
                    order_policy: '',
                    program: '',
                    critical_tc: '',
                    prd: '',
                    crd: '',
                    safety_inventory: '',
                    firm_plan: '',
                    ncnd: '',
                    rump: '',
                    critical_piece: '',
                    manufacturing: {
                        cycle_time: 0,
                        plan: 0,
                        manufacture: 0,
                        stock: 0
                    },
                    purchases: {
                        cycle_time: 0,
                        plan: 0,
                        purchase: 0,
                        stock: 0
                    },
                    order_quantity: {
                        average: 0,
                        min: 0,
                        max: 0,
                        multiple: 0
                    }
                },
                inventory: {
                    requires_inspection: '',
                    excess_tickets: '',
                    average_weight: '',
                    udm_weight: '',
                    expiration_days: '',
                    batch_control: '',
                    control_ns: '',
                    multi_inputs: '',
                    cdp_lot: '',
                    ns_cdp: '',
                    cycle_count: {
                        code: '',
                        tolerance: '',
                        tolerance_percentage: ''
                    }
                }
            }

            this.editionModal = {
                open: false,
                form: {
                    code: '',
                    galvanic_finish_code: '',
                    decorative_option_code: ''
                }
            }
        },

        save() {
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 6000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('pending-product.post'), this.form).then(resp => {
                    this.closeModal()
                    this.table.data = resp.data.data
                    this.$swal({
                        icon: 'success',
                        title: '¡Producto procesado con éxito!',
                        text: `El proceso de clonación, creación y reemplazo de producto finalizo con éxito, producto generado: ${rep.data.product.code} – ${resp.data.product.description}`,
                        confirmButtonText: 'Aceptar'
                    });

                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data.message,
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        },

        resetForm() {
            this.form = {
                from_piece: '',
                master: {
                    piece: this.modal.row.code,
                    description: this.modal.row.description,
                    type: '',
                    buyer: '',
                    planner: '',
                    warehouse: '',
                    umd_ldm: '',
                    udm_cost: '',
                    cod_class: '',
                    comfort_code: '',
                    inventory: 0,
                    unit_cost: 0,
                    zone: '',
                    revision_level: '',
                    tc_shopping: '',
                    tc_manufacture: '',
                    civ_cdu: '',
                    ref_cdu: ''
                },
                engineering: {
                    plane: '',
                    yield: 0,
                    waste: 0,
                    state: '',
                    cbn: '',
                    account_type: ''
                },
                planner: {
                    order_policy: '',
                    program: '',
                    critical_tc: '',
                    prd: '',
                    crd: '',
                    safety_inventory: '',
                    firm_plan: '',
                    ncnd: '',
                    rump: '',
                    critical_piece: '',
                    manufacturing: {
                        cycle_time: 0,
                        plan: 0,
                        manufacture: 0,
                        stock: 0
                    },
                    purchases: {
                        cycle_time: 0,
                        plan: 0,
                        purchase: 0,
                        stock: 0
                    },
                    order_quantity: {
                        average: 0,
                        min: 0,
                        max: 0,
                        multiple: 0
                    }
                },
                inventory: {
                    requires_inspection: '',
                    excess_tickets: '',
                    average_weight: '',
                    udm_weight: '',
                    expiration_days: '',
                    batch_control: '',
                    control_ns: '',
                    multi_inputs: '',
                    cdp_lot: '',
                    ns_cdp: '',
                    cycle_count: {
                        code: '',
                        tolerance: '',
                        tolerance_percentage: ''
                    }
                }
            }
        },

        select_max_item(obj) {
            let value = JSON.stringify(obj, (k, v) => typeof v === 'string' ? v.trim() : v)
            value = JSON.parse(value)

            this.form.from_piece = value.PRTNUM_01
            this.form.master.type = value.TYPE_01
            this.form.master.buyer = value.BUYER_01
            this.form.master.planner = value.PLANID_01
            this.form.master.warehouse = value.DELSTK_01
            this.form.master.umd_ldm = value.BOMUOM_01
            this.form.master.udm_cost = value.WGTDEM_01
            this.form.master.cod_class = value.CLSCDE_01
            this.form.master.comfort_code = value.COMCDE_01
            this.form.master.zone = value.DELLOC_01
            this.form.master.revision_level = value.REVLEV_01
            this.form.master.tc_shopping = value.PURLT_01
            this.form.master.tc_manufacture = value.MFGLT_01

            this.form.engineering = {
                plane: value.DRANUM_01,
                yield: value.YIELD_01,
                waste: value.SCRAP_01,
                state: value.STAENG_01,
                cbn: value.LLC_01,
                account_type: value.ACTTYP_01
            }

            this.form.planner = {
                order_policy: value.ORDPOL_01,
                program: value.SCHFLG_01,
                critical_tc: value.CRPHLT_01,
                prd: value.ROP_01,
                crd: value.ROQ_01,
                safety_inventory: value.SAFSTK_01,
                firm_plan: value.FRMPLN_01 === 'Y',
                ncnd: value.NCNR_01 === 'Y',
                rump: value.ROHS_01 === 'Y',
                critical_piece: value.SUPCDE_01 === 'Y',
                manufacturing: {
                    cycle_time: 0,
                    plan: 0,
                    manufacture: 0,
                    stock: 0
                },
                purchases: {
                    cycle_time: 0,
                    plan: 0,
                    purchase: 0,
                    stock: 0
                },
                order_quantity: {
                    average: 0,
                    min: 0,
                    max: 0,
                    multiple: 0
                }
            }

            this.form.inventory = {
                requires_inspection: value.INSRQD_01 === 'Y',
                excess_tickets: value.EXCREC_01,
                average_weight: value.WGT_01,
                udm_weight: value.WGTDEM_01,
                expiration_days: value.SHLIFE_01,
                batch_control: value.LOTTRK_01 === 'Y',
                control_ns: value.SERTRK_01 === 'Y',
                multi_inputs: value.MULREC_01 === 'Y',
                cdp_lot: value.LOTSFC_01 === 'Y',
                ns_cdp: value.SNSFC_01 === 'Y',
                cycle_count: {
                    code: value.CYCCDE_01,
                    tolerance: value.CYCDOL_01,
                    tolerance_percentage: value.CYCPER_01
                }
            }
        },

        updateProduct(){
            axios.post(route('pending-product.update'), this.editionModal.form).then(resp => {
                this.closeModal()
                this.table.data = resp.data
                this.$swal({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Producto actualizado con éxito',
                    confirmButtonText: 'Aceptar'
                });
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data?.message,
                    confirmButtonText: 'Aceptar'
                });
            });

        }
    }
}
</script>

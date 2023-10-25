<template>
    <div>
        <Head title="Clonador de productos"/>

        <portal to="application-title">
            Clonador de productos
        </portal>

        <div>
            <div class="alert alert-secondary show mb-2 shadow-sm" role="alert">
                <div class="flex items-center">
                    <div class="font-medium text-lg uppercase">Instrucciones</div>
                    <div class="badge badge-danger badge-rounded ml-auto">Importante</div>
                </div>
                <div class="mt-3 font-medium">
                    En este aplicativo es utilizado para crear nuevos productos en MAX con la nueva codificación, partiendo de un producto anteriormente creado,
                    por favor siga las instrucciones o pida soporte a sistemas:
                </div>
                <ul class="list-disc ml-6">
                    <li>Busque y seleccione un producto del codificador.</li>
                    <li>Busque y seleccione un producto de MAX (las propiedades de este serán la base para el nuevo producto)</li>
                    <li>Cambie las propiedades que sean necesarias</li>
                    <li>Verifique que toda la información sea correcta </li>
                    <li>Guarde los cambios, el producto sera almacenado en la base de datos de MAX</li>
                </ul>
            </div>

            <div class="intro-y box mt-5">
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="mt-2">
                            <label class="flex flex-col sm:flex-row">
                                Producto Nuevo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <autocomplete
                                url="/cloner/search-products"
                                show-field="description"
                                @selected-value="select_item"
                            />
                        </div>

                        <div class="mt-2">
                            <label class="flex flex-col sm:flex-row">
                                Producto a Clonar
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <autocomplete
                                url="/cloner/search-product-max"
                                show-field="PMDES1_01"
                                @selected-value="select_max_item"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="post intro-y overflow-hidden box mt-5">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800" role="tablist">
                    <li class="nav-item">
                        <Tippy
                            id="master-tab"
                            tag="button"
                            content="Maestro"
                            data-tw-toggle="tab"
                            data-tw-target="#master"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            <FileTextIcon class="w-4 h-4 mr-2"/>
                            Maestro
                        </Tippy>
                    </li>

                    <li class="nav-item">
                        <Tippy
                            id="engineering-tab"
                            tag="button"
                            content="Ingeniería"
                            data-tw-toggle="tab"
                            data-tw-target="#engineering"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            <FileTextIcon class="w-4 h-4 mr-2"/>
                            Ingeniería
                        </Tippy>
                    </li>

                    <li class="nav-item">
                        <Tippy
                            id="planner-tab"
                            tag="button"
                            content="Planificador"
                            data-tw-toggle="tab"
                            data-tw-target="#planner"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            <FileTextIcon class="w-4 h-4 mr-2"/>
                            Planificador
                        </Tippy>
                    </li>

                    <li class="nav-item">
                        <Tippy
                            id="inventory-tab"
                            tag="button"
                            content="Planificador"
                            data-tw-toggle="tab"
                            data-tw-target="#inventory"
                            href="javascript:;"
                            class="nav-link tooltip w-full sm:w-40 py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="true"
                        >
                            <FileTextIcon class="w-4 h-4 mr-2"/>
                            Inventario
                        </Tippy>
                    </li>
                </ul>

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
                                    ID Pieza
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>

                                <input type="text" class="form-control"
                                       :class="{ 'border-danger': v$.form.master.piece.$error }"
                                       v-model="form.master.piece" disabled>

                                <template v-if="v$.form.master.piece.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.piece.$errors" :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>

                            <div class="mt-2">
                                <label class="flex flex-col sm:flex-row">
                                    Descripción
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>

                                <input type="text" class="form-control"
                                       :class="{ 'border-danger': v$.form.master.description.$error }"
                                       v-model="form.master.description" disabled>

                                <template v-if="v$.form.master.description.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.description.$errors" :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>

                            <div class="mt-2">
                                <label class="flex flex-col sm:flex-row">
                                    Tipo Pieza
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>
                                <select class="form-select" :class="{ 'border-danger': v$.form.master.type.$error }"
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.type.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.master.buyer.$error }"
                                        v-model="form.master.buyer">
                                    <option value="" selected="selected">Seleccione...</option>
                                    <option v-for="buyer in buyers" :value="buyer.id">{{ buyer.name }}</option>
                                </select>

                                <template v-if="v$.form.master.buyer.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.buyer.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.master.planner.$error }"
                                        v-model="form.master.planner">
                                    <option value="" selected="selected">Seleccione...</option>
                                    <option v-for="planner in planners" :value="planner.id">{{ planner.name }}</option>
                                </select>

                                <template v-if="v$.form.master.planner.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.planner.$errors" :key="index">
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.warehouse.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.master.umd_ldm.$error }"
                                        v-model="form.master.umd_ldm">
                                    <option value="" selected>Seleccione...</option>
                                    <option value="KG">KG - Kilogramos</option>
                                    <option value="UN">UN - Unidad</option>
                                </select>

                                <template v-if="v$.form.master.umd_ldm.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.umd_ldm.$errors" :key="index">
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.udm_cost.$errors" :key="index">
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.cod_class.$errors" :key="index">
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
                                    <option v-for="comfort_code in comfort_codes" :value="comfort_code.id.trim()">
                                        {{ comfort_code.id.trim() }}
                                    </option>
                                </select>

                                <template v-if="v$.form.master.comfort_code.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.comfort_code.$errors" :key="index">
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
                                <input type="text" class="form-control" v-model="form.master.inventory" disabled>
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.master.unit_cost.$errors" :key="index">
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
                                <input type="text" class="form-control" v-model="form.engineering.plane" >
                            </div>

                            <div class="mt-2">
                                <label class="flex flex-col sm:flex-row">
                                    Porcentaje de Rendimiento
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.engineering.yield.$error }" v-model="form.engineering.yield" >

                                <template v-if="v$.form.engineering.yield.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.engineering.yield.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.engineering.waste.$error }" v-model="form.engineering.waste" >

                                <template v-if="v$.form.engineering.waste.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.engineering.waste.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.engineering.state.$error }" v-model="form.engineering.state">
                                    <option value="" selected>Seleccione...</option>
                                    <option value="1">1 - En Proyecto</option>
                                    <option value="2">2 - En Producción</option>
                                    <option value="3">3 - No Definido</option>
                                    <option value="4">4 - No Definido</option>
                                    <option value="5">5 - Obsoleto</option>
                                </select>

                                <template v-if="v$.form.engineering.state.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.engineering.state.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.engineering.cbn.$error }" v-model="form.engineering.cbn" >

                                <template v-if="v$.form.engineering.cbn.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.engineering.cbn.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.engineering.account_type.$error }" v-model="form.engineering.account_type">
                                    <option value="" selected="selected" >Seleccione...</option>
                                    <option v-for="account_type in account_types" :value="account_type.id.trim()">{{ `${account_type.id.trim()} - ${account_type.name.trim()}`}}</option>
                                </select>

                                <template v-if="v$.form.engineering.account_type.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.engineering.account_type.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.planner.order_policy.$error }" v-model="form.planner.order_policy">
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
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.order_policy.$errors" :key="index">
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

                                <select class="form-select" :class="{ 'border-danger': v$.form.planner.program.$error }" v-model="form.planner.program">
                                    <option value="" selected>Seleccione...</option>
                                    <option value="R">R - Ruta</option>
                                    <option value="Q">Q - Cola</option>
                                </select>

                                <template v-if="v$.form.planner.program.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.program.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.critical_tc.$error }" v-model="form.planner.critical_tc">

                                <template v-if="v$.form.planner.critical_tc.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.critical_tc.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.prd.$error }" v-model="form.planner.prd">

                                <template v-if="v$.form.planner.prd.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.prd.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.crd.$error }" v-model="form.planner.crd">

                                <template v-if="v$.form.planner.crd.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.crd.$errors" :key="index">
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

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.safety_inventory.$error }" v-model="form.planner.safety_inventory">

                                <template v-if="v$.form.planner.safety_inventory.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.planner.safety_inventory.$errors" :key="index">
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
                                <input type="checkbox" class="form-check-input mt-2" v-model="form.planner.firm_plan">
                            </div>

                            <div class="mt-2 form-switch">
                                <label class="flex flex-col sm:flex-row">
                                    NCND
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <input type="checkbox" class="form-check-input mt-2" v-model="form.planner.ncnd">
                            </div>

                            <div class="mt-2 form-switch">
                                <label class="flex flex-col sm:flex-row">
                                    RUMP
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <input type="checkbox" class="form-check-input mt-2" v-model="form.planner.rump">
                            </div>

                            <div class="mt-2 form-switch">
                                <label class="flex flex-col sm:flex-row">
                                    Pieza Critica
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <input type="checkbox" class="form-check-input mt-2" v-model="form.planner.critical_piece">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.manufacturing.cycle_time.$error }" v-model="form.planner.manufacturing.cycle_time" >

                                    <template v-if="v$.form.planner.manufacturing.cycle_time.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.manufacturing.cycle_time.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.manufacturing.plan.$error }" v-model="form.planner.manufacturing.plan">

                                    <template v-if="v$.form.planner.manufacturing.plan.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.manufacturing.plan.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.manufacturing.manufacture.$error }" v-model="form.planner.manufacturing.manufacture">

                                    <template v-if="v$.form.planner.manufacturing.manufacture.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.manufacturing.manufacture.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.manufacturing.stock.$error }" v-model="form.planner.manufacturing.stock">

                                    <template v-if="v$.form.planner.manufacturing.stock.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.manufacturing.stock.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.purchases.cycle_time.$error }" v-model="form.planner.purchases.cycle_time" >

                                    <template v-if="v$.form.planner.purchases.cycle_time.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.purchases.cycle_time.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.purchases.plan.$error }" v-model="form.planner.purchases.plan">

                                    <template v-if="v$.form.planner.purchases.plan.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.purchases.plan.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.purchases.purchase.$error }" v-model="form.planner.purchases.purchase">

                                    <template v-if="v$.form.planner.purchases.purchase.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.purchases.purchase.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.purchases.stock.$error }" v-model="form.planner.purchases.stock">

                                    <template v-if="v$.form.planner.purchases.stock.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.purchases.stock.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.order_quantity.average.$error }" v-model="form.planner.order_quantity.average" />

                                    <template v-if="v$.form.planner.order_quantity.average.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.order_quantity.average.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.order_quantity.min.$error }" v-model="form.planner.order_quantity.min">

                                    <template v-if="v$.form.planner.order_quantity.min.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.order_quantity.min.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.order_quantity.max.$error }" v-model="form.planner.order_quantity.max">

                                    <template v-if="v$.form.planner.order_quantity.max.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.order_quantity.max.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.planner.order_quantity.multiple.$error }" v-model="form.planner.order_quantity.multiple">

                                    <template v-if="v$.form.planner.order_quantity.multiple.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.planner.order_quantity.multiple.$errors" :key="index">
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
                                <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.requires_inspection">
                            </div>

                            <div class="mt-2 col-span-3">
                                <label class="flex flex-col sm:flex-row">
                                    Exceso Entradas
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>
                                <input type="number" class="form-control" v-model="form.inventory.excess_tickets">
                            </div>

                            <div class="mt-2 col-span-3">
                                <label class="flex flex-col sm:flex-row">
                                    Peso Promedio
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>

                                <input type="number" class="form-control" :class="{ 'border-danger': v$.form.inventory.average_weight.$error }" v-model="form.inventory.average_weight">

                                <template v-if="v$.form.inventory.average_weight.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.inventory.average_weight.$errors" :key="index">
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

                                <select class="form-control" :class="{ 'border-danger': v$.form.inventory.udm_weight.$error }" v-model="form.inventory.udm_weight">
                                    <option value="" selected>Seleccione...</option>
                                    <option value="UN">UN - Unidad</option>
                                    <option value="KG">KG - Kilogramo</option>
                                </select>

                                <template v-if="v$.form.inventory.udm_weight.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.inventory.udm_weight.$errors" :key="index">
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
                                    <input type="number" class="form-control" v-model="form.inventory.expiration_days">
                                </div>

                                <div class="mt-2 form-switch">
                                    <label class="flex flex-col sm:flex-row">
                                        Control Lote
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.batch_control">
                                </div>

                                <div class="mt-2 form-switch">
                                    <label class="flex flex-col sm:flex-row">
                                        Control N/S
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.control_ns">
                                </div>

                                <div class="mt-2 form-switch">
                                    <label class="flex flex-col sm:flex-row">
                                        Multi Entradas
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.multi_inputs">
                                </div>

                                <div class="mt-2 form-switch">
                                    <label class="flex flex-col sm:flex-row">
                                        Lote CDP
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.cdp_lot">
                                </div>

                                <div class="mt-2 form-switch">
                                    <label class="flex flex-col sm:flex-row">
                                        N/S CDP
                                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Opcional
                                        </span>
                                    </label>
                                    <input type="checkbox" class="form-check-input mt-2" v-model="form.inventory.ns_cdp">
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
                                    <select class="form-control" :class="{ 'border-danger': v$.form.inventory.cycle_count.code.$error }" v-model="form.inventory.cycle_count.code">
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
                                            <li class="text-danger" v-for="(error, index) of v$.form.inventory.cycle_count.code.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.inventory.cycle_count.tolerance.$error }" v-model="form.inventory.cycle_count.tolerance">

                                    <template v-if="v$.form.inventory.cycle_count.tolerance.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.inventory.cycle_count.tolerance.$errors" :key="index">
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

                                    <input type="number" class="form-control" :class="{ 'border-danger': v$.form.inventory.cycle_count.tolerance_percentage.$error }" v-model="form.inventory.cycle_count.tolerance_percentage">

                                    <template v-if="v$.form.inventory.cycle_count.tolerance_percentage.$error">
                                        <ul class="mt-1">
                                            <li class="text-danger" v-for="(error, index) of v$.form.inventory.cycle_count.tolerance_percentage.$errors" :key="index">
                                                {{ error.$message }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="items-center text-center p-5 border-t border-gray-200 dark:border-dark-5">
                    <button class="btn btn-primary uppercase " @click="save(form)">
                        <font-awesome-icon icon="copy" class="mr-2"/>
                        Clonar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
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
        buyers: Array,
        planners: Array,
        warehouses: Array,
        class_codes: Array,
        comfort_codes: Array,
        account_types: Array
    },

    components: {
        Autocomplete,
        Head
    },

    validations () {
        return {
            form: {
                master: {
                    piece: { required },
                    description: { required },
                    type: { required },
                    buyer: { required },
                    planner: { required },
                    warehouse: { required },
                    umd_ldm: { required },
                    udm_cost: { required },
                    cod_class: { required },
                    comfort_code: { required },
                    unit_cost: {required, minValue: minValue(0)}
                },
                engineering: {
                    state: { required },
                    account_type: { required },
                    yield: {required, minValue: minValue(0), maxValue: maxValue(100) },
                    waste: {required, minValue: minValue(0), maxValue: maxValue(100) },
                    cbn: { required, minValue: minValue(0) },
                },
                planner: {
                    order_policy: { required },
                    program: { required },
                    critical_tc: { required, minValue: minValue(0) },
                    prd: { required, minValue: minValue(0) },
                    crd: { required, minValue: minValue(0) },
                    safety_inventory: { required, minValue: minValue(0) },
                    manufacturing: {
                        cycle_time: { required, minValue: minValue(0) },
                        plan: { required, minValue: minValue(0) },
                        manufacture: { required, minValue: minValue(0) },
                        stock: { required, minValue: minValue(0) }
                    },
                    purchases: {
                        cycle_time: { required, minValue: minValue(0) },
                        plan: { required, minValue: minValue(0) },
                        purchase: { required, minValue: minValue(0) },
                        stock: { required, minValue: minValue(0) }
                    },
                    order_quantity: {
                        average: { required, minValue: minValue(0) },
                        min: { required, minValue: minValue(0) },
                        max: { required, minValue: minValue(0) },
                        multiple: { required, minValue: minValue(0) }
                    }
                },
                inventory: {
                    average_weight: { required, minValue: minValue(0) },
                    udm_weight: { required },
                    cycle_count: {
                        code: { required },
                        tolerance: { required, minValue: minValue(0) },
                        tolerance_percentage: { required, minValue: minValue(0), maxValue: maxValue(100) },
                    }
                }
            }
        }

    },

    data(){
        return {
            form: {
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
        }
    },

    methods: {
        select_item(obj){
            this.form.master.piece = obj.code
            this.form.master.description =  obj.description
        },

        select_max_item(obj){
            let value = JSON.stringify(obj, (k, v) => typeof v === 'string' ? v.trim() : v)
            value = JSON.parse(value)

            this.form.from_piece = value.PRTNUM_01
            this.form.master.type = value.TYPE_01
            this.form.master.buyer =  value.BUYER_01
            this.form.master.planner = value.PLANID_01
            this.form.master.warehouse = value.DELSTK_01
            this.form.master.umd_ldm =  value.BOMUOM_01
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

        save(data){
            this.v$.form.$touch();
            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            }else{
                axios.post(route('cloner.store'), data).then(resp => {
                    this.$swal({
                        icon: 'success',
                        title: '¡Producto Creado!',
                        text: resp.data,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        timer: 6000,
                    });
                    this.resetForm();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err.data)
                })
            }
        },

        resetForm(){
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
            this.v$.$reset()
        }
    }
}
</script>


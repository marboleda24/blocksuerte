<template>
    <div>
        <Head title="Nuevo Cliente"/>

        <portal to="application-title">
            Nuevo Cliente
        </portal>

        <portal to="actions">
            <Link :href="route('customers.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div class="intro-y box py-10 sm:py-20 mt-5">
            <div
                class="relative before:hidden before:lg:block before:absolute before:w-[47%] before:h-[3px] before:top-0 before:bottom-0 before:mt-4 before:bg-slate-100 before:dark:bg-darkmode-400 flex flex-col lg:flex-row justify-center px-5 sm:px-20">
                <div class="intro-x lg:text-center flex items-center lg:block flex-1 z-10">
                    <button class="w-10 h-10 rounded-full btn"
                            :class="{'btn-primary': step === 1 || step === 2 }">
                        1
                    </button>
                    <div class="lg:w-full font-medium text-base lg:mt-3 ml-3 lg:mx-auto">
                        Información Personal
                    </div>
                </div>
                <div class="intro-x lg:text-center flex items-center mt-5 lg:mt-0 lg:block flex-1 z-10">
                    <button class="w-10 h-10 rounded-full btn"
                            :class="{'btn-primary': step === 2, 'text-slate-500 bg-slate-100 dark:bg-darkmode-400 dark:border-darkmode-400': step === 1 }">
                        2
                    </button>
                    <div class="lg:w-full text-base lg:mt-3 ml-3 lg:mx-auto text-slate-600 dark:text-slate-400">
                        Información Tributaria
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-20 mt-10 pt-10 border-t border-slate-200/60 dark:border-darkmode-400"
                 v-if="step === 1">
                <div class="font-medium text-base">Información Personal</div>
                <div class="grid grid-cols-12 gap-4 gap-y-5 mt-5">
                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Tipo Tercero
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step1.type_third.$model"
                                class="form-select"
                                :class="{ 'border-danger': v$.form.step1.type_third.$error }"
                                @change="validate_type_third">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option value="NC">Nacional</option>
                            <option value="EX">Exterior</option>
                        </select>

                        <template v-if="v$.form.step1.type_third.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.type_third.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <template v-if="v$.form.step1.type_third.$model === 'NC'">
                        <!-- START: Document Type -->
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Tipo Documento (25)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.step1.document_type.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.step1.document_type.$error }">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="C">Cédula de ciudadanía</option>
                                <option value="N">NIT</option>
                            </select>

                            <template v-if="v$.form.step1.document_type.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.step1.document_type.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <!-- END: Document Type -->
                    </template>


                    <!-- START: Document -->
                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Documento (5)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                        </label>

                        <div class="relative flex w-full flex-wrap items-stretch mb-3">
                            <input v-model.trim="v$.form.step1.document.$model" type="number"
                                   placeholder="Sin Digito Verificador" class="form-control"
                                   :class="{ 'border-danger': v$.form.step1.document.$error }"/>
                            <span
                                class="font-normal absolute text-center text-gray-700 absolute rounded items-center justify-center w-12 mr-8 right-0 pr-2 py-2">
                                {{ `DV: ${dv}` }}
                            </span>
                        </div>

                        <template v-if="v$.form.step1.document.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.document.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                    <!-- END: Document -->


                    <template v-if="v$.form.step1.type_third.$model === 'NC'">
                        <!-- START: Customer Type -->
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Tipo Contribuyente (24)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.step1.customer_type.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.step1.customer_type.$error }"
                                    placeholder="tipo cliente">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="PN">Persona Natural</option>
                                <option value="PJ">Persona Jurídica</option>
                            </select>

                            <template v-if="v$.form.step1.customer_type.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.step1.customer_type.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <!-- END: Customer Type -->
                    </template>


                    <template v-if="form.step1.customer_type === 'PN'">
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Primer Nombre (33)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.step1.first_name.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.step1.first_name.$error }"
                                   placeholder="Primer Nombre" v-uppercase/>

                            <template v-if="v$.form.step1.first_name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.step1.first_name.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Segundo Nombre (34)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input v-model.trim="v$.form.step1.second_name.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.step1.second_name.$error }"
                                   placeholder="Segundo Nombre" v-uppercase/>

                            <template v-if="v$.form.step1.second_name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.step1.second_name.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Primer Apellido (31)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.step1.surname.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.step1.surname.$error }"
                                   placeholder="Primer Apellido" v-uppercase/>

                            <template v-if="v$.form.step1.surname.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.step1.surname.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Segundo Apellido (32)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                            </label>
                            <input v-model.trim="v$.form.step1.second_surname.$model" type="text"
                                   class="form-control"
                                   :class="{ 'border-danger': v$.form.step1.second_surname.$error }"
                                   placeholder="Segundo Apellido" v-uppercase/>

                            <template v-if="v$.form.step1.second_surname.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.step1.second_surname.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.step1.customer_type === 'PJ'">
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Razon social (35)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                            </label>

                            <input v-model.trim="v$.form.step1.business_name.$model" type="text"
                                   :class="{ 'border-danger': v$.form.step1.business_name.$error }"
                                   class="form-control" placeholder="Razon Social" v-uppercase/>

                            <template v-if="v$.form.step1.business_name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.step1.business_name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>


                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Razon Comercial (36)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Opcional
                            </span>
                        </label>
                        <input v-model.trim="form.step1.business_reason" type="text" class="form-control"
                               placeholder="Razon Comercial" v-uppercase/>
                    </div>


                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Pais (38)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step1.country.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.step1.country.$error }" placeholder="Pais"
                                @change="GetProvinces(form.step1.country)">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="country in countries" v-bind:key="country.pais" :value="country.pais">
                                {{ country.descripcion }}
                            </option>
                        </select>

                        <template v-if="v$.form.step1.country.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.country.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Departamento (39)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step1.province.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.step1.province.$error }"
                                @change="GetCities(form.step1.country, form.step1.province)">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="province in provinces" v-bind:key="province.departamento"
                                    :value="province.departamento">{{ province.descripcion }}
                            </option>
                        </select>

                        <template v-if="v$.form.step1.province.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.province.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>


                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Ciudad (40)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step1.city.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.step1.city.$error }" placeholder="Ciudad">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="city in cities" v-bind:key="city.ciudad" :value="city.ciudad">
                                {{ city.descripcion }}
                            </option>
                        </select>

                        <template v-if="v$.form.step1.city.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.city.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Dirección Principal (41)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input v-model.trim="v$.form.step1.address.$model" type="text" class="form-control"
                               :class="{ 'border-danger': v$.form.step1.address.$error }"
                               placeholder="Dirección Principal" v-uppercase/>

                        <template v-if="v$.form.step1.address.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.address.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>


                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Teléfono (44)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input v-model.trim="v$.form.step1.phone.$model" type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.step1.phone.$error }" placeholder="Teléfono"/>

                        <template v-if="v$.form.step1.phone.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.phone.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Celular (45)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Opcional
                            </span>
                        </label>

                        <input v-model.trim="v$.form.step1.cellphone.$model" type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.step1.cellphone.$error }" placeholder="Celular"/>

                        <template v-if="v$.form.step1.cellphone.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.cellphone.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Correo Electrónico (42)
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input v-model.trim="v$.form.step1.email.$model" type="email" class="form-control"
                               :class="{ 'border-danger': v$.form.step1.email.$error }"
                               placeholder="Correo Electrónico" v-lowercase/>

                        <template v-if="v$.form.step1.email.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.step1.email.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 flex items-center justify-center sm:justify-end mt-5">
                        <button class="btn btn-primary w-24 ml-2" @click="ValidateStep1">
                            Siguiente
                            <font-awesome-icon icon="arrow-right" class="ml-1"/>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-20 mt-10 pt-10 border-t border-slate-200/60 dark:border-darkmode-400"
                 v-else-if="step === 2">
                <div class="font-medium text-base">Información Tributaria</div>
                <div class="grid grid-cols-12 gap-4 gap-y-5 mt-5">

                    <template v-if="form.step1.customer_type === 'PJ'">
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Tipo de Persona Jurídica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.step2.type_legal_entity.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.step2.type_legal_entity.$error }">
                                <option value="" selected="selected" disabled>Seleccione...</option>
                                <option value="CI">Comercializador Internacional</option>
                                <option value="ZF">Zona Franca</option>
                                <option value="RC">Regimen común</option>
                                <option value="EX">Exterior</option>
                            </select>

                            <template v-if="v$.form.step2.type_legal_entity.$error">
                                <div v-if="!v$.form.step2.type_legal_entity.required" class="text-theme-6 mt-2">
                                    Campo obligatorio
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Vendedor
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step2.seller.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.step2.seller.$error }" placeholder="Vendedor">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="seller in sellers" v-bind:key="seller.id" :value="seller.id">
                                {{ seller.name }}
                            </option>
                        </select>

                        <template v-if="v$.form.step2.seller.$error">
                            <div v-if="!v$.form.step2.seller.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Actividad Economica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model.trim="v$.form.step2.main_activity.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.step2.main_activity.$error }"
                                placeholder="Actividad Principal">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="main_activity in main_activities" v-bind:key="main_activity.codigo"
                                    :value="main_activity.codigo">
                                {{ `${main_activity.codigo} - ${main_activity.descripcion}` }}
                            </option>
                        </select>

                        <template v-if="v$.form.step2.main_activity.$error">
                            <div v-if="!v$.form.step2.main_activity.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>
                        </template>
                    </div>

                    <template v-if="form.step1.customer_type === 'PJ'">
                        <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                            <label class="flex flex-col sm:flex-row">
                                Gran Contribuyente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input v-model="form.step2.great_contributor" class="mt-2 form-check-input"
                                   type="checkbox">
                        </div>
                    </template>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Gravado
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Opcional
                            </span>
                        </label>
                        <input v-model="form.step2.gravado" class="mt-2 form-check-input" type="checkbox">
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Responsable IVA
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                        </label>
                        <input v-model="form.step2.responsable_iva" class="mt-2 form-check-input" type="checkbox">
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Correo Electrónico Facturación Electronica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <input v-model.trim="v$.form.step2.email_fe.$model" type="email" class="form-control"
                               :class="{ 'border-danger': v$.form.step2.email_fe.$error }"
                               placeholder="Correo Electrónico de Facturación Electronica" v-lowercase/>

                        <template v-if="v$.form.step2.email_fe.$error">
                            <div v-if="!v$.form.step2.email_fe.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>

                            <div v-if="!v$.form.step2.email_fe.email" class="text-theme-6 mt-2">
                                debes escribir un correo electrónico valido
                            </div>
                        </template>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            Correos Copia Facturación Electronica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Opcional
                            </span>
                        </label>
                        <input v-model="form.step2.emails_copies_fe" type="email" class="form-control"
                               placeholder="Correos Copia Facturación Electronica" v-lowercase/>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            RUT entregado?
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <select v-model="form.step2.tieneRUT" class="form-select">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option :value="true">Si</option>
                            <option :value="false">No</option>
                        </select>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-6 md:col-span-4">
                        <label class="flex flex-col sm:flex-row">
                            RUT
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Opcional
                            </span>
                        </label>
                        <input @change="processFile($event)" type="file" ref="files" class="form-control"
                               placeholder="RUT"/>
                    </div>

                    <div class="intro-y col-span-12 flex items-center justify-center sm:justify-end mt-5">
                        <button class="btn btn-secondary" @click="step = 1">
                            <font-awesome-icon icon="arrow-left" class="mr-1"/>
                            Anterior
                        </button>
                        <button class="btn btn-primary ml-2" @click="completeWizard(form)">
                            Crear Cliente
                            <font-awesome-icon icon="arrow-right" class="ml-1"/>
                        </button>
                    </div>
                </div>

            </div>
        </div>


    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {email, helpers, minLength, maxLength, minValue, numeric, required, requiredIf} from '@/utils/i18n-validators'

const CancelToken = axios.CancelToken;
let source;

const validNames = helpers.regex(/^[a-z0-9_ ]*$/i)
const {withAsync} = helpers

const uppercase = {
    beforeUpdate(el) {
        el.value = el.value.toUpperCase()
    },
}

const lowercase = {
    beforeUpdate(el) {
        el.value = el.value.toLowerCase()
    },
}

export default {
    directives: {
        uppercase,
        lowercase
    },

    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        countries: Array,
        sellers: Array,
        main_activities: Array
    },

    components: {
        Head,
        Link
    },

    validations() {
        return {
            form: {
                step1: {
                    type_third: {
                        required
                    },
                    document_type: {
                        required: requiredIf(function () {
                            this.form.step1.type_third === 'EX' ? this.form.step1.document_type = 'E' : ''
                            return this.form.step1.type_third === 'NC'
                        })
                    },
                    document: {
                        required,
                        numeric,
                        minValue: minValue(0),
                        minLength: minLength(3),
                        isUnique: helpers.withMessage('Este cliente ya esta registrado', withAsync(async (value) => {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            if (value) {
                                try {
                                    return await axios.get(route('validate-client', value), {
                                        cancelToken: source.token
                                    }).then(resp => {
                                        return Boolean(resp.data);
                                    })
                                } catch (error) {
                                    return false
                                }
                            } else {
                                return false
                            }
                        })),
                        $autoDirty: true
                    },
                    customer_type: {
                        required: requiredIf(function () {
                            this.form.step1.type_third === 'EX' ? this.form.step1.customer_type = 'PJ' : ''
                            return this.form.step1.type_third === 'NC'
                        })
                    },
                    first_name: {
                        required: requiredIf(function () {
                            return this.form.step1.customer_type === 'PN'
                        }),
                        minLength: minLength(3),
                        validNames: helpers.withMessage('Nombre invalido', validNames),
                    },
                    second_name: {
                        validNames: helpers.withMessage('Nombre invalido', validNames)
                    },
                    surname: {
                        required: requiredIf(function () {
                            return this.form.step1.customer_type === 'PN'
                        }),
                        minLength: minLength(3),
                        validNames: helpers.withMessage('Apellido invalido', validNames)

                    },
                    second_surname: {
                        validNames: helpers.withMessage('Apellido invalido', validNames)
                    },
                    business_name: {
                        required: requiredIf(function () {
                            return this.form.step1.customer_type === 'PJ';
                        }),
                        minLength: minLength(3),
                        isUnique: helpers.withMessage('Este nombre ya esta registrado', withAsync(async (value) => {
                            if (this.form.step1.customer_type === 'PJ') {
                                if (source) source.cancel();
                                source = CancelToken.source();
                                if (value) {
                                    try {
                                        return await axios.get(route('business-name', value), {
                                            cancelToken: source.token
                                        }).then(resp => {
                                            return Boolean(resp.data);
                                        })
                                    } catch (error) {
                                        return false
                                    }
                                } else {
                                    return false
                                }
                            } else {
                                return true
                            }

                        })),
                        $autoDirty: true
                    },
                    country: {
                        required
                    },
                    province: {
                        required
                    },
                    city: {
                        required
                    },
                    address: {
                        required,
                        minLength: minLength(3),
                        maxLength: maxLength(60),
                    },
                    phone: {
                        required,
                        numeric,
                        minLength: minLength(6),
                        minValue: minValue(0)
                    },
                    cellphone: {
                        numeric,
                        minLength: minLength(6),
                        minValue: minValue(0)
                    },
                    email: {
                        required,
                        email
                    }
                },
                step2: {
                    seller: {
                        required
                    },
                    main_activity: {
                        required
                    },
                    email_fe: {
                        required,
                        email
                    },
                    type_legal_entity: {
                        required: requiredIf(function () {
                            return this.form.step1.customer_type === 'PJ';
                        }),
                    },
                    tieneRUT: {
                        required
                    }

                }
            }
        }

    },

    data() {
        return {
            form: {
                step1: {
                    type_third: '',
                    document_type: '',
                    document: '',
                    customer_type: '',
                    first_name: '',
                    second_name: '',
                    surname: '',
                    second_surname: '',
                    business_name: '',
                    business_reason: '',
                    country: '',
                    province: '',
                    city: '',
                    address: '',
                    phone: '',
                    cellphone: '',
                    email: '',
                },
                step2: {
                    seller: '',
                    main_activity: '',
                    great_contributor: false,
                    gravado: false,
                    responsable_iva: false,
                    email_fe: null,
                    emails_copies_fe: '',
                    rut_file: null,
                    type_legal_entity: '',
                    tieneRUT: '',
                }
            },
            provinces: [],
            cities: [],
            step: 1
        }
    },

    computed: {
        dv: function () {
            return this.form.step1.document ? this.calcule_dv(this.form.step1.document.toString()) : 0;
        },
    },

    methods: {
        ValidateStep1() {
            this.v$.form.step1.$touch();

            if (this.v$.form.step1.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups..',
                    text: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                this.step += 1
            }
        },

        GetProvinces(country) {
            axios.get(route('get-provinces'), {
                params: {
                    country: country
                }
            }).then(resp => {
                this.provinces = resp.data;
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error.data);
            });
        },

        GetCities(country, province) {
            axios.get(route('get-cities'), {
                params: {
                    country: country,
                    province: province
                }
            }).then(resp => {
                this.cities = resp.data;
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error.data);
            });
        },

        completeWizard(form) {
            this.v$.form.step2.$touch();

            if (this.v$.form.step2.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                if (!this.form.step2.gravado) {
                    this.$swal({
                        icon: 'question',
                        title: '¿Crear cliente sin IVA?',
                        text: "Este cliente sera creado sin IVA, ¿Esta seguro de continuar?",
                        showCancelButton: true,
                        confirmButtonText: '¡Si, Continuar!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$swal({
                                iconHtml: this.$h.loadIcon(),
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                title: 'Procesando solicitud',
                                text: 'Este proceso puede tardar unos segundos…',
                            });

                            const formData = new FormData();

                            Object.keys(form).forEach(function (element) {
                                Object.keys(form[element]).forEach(function (elem) {
                                    if (elem !== 'rut_file') {
                                        formData.append(`${element}[${elem}]`, form[element][elem])
                                    }
                                })
                            });

                            for (let i = 0; i < this.$refs.files.files.length; i++) {
                                let file = this.$refs.files.files[i];
                                formData.append('files[]', file);
                            }

                            axios.post(route('customers.store'), formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }).then(resp => {
                                this.$swal({
                                    icon: 'success',
                                    title: '¡Cliente creado con éxito!',
                                    text: 'El cliente fue creado y sera subido a MAX y DMS una vez cartera le asigne un cupo, si pasadas 24 horas cartera no lo han gestionado sera subido automáticamente',
                                    timer: 10000,
                                    timerProgressBar: true,
                                    confirmButtonText: 'Aceptar',
                                });
                                this.formReset();
                                this.$inertia.visit(route('customers.create'))
                            }).catch(error => {
                                this.$swal({
                                    icon: 'error',
                                    title: '¡Ups!',
                                    text: 'Hubo un error procesando la solicitud.',
                                    timer: 10000,
                                    timerProgressBar: true,
                                    confirmButtonText: 'Aceptar',
                                });
                                console.log(error.data);
                            })
                        }
                    })
                } else {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    const formData = new FormData();

                    Object.keys(form).forEach(function (element) {
                        Object.keys(form[element]).forEach(function (elem) {
                            if (elem !== 'rut_file') {
                                formData.append(`${element}[${elem}]`, form[element][elem])
                            }
                        })
                    });

                    for (let i = 0; i < this.$refs.files.files.length; i++) {
                        let file = this.$refs.files.files[i];
                        formData.append('files[]', file);
                    }

                    axios.post(route('customers.store'), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Cliente creado con éxito!',
                            text: 'El cliente fue creado y sera subido a MAX y DMS una vez cartera le asigne un cupo, si pasadas 24 horas cartera no lo han gestionado sera subido automáticamente',
                            timer: 10000,
                            timerProgressBar: true,
                            confirmButtonText: 'Aceptar',
                        })
                        this.formReset();
                        this.$inertia.visit(route('customers.create'))
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            timer: 10000,
                            timerProgressBar: true,
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error.data);
                    })
                }
            }
        },

        processFile(event) {
            this.form.step2.rut_file = event.target.files[0]
        },

        formReset() {
            this.form = {
                step1: {
                    document_type: '',
                    document: '',
                    customer_type: '',
                    first_name: '',
                    second_name: '',
                    surname: '',
                    second_surname: '',
                    business_name: '',
                    business_reason: '',
                    country: '',
                    province: '',
                    city: '',
                    address: '',
                    phone: '',
                    cellphone: '',
                    email: '',
                },
                step2: {
                    seller: '',
                    main_activity: '',
                    great_contributor: false,
                    gravado: false,
                    responsable_iva: false,
                    email_fe: null,
                    emails_copies_fe: '',
                    rut_file: null,
                    type_legal_entity: '',
                    tieneRUT: '',
                }
            }
            this.provinces = []
            this.cities = []
            this.v$.$reset()
        },

        calcule_dv(myNit) {
            var vpri,
                x,
                y,
                z;

            // Se limpia el Nit
            myNit = myNit.replace(/\s/g, ""); // Espacios
            myNit = myNit.replace(/,/g, ""); // Comas
            myNit = myNit.replace(/\./g, ""); // Puntos
            myNit = myNit.replace(/-/g, ""); // Guiones

            // Se valida el nit
            if (isNaN(myNit)) {
                console.log("El nit/cédula '" + myNit + "' no es válido(a).");
                return "";
            }
            ;

            // Procedimiento
            vpri = new Array(16);
            z = myNit.length;

            vpri[1] = 3;
            vpri[2] = 7;
            vpri[3] = 13;
            vpri[4] = 17;
            vpri[5] = 19;
            vpri[6] = 23;
            vpri[7] = 29;
            vpri[8] = 37;
            vpri[9] = 41;
            vpri[10] = 43;
            vpri[11] = 47;
            vpri[12] = 53;
            vpri[13] = 59;
            vpri[14] = 67;
            vpri[15] = 71;

            x = 0;
            y = 0;
            for (var i = 0; i < z; i++) {
                y = (myNit.substr(i, 1));
                // console.log ( y + "x" + vpri[z-i] + ":" ) ;

                x += (y * vpri [z - i]);
                // console.log ( x ) ;
            }

            y = x % 11;
            // console.log ( y ) ;

            return (y > 1) ? 11 - y : y;
        },

        upper(e) {
            e.target.value = e.target.value.toUpperCase()
        },

        validate_type_third() {
            this.form.step1.type_third === 'EX' ? this.form.step2.type_legal_entity = 'EX' : this.form.step2.type_legal_entity = ''
        }
    },
}
</script>

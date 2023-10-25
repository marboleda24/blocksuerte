<template>
    <div>
        <jet-dialog-modal :show="isOpen" max-width=lg>
            <template #title>
                Actualización de {{ form.title }}
            </template>

            <template #content>
                <div class="p-2">
                    <template v-if="form.field === 'contact'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Contacto
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   v-model="form.value"
                                   placeholder="Contacto">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'address1'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Dirección 1
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Dirección 1">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'address2'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Dirección 2
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Dirección 2">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'phone1'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Teléfono 1
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model.number="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Teléfono 1">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'phone2'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Teléfono 2
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model.number="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Teléfono 2">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'cellphone'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Celular
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="number" class="form-control"
                                   v-model.number="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Celular">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'contact_email'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                E-mail de contacto
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="email" class="form-control"
                                   v-model="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="E-mail de contacto">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'billing_email'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                E-mail de facturación electronica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="email" class="form-control"
                                   v-model="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="E-mail de facturación electronica">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'currency'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Moneda
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="COP">COP - Peso Colombiano</option>
                                <option value="USD">USD - Dolar Estadounidense</option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'customer_type'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Tipo de cliente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option :value="customer_type.CUSTYP_62.trim()"
                                        v-for="customer_type in customers_types">{{ customer_type.DESC_62.trim() }}
                                </option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'gravado'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Gravado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Y">SI</option>
                                <option value="N">NO</option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'paid_term'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Plazo de pago
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option :value="paid_term.CODE_36" v-for="paid_term in paid_terms">{{
                                        paid_term.DESC_36
                                    }}
                                </option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'discount'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Pocentaje de descuento
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model.number="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Porcentaje">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'seller'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Vendedor Asignado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option :value="seller.SLSREP_26.trim()" v-for="seller in sellers">
                                    {{ seller.SLSNME_26.trim() }}
                                </option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'responsible_electronic_billing'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Responsable facturación electronica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   v-model="form.value"
                                   placeholder="Responsable facturación electronica">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'phone_electronic_billing'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Teléfono Facturación Electronica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model.number="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Teléfono Facturación Electronica">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'code_city_ext'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Código de Ciudad Exterior
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>
                            <input type="text" class="form-control"
                                   v-model="form.value"
                                   :class="{ 'border-danger': v$.form.value.$error }"
                                   placeholder="Código de Ciudad Exterior">

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'rut'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                RUT Entregado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'great_contributor'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Gran Contribuyente
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'responsible_iva'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Responsable de IVA
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'economic_group'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Grupo Economico
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    v-model="form.value"
                                    :class="{ 'border-danger': v$.form.value.$error }">
                                <option value="" disabled selected>Seleccione...</option>
                                <option :value="economic_group.codigo" v-for="economic_group in economic_groups">
                                    {{ economic_group.descripcion }}
                                </option>
                            </select>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.field === 'copy_emails'">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Correos copia
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control"
                                      v-model="form.value"
                                      :class="{ 'border-danger': v$.form.value.$error }"
                                      cols="30"
                                      rows="5"></textarea>

                            <template v-if="v$.form.value.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.value.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div>
                            <label class="flex flex-col sm:flex-row">
                                Justificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea cols="30" rows="5"
                                      v-model="form.justify"
                                      class="form-control"
                                      :class="{ 'border-danger': v$.form.justify.$error }">
                            </textarea>

                            <template v-if="v$.form.justify.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.justify.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button  @click.prevent="update(form)" :disabled="form.processing" type="submit"
                        class="btn btn-primary">
                    Actualizar
                </button>
            </template>

        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {required, minLength, maxLength, numeric, minValue, maxValue, email, helpers} from '@/utils/i18n-validators'
const validNames = helpers.regex(/^[a-z0-9_ ]*$/i)

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal
    },

    validations() {
        switch (this.form.field) {
            case "contact":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(20),
                            required,
                            validNames: helpers.withMessage('Nombre invalido', validNames),
                        },
                        justify: {required}
                    }
                }
            case "address1":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(60),
                            required,
                        },
                        justify: {required}
                    }
                }
            case "address2":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(64),
                            required,
                        },
                        justify: {required}
                    }
                }
            case "phone1":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(15),
                            numeric,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "phone2":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(15),
                            numeric,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "cellphone":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(12),
                            numeric,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "contact_email":
                return {
                    form: {
                        value: {
                            email,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "billing_email":
                return {
                    form: {
                        value: {
                            email,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "currency":
            case "customer_type":
            case "gravado":
            case "seller":
            case "code_city_ext":
            case "rut":
            case "great_contributor":
            case "responsible_iva":
            case "economic_group":
            case "paid_term":
            case "copy_emails":
                return {
                    form: {
                        value: {
                            required,
                        },
                        justify: {required}
                    }
                }
            case "discount":
                return {
                    form: {
                        value: {
                            minValue: minValue(0),
                            maxValue: maxValue(20),
                            numeric,
                            required,
                        },
                        justify: {required}
                    }
                }
            case "responsible_electronic_billing":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(30),
                            required,
                            validNames: helpers.withMessage('Nombre invalido', validNames),
                        },
                        justify: {required}
                    }
                }
            case "phone_electronic_billing":
                return {
                    form: {
                        value: {
                            minLength: minLength(5),
                            maxLength: maxLength(20),
                            numeric,
                            required,
                        },
                        justify: {required}
                    }
                }
        }
    },

    props: {
        form: {
            field: '',
            value: null,
            justify: '',
            title: '',
            processing: false,
        },
        modal_data: Object,

        isOpen: {
            default: false
        }
    },
    data() {
        return {
            customers_types: [],
            paid_terms: [],
            sellers: [],
            economic_groups: []
        }
    },
    methods: {
        closeModal() {
            this.$emit('close')
            this.v$.$reset()
        },
        update(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            } else {
                axios.post(route('customers.update-info'), {
                    nit: this.modal_data.NIT,
                    customer_code: this.modal_data.CODIGO_CLIENTE,
                    field_name: this.form.field,
                    field_value: this.form.value,
                    justify: this.form.justify
                }).then(resp => {
                    this.$emit('success_update', resp.data)
                    this.$swal.close()
                    this.closeModal();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                        timer: 6000
                    });
                    console.log(err)
                })
            }
        },
        get_customer_types() {
            axios.post(route('third-parties.customer-types')).then(resp => {
                this.customers_types = resp.data
            }).catch(err => {
                console.log(err)
            })
        },

        get_paid_terms() {
            axios.post(route('third-parties.paid-terms')).then(resp => {
                this.paid_terms = resp.data
            }).catch(err => {
                console.log(err)
            })
        },

        get_sellers() {
            axios.post(route('third-parties.sellers')).then(resp => {
                this.sellers = resp.data
            }).catch(err => {
                console.log(err)
            })
        },

        get_economic_groups() {
            axios.post(route('third-parties.economic-groups')).then(resp => {
                this.economic_groups = resp.data
            }).catch(err => {
                console.log(err)
            })
        }


    },
    watch: {
        form: function () {
            if (this.form.field === 'customer_type') {
                this.get_customer_types()
            } else if (this.form.field === 'paid_term') {
                this.get_paid_terms()
            } else if (this.form.field === 'seller') {
                this.get_sellers()
            } else if (this.form.field === 'economic_group') {
                this.get_economic_groups()
            }
        }
    }
}
</script>


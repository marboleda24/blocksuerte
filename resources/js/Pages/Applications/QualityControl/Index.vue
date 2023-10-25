<template>
    <div>
        <Head title="Control de Calidad"/>

        <portal to="application-title">
            Control de Calidad
        </portal>

        <div>
            <div class="grid grid-cols-3 gap-4">
                <input class="form-control"
                       type="number"
                       v-model.number="form.production_order"
                       placeholder="escribe aquí la orden de producción sin los ultimos 4 ceros">
                <button class="btn btn-primary" type="button" @click="get_production_order(form.production_order)">
                    Consultar orden de producción
                </button>
                <button class="btn btn-secondary" type="button" @click="downloadReview">
                    Descargar información (Excel)
                </button>
            </div>

            <div class="intro-y box mt-10" v-if="production_order_data">
                <div class="p-5">
                    <div class="grid grid-cols-1 gap-5 text-xl text-center">
                        <div class="flex flex-row mx-auto text-2xl font-medium">
                            ORDER DE PRODUCCION <br> {{ production_order_data.production_order }}
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-5 text-xl text-center mt-5">
                        <div class="flex flex-row mx-auto">
                            <span class="font-medium mr-2">PIEZA:</span>
                            {{ production_order_data.state.PRTNUM_14 }}
                        </div>
                        <div class="flex flex-row mx-auto">
                            <span class="font-medium mr-2">DESCRIPCIÓN:</span>
                            {{ production_order_data.state.PMDES1_01 }}
                        </div>
                        <div class="flex flex-row mx-auto">
                            <span class="font-medium mr-2">ORDEN DE VENTA:</span>
                            {{ production_order_data.state.OV }}
                        </div>
                        <div class="flex flex-row mx-auto">
                            <span class="font-medium mr-2">ARTE:</span>
                            {{ production_order_data.state.UDFREF_10 }}
                        </div>
                    </div>

                    <div class="accordion accordion-boxed mt-10">
                        <div class="accordion-item"
                             v-for="(work_center, index) in production_order_data.work_centers">
                            <div :id="work_center.code" class="accordion-header">

                                <button
                                    class="accordion-button"
                                    :class="{'collapsed' : index > 0, 'text-green-600' : work_center.code === production_order_data.current_work_center[0]}"
                                    type="button"
                                    data-tw-toggle="collapse"
                                    :data-tw-target="`#${work_center.code}`"
                                    aria-expanded="false"
                                    :aria-controls="work_center.code"
                                >
                                    {{ `${work_center.code} - ${work_center.description}` }}
                                </button>
                            </div>

                            <div
                                :id="work_center.code"
                                class="accordion-collapse collapse"
                                :class="{'show' : index < 1}"
                                :aria-labelledby="work_center.code"
                                :data-tw-parent="`#${work_center.code}`"
                            >
                                <div class="accordion-body text-slate-600 dark:text-slate-500 leading-relaxed">

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="border border-b-2 dark:border-dark-5">
                                                CANT. INSPECCIONADA
                                            </th>
                                            <th class="border border-b-2 dark:border-dark-5">
                                                CANT. CONFORME
                                            </th>
                                            <th class="border border-b-2 dark:border-dark-5">
                                                CANT. NO INSPECCIONADA
                                            </th>
                                            <th class="border border-b-2 dark:border-dark-5">
                                                OPERARIO
                                            </th>
                                            <th class="border border-b-2 dark:border-dark-5">
                                                INSPECTOR
                                            </th>
                                            <th class="border border-b-2 dark:border-dark-5 text-center">
                                                <Tippy tag="button" content="Agregar inspeccion"
                                                       class="btn btn-sm btn-secondary"
                                                       :options="{ placement: 'left' }"
                                                       @click.native="registerReview(work_center.code)"
                                                >
                                                    <font-awesome-icon icon="plus"/>
                                                </Tippy>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-if="work_center.registries.length > 0">
                                            <tr v-for="registry in work_center.registries">
                                                <td class="border">{{ registry.quantity_inspected }}</td>
                                                <td class="border">{{ registry.conforming_quantity }}</td>
                                                <td class="border">{{ registry.non_conforming_quantity }}</td>
                                                <td class="border">{{ registry.operator.name }}</td>
                                                <td class="border">{{ registry.inspector.name }}</td>
                                                <td class="border text-center">
                                                    <Tippy tag="button" content="Detalles de la inspeccion"
                                                           class="btn btn-sm btn-secondary"
                                                           @click="viewReview(registry)"
                                                           :options="{ placement: 'left' }">
                                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                                    </Tippy>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td colspan="6" class="border col-span-6">
                                                    <div class="alert alert-danger-soft show flex items-center mb-2"
                                                         role="alert">
                                                        <AlertOctagonIcon class="w-6 h-6 mr-2"/>
                                                        Aun no se registran revisiones en este centro de trabajo!
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=3xl @close="closeModal">
                <template #title>
                    Control de calidad
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Tipo de registro
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                            </label>

                            <TomSelect v-model="form.type"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.type.$error }">
                                <option value="">Seleccione...</option>
                                <option value="inspection">Inspección</option>
                                <option value="production">Producción</option>
                            </TomSelect>

                            <template v-if="v$.form.type.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.type.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Cantidad Inspeccionada
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="number" class="form-control"
                                   :class="{ 'border-danger': v$.form.quantity_inspected.$error }"
                                   v-model="v$.form.quantity_inspected.$model">

                            <template v-if="v$.form.quantity_inspected.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.quantity_inspected.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Cantidad no conforme ({{ non_conforming_percent }} %)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="number" class="form-control"
                                   :class="{ 'border-danger': v$.form.non_conforming_quantity.$error }"
                                   v-model="v$.form.non_conforming_quantity.$model">

                            <template v-if="v$.form.non_conforming_quantity.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.non_conforming_quantity.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Cantidad conforme
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input type="number" class="form-control"
                                   :class="{ 'border-danger': v$.form.conforming_quantity.$error }"
                                   v-model="v$.form.conforming_quantity.$model"
                                   :readonly="form.type === 'inspection'">

                            <template v-if="v$.form.conforming_quantity.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.conforming_quantity.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Causa
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.cause_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.cause_id.$error }">
                                <option value="">Seleccione...</option>
                                <option v-for="cause in causes" :value="cause.id">{{ cause.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.cause_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.cause_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Operario
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.operator_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.operator_id.$error }">
                                <option value="">Seleccione...</option>
                                <option v-for="operator in operators" :value="operator.id">{{ operator.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.operator_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.operator_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <template v-if="form.type === 'inspection'">
                            <div class="mb-2">
                                <label class="flex flex-col sm:flex-row">
                                    Usuario inspección y empaque
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                                </label>

                                <select class="form-select"
                                        :class="{ 'border-danger': v$.form.inspection_user_id.$error }"
                                        v-model="v$.form.inspection_user_id.$model">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option v-for="user in inspection_users" :value="user.id">{{ user.name }}</option>
                                </select>

                                <template v-if="v$.form.inspection_user_id.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger"
                                            v-for="(error, index) of v$.form.inspection_user_id.$errors" :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>
                        </template>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Reportado a
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <TomSelect v-model="form.reported_to_id"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.reported_to_id.$error }">
                                <option value="">Seleccione...</option>
                                <option v-for="user in quality_users" :value="user.id">{{ user.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.reported_to_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.reported_to_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Tratamiento a la no conformidad
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control resize-none"
                                      :class="{ 'border-danger': v$.form.non_compliant_treatment.$error }"
                                      v-model="v$.form.non_compliant_treatment.$model" cols="30" rows="3">
                                </textarea>

                            <template v-if="v$.form.non_compliant_treatment.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.non_compliant_treatment.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Acciones Tomadas
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control resize-none"
                                      :class="{ 'border-danger': v$.form.actions.$error }"
                                      v-model="v$.form.actions.$model" cols="30" rows="3">
                            </textarea>

                            <template v-if="v$.form.actions.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.actions.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label class="flex flex-col sm:flex-row">
                                Observaciones
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <textarea class="form-control resize-none"
                                      :class="{ 'border-danger': v$.form.observations.$error }"
                                      v-model="v$.form.observations.$model" cols="30" rows="3">
                            </textarea>

                            <template v-if="v$.form.observations.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.observations.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2 col-span-3">
                            <label class="flex flex-col sm:flex-row">
                                Evidencia fotográfica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input type="file"
                                   ref="files"
                                   @change="fileChange($event)"
                                   accept="image/*"
                                   class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary" multiple>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cerrar
                    </button>
                    <button @click="storeReview(form)" type="button" class="btn btn-primary ">
                        Registrar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="reviewModal.open" max-width="5xl" @close="closeModal">
                <template #title>
                    Revision
                </template>

                <template #content>
                    <div class="grid grid-cols-4 gap-5">
                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Cantidad inspeccionada
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.quantity_inspected }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Cantidad conforme
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.conforming_quantity }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="hashtag" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Cantidad no conforme
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.non_conforming_quantity }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="message" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Tratamiento a la no conformidad
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.non_compliant_treatment }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="message" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Accciones
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.actions }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="message" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Observaciones
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.observations }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="helmet-safety" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Operario
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.operator.name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="user-check" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Inspector
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.inspector.name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box" v-if="reviewModal.data.reported_to">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="user-shield" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Reportado a
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.reported_to.name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="image-fit lg:mr-1 items-center justify-center">
                                    <font-awesome-icon icon="triangle-exclamation" size="3x"/>
                                </div>

                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="javascript:void(0)" class="font-medium">
                                        Causa
                                    </a>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ reviewModal.data.cause.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-5" v-if="reviewModal.data.files.length > 0">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <td class="text-center" colspan="2">ARCHIVOS</td>
                                </tr>
                                <tr>
                                    <td>NOMBRE</td>
                                    <td></td>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="file in reviewModal.data.files">
                                    <td>{{ file.name }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary" @click="downloadFile(file.path, file.name)">
                                            <font-awesome-icon icon="download" class="mr-1"/>
                                            Descargar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-secondary" @click="closeModal">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, requiredIf, minValue} from '@/utils/i18n-validators'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        FontAwesomeIcon,
        JetDialogModal,
        Head
    },

    props: {
        operators: Array,
        quality_users: Array,
        causes: Array,
        inspection_users: Array,
        plants: Array
    },

    validations() {
        return {
            form: {
                production_order: {required},
                quantity_inspected: {required, minValue: minValue(1)},
                conforming_quantity: {required, minValue: minValue(1)},
                non_conforming_quantity: {required, minValue: minValue(1)},
                cause_id: {required},
                operator_id: {required},
                reported_to_id: {required},
                non_compliant_treatment: {required},
                actions: {required},
                observations: {required},
                work_center: {required},
                type: {required},
                inspection_user_id: {
                    required: requiredIf(function () {
                        return this.form.type === 'inspection'
                    })
                }
            }
        }
    },

    data() {
        return {
            form: {
                production_order: '',
                quantity_inspected: 0,
                conforming_quantity: 0,
                non_conforming_quantity: 0,
                cause_id: '',
                operator_id: '',
                reported_to_id: '',
                non_compliant_treatment: '',
                actions: '',
                observations: '',
                work_center: '',
                type: '',
                inspection_user_id: '',
                files: null
            },
            production_order_data: null,
            isOpen: false,

            reviewModal: {
                open: false,
                data: {}
            }
        }
    },

    methods: {
        get_production_order(production_order) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('quality-control.get-production-order', production_order)).then(resp => {
                this.production_order_data = resp.data
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            });
        },

        closeModal() {
            this.isOpen = false
            this.form = {
                production_order: '',
                quantity_inspected: 0,
                conforming_quantity: 0,
                non_conforming_quantity: 0,
                cause_id: '',
                operator_id: '',
                reported_to_id: '',
                non_compliant_treatment: '',
                actions: '',
                observations: '',
                work_center: '',
                type: '',
                inspection_user_id: '',
                files: null
            }
            this.reviewModal = {
                open: false,
                data: {}
            }
        },

        registerReview(work_center) {
            this.isOpen = true
            this.form = {
                production_order: this.production_order_data.production_order,
                quantity_inspected: 0,
                conforming_quantity: 0,
                non_conforming_quantity: 0,
                cause_id: '',
                operator_id: '',
                reported_to_id: '',
                non_compliant_treatment: '',
                actions: '',
                observations: '',
                work_center: work_center,
                type: '',
                inspection_user_id: '',
                files: null
            }
        },

        storeReview(form) {
            const formData = new FormData();

            Object.keys(form).forEach(key => formData.append(key, form[key]))
            formData.delete('files');

            for (let i = 0; i < this.$refs.files.files.length; i++) {
                let file = this.$refs.files.files[i];
                formData.append('files[]', file);
            }

            axios.post(route('quality-control.save-review'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(resp => {
                this.production_order_data = resp.data
                this.closeModal();
                this.$swal({
                    title: '¡Éxito!',
                    text: "Linea creada con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
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

        downloadReview() {
            let fileName = "quality-control-review.xlsx"
            axios.get(route('quality-control.download-review'), {
                responseType: 'blob'
            }).then(resp => {
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', fileName)
                document.body.appendChild(link)
                link.click()
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

        report() {
            let fileName = "quality-control-review.xlsx"
            axios.get('characterize/report', {
                responseType: 'blob'
            }).then(resp => {
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', fileName)
                document.body.appendChild(link)
                link.click()
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

        fileChange(event){
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.files = ''
            }
            this.form.files = files;
        },

        downloadFile(path, name){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Archivo…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('quality-control.download-file'), {
                path: path,
                name: name
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', name);
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

        viewReview(row){
            this.reviewModal = {
                open: true,
                data: row
            }
        }
    },

    computed: {
        non_conforming_percent(){
            let percent = (this.form.non_conforming_quantity * 100) / this.form.quantity_inspected
            return isNaN(percent) ? 0 : percent.toFixed(2)
        },
    },

    watch: {
        'form.non_conforming_quantity': function () {
            if (this.form.type === 'inspection'){
                this.form.conforming_quantity = this.form.quantity_inspected - this.form.non_conforming_quantity
            }
        }
    }
}
</script>


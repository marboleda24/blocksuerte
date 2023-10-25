<template>
    <div>
        <Head :title="`Requerimiento ${requirement_data.consecutive}`"/>

        <portal to="application-title">
            Requerimiento #{{ requirement_data.consecutive }}
        </portal>

        <portal to="actions">
            <Link :href="route('design-requirements.index')" class="btn btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Requerimientos
            </Link>
        </portal>

        <proposal :requirement="requirement_data" :product_types="product_types" @store="storeProposal"
                  @update-proposals="storeProposal"/>

        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Especificaciones del Diseño</h2>
            </div>

            <div class="p-5">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="whitespace-nowrap">
                            <span class="font-semibold">CLIENTE:</span>
                            {{ requirement_data.customer.RAZON_SOCIAL }}
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="font-semibold">FECHA CREACIÓN: </span>
                            {{ $h.formatDate(requirement_data.created_at, 'DD-MM-YYYY') }}
                        </td>
                        <td class="whitespace-nowrap items-center">
                            <div class="grid grid-cols-2">
                                <div class="flex items-center">
                                    <span class="font-semibold">ESTADO:</span>
                                    <div v-html="requirement_state"></div>
                                </div>

                                <button class="btn btn-sm btn-secondary ml-auto"
                                        v-permission="'design-requirements.change-state'"
                                        v-if="requirement_data.state !== '5'"
                                        @click="changeState">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']"/>
                                </button>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td class="whitespace-nowrap">
                            <div class="grid grid-cols-2">
                                <div class="flex items-center">
                                    <span class="font-semibold">PRODUCTO: </span>
                                    {{ requirement_data.product }}
                                </div>
                                <button class="btn btn-sm btn-secondary ml-auto"
                                        v-permission="'design-requirements.change-product'"
                                        v-if="requirement_data.proposals.length === 0"
                                        @click="changeProduct">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']"/>
                                </button>
                            </div>

                        </td>

                        <td class="whitespace-nowrap">
                            <div class="grid grid-cols-2">
                                <div class="flex items-center">
                                    <span class="font-semibold mr-2">MARCA: </span>
                                    {{ requirement_data.brand.name }}
                                </div>
                                <button class="btn btn-sm btn-secondary ml-auto"
                                        v-permission="'design-requirements.change-brand'"
                                        v-if="requirement_data.state !== '5'"
                                        @click="loadBrands">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']"/>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="whitespace-nowrap">
                            <span class="font-semibold">VENDEDOR(A): </span>
                            {{ requirement_data.seller.name }}
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="grid grid-cols-2">
                                <div class="flex items-center">
                                    <span class="font-semibold mr-2">DISEÑADORA ASIGNADA:</span>
                                    <span v-if="requirement_data.assigned_designer">
                                            {{ requirement_data.assigned_designer.name }}
                                        </span>
                                    <span v-else class="text-danger uppercase">
                                            sin asignar
                                        </span>
                                </div>

                                <button class="btn btn-sm btn-secondary ml-auto"
                                        v-permission="'design-requirements.change-designer'"
                                        v-if="requirement_data.state !== '5'"
                                        @click="assign_designer">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']"/>
                                </button>
                            </div>
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="font-semibold">¿RENDER 3D?: </span>
                            {{ requirement_data.render === 'yes' ? 'SI' : 'NO' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="whitespace-normal">
                            <span class="font-semibold">DETALLES DEL REQUERIMIENTO: </span>
                            {{ requirement_data.details }}
                        </td>

                        <td class="whitespace-nowrap">
                            <span class="font-semibold">¿REQUIERE BASE?: </span>
                            {{ requirement_data.base === 'yes' ? 'SI' : 'NO' }}
                        </td>

                        <td class="whitespace-nowrap">
                            <span class="font-semibold">PARÁMETRO: </span>
                            {{
                                requirement_data.additional_service === 'yes' ? 'ORO O TERMINADO ESP' : 'SIN PARAMETRO'
                            }}
                        </td>
                    </tr>
                    </thead>
                </table>
            </div>


            <div class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60 dark:border-dark-5">
                <div class="grid grid-cols-4 gap-4">
                    <button class="btn btn-danger"
                            v-permission="'design-requirements.refuse'"
                            v-if="requirement_data.state !== '5' && requirement_data.state !== '6' && requirement_data.state !== '0'"
                            @click="refuse">
                        <font-awesome-icon icon="rotate-left" class="mr-2"/>
                        Rechazar
                    </button>

                    <button class="btn btn-danger"
                            @click="cancelRequirement"
                            v-if="requirement_data.state === '1' && requirement_data.proposals.length > 0 || requirement_data.state === '6' && requirement_data.proposals.length > 0"
                            v-permission="'design-requirements.cancel'">
                        <font-awesome-icon icon="ban" class="mr-2"/>
                        Anular
                    </button>

                    <button class="btn btn-primary"
                            @click="finishRequirement"
                            v-permission="'design-requirements.finish'"
                            v-if="requirement_data.state !== '5' && requirement_data.state !== '6'">
                        <font-awesome-icon :icon="['far', 'circle-check']" class="mr-2"/>
                        Finalizar
                    </button>

                    <button class="btn btn-danger"
                            @click="deleteRequirement"
                            v-if="requirement_data.proposals.length === 0">
                        <font-awesome-icon :icon="['far', 'trash-can']" class="mr-2"/>
                        Eliminar requerimiento
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5 mt-5">
            <div class="chat__box box col-span-2">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto">Comentarios</h2>

                    <div class="w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0"
                         v-permission="'design-requirements.logs'">
                        <Tippy
                            tag="button"
                            content="Visualizar logs"
                            class="btn btn-sm btn-secondary"
                            :options="{
                                placement: 'left'
                            }"
                            href="javascript:void(0)"
                            @click.native="log_modal_open = true"
                        >
                            <font-awesome-icon icon="list-ol"/>
                        </Tippy>
                    </div>
                </div>

                <div class="overflow-y-scroll scrollbar-hidden px-5 pt-5 flex-1 h-72 max-h-72" ref="comments">
                    <template v-for="log in requirement_data.logs">

                        <template v-if="log.created_user.id === $page.props.user.id && log.type === 'comment'">
                            <div class="chat__box__text-box flex items-end float-right mb-4">
                                <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md">
                                    {{ log.description }}
                                    <div class="mt-1 text-xs text-white text-opacity-80">
                                        {{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}
                                    </div>
                                </div>
                                <div class="w-10 h-10 hidden sm:block flex-none image-fit relative ml-2">
                                    <Tippy tag="img"
                                           :content="log.created_user.name"
                                           class="rounded-full"
                                           :src="log.created_user.profile_photo_url"
                                           :options="{ placement: 'top' }"
                                    />
                                </div>
                            </div>
                            <div class="clear-both"></div>
                        </template>

                        <template v-else-if="log.created_user.id !== $page.props.user.id && log.type === 'comment'">
                            <div class="chat__box__text-box flex items-end float-left mb-4">
                                <div class="w-10 h-10 hidden sm:block flex-none image-fit relative mr-2">
                                    <Tippy tag="img"
                                           :content="log.created_user.name"
                                           class="rounded-full"
                                           :src="log.created_user.profile_photo_url"
                                           :options="{ placement: 'top' }"
                                    />
                                </div>
                                <div
                                    class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md">
                                    {{ log.description }}
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}
                                    </div>
                                </div>
                            </div>
                            <div class="clear-both"></div>
                        </template>
                    </template>
                </div>

                <div class="pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400">
                    <textarea
                        class="chat__box__input form-control dark:bg-darkmode-600 h-12 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                        @keypress.enter.prevent="addComment"
                        v-model="comment"
                        rows="1"
                        placeholder="Escribe tu mensaje…"></textarea>

                    <a href="javascript:;"
                       @click="addComment"
                       class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5">
                        <font-awesome-icon :icon="['far', 'paper-plane']"/>
                    </a>
                </div>
            </div>

            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto">Archivos de Soporte</h2>
                    <div class="w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0"
                         v-permission="'design-requirements.upload-support-files'"
                         v-if="requirement_data.state !== '5'">
                        <Tippy
                            tag="button"
                            content="Agregar archivos"
                            class="btn btn-sm btn-secondary"
                            :options="{
                                placement: 'left'
                            }"
                            href="javascript:void(0)"
                            @click.native="uploadFile"
                        >
                            <font-awesome-icon icon="plus"/>
                        </Tippy>
                    </div>
                </div>

                <div class="p-2 max-h-72 overflow-y-scroll scrollbar-hidden">

                    <table class="table table-sm table-bordered" v-if="requirement_data.files.length > 0">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">NOMBRE</th>
                            <th class="whitespace-nowrap"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="file in requirement_data.files" v-bind:key="file.id">
                            <td>{{ $h.truncateString(file.name, 40) }}</td>
                            <td class="text-center">
                                <div class="flex flex-row items-center justify-center">
                                    <button class="btn btn-sm btn-outline-success mr-2"
                                            v-permission:has.disabled="'design-requirements.download-support-files'"
                                            @click="downloadFile(file)">
                                        <DownloadIcon class="w-5 h-5"/>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger"
                                            v-permission:has.disabled="'design-requirements.delete-support-files'"
                                            @click="removeFile(file)">
                                        <TrashIcon class="w-5 h-5"/>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>


                    <div v-else class="items-center justify-items-center text-center align-middle">
                        <img src="/dist/images/folder_empty.png" alt="" class="mx-auto w-48 object-center"
                             draggable="false">
                        <div class="text-lg font-medium text-danger">
                            Aun no se han agregado archivos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="log_modal_open" @close="log_modal_open = false" max-width="3xl">
            <template #title>
                Logs
            </template>

            <template #content>
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">Descripción</th>
                            <th class="whitespace-nowrap">Responsable</th>
                            <th class="whitespace-nowrap">Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="log in requirement_data.logs" v-bind:key="log.id">
                            <tr v-if="log.type === 'log'">
                                <td>{{ log.description }}</td>
                                <td>{{ log.created_user.name }}</td>
                                <td>{{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}</td>
                            </tr>

                        </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="change_product_modal.open" @close="change_product_modal.open = false" max-width="3xl">
            <template #title>
                Cambiar producto
            </template>

            <template #content>
                <div class="grid grid-cols-3 gap-5">
                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Tipo de producto
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="change_product_modal.product_type_code"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                @change="getLines(change_product_modal.product_type_code)">
                            <option v-for="product_type in product_types" :value="product_type.code">
                                {{ product_type.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Linea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="change_product_modal.line_code"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                @change="getSublines(change_product_modal.line_code)">
                            <option v-for="line in lines" :value="line.code">
                                {{ line.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Sublinea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="change_product_modal.subline_code"
                                @change="getAnotherInputs(change_product_modal.line_code, change_product_modal.subline_code)">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="subline in sublines" :value="subline.code">
                                {{ subline.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Característica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="change_product_modal.feature_code">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="feature in features" :value="feature.code">
                                {{ feature.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Material
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="change_product_modal.material_id">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="material in materials" :value="material.id">
                                {{ material.material.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Medida
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                v-model="change_product_modal.measurement_id">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="measurement in measurements" :value="measurement.id">
                                {{ $h.denominationCreator(measurement.detail) }}
                            </option>
                        </select>
                    </div>
                </div>
            </template>

            <template #footer>
                <button class="btn btn-primary mr-2"
                        @click="changeProductStore">
                    Cambiar producto
                </button>
                <button @click="closeModal()" type="button" class="btn btn-secondary">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>

    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head, Link} from '@inertiajs/vue3'
import Autocomplete from '@/GlobalComponents/Autocomplete/Main.vue'
import Proposal from "@/Pages/Applications/DesignRequirements/Proposal.vue";
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Autocomplete,
        Head,
        Link,
        Proposal
    },

    props: {
        requirement: Object,
        designers: Array,
        product_types: Array
    },

    validations(){
        return {
            change_product_modal: {
                id: {required},
                product_type_code: {required},
                line_code: {required},
                subline_code: {required},
                feature_code: {required},
                material_id: {required},
                measurement_id: {required},
            }
        }
    },

    data() {
        return {
            requirement_data: this.requirement,
            comment: '',
            log_modal_open: false,
            proposal_view_modal_open: false,
            brands: [],
            lines: [],
            sublines: [],
            features: [],
            materials: [],
            measurements: [],

            change_product_modal: {
                open: false,
                id: '',
                product_type_code: '',
                line_code: '',
                subline_code: '',
                feature_code: '',
                material_id:'',
                measurement_id: '',
            }
        }
    },

    methods: {
        changeProduct() {
            this.change_product_modal = {
                open: true,
                id: this.requirement.id,
                product_type_code: this.requirement.product_type.code,
                line_code: this.requirement.line.code,
                subline_code: this.requirement.subline.code,
                feature_code: this.requirement.feature.code,
                material_id: this.requirement.material.id,
                measurement_id: this.requirement.measurement.id,
            }
            this.getLines(this.change_product_modal.product_type_code)
            this.getSublines(this.change_product_modal.line_code)
            this.getAnotherInputs(this.change_product_modal.line_code, this.change_product_modal.subline_code)
        },

        changeProductStore(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cambiando producto…',
                text: 'Este proceso puede tardar unos segundos…',
            });
            axios.post(route('design-requirement.change-product'), this.change_product_modal).then(resp => {
                this.closeModal()
                this.$swal({
                    title: '¡Éxito!',
                    text: "Producto cambiado con éxito",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
                this.requirement_data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })

        },

        getLines(product_type_code) {
            this.resetFields(true, true, true, true, true)

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-lines'), {
                params: {
                    product_type: product_type_code
                }
            }).then(resp => {
                this.lines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getSublines(line_code) {
            this.resetFields(false, true, true, true, true)

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-sublines'), {
                params: {
                    line_code: line_code
                }
            }).then(resp => {
                this.sublines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getAnotherInputs(line_code, subline_code) {
            this.resetFields(false, false, true, true, true)

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-other-inputs'), {
                params: {
                    line_code: line_code,
                    subline_code: subline_code
                }
            }).then(resp => {
                this.features = resp.data.features
                this.materials = resp.data.materials
                this.measurements = resp.data.measurements
                this.$swal.close();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        resetFields(lines, sublines = false, features = false, materials = false, measurements = false) {
            if (lines) this.lines = []
            if (sublines) this.sublines = []
            if (features) this.features = []
            if (materials) this.materials = []
            if (measurements) this.measurements = []
        },

        storeProposal(data) {
            this.requirement_data.logs = data.logs
            this.requirement_data.proposals = data.proposals
        },

        async changeState() {
            const steps = ['1', '2']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                state: this.requirement_data.state,
                justify: ''
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Estado',
                        text: 'Por favor, seleccione un estado',
                        inputValue: values.state,
                        input: 'select',
                        inputOptions: {
                            '0': 'Anulado',
                            '1': 'Pendiente Revision',
                            '2': 'Asignado',
                            '3': 'Iniciado',
                            '4': 'Por Corregir',
                            '5': 'Finalizado',
                        },
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-select',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                    })
                } else if (currentStep === 1) {
                    result = await swalQueueStep.fire({
                        title: 'Justificación',
                        text: 'Por favor, escriba una justificación para esta acción',
                        inputValue: values.justify,
                        input: 'textarea',
                        showCancelButton: currentStep > 0,
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-control',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        confirmButtonText: 'Cambiar Estado',

                    })
                }
                if (result.value) {
                    currentStep === 0
                        ? values.state = result.value
                        : values.justify = result.value
                    currentStep++
                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    currentStep--
                } else {
                    break
                }
            }

            if (currentStep === steps.length) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.post(route('design-requirements.change-state'), {
                    id: this.requirement_data.id,
                    state: values.state,
                    justify: values.justify
                }).then(resp => {
                    this.requirement_data.state = resp.data.state
                    this.requirement_data.logs = resp.data.logs
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Estado actualizado con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        async uploadFile() {
            this.$swal({
                icon: 'info',
                title: 'Por favor, seleccione un archivo',
                input: 'file',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Cargar Archivo',
                cancelButtonText: 'Cancelar',
                customClass: {
                    input: 'form-select',
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary mr-2'
                },
                buttonsStyling: false,
                reverseButtons: true,
                inputAttributes: {
                    required: true,
                    id: 'upload_file',
                    accept: 'image/*,.pdf,.csv,.xlsx,.xls,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword'
                },
                inputValidator: (inputValue) => {
                    return !inputValue && 'Debes seleccionar un archivo'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    let formData = new FormData();
                    formData.append('file', inputValue.value);
                    formData.append('id', this.requirement_data.id);

                    axios.post(route('design-requirements.upload-file'), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(resp => {
                        this.requirement_data.files = resp.data.files
                        this.requirement_data.logs = resp.data.logs
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: 'Ups.. hubo un error procesando la solicitud',
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 6000,
                        });
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            })
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

            axios.post(route('design-requirements.download-file'), {
                path: file.path
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

        removeFile(file) {
            this.$swal({
                title: '¿Eliminar archivo?',
                text: '¡Esta acción no es reversible!, por favor escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Eliminar',
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
                    axios.post(route('design-requirements.remove-file'), {
                        id: this.requirement_data.id,
                        path: file.path,
                        file_id: file.id,
                        justify: inputValue.value
                    }).then(resp => {
                        this.requirement_data.files = resp.data.files
                        this.requirement_data.logs = resp.data.logs
                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Archivo eliminado con éxito!",
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
                        console.error(err);
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            });
        },

        addComment() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('design-requirements.add-comment'), {
                id: this.requirement_data.id,
                comment: this.comment
            }).then(resp => {
                this.requirement_data.logs = resp.data
                this.comment = ''
                this.endScrollComments()
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

        async assign_designer() {
            const rObj = {
                0: 'Seleccione…'
            };
            this.designers.map(function (obj) {
                rObj[obj.id] = obj.name;
                return rObj;
            })

            if (this.requirement_data.assigned_designer) {
                const steps = ['1', '2']
                const swalQueueStep = this.$swal.mixin({
                    confirmButtonText: 'Siguiente',
                    cancelButtonText: 'Atrás',
                    progressSteps: steps,
                    validationMessage: 'Este campo es obligatorio'
                })

                const values = {
                    designer: this.requirement_data.assigned_designer.id,
                    justify: ''
                }

                let currentStep

                for (currentStep = 0; currentStep < steps.length;) {
                    let result = {};
                    if (currentStep === 0) {
                        result = await swalQueueStep.fire({
                            title: 'Diseñador',
                            text: 'Por favor, seleccione un diseñador',
                            inputValue: values.designer,
                            input: 'select',
                            inputOptions: rObj,
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            currentProgressStep: currentStep,
                            inputAttributes: {
                                required: true
                            },
                            customClass: {
                                input: 'form-select',
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary mr-2'
                            },
                            buttonsStyling: false,
                            reverseButtons: true,
                        })
                    } else if (currentStep === 1) {
                        result = await swalQueueStep.fire({
                            title: 'Justificación',
                            text: 'Por favor, escriba una justificación para esta acción',
                            inputValue: values.justify,
                            input: 'textarea',
                            showCancelButton: currentStep > 0,
                            currentProgressStep: currentStep,
                            inputAttributes: {
                                required: true
                            },
                            customClass: {
                                input: 'form-control',
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary mr-2'
                            },
                            buttonsStyling: false,
                            reverseButtons: true,
                            confirmButtonText: 'Cambiar Diseñadora',

                        })
                    }

                    if (result.value) {
                        currentStep === 0
                            ? values.designer = result.value
                            : values.justify = result.value
                        currentStep++
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        currentStep--
                    } else {
                        break
                    }
                }

                if (currentStep === steps.length) {


                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Actualizando información…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    axios.post(route('design-requirements.change-designer'), {
                        id: this.requirement_data.id,
                        designer: values.designer,
                        justify: values.justify
                    }).then(resp => {
                        this.requirement_data.assigned_designer = resp.data.designer
                        this.requirement_data.logs = resp.data.logs
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Diseñador actualizado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(err)
                    })
                }
            } else {
                this.$swal({
                    icon: 'info',
                    title: 'Asignar Diseñador',
                    text: 'Por favor, selecciona el Diseñador que va a gestionar este requerimiento',
                    input: 'select',
                    inputOptions: rObj,
                    showCancelButton: true,
                    inputAttributes: {
                        required: true
                    },
                    customClass: {
                        input: 'form-select w-full',
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary mr-2'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    confirmButtonText: 'Asignar Diseñador',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (inputValue) => {
                        if (inputValue === '0') return 'Debes seleccionar una opción...'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {


                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Actualizando información…',
                            text: 'Este proceso puede tardar unos segundos…',
                        });

                        axios.post(route('design-requirements.assign-designer'), {
                            id: this.requirement_data.id,
                            designer: parseInt(inputValue.value)
                        }).then(resp => {
                            this.$swal({
                                title: 'Diseñador asignado con éxito',
                                text: "El Diseñador fue asignado con éxito",
                                icon: 'info',
                                confirmButtonText: 'Aceptar',
                                timerProgressBar: true,
                                timer: 10000,
                            });
                            this.requirement_data.assigned_designer = resp.data.designer
                            this.requirement_data.logs = resp.data.logs
                            this.requirement_data.state = resp.data.state
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err);
                        })
                    }
                })
            }
        },

        async changeBrand() {
            const brands = []

            this.brands.map(elem => {
                brands[elem.id] = elem.name
            })

            const steps = ['1', '2']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                brand: this.requirement_data.brand.id,
                justify: ''
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Marca',
                        text: 'Por favor, seleccione una marca',
                        inputValue: values.brand,
                        input: 'select',
                        inputOptions: brands,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            select: 'form-select',
                            input: 'form-select',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                    })
                } else if (currentStep === 1) {
                    result = await swalQueueStep.fire({
                        title: 'Justificación',
                        text: 'Por favor, escriba una justificación para esta acción',
                        inputValue: values.justify,
                        input: 'textarea',
                        showCancelButton: currentStep > 0,
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-control',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        confirmButtonText: 'Cambiar Marca',

                    })
                }

                if (result.value) {
                    currentStep === 0
                        ? values.brand = result.value
                        : values.justify = result.value
                    currentStep++
                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    currentStep--
                } else {
                    break
                }
            }

            if (currentStep === steps.length) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.post(route('design-requirements.change-brand'), {
                    id: this.requirement_data.id,
                    brand: values.brand,
                    justify: values.justify
                }).then(resp => {
                    this.requirement_data.brand = resp.data.brand
                    this.requirement_data.logs = resp.data.logs
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Marca actualizada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        async changeMeasurements() {
            const measurements = []

            this.measurements.map(elem => {
                measurements[elem.id] = this.$h.denominationCreator(elem.detail)
            })
            const steps = ['1', '2']
            const swalQueueStep = this.$swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Atrás',
                progressSteps: steps,
                validationMessage: 'Este campo es obligatorio'
            })

            const values = {
                measurement: this.requirement_data.measurement.id,
                justify: ''
            }

            let currentStep

            for (currentStep = 0; currentStep < steps.length;) {
                let result = {};
                if (currentStep === 0) {
                    result = await swalQueueStep.fire({
                        title: 'Medida',
                        text: 'Por favor, seleccione una medida',
                        inputValue: values.measurement,
                        input: 'select',
                        inputOptions: measurements,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-select',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                    })
                } else if (currentStep === 1) {
                    result = await swalQueueStep.fire({
                        title: 'Justificación',
                        text: 'Por favor, escriba una justificación para esta acción',
                        inputValue: values.justify,
                        input: 'textarea',
                        showCancelButton: currentStep > 0,
                        currentProgressStep: currentStep,
                        inputAttributes: {
                            required: true
                        },
                        customClass: {
                            input: 'form-control',
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary mr-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        confirmButtonText: 'Cambiar Medida',

                    })
                }

                if (result.value) {
                    currentStep === 0
                        ? values.measurement = result.value
                        : values.justify = result.value
                    currentStep++
                } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                    currentStep--
                } else {
                    break
                }
            }

            if (currentStep === steps.length) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Actualizando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.post(route('design-requirements.change-measurement'), {
                    id: this.requirement_data.id,
                    measurement: values.measurement,
                    justify: values.justify
                }).then(resp => {
                    this.requirement_data.measurement = resp.data.measurement
                    this.requirement_data.logs = resp.data.logs
                    this.requirement_data.measure = resp.data.measure
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Medida actualizada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        loadBrands() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('design-requirements.get-brands'), {
                params: {
                    customer_code: this.requirement_data.customer.CODIGO_CLIENTE
                }
            }).then(resp => {
                this.brands = resp.data
                this.changeBrand()
            }).catch(err => {
                console.log(err)
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        loadMeasurements() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('design-requirements.get-measurements'), {
                params: {
                    line_code: this.requirement_data.line.code,
                    subline_code: this.requirement_data.subline.code
                }
            }).then(resp => {
                this.measurements = resp.data
                this.changeMeasurements()
            }).catch(err => {
                console.log(err)
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        endScrollComments() {
            this.$refs.comments.scrollTop = this.$refs.comments.scrollHeight;
        },

        closeModal() {
            this.log_modal_open = false
            this.proposal_view_modal_open = false
            this.change_product_modal = {
                open: false,
                product_type_code: '',
                line_code: '',
                subline_code: '',
                feature_code: '',
                material_id: '',
                measurement_id: '',
            }
        },

        refuse() {
            this.$swal({
                title: '¿Rechazar Requerimiento?',
                text: 'Escribe una justificación para esta acción',
                icon: 'question',
                input: 'textarea',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Rechazar',
                inputValidator: (inputValue) => {
                    return !inputValue && 'Por favor, escribe una justificación'
                }
            }).then((inputValue) => {
                if (inputValue.value) {


                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Rechazando requerimiento…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    axios.post(route('design-requirements.refuse'), {
                        'id': this.requirement_data.id,
                        'justify': inputValue.value
                    }).then(resp => {
                        this.requirement_data.logs = resp.data.logs;
                        this.requirement_data.state = resp.data.state;

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡El requerimiento fue rechazado!",
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
                    });
                } else {
                    inputValue.dismiss = this.$swal.DismissReason.cancel
                }
            });
        },

        viewProposal(proposal) {
            this.proposal_view_modal_open = true
        },

        update_info() {
            axios.get(route('design-requirements.requirement-info', this.requirement.id)).then(resp => {
                this.requirement_data = resp.data
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

        finishRequirement() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Validando información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('design-requirements.verify', this.requirement.id)).then(resp => {
                if (resp.data === true) {
                    this.$swal({
                        title: '¿Finalizar Requerimiento?',
                        html: `El requerimiento será finalizado y se generará un nuevo arte por cada propuesta aprobada. Recuerde que este proceso <strong class="text-danger"> NO ES REVERSIBLE </strong>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Si, finalizar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$swal({
                                iconHtml: this.$h.loadIcon(),
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                title: 'Finalizando requerimiento…',
                                text: 'Este proceso puede tardar unos segundos…',
                            });

                            axios.post(route('design-requirement.finalize'), {
                                id: this.requirement.id
                            }).then(resp => {
                                this.requirement_data = resp.data.requirement

                                this.$swal({
                                    icon: 'success',
                                    title: 'Requerimiento finalizado con éxito',
                                    html: resp.data.html,
                                    confirmButtonText: 'Aceptar',
                                })

                                this.$inertia.visit(route('design-requirements.index'));
                            }).catch(err => {
                                this.$swal({
                                    icon: 'error',
                                    title: '¡Ups!',
                                    text: 'Hubo un error procesando la solicitud.',
                                    confirmButtonText: 'Aceptar',
                                });
                                console.log(err);
                            })
                        }
                    })
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Este requerimiento aun tiene propuestas pendientes, para poder cerrarlo es necesario que al menos 1 propuesta sea aprobada y las demás estén en estado anulada o aprobada',
                        confirmButtonText: 'Aceptar',
                    });
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

        deleteRequirement() {
            this.$swal({
                title: '¿Eliminar requerimiento?',
                text: "¡Esta acción solo es reversible por un administrador!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('design-requirements.destroy', this.requirement_data.id)).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Requerimiento Eliminado con exito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                        this.$inertia.visit(route('design-requirements.index'));
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

        cancelRequirement() {
            this.$swal({
                title: '¿Anular requerimiento?',
                text: "¡Esta acción solo es reversible por un administrador!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, anular!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(route('design-requirements.cancel'), {
                        id: this.requirement_data.id
                    }).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Requerimiento anulado con exito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                        this.$inertia.visit(route('design-requirements.index'));
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
        }
    },

    computed: {
        requirement_state() {
            switch (this.requirement_data.state) {
                case "0":
                    return '<span class="ml-2 badge badge-danger">Anulado</span>'
                case "1":
                    return '<span class="ml-2 badge badge-warning">Pendiente Revision</span>'
                case "2":
                    return '<span class="ml-2 badge badge-danger">Asignado</span>'
                case "3":
                    return '<span class="ml-2 badge badge-primary">Iniciado</span>'
                case "4":
                    return '<span class="ml-2 badge badge-primary">Por Corregir</span>'
                case "5":
                    return '<span class="ml-2 badge badge-success">Finalizado</span>'
                case "6":
                    return '<span class="ml-2 badge badge-danger">Rechazado</span>'
            }
        }
    },

    mounted() {
        this.endScrollComments()
    }
}
</script>

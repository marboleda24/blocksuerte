<template>
    <div>
        <Head title="Activos - Mantenimiento"/>

        <portal to="application-title">
            Activos - Mantenimiento
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Crear activo
            </button>
        </portal>

        <div>
            <div class="intro-y box">
                <div class="p-5">
                    <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                                    class="overflow-y-auto">
                        <template v-slot:files="{row}">
                            <div class="text-center">
                                <button class="btn btn-sm btn-primary"
                                        @click="showFiles(row.files)"
                                        v-if="row.files.length > 0">
                                    <font-awesome-icon icon="eye"/>
                                </button>

                                <div v-else>
                                    –
                                </div>
                            </div>
                        </template>

                        <template v-slot:actions="{row}">
                            <div class="dropdown text-center">
                                <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                        data-tw-toggle="dropdown">
                                    <font-awesome-icon icon="bars"/>
                                </button>
                                <div class="dropdown-menu w-40">
                                    <div class="dropdown-content">
                                        <a href="javascript:void(0)"
                                           @click="edit(row)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                            Editar
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="editResume(row)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                            Editar HV
                                        </a>

                                        <a :href="route('maintenance.asset.resume-pdf', row.id)"
                                           target="_blank"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'file-pdf']" class="mr-1"/>
                                            Imprimir HV
                                        </a>

                                        <a href="javascript:void(0)"
                                           @click="delete(row.id)"
                                           class="dropdown-item">
                                            <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                            Eliminar
                                        </a>

                                        <Link :href="route('maintenance.asset.report', row.id)"
                                              class="dropdown-item">
                                            <font-awesome-icon :icon="['fas', 'chart-pie']" class="mr-1"/>
                                            Reportes
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </v-client-table>
                </div>
            </div>

            <jet-dialog-modal :show="isOpen" max-width=xl>
                <template #title>
                    {{ title }}
                </template>

                <template #content>
                    <div class="grid grid-cols-2 gap-4 p-2">
                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Código
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.code.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.code.$error }" placeholder="Código"
                                   :disabled="editMode"/>

                            <template v-if="v$.form.code.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.code.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Nombre
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.name.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.name.$error }" placeholder="Nombre"/>

                            <template v-if="v$.form.name.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Clasificación
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model="v$.form.classification_id.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.classification_id.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="classification in asset_classifications" :value="classification.id"
                                        v-bind:key="classification.id">{{ classification.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.classification_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.classification_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Estado
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model="v$.form.state.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.state.$error }">
                                <option value="">Seleccione…</option>
                                <option value="good">Bueno</option>
                                <option value="repair">En Reparación</option>
                                <option value="discarded">Descartado</option>
                            </select>

                            <template v-if="v$.form.state.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.state.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Centro de trabajo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model="v$.form.work_center_id.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.work_center_id.$error }">
                                <option value="">Seleccione…</option>
                                <option v-for="work_center in work_centers" :value="work_center.id"
                                        v-bind:key="work_center.id">{{ work_center.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.work_center_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.work_center_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Prioridad
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model="v$.form.priority.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.priority.$error }">
                                <option value="">Seleccione…</option>
                                <option value="critical">Critica</option>
                                <option value="normal">Normal</option>
                                <option value="low">Baja</option>
                            </select>

                            <template v-if="v$.form.priority.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.priority.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Ficha Técnica
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea v-model.trim="form.data_sheet" class="form-control resize-none" cols="30"
                                      rows="5"></textarea>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Funcionalidad
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea v-model.trim="form.functionality" class="form-control resize-none" cols="30"
                                      rows="5"></textarea>
                        </div>

                        <div class="mb-1 col-span-2">
                            <label class="flex flex-col sm:flex-row">
                                Archivos
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Max 4MB (JPG, PNG)
                                </span>
                            </label>

                            <input type="file"
                                   ref="asset_files"
                                   accept="image/*"
                                   class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                                   multiple/>

                            <div class="mt-10" v-if="editMode && form.files.length > 0">
                                <div class="overflow-x-auto">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">Nombre</th>
                                                <th class="whitespace-nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="file in form.files">
                                                <td>{{ $h.truncateStringReverse(file.path, 40) }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-primary mr-2" @click="downloadFile(file)" >
                                                        <font-awesome-icon icon="download"/>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" @click="deleteFile(file.id)">
                                                        <font-awesome-icon icon="trash-can"/>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mb-1 col-span-2">
                            <label class="flex flex-col sm:flex-row">
                                Comentarios
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea v-model.trim="form.comments" class="form-control resize-none" cols="30"
                                      rows="2"></textarea>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)" :disabled="form.processing"
                            type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click.prevent="update(form)" type="button"
                            class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>

            <jet-dialog-modal :show="modalFiles.open" max-width=xl>
                <template #title>
                    Archivos
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">Nombre</th>
                                    <th class="whitespace-nowrap"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="file in modalFiles.files">
                                    <td>{{ $h.truncateStringReverse(file.path, 40) }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary mr-2" @click="viewFile(file)" >
                                            <font-awesome-icon icon="eye"/>
                                        </button>
                                        <button class="btn btn-sm btn-primary mr-2" @click="downloadFile(file)" >
                                            <font-awesome-icon icon="download"/>
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="deleteFile(file.id, true)">
                                            <font-awesome-icon icon="trash-can"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>


            <jet-dialog-modal :show="resumeModal.open" max-width=xl>
                <template #title>
                    {{resumeModal.title}}
                </template>

                <template #content>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Modelo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.model"
                                   type="text"
                                   class="form-control"
                                   placeholder="Modelo"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Marca
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.brand"
                                   type="text"
                                   class="form-control"
                                   placeholder="Marca"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Potencia
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.power"
                                   type="text"
                                   class="form-control"
                                   placeholder="Potencia"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Amperaje
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.amperage"
                                   type="text"
                                   class="form-control"
                                   placeholder="Amperaje"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Voltage
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.voltage"
                                   type="text"
                                   class="form-control"
                                   placeholder="Voltage"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Frecuencia
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.frequency"
                                   type="text"
                                   class="form-control"
                                   placeholder="Frecuencia"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Watts
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.watts"
                                   type="text"
                                   class="form-control"
                                   placeholder="Watts"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                RPM
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.rpm"
                                   type="text"
                                   class="form-control"
                                   placeholder="RPM"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Frecuencia de mantenimiento (dias)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.maintenance_frequency"
                                   type="text"
                                   class="form-control"
                                   placeholder="Frecuencia de mantenimiento (dias)"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Dimensiones
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.dimension"
                                   type="text"
                                   class="form-control"
                                   placeholder="Dimensiones"/>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Descripcion de mantenimiento preventivo
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <textarea class="form-control"
                                      v-model.trim="resumeModal.form.preventive_maintenance_description" cols="30" rows="10">
                            </textarea>
                        </div>

                        <div class="mb-1">
                            <label class="flex flex-col sm:flex-row">
                                Precauciones
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>

                            <input v-model.trim="resumeModal.form.precautions"
                                   type="text"
                                   class="form-control"
                                   placeholder="Precauciones"/>
                        </div>
                    </div>

                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click.prevent="updateResume(resumeModal.form)" type="button"
                            class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>

        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head, Link} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import dayjs from "dayjs";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

import 'dayjs/locale/es'
dayjs.locale('es');

const CancelToken = axios.CancelToken;
let source;

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        assets: Array,
        work_centers: Array,
        asset_classifications: Array,
        users: Array
    },

    components: {
        FontAwesomeIcon,
        JetDialogModal,
        Head,
        Link
    },

    validations() {
        return {
            form: {
                code: {
                    required,
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('maintenance.assets.validate-code', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
                name: {
                    required,
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('maintenance.assets.validate-name', value), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
                classification_id: {
                    required
                },
                state: {
                    required
                },
                work_center_id: {
                    required
                },
                priority: {
                    required
                },
            },
        }
    },

    data() {
        return {
            table: {
                data: this.assets,
                columns: [
                    'code',
                    'name',
                    'classification',
                    'state',
                    'work_center',
                    'priority',
                    'last_revision',
                    'created_by',
                    'created_at',
                    'updated_at',
                    'files',
                    'actions'
                ],
                options: {
                    headings: {
                        code: 'CÓDIGO',
                        name: 'NOMBRE',
                        classification: 'CLASIFICACIÓN',
                        state: 'ESTADO',
                        work_center: 'CENTRO DE TRABAJO',
                        priority: 'PRIORIDAD',
                        last_revision: 'ULTIMA REVISION',
                        created_by: 'CREADO POR',
                        created_at: 'CREADO EN',
                        updated_at: 'ACTUALIZADO EN',
                        files: 'ARCHIVOS',
                        actions: '',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['id', 'name', 'classification', 'state', 'work_center', 'priority', 'last_revision', 'created_by', 'created_at', 'updated_at'],
                    templates: {
                        classification: function (h, row) {
                            return row.classification.name
                        },
                        state: function (h, row) {
                            switch (row.state) {
                                case 'good':
                                    return <span class="badge badge-success badge-rounded">Bueno</span>
                                case 'repair':
                                    return <span class="badge badge-warning badge-rounded">Reparación</span>
                                case 'discarded':
                                    return <span class="badge badge-danger badge-rounded">Descartado</span>
                            }
                        },
                        work_center: function (h, row) {
                            return row.work_center.name
                        },
                        priority: function (h, row) {
                            switch (row.priority) {
                                case 'low':
                                    return <span class="badge badge-success badge-rounded">Baja</span>

                                case 'normal':
                                    return <span class="badge badge-warning badge-rounded">Normal</span>

                                case 'critical':
                                    return <span class="badge badge-danger badge-rounded">Critica</span>
                            }
                        },
                        last_revision: function (h, row) {
                            return row.last_revision ? dayjs(new Date(row.last_revision)).format('DD-MM-YYYY') : 'N/A'
                        },
                        created_by: function (h, row) {
                            return row.created_by.name
                        },
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY')
                        },
                        updated_at: function (h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY')
                        },
                    }
                },
            },

            form: {
                code: '',
                name: '',
                classification_id: '',
                state: '',
                work_center_id: '',
                priority: '',
                comments: '',
                data_sheet: '',
                functionality: '',
                processing: false
            },
            isOpen: false,
            editMode: false,
            title: 'Activos',

            modalFiles: {
                open: false,
                files: []
            },

            resumeModal: {
                open: false,
                title: '',
                form: {
                    asset_id: null,
                    model: '',
                    brand: '',
                    power: '',
                    amperage: '',
                    voltage: '',
                    frequency: '',
                    watts: '',
                    rpm: '',
                    maintenance_frequency: '',
                    dimension: '',
                    preventive_maintenance_description: '',
                    precautions: ''
                }
            }
        }
    },

    methods: {
        openModal() {
            this.isOpen = true
        },

        closeModal() {
            this.form = {
                code: '',
                name: '',
                classification_id: '',
                state: '',
                work_center_id: '',
                priority: '',
                comments: '',
                data_sheet: '',
                functionality: '',
                processing: false
            }
            this.isOpen = false
            this.editMode = false
            this.v$.form.$reset()

            this.modalFiles = {
                open: false,
                files: []
            }

            this.resumeModal = {
                open: false,
                title: '',
                form: {
                    asset_id: null,
                    model: '',
                    brand: '',
                    power: '',
                    amperage: '',
                    voltage: '',
                    frequency: '',
                    watts: '',
                    rpm: '',
                    maintenance_frequency: '',
                    dimension: '',
                    preventive_maintenance_description: '',
                    precautions: ''
                }
            }
        },

        edit(row) {
            this.form = Object.assign({}, row);
            this.form.comments = row.comments === 'null' ? '' : row.comments
            this.form.functionality = row.functionality === 'null' ? '' : row.functionality
            this.editMode = true
            this.openModal()
        },

        delete(id) {
            this.$swal({
                title: '¿Eliminar activo?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, Eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('maintenance.assets.destroy', id)).then(resp => {
                        this.tableData = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Activo eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error eliminando el activo.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },

        save(form) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                this.form.processing = true;

                const formData = new FormData();
                Object.keys(form).forEach(key => formData.append(key, form[key]));

                let files = this.$refs.asset_files.files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                axios.post(route('maintenance.assets.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.table.data = resp.data
                    this.closeModal();
                    this.v$.form.$reset()
                    this.form.processing = false;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Activo creado con éxito",
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar'
                    });
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud. Mensaje de error: ' + err.data,
                        confirmButtonText: 'Aceptar',
                    });
                    this.form.processing = false;
                })
            }
        },

        update(form) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            const formData = new FormData();
            Object.keys(form).forEach(key => formData.append(key, form[key]));

            let files = this.$refs.asset_files.files;
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            axios.post(route('maintenance.assets.update', form.id), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(resp => {
                this.table.data = resp.data;
                this.closeModal()

                this.$swal({
                    title: '¡Éxito!',
                    text: "Activo actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                })
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error actualizando la clasificación.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(error);
            });
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

            axios.post(route('maintenance.assets.files.download'), {
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

        deleteFile(id, closeModal = false){
            this.$swal({
                title: '¿Eliminar Archivo?',
                text: "¡Esta acción no es reversible!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('maintenance.assets.files.delete'), {
                        id: id
                    }).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Archivo eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })

                        if (closeModal){
                            this.closeModal()
                        }else {
                            this.form = resp.data
                        }
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: 'Ups!',
                            text: 'Hubo un error eliminando el registro.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },

        viewFile(file){
            this.$swal({
                imageUrl: file.base64_file,
                imageAlt: 'image',
                confirmButtonText: 'Cerrar'
            })
        },

        showFiles(files){
            this.modalFiles = {
                open: true,
                files: files
            }
        },

        editResume(asset){
            this.resumeModal = {
                open: true,
                title: `${asset.code} – ${asset.name}`,
                form: {
                    asset_id: asset.id,
                    model: asset.resume?.model,
                    brand: asset.resume?.brand,
                    power: asset.resume?.power,
                    amperage: asset.resume?.amperage,
                    voltage: asset.resume?.voltage,
                    frequency: asset.resume?.frequency,
                    watts: asset.resume?.watts,
                    rpm: asset.resume?.rpm,
                    maintenance_frequency: asset.resume?.maintenance_frequency,
                    dimension: asset.resume?.dimension,
                    preventive_maintenance_description: asset.resume?.preventive_maintenance_description,
                    precautions: asset.resume?.precautions,
                }
            }
        },

        updateResume(form){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.post(route('maintenance.asset.resume-update'), form).then(resp => {
                this.$swal({
                    title: '¡Éxito!',
                    text: "Hoja de vida actualizada con éxito",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });

                this.table.data = resp.data
                this.closeModal()
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

}
</script>


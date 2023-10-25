<template>
    <div>
        <Head title="Características"/>

        <portal to="application-title">
            Características
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Característica
            </button>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:actions="{row}">
                    <div class="dropdown text-center">
                        <button class="dropdown-toggle btn btn-secondary" aria-expanded="false"
                                data-tw-toggle="dropdown">
                            <font-awesome-icon icon="bars"/>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-content">
                                <a href="javascript:void(0)"
                                   @click="edit(row)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                                    Editar
                                </a>
                                <a href="javascript:void(0)"
                                   @click="deleteRow(row.code)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'trash-can']" class="mr-1"/>
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=lg>
                <template #title>
                    {{ title }}
                </template>

                <template #content>

                    <div class="p-2">
                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Linea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.line.$model" type="text" class="form-select"
                                    :class="{ 'border-danger': v$.form.line.$error }"
                                    @change="getSublines" autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="line in lines" v-bind:key="line.code" :value="line.code">
                                    {{ line.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.line.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.line.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Sublinea
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <select v-model.trim="v$.form.subline.$model" type="text" class="form-select"
                                    :class="{ 'border-danger': v$.form.subline.$error }"
                                    @change="generateCode" autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="subline in sublines" v-bind:key="subline.code"
                                        :value="subline.code">{{ subline.name }}
                                </option>
                            </select>

                            <template v-if="v$.form.subline.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.subline.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
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
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.name.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Abreviatura
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Obligatorio
                                </span>
                            </label>

                            <input v-model.trim="v$.form.abbreviation.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.abbreviation.$error }"
                                   placeholder="Abreviatura"/>

                            <template v-if="v$.form.abbreviation.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.abbreviation.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Comentarios
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Opcional
                                </span>
                            </label>
                            <textarea class="form-control" name="comments" id="comments" cols="30" rows="5"
                                      v-model="form.comments"></textarea>
                        </div>

                        <div class="">
                            <label class="flex flex-col sm:flex-row">
                                Código
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Autogenerado
                                </span>
                            </label>
                            <input v-model.trim="v$.form.code.$model" type="text" class="form-control"
                                   :class="{ 'border-danger': v$.form.code.$error }" placeholder="Código"
                                   disabled/>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button v-show="!editMode" @click.prevent="save(form)"
                            :disabled="form.processing" type="submit" class="btn btn-primary">
                        Guardar
                    </button>

                    <button v-show="editMode" @click="update(form)" type="button"
                            :disabled="form.processing" class="btn btn-primary">
                        Actualizar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength, maxLength, helpers} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

const { withAsync } = helpers


export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Head
    },

    props: {
        data: Array,
        lines: Array,
    },

    validations() {
        return {
            form: {
                line: {required},
                subline: {required},
                code: {required},
                name: {
                    required,
                    minLength: minLength(2),
                    maxLength: maxLength(255),
                    isUnique: helpers.withMessage('Este nombre ya se encuentra registrado', withAsync(async (value) => {
                        if (this.editMode){
                            return true
                        }
                        if(value) {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            try {
                                return await axios.get(route('features.validate-name', [this.form.line, this.form.subline, value]), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                console.error(error)
                                return false
                            }
                        }
                        else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                abbreviation: {
                    minLength: minLength(2),
                    maxLength: maxLength(10),
                    isUnique: helpers.withMessage('Esta abreviatura ya se encuentra registrada', withAsync(async (value) => {
                        if (this.editMode){
                            return true
                        }
                        if(value) {
                            if (source) source.cancel();
                            source = CancelToken.source();
                            try {
                                return await axios.get(route('features.validate-abbreviation', [this.form.line, this.form.subline, value]), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                console.error(error)
                                return false
                            }
                        }
                        else {
                            return true
                        }
                    })),
                    $autoDirty: true
                },
            }
        }
    },

    data() {
        return {
            columns: [
                'line',
                'subline',
                'code',
                'name',
                'abbreviation',
                'comments',
                'created_by',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    line: 'LINEA',
                    subline: 'SUBLINEA',
                    code: 'CÓDIGO',
                    name: 'NOMBRE',
                    abbreviation: 'ABREVIATURA',
                    comments: 'COMENTARIOS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: '',
                },

                uniqueKey: "id",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['line', 'subline', 'code', 'name', 'abbreviation', 'created_by', 'created_at', 'updated_at'],
                dateColumns: ['created_at', 'updated_at'],
                filterAlgorithm: {
                    line(row, query) {
                        return (`${row.line.code} - ${row.line.name}`).toLowerCase().includes(query.toLowerCase())
                    },
                    subline(row, query) {
                        return (`${row.subline.code} - ${row.subline.name}`).toLowerCase().includes(query.toLowerCase())
                    },
                    created_by(row, query) {
                        return (row.created_by.name).toLowerCase().includes(query.toLowerCase())
                    }
                },
                templates: {
                    line(h, row) {
                        return `${row.line.code} - ${row.line.name}`

                    },
                    subline(h, row) {
                        return `${row.subline.code} - ${row.subline.name}`
                    },
                    code(h, row) {
                        return row.code.substring(4, 6)
                    },
                    created_by(h, row) {
                        return row.created_by.name
                    },
                    created_at(h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                    },
                    updated_at(h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY');
                    }
                },
                customSorting: {
                    line(ascending) {
                        return function (a, b) {
                            const lastA = (`${a.line.code} - ${a.line.name}`).toLowerCase();
                            const lastB = (`${b.line.code} - ${b.line.name}`).toLowerCase();

                            if (ascending)
                                return lastA >= lastB ? 1 : -1;

                            return lastA <= lastB ? 1 : -1;
                        }
                    },
                    subline(ascending) {
                        return function (a, b) {
                            const lastA = (`${a.subline.code} - ${a.subline.name}`).toLowerCase();
                            const lastB = (`${b.subline.code} - ${b.subline.name}`).toLowerCase();

                            if (ascending)
                                return lastA >= lastB ? 1 : -1;

                            return lastA <= lastB ? 1 : -1;
                        }
                    },

                    created_by(ascending) {
                        return function (a, b) {
                            const lastA = a.created_by.name.toLowerCase();
                            const lastB = b.created_by.name.toLowerCase();

                            if (ascending)
                                return lastA >= lastB ? 1 : -1;

                            return lastA <= lastB ? 1 : -1;
                        }
                    },

                }
            },
            editMode: false,
            isOpen: false,
            form: {
                line: null,
                subline: null,
                code: null,
                name: null,
                abbreviation: null,
                comments: null,
            },
            errors: {},
            tableData: this.data,
            title: 'Crear Característica',
            sublines: []
        }
    },

    methods: {
        openModal: function () {
            this.isOpen = true;
        },

        closeModal: function () {
            this.isOpen = false;
            this.reset();
            this.editMode = false;
            this.errors = {};
            this.v$.$reset()
        },

        reset: function () {
            this.form = {
                line: null,
                subline: null,
                code: null,
                name: null,
                abbreviation: null,
                comments: null,
            },
                this.title = 'Crear Característica'
        },

        save: function (data) {
            this.v$.form.$touch();
            if (!this.v$.form.$invalid) {
                axios.post(route('features.store'), data).then(res => {
                    this.closeModal();
                    this.tableData = res.data;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "¡Característica creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.errors = err.response.data.errors
                    this.$swal({
                        icon: 'error',
                        title: 'Ups.. ',
                        text: 'Hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        timer: 6000,
                    })
                });
            }
        },

        deleteRow: function (row) {
            this.$swal({
                title: '¿Eliminar registro?',
                text: "¡Esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('features.destroy', row)).then(resp => {
                        this.tableData = resp.data;
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Registro eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error eliminando el registro.',
                            confirmButtonText: 'Aceptar',
                        });
                        console.log(error);
                    });
                }
            })
        },

        edit: function (data) {
            this.form = Object.assign({}, data);
            this.editMode = true;
            this.title = 'Editar ' + data.name
            this.form.line = data.line.code;
            this.form.subline = data.subline.code;
            this.getSublines();
            this.openModal();
        },

        update: function (row) {
            axios.put(route('features.update', row.code), row).then(resp => {
                this.tableData = resp.data;
                this.reset();
                this.closeModal();
                this.errors = {};

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => this.errors = err.response.data.errors);
        },

        getSublines: function (event) {
            axios.get(route('features.get-sublines'), {
                params: {
                    line_code: this.form.line
                }
            }).then(resp => {
                this.sublines = resp.data
            }).catch(error => {
                console.log(error.data);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        generateCode: function () {
            axios.get(route('features.get-latest-code'), {
                params: {
                    line: this.form.line,
                    subline: this.form.subline
                }
            }).then(resp => {
                this.form.code = this.$h.alphaNumericIncrementWithCode(resp.data, this.form.subline, 2, 4, 6)
            }).catch(error => {
                console.log(error);
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error recuperando la información.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },
    }
}
</script>

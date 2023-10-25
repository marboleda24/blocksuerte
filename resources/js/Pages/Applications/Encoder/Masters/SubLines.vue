<template>
    <div>
        <Head title="Sublineas"/>

        <portal to="application-title">
            Sublineas
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Sublinea
            </button>
        </portal>

        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:features="{row}">
                    <div class="inline-flex">
                        <span v-for="features in row.measurement_characteristic" v-bind:key="features.id"
                              class="badge badge-success">
                            {{ features.name }}
                        </span>
                    </div>
                </template>

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
                                    @change="GenerateCode" autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="line in lines" v-bind:key="line.code" :value="line.code">
                                    {{line.name }}
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
                                Caracteristica(s)
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                            Obligatorio
                                        </span>
                            </label>

                            <TomSelect v-model="form.features"
                                       class="w-full"
                                       :class="{ 'border-danger': v$.form.features.$error }"
                                       multiple>
                                <option v-for="feature in features" :value="feature.code">{{ feature.name }}</option>
                            </TomSelect>

                            <template v-if="v$.form.features.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.features.$errors" :key="index">
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
                                   :class="{ 'border-danger': v$.form.code.$error }" placeholder="Código" disabled/>
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
import {Head} from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'
import {required, minLength, maxLength} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const CancelToken = axios.CancelToken;
let source;

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head,
        JetDialogModal
    },

    props: {
        data: Array,
        features: Array,
        lines: Array
    },

    validations() {
        return {
            form: {
                line: {required},
                name: {
                    required,
                    minLength: minLength(2),
                    maxLength: maxLength(255),
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('sublines.validate-name', [this.form.line, value]), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            console.log(error);
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
                abbreviation: {
                    maxLength: maxLength(10),
                    async isUnique(value) {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value === '') return true
                        try {
                            const resp = await axios.get(route('sublines.validate-abbreviation', [this.form.line, value]), {cancelToken: source.token});
                            return Boolean(resp.data);
                        } catch (error) {
                            if (axios.isCancel(error)) {
                                console.log('Operation canceled by the user.');
                                return true;
                            }
                        }
                    }
                },
                features: {
                    required,
                    minLength: minLength(1)
                },
                code: {required}
            }
        }
    },

    data() {
        return {
            columns: [
                'line',
                'code',
                'name',
                'abbreviation',
                'comments',
                'features',
                'created_by',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    line: 'LINEA',
                    code: 'CÓDIGO',
                    name: 'NOMBRE',
                    abbreviation: 'ABREVIATURA',
                    comments: 'COMENTARIOS',
                    features: 'CARACTERÍSTICAS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: '',
                },

                uniqueKey: "code",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['code', 'name', 'abbreviation', 'created_by', 'created_at', 'updated_at'],
                templates: {
                    line(h, row) {
                        return `${row.line.code} - ${row.line.name}`
                    },
                    code(h, row) {
                        return row.code.substring(2, 4)
                    },
                    created_by(h, row) {
                        return row.created_by.name
                    },
                    created_at(h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MMMM-YYYY');
                    },
                    updated_at(h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MMMM-YYYY');
                    }
                }
            },
            editMode: false,
            isOpen: false,
            form: {
                line: '',
                code: '',
                name: '',
                comments: null,
                features: [],
                abbreviation: '',
            },
            errors: {},
            tableData: this.data,
            title: 'Crear Sublinea'
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
                line: '',
                code: '',
                name: '',
                comments: '',
                features: [],
                abbreviation: '',
            }
            this.title = 'Crear sublinea'
        },

        save: function (data) {
            this.v$.$touch();
            if (!this.v$.$invalid) {
                axios.post(route('sublines.store'), data).then(res => {
                    this.closeModal();
                    this.tableData = res.data;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Sublinea creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => {
                    this.errors = err.response.data.errors
                    this.$swal({

                        icon: 'error',
                        title: 'Ups..',
                        text: 'hubo un error procesando la solicitud',
                        timerProgressBar: true,
                        showConfirmButton: false,
                        timer: 6000,
                    });
                })
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
                    axios.delete(route('sublines.destroy', row)).then(resp => {
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
            this.form = {
                line: data.line.code,
                code: data.code,
                name: data.name,
                comments: data.comments,
                features: data.measurement_characteristic.map(function (x) {
                    return x.code
                }),
                abbreviation: data.abbreviation,
            }
            this.editMode = true;
            this.title = 'Editar ' + data.name
            this.openModal();
        },

        update: function (row) {
            axios.put(route('sublines.update', row.code), row).then(resp => {
                this.tableData = resp.data;
                this.closeModal();

                this.$swal({
                    title: '¡Éxito!',
                    text: "Registro actualizado con éxito!",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                })
            }).catch(err => {
                console.log(err);
            })
        },

        GenerateCode: function () {
            axios.get(route('sublines.get-latest-code'), {
                params: {
                    line: this.form.line,
                }
            }).then(resp => {
                this.form.code = this.$h.alphaNumericIncrementWithCode(resp.data, this.form.line, 2, 2, 4)
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

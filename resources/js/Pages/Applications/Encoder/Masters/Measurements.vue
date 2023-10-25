<template>
    <div>
        <Head title="Medidas"/>

        <portal to="application-title">
            Medidas
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="openModal()">
                <font-awesome-icon icon="plus" class="mr-2"/>
                Agregar Medida
            </button>
        </portal>
        <div>
            <v-client-table :data="tableData" :columns="columns" :options="options" ref="table1"
                            class="overflow-y-auto">
                <template v-slot:denomination="{row}">
                    {{ $h.denominationCreator(row.detail) }}
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
                                   @click="deleteRow(row.id)"
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

                            <select v-model.trim="v$.form.line.$model" class="form-select"
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

                            <select v-model.trim="v$.form.subline.$model" class="form-select"
                                    :class="{ 'border-danger': v$.form.subline.$error }"
                                    @change="get_props"
                                    autofocus>
                                <option value="" selected disabled>Seleccione...</option>
                                <option v-for="subline in sublines" v-bind:key="subline.code" :value="subline.code">
                                    {{ subline.name }}
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

                        <template v-if="form.subline_props">
                            <div class="mb-4" v-for="(prop, key) in form.subline_props">
                                <label class="flex flex-col sm:flex-row">
                                    {{ prop.name }}
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Opcional
                                    </span>
                                </label>

                                <div class="grid grid-cols-2 gap-6 ">
                                    <input class="form-control"
                                           :class="{ 'border-danger': v$.form.subline_props.$each.$response.$data[key]?.value.$error }"
                                           type="text"
                                           v-model="prop.value">

                                    <select v-model="prop.unit" class="form-select">
                                        <option v-for="unit in units" :value="unit.code">{{ unit.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>

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

                        <div class="mb-4">
                            <label class="flex flex-col sm:flex-row">
                                Denominacion
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                    Autogenerado
                                </span>
                            </label>
                            <input v-model.trim="this.form.denomination" type="text" class="form-control"
                                   placeholder="Denominacion" readonly/>

                            <template v-if="v$.form.denomination.$error">
                                <ul class="mt-1">
                                    <li class="text-danger"
                                        v-for="(error, index) of v$.form.denomination.$errors" :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
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
import {helpers, minValue, required} from '@/utils/i18n-validators'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

const {withAsync} = helpers
const CancelToken = axios.CancelToken;
let source;

const validNumber = helpers.regex(/(\d+(?:\.\d+)?)(\/\d+(?:\.\d+)?)*$/)

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
        units: Array
    },

    validations() {
        return {
            form: {
                line: {required},
                subline: {required},
                denomination: {
                    required,
                    isUnique: helpers.withMessage('Esta medida ya se encuentra registrada', withAsync(async (value) => {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value) {
                            try {
                                return await axios.post(route('measurements.validate-denomination'), {
                                    line: this.form.line,
                                    subline: this.form.subline,
                                    denomination: value
                                },{
                                    cancelToken: source.token,
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                console.log(error)
                                return false
                            }
                        } else {
                            return false
                        }
                    })),
                    $autoDirty: true
                },
                subline_props: {
                    $each: helpers.forEach({
                        value: {
                            validNumber: helpers.withMessage('Solo se permiten numeros o fracciones', validNumber),
                        }
                    })
                }
            }
        }
    },

    data() {
        return {
            columns: [
                'id',
                'line',
                'subline',
                'measurement',
                'comments',
                'created_by',
                'created_at',
                'updated_at',
                'actions'
            ],
            options: {
                headings: {
                    id: '#',
                    line: 'LINEA',
                    subline: 'SUBLINEA',
                    measurement: 'DENOMINACION',
                    comments: 'COMENTARIOS',
                    created_by: 'CREADO POR',
                    created_at: 'CREADO',
                    updated_at: 'ACTUALIZADO',
                    actions: '',
                },
                uniqueKey: "code",
                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['id', 'line', 'subline', 'name', 'created_by', 'created_at', 'updated_at'],
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
                    created_by(h, row) {
                        return row.created_by.name
                    },
                    created_at(h, row) {
                        return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                    },
                    updated_at(h, row) {
                        return dayjs(new Date(row.updated_at)).format('DD-MM-YYYY')
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
                comments: null,
                subline_props: [],
                denomination: ''
            },
            errors: {},
            tableData: this.data,
            title: 'Crear medida',
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
        },

        reset: function () {
            this.form = {
                line: null,
                subline: null,
                comments: null,
                subline_props: [],
            }
            this.title = 'Crear medida'
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
                    axios.delete(route('measurements.destroy', row)).then(resp => {
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

        save(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({
                    title: '¡Ups!',
                    text: "Verifica que toda la información sea correcta",
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                })
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('measurements.store'), data).then(res => {
                    this.reset();
                    this.closeModal();
                    this.editMode = false;
                    this.errors = {};
                    this.tableData = res.data;

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Medida creada con éxito!",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                }).catch(err => this.errors = err.response.data.errors)
            }

        },

        edit: function (data) {
            this.editMode = true;
            this.title = 'Editar ' + data.denomination;
            this.form = {
                id: data.id,
                line: data.line_code,
                subline: data.subline_code,
                comments: data.comments,
                subline_props: data.detail.map(function (x) {
                    return {
                        id: x.id,
                        code: x.characteristic.code,
                        name: x.characteristic.name,
                        unit: x.unit.code,
                        value: x.value
                    }
                }),
            }
            this.getSublines()
            this.openModal();
        },

        update(row) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.put(route('measurements.update', row.id), row).then(resp => {
                this.tableData = resp.data;
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

        getSublines: function () {
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

        get_props() {
            let subline = this.form.subline
            const values = this.sublines.filter(function (obj) {
                return obj.code === subline
            }).map(function (obj) {
                return obj.measurement_characteristic
            })[0]
            const array = [];
            for (let i = 0; i < values.length; i++) {
                array.push({
                    code: values[i].code,
                    name: values[i].name,
                    value: 0,
                    unit: 'mm'
                })
            }
            this.form.subline_props = array
        }
    },

    computed: {
        denomination() {
            let regex = new RegExp(/(\d+(?:\.\d+)?)(\/\d+(?:\.\d+)?)*$/gm)

            if (this.form.subline_props.length > 0) {
                const group = this.form.subline_props.reduce(function (rv, x) {
                    (rv[x['unit']] = rv[x['unit']] || []).push(x);
                    return rv;
                }, {})

                const measurement = [];

                for (const groupKey in group) {
                    let regx = group[groupKey].map(function (x) {
                        if (x.value && x.value > 0 || regex.test(x.value)) {
                            return `${x.code}:${x.value}`
                        }
                    }, null);

                    regx = regx.filter(x => x !== undefined);

                    if (regx.length > 0) {
                        measurement.push(`${regx.length > 1 ? regx.join(' ') : regx.join('')}${groupKey}`)
                    }
                }
                return measurement.length > 1 ? measurement.join(' ') : measurement.join('');
            }

        }
    },

    watch: {
        denomination() {
            this.form.denomination = this.denomination
        }
    }
}
</script>

<template>
    <div>
        <Head title="Asignación de Cupos"/>

        <portal to="application-title">
            Asignación de Cupos
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
                                   @click="view(row.id)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['far', 'eye']" class="mr-1"/>
                                    Ver
                                </a>
                                <a href="javascript:void(0)"
                                   @click="assing_credit_limit(row.id, row.document)"
                                   class="dropdown-item">
                                    <font-awesome-icon :icon="['fas', 'wallet']" class="mr-1"/>
                                    Asignar Cupo
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title>
                    Verificar información
                </template>

                <template #content v-if="infoModal">

                    <div class="grid grid-cols-4 gap-4 text-base">
                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                TIPO DOCUMENTO
                            </span>
                            <span v-if="infoModal.document_type === 'C'">CEDULA CIUDADANIA</span>
                            <span v-else-if="infoModal.document_type === 'E'">CEDULA EXTRANJERIA</span>
                            <span v-else-if="infoModal.document_type === 'N'">NIT</span>
                            <span v-else>TARJETA DE IDENTIDAD</span>
                        </div>

                        <div class="border rounded-lg uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                DOCUMENTO
                            </span>
                            {{ infoModal.document }}
                        </div>

                        <div class="border rounded-lg uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                DV
                            </span>
                            {{ calcule_dv(infoModal.document) }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                TIPO CLIENTE
                            </span>
                            <span v-if="infoModal.customer_type === 'PN'">PERSONA NATURAL</span>
                            <span v-else>PERSONA JURIDICA</span>
                        </div>

                        <template v-if="infoModal.business_name">
                            <div class="border rounded-lg col-span-2 uppercase">
                                <span
                                    class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                    RAZON SOCIAL
                                </span>
                                {{ infoModal.business_name }}
                            </div>
                        </template>

                        <template v-else>
                            <div class="border rounded-lg col-span-2 uppercase">
                                <span
                                    class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                    PRIMER NOMBRE
                                </span>
                                {{ infoModal.first_name }}
                            </div>

                            <template v-if="infoModal.second_name">
                                <div class="border rounded-lg col-span-2 uppercase">
                                    <span
                                        class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                        SEGUNDO NOMBRE
                                    </span>
                                    {{ infoModal.second_name }}
                                </div>
                            </template>

                            <div class="border rounded-lg col-span-2 uppercase">
                                <span
                                    class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                    PRIMER APELLIDO
                                </span>
                                {{ infoModal.surname }}
                            </div>

                            <template v-if="infoModal.second_surname">
                                <div class="border rounded-lg col-span-2 uppercase">
                                    <span
                                        class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                        SEGUNDO APELLIDO
                                    </span>
                                    {{ infoModal.second_surname }}
                                </div>
                            </template>
                        </template>

                        <div class="border rounded-lg col-span-2 uppercase">
                           <span
                               class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                RAZON COMERCIAL
                            </span>
                            {{ infoModal.business_reason }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                ACTIVIDAD ECONOMICA
                            </span>
                            {{ infoModal.main_activity }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                PAIS
                            </span>
                            {{ infoModal.country_name.descripcion }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                DEPARTAMENTO
                            </span>
                            {{ infoModal.province_name.descripcion }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                CIUDAD
                            </span>
                            {{ infoModal.city_name.descripcion }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                DIRECCIÓN
                            </span>
                            {{ infoModal.address }}
                        </div>

                        <template v-if="infoModal.phone">
                            <div class="border rounded-lg col-span-2 uppercase">
                                <span
                                    class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                    TELÉFONO
                                </span>
                                {{ infoModal.phone }}
                            </div>
                        </template>

                        <template v-if="infoModal.cellphone">
                            <div class="border rounded-lg col-span-2 uppercase">
                                <span
                                    class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                    CELULAR
                                </span>
                                {{ infoModal.cellphone }}
                            </div>
                        </template>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                ¿GRAN CONTRIBUYENTE?
                            </span>
                            <span v-if="infoModal.great_contributor === '1'">SI</span>
                            <span v-else>NO</span>
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                ¿RESPONSABLE IVA?
                            </span>
                            <span v-if="infoModal.responsable_iva === '1'">SI</span>
                            <span v-else>NO</span>
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                CREADO POR
                            </span>
                            {{ infoModal.createdby.name }}
                        </div>

                        <div class="border rounded-lg col-span-2 uppercase">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                VENDEDOR ASIGNADO
                            </span>
                            {{ infoModal.seller.name }}
                        </div>

                        <div class="border rounded-lg col-span-2">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                EMAIL
                            </span>
                            {{ infoModal.email.toLowerCase() }}
                        </div>

                        <div class="border rounded-lg col-span-2">
                            <span
                                class="font-semibold inline-block py-1 px-2 rounded-l-lg text-gray-600 bg-gray-200 last:mr-0 mr-1">
                                EMAIL FE
                            </span>
                            {{ infoModal.email_fe.toLowerCase() }}
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>


            <jet-dialog-modal :show="isOpenAssignment" max-width=md>
                <template #title>
                    Asignacion de plazo, descuento y cupo de credito
                </template>

                <template #content>

                    <div class="m-2">
                        <label class="flex flex-col sm:flex-row">
                            Plazo de pago
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                        </label>

                        <select v-model.trim="v$.form.term.$model" class="form-select"
                                :class="{ 'border-danger': v$.form.term.$error }" placeholder="Plazo de pago">
                            <option value="" selected="selected" disabled>Seleccione...</option>
                            <option v-for="term in terms" v-bind:key="term.CODE_36" :value="term.CODE_36">
                                {{ term.DESC_36 }}
                            </option>
                        </select>

                        <template v-if="v$.form.term.$error">
                            <div v-if="!v$.form.term.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>
                        </template>
                    </div>

                    <div class="m-2">
                        <label class="flex flex-col sm:flex-row">
                            Porcentaje de descuento
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                        </label>

                        <input v-model.trim="v$.form.discount.$model" type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.discount.$error }"
                               placeholder="Porcentaje descuento"/>


                        <template v-if="v$.form.discount.$error">
                            <div v-if="!v$.form.discount.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>

                            <div v-if="!v$.form.discount.minValue" class="text-theme-6 mt-2">
                                el valor mínimo para este campo es
                                {{ v$.form.discount.$params.minValue.min }}.
                            </div>

                            <div v-if="!v$.form.discount.maxValue" class="text-theme-6 mt-2">
                                el valor máximo para este campo es
                                {{ v$.form.discount.$params.maxValue.max }}.
                            </div>
                        </template>
                    </div>


                    <div class="m-2">
                        <label class="flex flex-col sm:flex-row">
                            Cupo de credito
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                        Obligatorio
                                    </span>
                        </label>

                        <input v-model.trim="v$.form.credit_limit.$model" type="number" class="form-control"
                               :class="{ 'border-danger': v$.form.credit_limit.$error }"
                               placeholder="Cupo de credito"/>


                        <template v-if="v$.form.credit_limit.$error">
                            <div v-if="!v$.form.credit_limit.required" class="text-theme-6 mt-2">
                                Campo obligatorio
                            </div>

                            <div v-if="!v$.form.credit_limit.minValue" class="text-theme-6 mt-2">
                                el valor mínimo para este campo es
                                {{ v$.form.credit_limit.$params.minValue.min }}.
                            </div>

                            <div v-if="!v$.form.credit_limit.maxValue" class="text-theme-6 mt-2">
                                el valor máximo para este campo es
                                {{ v$.form.credit_limit.$params.maxValue.max }}.
                            </div>
                        </template>
                    </div>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="save(form)" :disabled="form.processing" type="button"
                            class="btn btn-primary">
                        Guardar
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
import {required, maxValue, minValue, numeric} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        customers: Array,
        terms: Array
    },

    components: {
        JetDialogModal,
        Head
    },

    validations() {
        return {
            form: {
                term: {
                    required
                },
                discount: {
                    required,
                    numeric,
                    minValue: minValue(0),
                    maxValue: maxValue(20)
                },
                credit_limit: {
                    required,
                    numeric,
                    minValue: minValue(0),
                    maxValue: maxValue(100000000)
                }
            }
        }

    },

    data() {
        return {
            columns: [
                'document',
                'name',
                'seller',
                'actions'
            ],
            options: {
                headings: {
                    document: 'DOCUMENTO',
                    name: 'RAZON SOCIAL / NOMBRES',
                    seller: 'VENDEDOR',
                    actions: '',
                },

                clientSorting: false,
                sortable: ['document', 'name', 'seller'],
            },
            infoModal: null,
            isOpen: false,
            isOpenAssignment: false,
            tableData: this.customers,

            form: {
                id: '',
                document: '',
                term: '',
                discount: 0,
                credit_limit: 0,
                processing: false,
            }
        }
    },

    methods: {
        view(id) {
            this.loading(true);
            axios.get(route('customers.credit-limit-gestion.view'), {
                params: {
                    id: id,
                }
            }).then(resp => {
                this.infoModal = resp.data;
                this.loading(false);
                this.openModal();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(error);
            });
        },

        loading(bool) {
            if (bool) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información...',
                    text: 'Este proceso puede tardar unos segundos.',
                });
            } else {
                this.$swal.close()
            }
        },

        openModal: function () {
            this.isOpen = true;
        },


        closeModal: function () {
            this.isOpen = false;
            this.isOpenAssignment = false;
            this.infoModal = null;
            this.form = {
                id: '',
                document: '',
                term: '',
                discount: 0,
                credit_limit: 0,
                processing: false,

            }
        },

        assing_credit_limit(id, document) {
            this.form.id = id;
            this.form.document = document;
            this.isOpenAssignment = true;
        },

        save(data) {
            this.v$.$touch();
            if (this.v$.$invalid) {
                this.$swal({

                    icon: 'error',
                    title: 'Verifica que toda la información sea correcta',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
            } else {
                this.form.processing = true;
                axios.post(route('customers.credit-limit-gestion.store-new-customer'),
                    data
                ).then(resp => {
                    this.tableData = resp.data;
                    this.$swal({
                        icon: 'success',
                        title: '¡Cliente creado con éxito!',
                        text: 'El cliente fue creado correctamente en MAX y DMS',
                        timer: 10000,
                        timerProgressBar: true,
                        confirmButtonText: 'Aceptar',
                    });
                    this.closeModal();
                }).catch(error => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.log(error);
                    this.form.processing = false;
                });
            }
        },

        calcule_dv(document) {
            var vpri, x, y, z;
            var myNit = document.toString();

            // clear documento
            myNit = myNit.replace(/\s/g, ""); // spaces
            myNit = myNit.replace(/,/g, ""); // comma
            myNit = myNit.replace(/\./g, ""); // points
            myNit = myNit.replace(/-/g, ""); // dashs

            // verify  valid document
            if (isNaN(myNit)) {
                console.log("El nit/cédula '" + myNit + "' no es válido(a).");
                return "";
            }
            ;

            // proccess
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
            for (let i = 0; i < z; i++) {
                y = (myNit.substr(i, 1));
                x += (y * vpri [z - i]);
            }
            y = x % 11;
            return (y > 1) ? 11 - y : y;
        }
    }
}
</script>

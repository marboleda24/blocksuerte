<template>
    <Head :title="customer_data.RAZON_SOCIAL"/>

    <portal to="application-title">
        {{ customer_data.RAZON_SOCIAL }}
    </portal>

    <div>
        <div class="px-5 pt-5 intro-y box">
            <div class="flex flex-col pb-5 -mx-5 lg:flex-row">
                <div class="flex flex-1 justify-center items-center px-5 lg:justify-start">
                    <div class="relative flex-none w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 image-fit">
                        <img
                            alt="photo" class="rounded-full"
                            :src="'https://ui-avatars.com/api/?name='+encodeURI(customer_data.RAZON_SOCIAL)+'&color=7F9CF5&background=EBF4FF'">
                    </div>
                    <div class="ml-5">
                        <div class="text-lg font-medium truncate sm:whitespace-normal">
                            {{ customer_data.RAZON_SOCIAL }}
                        </div>
                        <div class="text-gray-600">
                            {{ `${customer_data.CODIGO_CLIENTE} - ${customer_data.NIT}` }}
                        </div>
                    </div>
                </div>
                <div
                    class="flex-1 px-5 pt-5 mt-6 border-t border-r border-l border-gray-200 lg:mt-0 dark:text-gray-300 dark:border-dark-5 lg:border-t-0 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">
                        Detalles de contacto
                    </div>
                    <div class="flex flex-col justify-center items-center mt-4 lg:items-start">
                        <div class="flex items-center truncate sm:whitespace-normal">
                            <font-awesome-icon :icon="['far', 'envelope']" class="mr-2"/>
                            {{ customer_data.CORREO_CONTACTO }}
                        </div>
                        <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                            <font-awesome-icon icon="map-marker-alt" class="mr-2"/>
                            {{ customer_data.DIRECCION }}
                        </div>
                        <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                            <font-awesome-icon icon="phone" class="mr-2"/>
                            {{ customer_data.TEL1 }}
                        </div>
                        <div class="flex items-center mt-3 truncate sm:whitespace-normal">
                            <font-awesome-icon :icon="['far', 'question-circle']" class="mr-2"/>

                            <div v-if="$gates.hasPermission('customer.change_state_customer')">
                                <div class="cursor-pointer badge badge-success badge-rounded"
                                     v-if="customer_data.ACTIVO === 'R'">
                                    ACTIVO
                                </div>

                                <div class="cursor-pointer badge badge-danger badge-rounded"
                                       v-else>
                                    INACTIVO
                                </div>
                            </div>
                            <div v-else>
                                <span class="badge badge-success badge-rounded" v-if="customer_data.ACTIVO === 'R'">ACTIVO</span>
                                <span class="badge badge-danger badge-rounded" v-else>ACTIVO</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="flex-1 px-5 pt-5 mt-6 border-t border-gray-200 lg:mt-0 lg:border-0 dark:border-dark-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-5"> Estadísticas</div>
                    <div class="flex justify-center items-center mt-2 lg:justify-start">
                        <div class="flex mr-2"> Pedidos EVPIU: {{ orders_count }}
                            <span class="ml-3 font-medium text-gray-600"></span>
                        </div>
                    </div>

                    <button class="btn btn-sm w-full btn-secondary mt-2" @click="isOpen = true">Ver logs</button>
                </div>
            </div>
        </div>

        <div class="box mt-5">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Archivos</h2>
                <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                    <button class="btn btn-sm btn-primary" @click="uploadFiles">
                        Subir archivos
                    </button>
                </div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto" v-if="customer_data.files.length > 0">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">Nombre</th>
                            <th class="whitespace-nowrap"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="file in customer_data.files">
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

                <div class="alert alert-warning show flex items-center" role="alert" v-else>
                    <AlertTriangleIcon class="w-6 h-6 mr-2"/>
                    Aun no se agrega ningún archivo
                </div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 my-4">
            <business-name
                :business_name="customer_data.RAZON_SOCIAL"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <comercial-name
                :comercial_name="customer_data.RAZON_COMERCIAL"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <contact
                :contact="customer_data.CONTACTO"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <location
                :country="customer_data.PAIS"
                :department="customer_data.ESTADO"
                :city="customer_data.CIUDAD"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdateLocation"
            />

            <phone1
                :phone1="customer_data.TEL1"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <phone2
                :phone2="customer_data.TEL2"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <cellphone
                :cellphone="customer_data.CELULAR"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <contact-mail
                :contact_email="customer_data.CORREO_CONTACTO"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <billing-mail
                :billing_email="customer_data.CORREO_FE"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <copy-mails
                :copy_emails="customer_data.CORREOS_COPIA"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <address1
                :address1="customer_data.DIRECCION"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <address2
                :address2="customer_data.DIRECCION2"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <currency
                :currency="customer_data.MONEDA"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <customer-type
                :customer_type="customer_data.TIPO_CLIENTE"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <gravado
                :gravado="customer_data.GRABADO"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <payment-term
                :paid_term="customer_data.CODIGO_PLAZO"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <discount-rate
                :discount="customer_data.DESCUENTO"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <seller
                :seller="customer_data.VENDEDOR"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <electronic-invoicing-manager
                :electronic_invoicing_manager="customer_data.RESPONSABLE_FE"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <electronic-invoicing-phone
                :electronic_invoicing_phone="customer_data.TEL_FE"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <foreign-city-code
                :foreign_city_code="customer_data.CIUDAD_EXT"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <rut-delivered
                :rut="customer_data.RUT"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <great-contributor
                :great_contributor="customer_data.GRAN_CONTRIBUYENTE"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <responsible-taxes
                :responsible_taxes="customer_data.RESPONSABLE_IVA"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />

            <economic-activity
                :economic_activity="customer_data.ACTIVIDAD_PPAL"
                :customer_code="customer_data.CODIGO_CLIENTE"
                @success="afterUpdate"
            />
        </div>

        <jet-dialog-modal :show="isOpen" @close="isOpen = false" max-width=lg>
            <template #title>
                Log de cambios
            </template>

            <template #content>
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">Campo</th>
                            <th class="whitespace-nowrap">Descripción</th>
                            <th class="whitespace-nowrap">Usuario</th>
                            <th class="whitespace-nowrap">Fecha</th>
                        </tr>
                        </thead>
                        <tbody v-if="logs.length > 0">
                        <tr v-for="log in logs">
                            <td>{{ log.field }}</td>
                            <td>{{ log.justify }}</td>
                            <td>{{ log.user.name }}</td>
                            <td>{{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}</td>
                        </tr>
                        </tbody>

                        <tbody v-else>
                            <tr class="text-center">
                                <td colspan="4">No se encontraron registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <template #footer>
                <button @click="isOpen = false" type="button" class="btn btn-secondary mr-2">
                    Cerrar
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import Contact from "./Components/Contact.vue";
import Cellphone from "./Components/Cellphone.vue";
import Address1 from "./Components/Address1.vue";
import Address2 from "./Components/Address2.vue";
import Phone1 from "./Components/Phone1.vue";
import Phone2 from "./Components/Phone2.vue";
import ContactMail from "./Components/ContactMail.vue";
import BillingMail from "./Components/BillingMail.vue";
import CopyMails from "./Components/CopyMails.vue";
import Currency from "./Components/Currency.vue";
import CustomerType from "./Components/CustomerType.vue";
import PaymentTerm from "./Components/PaymentTerm.vue";
import DiscountRate from "./Components/DiscountRate.vue";
import Seller from "./Components/Seller.vue";
import EconomicActivity from "./Components/EconomicActivity.vue";
import GreatContributor from "./Components/GreatContributor.vue";
import ResponsibleTaxes from "./Components/ResponsibleTaxes.vue";
import ForeignCityCode from "./Components/ForeignCityCode.vue";
import RutDelivered from "./Components/RutDelivered.vue";
import ElectronicInvoicingManager from "./Components/ElectronicInvoicingManager.vue";
import ElectronicInvoicingPhone from "./Components/ElectronicInvoicingPhone.vue";
import Gravado from "./Components/Gravado.vue";
import Location from "./Components/Location.vue"
import BusinessName from "./Components/BusinessName.vue";
import ComercialName from "./Components/ComercialName.vue";
import JetDialogModal from '@/Jetstream/DialogModal.vue';

export default {
    props: {
        customer: Object,
        orders_count: Number,
        logs: Array
    },

    data() {
        return {
            customer_data: this.customer,
            isOpen: false
        }
    },

    components: {
        Head,
        Link,
        Contact,
        Cellphone,
        Address1,
        Address2,
        Phone1,
        Phone2,
        ContactMail,
        BillingMail,
        CopyMails,
        Currency,
        CustomerType,
        PaymentTerm,
        DiscountRate,
        Seller,
        EconomicActivity,
        GreatContributor,
        ResponsibleTaxes,
        ForeignCityCode,
        RutDelivered,
        ElectronicInvoicingManager,
        ElectronicInvoicingPhone,
        Gravado,
        Location,
        BusinessName,
        ComercialName,
        JetDialogModal
    },

    methods: {
        afterUpdate(data) {
            this.customer_data[data.property] = data.value
        },

        afterUpdateLocation(data){
            this.customer_data.PAIS = data.country
            this.customer_data.ESTADO = data.department
            this.customer_data.CIUDAD = data.city
        },

        async uploadFiles() {
            this.$swal({
                icon: 'info',
                title: 'Cargar archivos',
                text: 'Por favor, seleccione un archivo',
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
                    ref: 'customer_files',
                    accept: 'image/*,.pdf,.csv,.xlsx,.xls,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword',
                    multiple: true
                },
                inputValidator: (inputValue) => {
                    return !inputValue && 'Debes seleccionar un archivo'
                }
            }).then((inputValue) => {
                if (inputValue.value) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Subiendo archivos…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    let formData = new FormData();
                    formData.append('customer_code', this.customer_data.CODIGO_CLIENTE);

                    let files = inputValue.value;
                    for (let i = 0; i < files.length; i++) {
                        formData.append('files[]', files[i]);
                    }

                    axios.post(route('customer.files.upload'), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(resp => {
                        this.customer_data.files = resp.data
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Archivo(s) subido con éxito",
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            confirmButtonText: 'Aceptar'
                        });
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

            axios.post(route('customer.files.download'), {
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

        deleteFile(id){
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

                    axios.post(route('customer.files.delete'), {
                        customer_code: this.customer_data.CODIGO_CLIENTE,
                        id: id
                    }).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Archivo eliminado con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        })

                        this.customer_data.files = resp.data
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

    }
}
</script>


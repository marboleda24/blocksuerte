<template>
    <div>
        <Head title="Propuestas pendientes 3D"/>

        <portal to="application-title">
            Propuestas pendientes 3D
        </portal>

        <div>
            <div class="post intro-y overflow-hidden box">
                <ul class="post__tabs nav nav-tabs flex-col sm:flex-row bg-slate-200 dark:bg-darkmode-800"
                    role="tablist">
                    <li class="nav-item w-full">
                        <button
                            id="proposal-tab"
                            data-tw-toggle="tab"
                            data-tw-target="#proposal"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4 active"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Pendientes 3D
                        </button>
                    </li>

                    <li class="nav-item w-full">
                        <button
                            id="pending-tab"
                            data-tw-toggle="tab"
                            data-tw-target="#pending"
                            href="javascript:;"
                            class="nav-link tooltip w-full py-4"
                            role="tab"
                            aria-controls="content"
                            aria-selected="false"
                        >
                            Pendientes Peso/Area
                        </button>
                    </li>
                </ul>

                <div class="post__content tab-content p-2">
                    <div id="proposal" class="tab-pane p-2 active" role="tabpanel" aria-labelledby="proposal-tab">
                        <v-client-table :data="table.data" :columns="table.columns" :options="table.options"
                                        ref="table1"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <button class="btn btn-secondary" @click="openModal(row)">
                                    <font-awesome-icon :icon="['far', 'eye']"/>
                                </button>
                            </template>
                        </v-client-table>
                    </div>
                    <div id="pending" class="tab-pane p-2" role="tabpanel" aria-labelledby="pending-tab">
                        <v-client-table :data="table.pending" :columns="table.columns" :options="table.options"
                                        ref="table2"
                                        class="overflow-y-auto">
                            <template v-slot:actions="{row}">
                                <button class="btn btn-secondary" @click="openModal(row, true)">
                                    <font-awesome-icon :icon="['far', 'eye']"/>
                                </button>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>

            <jet-dialog-modal :show="modal.open" @close="closeModal" max-width="5xl">
                <template #title>
                    Visualización de Propuesta
                </template>

                <template #content>
                    <div class="overflow-x-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr class="uppercase text-center">
                                <th class="whitespace-nowrap">Ver.</th>
                                <th class="whitespace-nowrap">Requerimiento</th>
                                <th class="whitespace-nowrap">Propuesta</th>
                                <th class="whitespace-nowrap">Cliente</th>
                                <th class="whitespace-nowrap">Vendedor</th>
                                <th class="whitespace-nowrap">Producto</th>
                                <th class="whitespace-nowrap">Medida</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <td>{{ modal.data.version }}</td>
                                <td>{{ modal.data.header.id }}</td>
                                <td>{{ modal.data.id }}</td>
                                <td>{{ modal.data.header.customer.RAZON_SOCIAL }}</td>
                                <td>{{ modal.data.header.seller.name }}</td>
                                <td>{{ modal.data.product }}</td>
                                <td>{{ modal.data.measure }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                            <tr class="uppercase text-center">
                                <th class="whitespace-nowrap">Marca</th>
                                <th class="whitespace-nowrap">Detalles Requerimiento</th>
                                <th class="whitespace-nowrap">Detalles Propuesta</th>
                                <th class="whitespace-nowrap">2D</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <td>{{ modal.data.header.brand.name }}</td>
                                <td>{{ modal.data.header.details }}</td>
                                <td>{{ modal.data.details }}</td>
                                <td class="text-center">
                                    <button class="btn btn-secondary mr-2" @click="view2D(modal.data.url2D)">
                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="chat__box box mt-10 border border-solid border-gray-300">
                        <div
                            class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                            <h2 class="font-medium text-base mr-auto">Comentarios</h2>

                            <div class="w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                <Tippy
                                    tag="button"
                                    content="Visualizar logs"
                                    class="btn btn-sm btn-secondary"
                                    :options="{
                                placement: 'left'
                            }"
                                    href="javascript:void(0)"
                                    @click.native=""
                                >
                                    <font-awesome-icon icon="list-ol"/>
                                </Tippy>
                            </div>
                        </div>

                        <div class="overflow-y-scroll scrollbar-hidden px-5 pt-5 flex-1 h-56 max-h-56"
                             ref="proposal_comments">
                            <template v-for="log in modal.data.logs">

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

                                <template
                                    v-else-if="log.created_user.id !== $page.props.user.id && log.type === 'comment'">
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

                        <div
                            class="pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400">
                        <textarea
                            class="chat__box__input form-control dark:bg-darkmode-600 h-12 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                            @keypress.enter.prevent="addComment"
                            v-model="comment"
                            rows="1"
                            placeholder="Escribe tu mensaje…"></textarea>

                            <a href="javascript:"
                               @click="addComment"
                               class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5">
                                <font-awesome-icon :icon="['far', 'paper-plane']"/>
                            </a>
                        </div>
                    </div>

                    <div class="mt-10">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="input-form">
                                <label class="form-label w-full flex flex-col sm:flex-row">
                                    Area
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                        Opcional
                                    </span>
                                </label>

                                <input type="number" class="form-control"
                                       :class="{ 'border-danger': v$.form.area.$error }"
                                       v-model="form.area">

                                <template v-if="v$.form.area.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.area.$errors"
                                            :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>

                            <div class="input-form">
                                <label class="form-label w-full flex flex-col sm:flex-row">
                                    Peso
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                        Obligatorio
                                    </span>
                                </label>

                                <input type="number" class="form-control"
                                       :class="{ 'border-danger': v$.form.weight.$error }"
                                       v-model="form.weight">

                                <template v-if="v$.form.weight.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.weight.$errors"
                                            :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>

                            <div class="input-form" v-if="!modal.isPending">
                                <label class="form-label w-full flex flex-col sm:flex-row">
                                    Archivo 3D
                                    <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                        Obligatorio
                                    </span>
                                </label>

                                <input type="file"
                                       accept="image/*"
                                       ref="file_3d"
                                       class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                                       :class="{ 'border-danger': v$.form.file3D.$error }"
                                       @change="image3DFileChanged($event)">

                                <template v-if="v$.form.file3D.$error">
                                    <ul class="mt-1">
                                        <li class="text-danger" v-for="(error, index) of v$.form.file3D.$errors"
                                            :key="index">
                                            {{ error.$message }}
                                        </li>
                                    </ul>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button class="btn btn-danger mr-auto" @click="returnWithoutGestion(modal.data.id)">
                        Devolver sin gestionar
                    </button>

                    <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                        Cancelar
                    </button>

                    <button @click="updateProposal()" type="button" class="btn btn-primary" v-if="modal.isPending">
                        Actualizar propuesta
                    </button>

                    <button @click="store3D()" type="button" class="btn btn-primary" v-else>
                        Actualizar 3D
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3"
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {required, minValue, requiredIf} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        proposals: Array,
        pending: Array
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    validations() {
        return {
            form: {
                file3D: {
                    required: requiredIf(function () {
                        return !this.modal.isPending
                    })
                },
                weight: {
                    minValue: minValue(0),
                    required: requiredIf(function () {
                        return this.modal.isPending
                    })
                },
                area: {
                    minValue: minValue(0),
                    required: requiredIf(function () {
                        return this.modal.isPending
                    })
                },
            }
        }
    },

    data() {
        return {
            table: {
                data: this.proposals,
                pending: this.pending,
                columns: [
                    'requirement',
                    'id',
                    'customer',
                    'seller',
                    'designer',
                    'product',
                    'measure',
                    'brand',
                    'created_at',
                    'actions'
                ],
                options: {
                    headings: {
                        requirement: "# REQUERIMIENTO",
                        id: "# PROPUESTA",
                        customer: "CLIENTE",
                        seller: "VENDEDOR",
                        designer: "DISEÑADOR",
                        product: "PRODUCTO",
                        measure: "MEDIDA",
                        brand: "MARCA",
                        created_at: "CREADO EL",
                        actions: ""
                    },
                    clientSorting: false,
                    sortable: ['id', 'customer', 'seller', 'designer', 'product', 'measure', 'created_at'],
                    templates: {
                        requirement: function (h, row) {
                            return row.header.id
                        },
                        customer: function (h, row) {
                            return row.header.customer.RAZON_SOCIAL
                        },
                        seller: function (h, row) {
                            return row.header.seller.name
                        },
                        designer: function (h, row) {
                            return row.created_user ? row.created_user.name :
                                <span class="badge badge-danger">sin asignar</span>
                        },
                        brand: function (h, row) {
                            return row.header.brand.name
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    }
                },
            },

            modal: {
                isPending: false,
                open: false,
                data: {}
            },

            form: {
                file3D: {},
                weight: 0,
                area: 0
            },
            comment: ''
        }
    },
    methods: {
        openModal(proposal, isPending = false) {
            this.modal = {
                isPending: isPending,
                open: true,
                data: proposal
            }
        },

        closeModal() {
            this.modal = {
                isPending: false,
                open: false,
                data: {}
            }
            this.v$.form.$reset()
        },

        view2D(url) {
            url = url.replace('/var/www/html/storage/app', '')

            this.$swal({
                title: "Archivo 2D",
                imageUrl: url,
                imageAlt: '2D-image',
                confirmButtonText: 'Cerrar'
            })
        },

        store3D() {
            this.v$.form.$touch()
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

                const formData = new FormData();
                let file = this.$refs.file_3d.files[0];

                formData.append('file_3d', file);
                formData.append('id', this.modal.data.id)
                formData.append('weight', this.form.weight)

                axios.post(route('design-requirements.proposal.update-3D'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Propuesta actualizada con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.closeModal()
                    this.table.data = resp.data
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },

        updateProposal(){
            this.v$.form.$touch()
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

                axios.post(route('design-requirements.proposal.update-proposal-3d'), {
                    id: this.modal.data?.id,
                    weight: this.form.weight,
                    area: this.form.area,
                }).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Propuesta actualizada con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.closeModal()
                    this.table.pending = resp.data
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            }
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

            axios.post(route('design-requirements.proposal.add-comment'), {
                id: this.modal.data.id,
                comment: this.comment
            }).then(resp => {
                this.modal.data.logs = resp.data
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

        image3DFileChanged(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.file3D = ''
            }
            this.form.file3D = files[0];
        },

        endScrollComments() {
            this.$refs.proposal_comments.scrollTop = this.$refs.proposal_comments.scrollHeight;
        },

        returnWithoutGestion(id){
            this.$swal({
                icon: 'question',
                title: '¿Devolver propuesta sin gestionar?',
                text: "¡Se devolverá la propuesta al area de diseño grafico sin ninguna gestion!",
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!',
                input: 'textarea',
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
                        title: 'Cargando Información…',
                        text: 'Este proceso puede tardar unos segundos.',
                    });

                    axios.post(route('design-requirement.return-without-gestion'), {
                        id: id,
                        origin: '3D',
                        justify: inputValue.value
                    }).then(resp => {
                        this.table.data = resp.data

                        this.$swal({
                            title: '¡Éxito!',
                            text: "¡Propuesta devuelta con éxito!",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: err.response?.data,
                            confirmButtonText: 'Aceptar'
                        });
                    })
                }
            })
        }
    }
}
</script>

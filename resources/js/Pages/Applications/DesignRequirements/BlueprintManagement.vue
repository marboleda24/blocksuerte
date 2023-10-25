<template>
    <Head title="Propuestas pendientes Planos"/>

    <portal to="application-title">
        Propuestas pendientes Planos
    </portal>
    <div>
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options" ref="table1"
                        class="overflow-y-auto">
            <template v-slot:actions="{row}">
                <button class="btn btn-secondary" @click="openModal(row)">
                    <font-awesome-icon :icon="['far', 'eye']"/>
                </button>
            </template>
        </v-client-table>

        <jet-dialog-modal :show="modalOpen" @close="closeModal" max-width="5xl">
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
                                <th class="whitespace-nowrap">Cliente</th>
                                <th class="whitespace-nowrap">Vendedor</th>
                                <th class="whitespace-nowrap">Producto</th>
                                <th class="whitespace-nowrap">Medida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>{{ modalData.version }}</td>
                                <td>{{ modalData.header.consecutive }}</td>
                                <td>{{ modalData.header.customer.RAZON_SOCIAL }}</td>
                                <td>{{ modalData.header.seller.name }}</td>
                                <td>{{ modalData.product }}</td>
                                <td>{{ modalData.measure }}</td>
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
                                <td>{{ modalData.header.brand.name }}</td>
                                <td>{{ modalData.header.details }}</td>
                                <td>{{ modalData.details }}</td>
                                <td class="text-center">
                                    <button class="btn btn-secondary mr-2" @click="view2D(modalData.url2D)">
                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div class="chat__box box mt-10 border border-solid border-gray-300">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
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

                    <div class="overflow-y-scroll scrollbar-hidden px-5 pt-5 flex-1 h-56 max-h-56" ref="proposal_comments">
                        <template v-for="log in modalData.logs">

                            <template v-if="log.created_user.id === $page.props.user.id && log.type === 'comment'">
                                <div class="chat__box__text-box flex items-end float-right mb-4">
                                    <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md">
                                        {{ log.description }}
                                        <div class="mt-1 text-xs text-white text-opacity-80"> {{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }} </div>
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
                                    <div class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md">
                                        {{ log.description }}
                                        <div class="mt-1 text-xs text-slate-500">{{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}</div>
                                    </div>
                                </div>
                                <div class="clear-both"></div>
                            </template>
                        </template>
                    </div>

                    <div class="pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400">
                        <textarea class="chat__box__input form-control dark:bg-darkmode-600 h-12 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                                  @keypress.enter.prevent="addComment"
                                  v-model="comment"
                                  rows="1"
                                  placeholder="Escribe tu mensaje…"></textarea>

                        <a href="javascript:;"
                           @click="addComment"
                           class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5">
                            <font-awesome-icon :icon="['far', 'paper-plane']" />
                        </a>
                    </div>
                </div>

                <div class="mt-10">
                    <div class="alert alert-warning show flex items-center mb-10" role="alert">
                        <AlertTriangleIcon class="w-6 h-6 mr-2"/>
                        <strong>NOTA: &nbsp;</strong> Por favor seleccione el plano o haga clic en el boton "Identificar Plano" para que el sistema busque el plano para este producto
                    </div>

                    <div class="grid grid-cols-5 gap-4">
                        <div class="input-form col-span-3">
                            <label class="form-label w-full flex flex-col sm:flex-row">
                                Plano
                                <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                    Obligatorio
                                </span>
                            </label>

                            <select class="form-select"
                                    :class="{ 'border-danger': v$.form.blueprint_id.$error }"
                                    v-model="form.blueprint_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="blueprint in blueprints" :value="blueprint.id">{{ `Plano - ${blueprint.id}` }}</option>
                            </select>

                            <template v-if="v$.form.blueprint_id.$error">
                                <ul class="mt-1">
                                    <li class="text-danger" v-for="(error, index) of v$.form.blueprint_id.$errors"
                                        :key="index">
                                        {{ error.$message }}
                                    </li>
                                </ul>
                            </template>
                        </div>
                        <button class="btn btn-primary" @click="freshBlueprints(modalData.line.code)">
                            Obtener / Refrescar Planos
                        </button>

                        <button class="btn btn-primary" @click="identify_blueprint">
                            Identificar Plano
                        </button>
                    </div>
                </div>

            </template>

            <template #footer>
                <button class="btn btn-danger mr-auto" @click="returnWithoutGestion(modalData.id)">
                    Devolver sin gestionar
                </button>

                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button @click="store()" type="button" class="btn btn-primary">
                    Asignar Plano
                </button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3"
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core';
import {required} from "@/utils/i18n-validators";
export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        proposals: Array,
    },

    components: {
        Head,
        Link,
        JetDialogModal
    },

    validations(){
        return {
            form: {
                blueprint_id: {required}
            }
        }
    },

    data(){
        return {
            table: {
                data: this.proposals,
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
                            return row.created_user ? row.created_user.name : <span class="badge badge-danger">sin asignar</span>
                        },
                        brand: function (h, row) {
                            return row.header.brand.name
                        },
                        created_at: function (h, row) {
                            return this.$h.formatDate(row.created_at, 'YYYY-MM-DD hh:mm a')
                        }
                    }
                }
            },
            form: {
                blueprint_id: ''
            },
            modalOpen: false,
            modalData: {},
            comment: '',
            blueprints: []
        }
    },

    methods: {
        openModal(proposal){
            this.modalData = proposal
            this.modalOpen = true
        },

        closeModal(){
            this.modalOpen = false
            this.modalData = {}
            this.v$.form.$reset()
            this.blueprints = []
        },

        view2D(url){
            url = url.replace('/var/www/html/storage/app', '')

            this.$swal({
                title: "Archivo 2D",
                imageUrl: url,
                imageAlt: '2D-image',
                confirmButtonText: 'Cerrar'
            })
        },

        addComment(){
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
                id: this.modalData.id,
                comment: this.comment
            }).then(resp => {
                this.modalData.logs = resp.data
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

        endScrollComments(){
            this.$refs.proposal_comments.scrollTop = this.$refs.proposal_comments.scrollHeight;
        },

        store(){
            this.v$.form.$touch()
            if (!this.v$.form.$invalid) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Guardando…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('design-requirements.blueprint-management.update'), this.form).then(resp => {
                    this.closeModal()
                    this.table.data = resp.data

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Plano actualizado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                    })
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                    console.error(err);
                })
            }
        },

        freshBlueprints(line_code){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('design-requirements.blueprint-management.load'), {
                params: {
                    line_code: line_code
                }
            }).then(resp => {
                this.blueprints = resp.data
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

        identify_blueprint(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('design-requirements.blueprint-management.identify', this.modalData.id)).then(resp => {
                if (resp.data.state === 'blueprint-exist'){
                    this.closeModal()
                    this.table.data = resp.data.proposals

                    this.$swal({
                        icon: 'success',
                        title: '¡Plano Registrado!',
                        text: resp.data.msg,
                        confirmButtonText: 'Aceptar',
                    })
                }else{
                    this.$swal({
                        icon: 'info',
                        title: '¡No se encontró plano!',
                        text: 'El sistema no pudo identificar el plano adecuado para esta propuesta',
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
                        origin: 'Planos',
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

<template>
    <div>
        <Head title="Artes Arcaicos"/>

        <portal to="application-title">
            Artes Arcaicos
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label>Codigo Del Arte</label>
                        <input type="text"
                               class="form-control"
                               :class="{ 'border-danger': v$.form.art.$error}"
                               v-model="form.art"
                               v-uppercase/>

                        <template v-if="v$.form.art.$error ">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.art.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label class="flex flex-col sm:flex-row">
                            Producto
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                                Obligatorio
                            </span>
                        </label>

                        <autocomplete
                            ref="product_item"
                            :url="route('order.search-products')"
                            class="w-full"
                            show-field="field"
                            @selected-value="onSelectProduct"
                        />

                        <template v-if="v$.form.product_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger"
                                    v-for="(error, index) of v$.form.product_code.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div>
                        <label>Archivo</label>
                        <input type="file" ref="file" @change="fileChange($event)" class="form-control" accept="application/pdf"/>
                    </div>
                    <button class="btn btn-sm btn-primary" @click="save()">
                        AGREGAR
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
                <template v-slot:code="{row}">
                    <div>
                        <button  class="btn btn-sm btn-secondary" v-if="row.archaic === '1' && row.proposal_id === null " @click="openModal(row.code)">
                            {{ row.code }}
                        </button>
                        <a  v-else :href="route('design-requirements.proposal-print', row.proposal_id)"  target="_blank" class="btn btn-sm btn-secondary" >
                            {{ row.code }}
                        </a>
                    </div>
                </template>
            </v-client-table>

            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title>
                    Visualizar arte
                </template>

                <template #content>
                    <template v-if="artUrl">
                        <embed class="pdfobject" :src="artUrl" type="application/pdf" style="overflow: auto; width: 100%; height: 600px;">
                    </template>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>
<script lang="jsx">
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import {Head, Link} from "@inertiajs/vue3";
import {helpers, required} from "@/utils/i18n-validators";
import useVuelidate from '@vuelidate/core';
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";

const validName = helpers.regex(/^[A-Z{1,2}][0-9]{5}/gi);
const {withAsync} = helpers

const CancelToken = axios.CancelToken;
let source;

const uppercase = {
    beforeUpdate(el) {
        el.value = el.value.toUpperCase()
    }
}

export default {
    components: {
        Autocomplete,
        Link,
        Head,
        JetDialogModal
    },

    props: {
        arts: Array,
    },

    setup() {
        return {v$: useVuelidate()}
    },

    validations() {
        return {
            form: {
                art: {
                    required,
                    isUnique: helpers.withMessage('Este arte ya se encuentra registrado', withAsync(async (value) => {
                        if (source) source.cancel();
                        source = CancelToken.source();
                        if (value) {
                            try {
                                return await axios.get(route('archaic-art.verify-art', value), {
                                    cancelToken: source.token
                                }).then(resp => {
                                    return Boolean(resp.data);
                                })
                            } catch (error) {
                                return false
                            }
                        } else {
                            return false
                        }
                    })),
                    $autoDirty: true,
                    validName: helpers.withMessage('El arte debe comenzar con una letra y tener 5 numeros', validName)
                },

                record: {required},
                product_code: {required},
            },
        }
    },

    data() {
        return {
            table:{
                data: this.arts,
                columns: [
                    'code',
                    'archaic',
                    'product_code',
                    'product_description',
                ],
                options: {
                    headings: {
                        code: 'ARTE',
                        archaic: 'ARCAICO',
                        product_code: 'CODIGO',
                        product_description: 'DESCRIPCION'
                    },
                    templates: {
                        archaic(h, row){
                            if (!row.proposal_id){
                                return <span class="badge badge-rounded badge-warning">SI</span>
                            }
                            return <span class="badge badge-rounded badge-success">NO</span>
                        },

                    },
                    cellClasses: {
                        archaic: [{
                            class: 'text-center',
                            condition: row => row
                        }],
                    }
                },
            },
            isOpen: false,

            form: {
                art: "",
                record: "",
                product_code: '',
            },

            artUrl: ''
        }
    },

    directives: {
        uppercase,
    },

    methods: {
        fileChange(event){
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.form.record = ''
            }
            this.form.record = files[0];
        },

        save() {
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                const formData = new FormData();
                Object.keys(this.form).forEach(key => formData.append(key, this.form[key]));
                formData.delete('record');

                let file = this.$refs.file.files[0];
                formData.append('file', file);

                axios.post(route('archaic-art.save'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.table.data = resp.data;

                    this.form = {
                        art: "",
                        record: "",
                        product_code: null,
                    }

                    this.v$.form.reset()

                    this.$swal({
                        title: '¡Éxito!',
                        text: "Arte creado con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })

                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data.message,
                        confirmButtonText: 'Aceptar',
                    });
                })
            }
        },

        closeModal() {
            this.isOpen = false;
        },

        openModal(arte){
            this.artUrl = `http://192.168.1.12/intranet_ci/assets/Artes/${arte}.pdf`;
            this.isOpen = true
        },

        onSelectProduct(obj){
            this.form.product_code = obj.code
        }
    }
}
</script>

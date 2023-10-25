<template>
    <div>
        <Head title="Validacion Razon"/>

        <portal to="application-title">
            Validacion Razon
        </portal>

        <v-client-table :data="table.data" :columns="table.columns" :options="table.options">
            <template v-slot:actions="{row}">
                <div class="text-center">
                    <button class="btn btn-primary btn-sm" @click="updateReason(row.OV)">
                        <font-awesome-icon :icon="['far', 'pen-to-square']" class="mr-1"/>
                        Actualizar Razon
                    </button>
                </div>
            </template>
        </v-client-table>
    </div>
</template>

<script lang="jsx">
import {Head} from "@inertiajs/vue3";

export default {
    props: {
        data: Array,
        reasons: Array
    },

    components: {
        Head
    },

    data() {
        return {
            table: {
                data: this.data,
                columns: [
                    'OV',
                    'NIT',
                    'CUSTOMER',
                    'OC',
                    'CREATION_DATE',
                    'MODIFIED_BY',
                    'actions'
                ],
                options: {
                    headings: {
                        OV: 'OV',
                        NIT: 'NIT',
                        CUSTOMER: 'CLIENTE',
                        OC: 'OC',
                        CREATION_DATE: 'CREADO EL',
                        MODIFIED_BY: 'MODIFICADO POR',
                        actions: ''
                    },
                    sortable: ['OV', 'NIT', 'CUSTOMER', 'OC', 'CREATION_DATE', 'MODIFIED_BY'],
                }
            }
        }
    },

    methods: {
        updateReason(ov) {
            let reasons_list = [];

            reasons_list[0] = 'Seleccione…'

            this.reasons.map(function (elem) {
                reasons_list[elem.CODE_36.trim()] = elem.DESC_36.trim()
            })

            reasons_list = reasons_list.sort((a, b) => {
                return b - a
            })


            this.$swal({
                icon: 'info',
                title: 'Actualizacion de razon',
                text: `Por favor, seleccionar la razon para la OV: ${ov}`,
                input: 'select',
                inputOptions: reasons_list,
                showCancelButton: true,
                inputAttributes: {
                    required: true
                },
                confirmButtonText: 'Actualizar',
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
                        title: 'Actualizando…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    axios.post(route('reason-validation.update'), {
                        ov: ov,
                        reason: inputValue.value
                    }).then(resp => {
                        this.table.data = resp.data

                        this.$swal({
                            title: '¡Éxito!',
                            text: "Documento actualizado con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: error?.response?.data,
                            confirmButtonText: 'Aceptar',
                        });
                    })
                }
            })
        }
    }
}
</script>

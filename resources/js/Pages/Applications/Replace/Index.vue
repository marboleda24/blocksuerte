<template>
    <div>
        <Head title="Cambio de información" />

        <portal to="application-title">
            Cambio de información
        </portal>

        <div>
            <div class="grid grid-cols-3 gap-4">
                <div class="input-group col-span-2">
                    <div id="input-group-search" class="input-group-text">Buscar</div>
                    <input type="text" class="form-control" v-model="search" placeholder="Escribe algo para comenzar la busqueda" aria-label="search" aria-describedby="input-group-search" />
                </div>

                <button class="btn btn-primary" @click="searchData(search)">
                    <font-awesome-icon icon="search" class="mr-2"/>
                    Buscar Productos
                </button>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-8">
                <div class="input-group">
                    <div id="input-group-old" class="input-group-text w-36">Valor Anterior</div>
                    <input type="text" class="form-control" v-model="transform_data.old" placeholder="Expresion regular o coincidencia" aria-label="search" aria-describedby="input-group-old" />
                </div>

                <div class="input-group">
                    <div id="input-group-new" class="input-group-text w-36">Valor Nuevo</div>
                    <input type="text" class="form-control" v-model="transform_data.new" placeholder="Nuevo valor o regex" aria-label="search" aria-describedby="input-group-new" />
                </div>
                <button class="btn btn-primary" @click="previewData">
                    <font-awesome-icon :icon="['far', 'eye']" class="mr-2"/>
                    Previsualizar información
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-8">
                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">
                            Part Master - MAX
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto max-h-screen">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">Código</th>
                                        <th class="whitespace-nowrap">Descripción Actual</th>
                                        <th class="whitespace-nowrap">Nueva Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="results.part_master.length > 0">
                                        <tr v-for="row in results.part_master">
                                            <td>{{ row.code }}</td>
                                            <td>{{ row.description }}</td>
                                            <td>{{ row.new_description }}</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr>
                                            <td colspan="3" class="text-danger text-center">NO SE ENCONTRO INFORMACIÓN</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">
                            Part Sales - MAX
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto max-h-screen">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="whitespace-nowrap">Código</th>
                                    <th class="whitespace-nowrap">Descripción Actual</th>
                                    <th class="whitespace-nowrap">Nueva Descripción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="results.part_sales.length > 0">
                                    <tr v-for="row in results.part_sales">
                                        <td>{{ row.code }}</td>
                                        <td>{{ row.description }}</td>
                                        <td>{{ row.new_description }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-danger text-center">NO SE ENCONTRO INFORMACIÓN</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">
                            Productos - Visual Control
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto max-h-screen">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="whitespace-nowrap">Código</th>
                                    <th class="whitespace-nowrap">Descripción Actual</th>
                                    <th class="whitespace-nowrap">Nueva Descripción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="results.products.length > 0">
                                    <tr v-for="row in results.products">
                                        <td>{{ row.code }}</td>
                                        <td>{{ row.description }}</td>
                                        <td>{{ row.new_description }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-danger text-center">NO SE ENCONTRO INFORMACIÓN</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">
                            Materias Primas - Visual Control
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto max-h-screen">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="whitespace-nowrap">Código</th>
                                    <th class="whitespace-nowrap">Descripción Actual</th>
                                    <th class="whitespace-nowrap">Nueva Descripción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="results.source_materials.length > 0">
                                    <tr v-for="row in results.source_materials">
                                        <td>{{ row.code }}</td>
                                        <td>{{ row.description }}</td>
                                        <td>{{ row.new_description }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-danger text-center">NO SE ENCONTRÓ INFORMACIÓN</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-row mt-4 p-2">
                <button class="btn btn-primary mx-auto w-2/5" @click="replaceData(results)">
                    <font-awesome-icon :icon="['far', 'clone']" class="mr-2"/>
                    Reemplazar Información
                </button>
            </div>

        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    components: {
        Head
    },

    data(){
        return {
            search: '',
            results: {
                part_master: [],
                part_sales: [],
                products: [],
                source_materials: []
            },
            transform_data: {
                old: '',
                new: '',
            }
        }
    },

    methods: {
        searchData(search){
            if(search === '' || search === null){
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. el campo búsqueda no puede ir vacio',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
            }else{
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });
                axios.get(route('replace-data.search-data'), {
                    params: {
                        search: search
                    }
                }).then(resp => {
                    this.results = resp.data
                    this.$swal.close()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error eliminando el registro.',
                        confirmButtonText: 'Aceptar',
                        timer: 6000
                    });
                    console.log(err)
                })
            }
        },

        previewData(){
            const transformData = this.transform_data;

            var re = new RegExp(transformData.old, 'gm');

            console.log(re);

            this.results.part_master =  this.results.part_master.map(function (item) {
                return {
                    code: item.code,
                    description: item.description,
                    new_description:  item.description.replace(re, `${transformData.new}`)
                }
            })

            this.results.part_sales =  this.results.part_sales.map(function (item) {
                return {
                    code: item.code,
                    description: item.description,
                    new_description:  item.description.replace(re, `${transformData.new}`)
                }
            })

            this.results.products =  this.results.products.map(function (item) {
                return {
                    code: item.code,
                    description: item.description,
                    new_description:  item.description.replace(re, `${transformData.new}`)
                }
            })

            this.results.source_materials =  this.results.source_materials.map(function (item) {
                return {
                    code: item.code,
                    description: item.description,
                    new_description:  item.description.replace(re, `${transformData.new}`)
                }
            })
        },

        replaceData(results){
            this.$swal({
                title: '¿Esta seguro que desea continuar?',
                text: "¡Esta apunto de reemplazar información, esta acción no es reversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Reemplazando información…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });
                    axios.post(route('replace-data.store'), results).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: resp.data,
                            confirmButtonText: 'Aceptar',
                            timer: 6000
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                            timer: 6000
                        });
                    })
                }
            })
        }
    }
}
</script>

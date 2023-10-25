<template>
    <div>
        <Head title="Reemplazo de información"/>

        <portal to="application-title">
            Reemplazo de información
        </portal>

        <div>
            <div class="grid grid-cols-5 gap-4">
                <div class="mt-2">
                    <label>Base de datos</label>
                    <select class="form-select" v-model="form.database" @change="get_tables(form.database)"
                            :disabled="count_data > 0">
                        <option value="" disabled selected>Seleccione...</option>
                        <option value="DMSP">DMS Pruebas</option>
                        <option value="DMS">DMS Producción</option>
                        <option value="MAXP">MAX Pruebas</option>
                        <option value="MAX">MAX Producción</option>
                    </select>
                </div>

                <div class="mt-2">
                    <label>Tabla</label>
                    <select class="form-select" v-model="form.table" @change="get_columns(form.database, form.table)"
                            :disabled="count_data > 0">
                        <option value="" disabled selected>Seleccione...</option>
                        <option v-for="table in tables" :value="table">{{ table }}</option>
                    </select>
                </div>

                <div class="mt-2">
                    <label>Columna</label>
                    <select class="form-select" v-model="form.column" :disabled="count_data > 0">
                        <option value="" disabled selected>Seleccione...</option>
                        <option v-for="(column, index) in columns" :value="index">{{ index }}</option>
                    </select>
                </div>

                <div class="mt-2">
                    <label>Busqueda</label>
                    <input type="text" class="form-control" v-model="form.quer" :disabled="count_data > 0">
                </div>

                <div class="mt-2">
                    <button class="btn btn-primary h-full w-full" @click="count_rows(form)" v-if="count_data === 0">
                        Buscar
                    </button>

                    <button class="btn btn-secondary h-full w-full" @click="reset" v-else>
                        Nueva busqueda
                    </button>
                </div>
            </div>

            <template v-if="count_data > 0">
                <div class="grid grid-cols-5 gap-4 mt-5">
                    <div class="mt-2">
                        <label>Columna</label>
                        <select class="form-select" v-model="form.column_replace">
                            <option value="" disabled selected>Seleccione...</option>
                            <option v-for="(column, index) in columns" :value="index">{{ index }}</option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <label>Reemplazar</label>
                        <input type="text" class="form-control" v-model="form.replace_data">
                    </div>

                    <div class="mt-2">
                        <label>Por</label>
                        <input type="text" class="form-control" v-model="form.to_data">
                    </div>

                    <div class="mt-2">
                        <button class="btn btn-primary h-full w-full" @click="replace_data(form)">
                            Reemplazar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    components: {
        Head
    },

    data() {
        return {
            form: {
                database: '',
                table: '',
                column: '',
                quer: '',
                column_replace: '',
                replace_data: '',
                to_data: ''
            },
            tables: [],
            columns: [],
            count_data: 0
        }
    },

    methods: {
        get_tables(db) {
            this.form.table = ''
            this.form.column = ''
            this.form.quer = ''
            this.tables = []
            this.columns = []

            axios.get(route('queries.replace-data.get-tables'), {
                params: {
                    db: db
                }
            }).then(resp => {
                this.tables = resp.data

            }).catch(err => {
                console.log(err.data)
            })
        },

        get_columns(db, table) {
            this.form.column = ''
            this.form.quer = ''
            this.columns = []

            axios.get(route('queries.replace-data.get-columns'), {
                params: {
                    db: db,
                    table: table
                }
            }).then(resp => {
                this.columns = resp.data

            }).catch(err => {
                console.log(err.data)
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        count_rows(data) {
            axios.get(route('queries.replace-data.count-data'), {
                params: {
                    db: data.database,
                    table: data.table,
                    column: data.column,
                    q: data.quer
                }
            }).then(resp => {
                this.count_data = resp.data
            }).catch(err => {
                console.log(err.data)
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        replace_data(data) {
            this.$swal({
                title: '¡Peligro!',
                text: "recuerde que esta acción no es reversible y puede causar Daños en la Base de Datos, " +
                    "si esta usando una base de datos de produccion le recomendamos crear una copia de seguridad primero",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, Reemplazar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        title: 'Confirme nuevamente esta acción',
                        text: "Esta a punto de reemplazar información en una base de datos, ¿esta seguro de continuar?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '¡Si, Continuar!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$swal({
                                iconHtml: this.$h.loadIcon(),
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                title: 'Procesando peticion…',
                                text: 'Dependiendo del volumen de datos este proceso puede tardar varios minutos, por favor no cierre ni salga de esta pagina ' +
                                    'hasta que el proceso haya terminado',
                            });

                            axios.post(route('queries.replace-data.replace'), data).then(resp => {
                                this.$swal({
                                    icon: 'success',
                                    title: 'Proceso finalizado',
                                    text: 'Los datos fueron reemplazados con éxito',
                                    timerProgressBar: true,
                                    confirmButtonText: 'Aceptar',
                                    timer: 6000,
                                })
                                this.reset();
                                console.log(resp.data);
                            }).catch(err => {
                                console.log(err.data)
                                this.$swal({
                                    icon: 'error',
                                    title: '¡Ups!',
                                    text: 'Hubo un error procesando la solicitud, la tranccion fue revertida',
                                    confirmButtonText: 'Aceptar',
                                });
                            })
                        }
                    });
                }
            })
        },

        reset() {
            this.form = {
                database: '',
                table: '',
                column: '',
                quer: '',
                column_replace: '',
                replace_data: '',
                to_data: ''
            }
            this.tables = []
            this.columns = []
            this.count_data = 0
        }

    }
}
</script>


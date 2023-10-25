<template>
    <div>
        <Head title="Monitor de trabajos" />

        <portal to="application-title">
            Monitor de trabajos
        </portal>

        <portal to="actions" >
            <button class="btn btn-primary mr-2" @click="load_data(true)">
                <span>
                    <font-awesome-icon icon="rotate" class="mr-2"/>
                </span>
                Actualizar
            </button>

            <button class="btn btn-danger" @click="purge">
                <span>
                    <font-awesome-icon icon="trash-can" class="mr-2"/>
                </span>
                Purgar registros
            </button>
        </portal>

        <div>
            <div class="grid grid-cols-12 gap-6 mt-5 mb-10">
                <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y" v-for="metric in metrics">
                    <div class="report-box">
                        <div class="box p-5">
                            <div class="flex">
                                <div>
                                    <div class="text-3xl font-medium leading-8">{{ metric.formated }}</div>
                                    <div class="text-base text-slate-500 mt-1">{{ metric.title }}</div>
                                </div>
                                <div class="ml-auto">
                                    <div class="report-box__indicator tooltip cursor-pointer p-2"
                                         :class="{
                                            'bg-success': metric.changes.includes('Arriba'),
                                            'bg-danger': metric.changes.includes('Abajo'),
                                            'bg-warning': metric.changes.includes('Sin cambios')
                                        }">
                                        {{ metric.changes }}
                                        <font-awesome-icon icon="angle-up" class="w-4 h-4 ml-0.5" v-if="metric.changes.includes('Arriba')"/>
                                        <font-awesome-icon icon="angle-down" class="w-4 h-4 ml-0.5" v-else-if="metric.changes.includes('Abajo')"/>
                                        <font-awesome-icon icon="angle-right" class="w-4 h-4 ml-0.5" v-else-if="metric.changes.includes('Sin cambios')"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
            </v-client-table>


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
            timer: null,
            queues: [],
            metrics: [],
            table: {
                data: [],
                columns: [
                    "state",
                    "job",
                    "details",
                    "progress",
                    "started_at",
                    "duration",
                    "error"
                ],
                options: {
                    headings: {
                        state: 'ESTADO',
                        job: 'JOB',
                        details: 'DETALLES',
                        progress: 'PROGRESO',
                        started_at: 'INICIADO',
                        duration: 'DURACION',
                        error: 'ERROR'
                    },
                    templates: {
                        state(h, row){
                            if (row.state === 'running'){
                                return <div class="text-center"><span class="badge badge-rounded badge-primary">en ejecucion</span></div>
                            }else if (row.state === 'success'){
                                return <div class="text-center"><span class="badge badge-rounded badge-success">finalizado</span></div>
                            }else {
                                return <div class="text-center"><span class="badge badge-rounded badge-danger">fallido</span></div>
                            }
                        },
                        job(h, row){
                            return <div><b>{row.name}</b> <br/> <span># {row.job_id}</span></div>
                        },
                        details(h, row){
                            return <div><b>Cola:</b> <span> {row.queue}</span> <br/> <b>Intentos: </b> <span>{row.attempt}</span> </div>
                        },
                        progress(h, row){
                            switch (row.progress){
                                case "0":
                                case null:
                                    return <div class="text-center">–</div>
                                case "50":
                                    return <div class="progress h-4 mt-3">
                                        <div class="progress-bar w-2/3" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                            {row.progress}%
                                        </div>
                                    </div>
                                case "100":
                                    return <div class="progress h-4 mt-3">
                                        <div class="progress-bar w-full" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                            {row.progress}%
                                        </div>
                                    </div>
                            }
                        },
                        started_at(h, row){
                            return this.$h.timeAgo(row.started_at)
                        },
                        duration(h, row){
                            const duration = this.$h.diffTime(row.started_at, row.finished_at)
                            return `${duration.hours}:${duration.minutes}:${duration.seconds}`;
                        },
                        error(h, row){
                            if (row.failed && row.exception_message !== null){
                                return <textarea cols="30" rows="2" class="form-control">{row.exception_message}</textarea>
                            }else {
                                return <div class="text-center">–</div>
                            }
                        }
                    }

                }
            }
        }
    },

    methods: {
        load_data(showMsg){
            if (showMsg){
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información…',
                    text: 'Este proceso puede tardar unos segundos…',
                });
            }

            axios.get(route('custom.monitor.load-data')).then(resp => {
                this.table.data = resp.data.jobs
                this.queues = resp.data.queues
                this.metrics = resp.data.metrics
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        purge(){
            this.$swal({
                title: '¿Purgar registros?',
                text: "¡Esta acción no es reversible!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '¡Si, Purgar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Purgando registros…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    axios.post(route('custom.monitor.purge')).then(resp => {
                        this.table.data = resp.data.jobs
                        this.queues = resp.data.queues
                        this.metrics = resp.data.metrics
                        this.$swal.close()
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud.',
                            confirmButtonText: 'Aceptar',
                        });
                    })
                }
            })

        }
    },

    mounted() {
        this.load_data(false)
        this.timer = setInterval(() => {
            this.load_data(false)
        }, 10000)
    },

    beforeUnmount() {
        clearInterval(this.timer)
    },


}
</script>


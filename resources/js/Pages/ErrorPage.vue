<template>
    <div>
        <Head :title="title"/>


        <div class="max-w-7xl items-center justify-center mx-auto ">
            <div class="grid grid-cols-2 place-items-center h-screen">
                <div class="-intro-x">
                    <img alt="Error page" class="h-1/2" src="/dist/images/Monster404.png"
                         draggable="false">
                </div>

                <div class="text-white mt-10 lg:mt-0">
                    <div class="intro-x text-6xl font-medium">
                        {{ status }}
                    </div>
                    <div class="intro-x text-xl lg:text-3xl font-medium">
                        {{ title }}
                    </div>
                    <div class="intro-x text-lg mt-3">
                        {{ description }}
                    </div>
                    <button v-if="status !== 202" @click="goHome" class="btn btn-info mt-2">
                        Pagina Principal
                    </button>
                    <button v-else @click="goBack" class="btn btn-info mt-2">
                        Volver Atrás
                    </button>
                </div>

                <div class="col-span-2 -mt-56">
                    <div class="box p-5 max-h-36 overflow-y-auto">
                        <template v-if="code !== 0">
                            <strong>Code:</strong> {{ code }} |
                        </template>
                        <template v-if="msg">
                            <strong>Msg:</strong> {{ msg }} |
                        </template>
                        <strong>File:</strong> {{ file }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="jsx">
import Empty from '@/Layouts/Empty.vue'
import {Head, Link, router} from '@inertiajs/vue3'

export default {
    components: {
        Empty,
        Head,
        Link
    },

    props: {
        status: Number,
        code: Number,
        msg: String,
        trace: String,
        file: String
    },

    layout: Empty,

    metaInfo() {
        return {
            title: this.status
        }
    },

    methods: {
        goHome() {
            router.get(route('dashboard'));
        },

        goBack() {
            router.get(route('orders.index'));
        }
    },

    computed: {
        title() {
            return {
                503: "¡Servicio no disponible!",
                500: "Server Error",
                404: "Página no encontrada",
                403: "Acceso prohibido",
                202: "Edición bloqueada"
            }[this.status];
        },
        description() {
            return {
                503: "Lo sentimos, estamos haciendo tareas de mantenimiento. Por favor, vuelva más tarde.",
                500: "Ups, algo salió mal en nuestros servidores.",
                404: "Lo sentimos, la página que estás buscando no se pudo encontrar.",
                403: "Lo sentimos, no tiene permisos suficientes para acceder a esta página.",
                202: "Lo sentimos, pero solo puede editar pedidos en estado borrador, anulado o rechazado"
            }[this.status];
        },
    }
}
</script>

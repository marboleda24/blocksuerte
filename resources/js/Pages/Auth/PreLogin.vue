<template>
    <div>
        <Head title="Bienvenid@" />

        <div class="container sm:px-10">
            <div class="block xl:grid grid-cols-2 gap-4">
                <!-- BEGIN: Login Info -->
                <div class="hidden xl:flex flex-col min-h-screen">
                    <div class="my-auto">
                        <img alt="evpiu" class="-intro-x w-2/3 mt-14" src="img/login_img.png" draggable="false">
                        <div class="-intro-x text-white font-medium text-3xl leading-tight mt-10">
                            ¡Bienvenido a EVPIU!
                        </div>
                        <div class="-intro-x mt-5 text-xl text-white">Plataforma de información unificada de Estrada
                            Velasquez
                        </div>
                    </div>
                </div>
                <!-- END: Login Info -->
                <!-- BEGIN: Login Form -->
                <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                    <div
                        class="my-auto mx-auto xl:ml-50 bg-white xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                        <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">¡Bienvenido!</h2>
                        <div class="intro-x text-2xl xl:text-3xl text-center xl:text-left">Por favor, selecciona una
                            opción
                        </div>

                        <div class="intro-x mt-5 xl:mt-8 xl:flex justify-center xl:justify-start">
                            <Link :href="route('login')" class="btn btn-primary py-3 px-4 w-full align-top mr-2">
                                <font-awesome-icon icon="sign-in-alt" class="mr-2"/>
                                Acceso Intranet
                            </Link>

                            <Link :href="route('employee-requests.index')"
                                  class="btn btn-primary py-3 px-4 w-full align-top">
                                <font-awesome-icon icon="users" class="mr-2"/>
                                Empleados
                            </Link>
                        </div>
                        <div class="intro-x mt-5 xl:pt-6 xl:flex justify-center xl:justify-start border-t">
                            <Link :href="route('guest.codes')"
                                  class="btn btn-primary py-3 px-4 w-full align-top mr-2">
                                <font-awesome-icon icon="boxes-stacked" class="mr-2"/>
                                Productos
                            </Link>

                            <Link :href="route('guest.arts')"
                                  class="btn btn-primary py-3 px-4 w-full align-top">
                                <font-awesome-icon icon="pen-ruler" class="mr-2"/>
                                Artes
                            </Link>
                        </div>
                    </div>
                </div>
                <!-- END: Login Form -->
            </div>
        </div>
    </div>


</template>

<script lang="jsx">
import Empty from '@/Layouts/Empty.vue'
import {Link, Head} from '@inertiajs/vue3';
import dom from "@left4code/tw-starter/dist/js/dom";

export default {
    layout: Empty,

    components: {
        Empty,
        Link,
        Head
    },

    mounted() {
        dom("body").removeClass("app").removeClass('p-0').addClass("login");
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    }
}
</script>

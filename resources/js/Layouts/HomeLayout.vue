<template>
    <div>
        <div class="bg-homeBackground bg-cover bg-no-repeat bg-center">
            <nav class="w-full py-4">
                <div class="w-full container mx-auto flex flex-wrap items-center justify-between">
                    <nav>
                        <ul class="text-lg font-medium mr-auto text-gray-800">
                            <li><img alt="evpiu" class="w-32" src="img/evpiu_v2.png" draggable="false"></li>
                        </ul>
                    </nav>

                    <div class="flex items-center text-lg no-underline text-gray-800 pr-6">
                        <inertia-link v-if="$page.props.user" :href="route('dashboard')">
                            <font-awesome-icon icon="tachometer-alt" class="mr-2"/>
                        </inertia-link>

                        <inertia-link v-else :href="route('login')">
                            <font-awesome-icon icon="sign-in-alt" class="mr-2"/>
                        </inertia-link>
                    </div>
                </div>
            </nav>

            <header class="w-full container mx-auto">
                <div class="flex flex-col items-center py-24">
                    <div class="font-bold text-gray-800 uppercase hover:text-gray-700 text-5xl">
                        Bienvenido a EVPIU
                    </div>
                    <p class="text-lg text-gray-800">
                        Plataforma de Información Unificada
                    </p>
                </div>
            </header>

            <!-- Topic Nav -->
            <nav class="w-full py-4 border-b">
                <div class="w-full flex-grow items-center">
                    <portal-target name="categories"></portal-target>
                </div>
            </nav>
        </div>

        <div class="p-6">
            <slot></slot>
        </div>

        <div class="bg-homeBackground bg-cover bg-no-repeat bg-center">
            <footer class="w-full border-t pb-2">
                <div class="w-full container mx-auto flex flex-col items-center">
                    <div class="flex flex-col md:flex-row text-center md:text-left md:justify-between pt-6">
                        <a href="#" class="px-3">Política de Privacidad</a>
                        <a href="#" class="px-3">Términos & Condiciones</a>
                    </div>
                    <div class="pb-4 pt-2">Copyright © 2021 Estrada Velasquez. Todos los derechos reservados </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script lang="jsx">
import dom from "@left4code/tw-starter/dist/js/dom";
import {defineComponent} from "vue";


export default defineComponent({
    metaInfo: {
        title: 'Bienvenido'
    },

    mounted() {
        dom("body").removeClass("login").addClass("app").addClass('p-0');
        dom('html').addClass("bg-white");
    }
})
</script>

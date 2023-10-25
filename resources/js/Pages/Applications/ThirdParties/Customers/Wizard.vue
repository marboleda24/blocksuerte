<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Dashboard
            </h2>
        </portal>

        <div>
            <h2 class="intro-y text-2xl font-medium mt-10 text-center mr-auto"> SELECCIONA EL TIPO DE CLIENTE A CREAR </h2>

            <div class="flex flex-col lg:flex-row mt-10">
                <div class="intro-y flex-1 box py-16 mb-5 lg:mb-0">
                    <font-awesome-icon icon="city" size="5x" class="text-theme-1 dark:text-theme-10 mx-auto block text-theme-1 dark:text-theme-10 mx-auto"/>
                    <div class="text-2xl font-medium text-center mt-5">
                        CLIENTES NACIONALES
                    </div>
                    <div class="text-lg text-gray-600 dark:text-gray-400 px-10 text-center mx-auto mt-2">
                        Clientes dentro del territorio nacional.
                    </div>
                    <inertia-link :href="route('customers.create')" class="btn btn-rounded-primary py-3 px-4 block mx-auto mt-8 w-48">
                        CONTINUAR
                        <font-awesome-icon icon="arrow-right" class="ml-2"/>
                    </inertia-link>
                </div>

                <div class="intro-y flex-1 box py-16 lg:ml-10 mb-5 lg:mb-0">
                    <font-awesome-icon icon="globe-americas" size="5x" class="text-theme-1 dark:text-theme-10 mx-auto block text-theme-1 dark:text-theme-10 mx-auto"/>
                    <div class="text-2xl font-medium text-center mt-5">
                        CLIENTES DEL EXTERIOR
                    </div>
                    <div class="text-lg text-gray-600 dark:text-gray-400 px-10 text-center mx-auto mt-2">
                        Clientes fuera del territorio nacional.
                    </div>
                    <inertia-link :href="route('customers.create')" class="btn btn-rounded-primary py-3 px-4 block mx-auto mt-8 w-48">
                        CONTINUAR
                        <font-awesome-icon icon="arrow-right" class="ml-2"/>
                    </inertia-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {defineComponent} from "vue";

export default defineComponent({
    metaInfo: {
        title: 'Nuevo Cliente'
    }
})
</script>


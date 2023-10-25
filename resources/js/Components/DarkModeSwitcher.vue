<template>
    <!-- BEGIN: Dark Mode Switcher-->
    <div
        class="dark-mode-switcher cursor-pointer shadow-lg fixed bottom-0 right-0 box border border-gray-300 rounded-full w-20 h-12 flex items-center justify-center z-50 mb-10 mr-10 dark:border-darkmode-600"
        @click="switchMode"
    >
        <div class="text-yellow-500"
             v-if="!darkMode">
            <font-awesome-icon :icon="['far', 'sun']" size="lg"/>
        </div>
        <div class="text-white"
             v-if="darkMode">
            <font-awesome-icon :icon="['far', 'moon']" size="lg"/>
        </div>
    </div>
    <!-- END: Dark Mode Switcher-->
</template>

<script setup>
import {computed} from "vue";
import {useDarkModeStore} from "@/store/dark-mode";
import dom from "@left4code/tw-starter/dist/js/dom";

const darkModeStore = useDarkModeStore();
const darkMode = computed(() => darkModeStore.darkMode);

const setDarkModeClass = () => {
    darkMode.value
        ? dom("html").addClass("dark")
        : dom("html").removeClass("dark");
};

const switchMode = () => {
    darkModeStore.setDarkMode(!darkMode.value);
    setDarkModeClass();
};

setDarkModeClass();
</script>

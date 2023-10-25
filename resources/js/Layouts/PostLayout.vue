<template>
    <div>
        <slot></slot>
    </div>
</template>

<script lang="jsx">
import dom from "@left4code/tw-starter/dist/js/dom";
import {defineComponent} from "vue";


export default defineComponent({
    mounted() {
        dom("body").removeClass("login").addClass("app").addClass('p-0');
        dom('html').addClass("bg-white");
    }
})
</script>


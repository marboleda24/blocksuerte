<template>
    <div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-labelledby="modalComponent" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg pt-20" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mx-auto text-2xl" id="modalTitle" data-private="lipsum">
                        <slot name="title"/>
                    </h2>
                    <button v-if="!noclose" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <button v-if="!title && !noclose" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <slot></slot>
                </div>
                <div class="modal-footer text-center">
                    <slot name="footer"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import dom from "@left4code/tw-starter/dist/js/dom";
import {defineComponent} from "vue";

export default defineComponent({
    name: "ModalComponent",
    props: ["id", "title", "visible", "size", "noclose"],
    //sizes: 'sm', null, 'lg'
    mounted() {
        // sets up a listener that triggers the "@close" event when the user closes the modal
        const self = this;
        dom("#" + this.id).on("hidden.bs.modal", function() {
            self.$emit("close");
        });
    },
    watch: {
        visible() {
            dom("#" + this.id).modal(this.visible ? "show" : "hide");
        },
    },
    methods: {
        closeModal() {
            dom("#" + this.id).modal("hide");
        },
    },
};
</script>

<style scoped></style>

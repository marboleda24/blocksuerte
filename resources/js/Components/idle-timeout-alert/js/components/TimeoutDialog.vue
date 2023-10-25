<template>
    <modal-component id="timeout-modal" :visible="showModal" noclose="true" @close="resetTimeout()">
        <template #title>
            ¡No hemos tenido noticias tuyas en un tiempo!
        </template>
        <div>
            <img src="/img/clock.png" alt="" class="mx-auto" style="width: 30%">
            <p class="text-center text-xl">La sesión se cerrara automáticamente en:</p>
            <p class="text-center text-xl">
                <span class="text-custom-1" style="font-size: 1.5em;">{{ this.remainingTime }}</span>
            </p>
            <p class="text-center text-xl">segundos</p>
        </div>

        <template #footer>
            <button
                class="btn btn-primary uppercase"
                @click="resetTimeout()">
                <span>¡Todavía estoy aquí!</span>
            </button>
        </template>
    </modal-component>
</template>

<script lang="jsx">
import ModalComponent from "./ModalComponent.vue";
import {defineComponent} from "vue";
const timeToAlert = 121;

export default defineComponent({
    name: "TimeoutDialog",
    props: ["keepAlive", "ignoreActivity"],
    components: {
        ModalComponent,
    },
    data() {
        return {
            showModal: false,
            remainingTime: undefined,
            isIdle: false,
            timer: setInterval(() => {
                if (this.remainingTime === undefined) return;

                this.remainingTime = this.remainingTime <= 0 ? 0 : this.remainingTime - 1;

                if (this.remainingTime <= 0 && this.$page.url !== '/login') {
                    setTimeout(() => {
                        this.showModal = false
                        this.$inertia.visit(route('login'), {
                            method: 'get',
                            replace: true,
                            preserveState: false,
                            preserveScroll: false,
                            data: {},
                        });
                    }, 1000);
                    return;
                }

                if (this.remainingTime <= timeToAlert) {
                    if (!this.showModal) {
                        if (this.keepAlive || (this.ignoreActivity !== true && !this.isIdle)) {
                            return this.resetTimeout();
                        }

                        // another check to make sure if we still need to show modal
                        this.checkTimeout(remainingTime => {
                            if (remainingTime <= timeToAlert) {
                                this.showModal = true;
                            }
                        });
                    } else {
                        // modal is already shown, but maybe timeout reset in another tab or window
                        // check again every 5 seconds
                        if (this.remainingTime % 5 === 1) {
                            this.checkTimeout(remainingTime => {
                                if (remainingTime > timeToAlert) {
                                    this.showModal = false;
                                }
                            });
                        }
                    }
                }
            }, 1000)
        };
    },
    mounted() {
        this.checkTimeout();
    },
    onIdle() {
        this.isIdle = true;
    },
    onActive() {
        this.isIdle = false;
    },
    methods: {
        checkTimeout(callback) {
            axios
                .get("/idle-timeout-alert/check")
                .then(response => {
                    this.remainingTime = response.data;
                    if (callback) {
                        callback(response.data);
                    }
                })
                .catch(e => {
                    if (this.$page.url !== '/login'){
                        this.$inertia.visit(route('login'), {
                            method: 'get',
                            replace: true,
                            preserveState: false,
                            preserveScroll: false,
                            data: {},
                        });
                    }
                });
        },
        resetTimeout() {
            axios
                .post("/idle-timeout-alert/ping")
                .then(response => {
                    this.showModal = false;
                    this.remainingTime = response.data;
                })
                .catch(e => {
                    if (this.$page.url !== '/login'){
                        this.$inertia.visit(route('login'), {
                            method: 'get',
                            replace: true,
                            preserveState: false,
                            preserveScroll: false,
                            data: {},
                        });
                    }
                });
        },
    },

    beforeDestroy() {
        this.remainingTime = undefined
        clearInterval(this.timer)
    }

})
</script>

import IdleVue from 'idle-vue';
import Vue from "vue";

Vue.use(IdleVue, {
    idleTime: 30000,
    eventEmitter: new Vue(),
});

Vue.component('modal-component', require('./components/ModalComponent.vue').default);
Vue.component('timeout-dialog', require('./components/TimeoutDialog.vue').default);

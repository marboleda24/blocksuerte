
import './bootstrap';
import './assets/css/app.css'

import {createApp, h, markRaw} from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import {InertiaProgress} from '@inertiajs/progress';
import {createPinia} from "pinia";
import {ClientTable} from '@dcorrea-estrav/v-tables-3';
import {fas} from '@fortawesome/free-solid-svg-icons';
import {far} from '@fortawesome/free-regular-svg-icons';
import {library} from '@fortawesome/fontawesome-svg-core';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import VueSweetalert2 from 'vue-sweetalert2'
import Portal from 'vue3-portal';
import VueGates from 'vue-gates';


import globalComponents from "@/GlobalComponents";
import utils from "@/utils";
import "./libs";
import Permissions from './Plugins/Permissions';

import SortControl from '@/Layouts/Datatables/SortControl.vue';
import GenericFilter from '@/Layouts/Datatables/GenericFilter.vue';
import PerPageSelector from '@/Layouts/Datatables/PerPageSelector.vue';
import VtChildRowToggler from "@/Layouts/Datatables/VtChildRowToggler.vue";
import VtColumnsDropdown from "@/Layouts/Datatables/VtColumnsDropdown.vue";
import {tailwindTheme} from './Layouts/Datatables/themes/tailwind'
import 'alpinejs';

import Main from '@/Layouts/TopMenu/Main.vue';
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";

library.add(fas, far);

const pinia = createPinia()

createInertiaApp({
    title: title => title ? `${title} | EVPIU` : 'EVPIU',
    resolve: name => {

        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );

        page.then((module) => {
            module.default.layout = module.default.layout || Main;
        });

        return page
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(VueGates, {superRole: 'super-admin'})
            .use(VueSweetalert2, {
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary mr-2'
                },
                buttonsStyling: false,
                inputStyling: false,
                reverseButtons: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            })
            .use(globalComponents)
            .use(utils)
            .use(ClientTable, {}, tailwindTheme, {
                sortControl: markRaw(SortControl),
                genericFilter: markRaw(GenericFilter),
                perPageSelector: markRaw(PerPageSelector),
                childRowToggler: markRaw(VtChildRowToggler),
                columnsDropdown: markRaw(VtColumnsDropdown)
            })
            .use(Permissions)
            .use(Portal)
            .mixin({methods: {route}})
            .component("font-awesome-icon", FontAwesomeIcon)
            .mount(el);
    },
});

InertiaProgress.init({color: '#ffffff'});







import {ref} from "vue";
import dom from "@left4code/tw-starter/dist/js/dom";
import {Inertia} from "@inertiajs/inertia";

// Toggle mobile menu
const activeMobileMenu = ref(false);
const toggleMobileMenu = () => {
    activeMobileMenu.value = !activeMobileMenu.value;
};

// Setup mobile menu
const linkTo = (menu, path) => {
    if (menu.subMenu) {
        menu.activeDropdown = !menu.activeDropdown;
    } else {
        activeMobileMenu.value = false;
        Inertia.get(path)
    }
};

const enter = (el, done) => {
    dom(el).slideDown(300);
};

const leave = (el, done) => {
    dom(el).slideUp(300);
};

export {activeMobileMenu, toggleMobileMenu, linkTo, enter, leave};
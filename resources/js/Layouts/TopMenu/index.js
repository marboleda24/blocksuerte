import {ref} from "vue";
import dom from "@left4code/tw-starter/dist/js/dom";
import {router} from "@inertiajs/vue3";
// Toggle search dropdown
const searchDropdown = ref(false);

const showSearchDropdown = () => {
    searchDropdown.value = true;
};

const hideSearchDropdown = () => {
    searchDropdown.value = false;
};

// Setup side menu
const findActiveMenu = (subMenu, url) => {
    let match = false;
    subMenu.forEach((item) => {
        if (item.pageName === url && !item.ignore) {
            match = true;
        } else if (!match && item.subMenu) {
            match = findActiveMenu(item.subMenu, url);
        }
    });
    return match;
};

const nestedMenu = (menu, url) => {
    menu.forEach((item, key) => {
        if (typeof item !== "string") {
            let menuItem = menu[key];
            menuItem.active =
                (item.pageName === url ||
                    (item.subMenu && findActiveMenu(item.subMenu, url))) &&
                !item.ignore;

            if (item.subMenu) {
                menuItem.activeDropdown = findActiveMenu(item.subMenu, url);
                menuItem = {
                    ...item,
                    ...nestedMenu(item.subMenu, url),
                };
            }
        }
    });

    return menu;
};

const linkTo = (menu, url, event) => {
    if (menu.subMenu) {
        menu.activeDropdown = !menu.activeDropdown;
    } else {
        router.get(url)
    }
};

const enter = (el, done) => {
    dom(el).slideDown(300);
};

const leave = (el, done) => {
    dom(el).slideUp(300);
};

export {searchDropdown, showSearchDropdown, hideSearchDropdown, nestedMenu, linkTo, enter, leave};

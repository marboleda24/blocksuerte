<template>
    <!-- BEGIN: Mobile Menu -->
    <div class="mobile-menu md:hidden">
        <div class="mobile-menu-bar">
            <a href="" class="flex mr-auto">
                <img
                    alt="Midone Tailwind HTML Admin Template"
                    class="w-full"
                    src="/images/ev_brand.png"
                />
            </a>
            <BarChart2Icon
                class="w-8 h-8 text-white transform -rotate-90"
                @click="toggleMobileMenu"
            />
        </div>
        <transition @enter="enter" @leave="leave">
            <ul
                v-if="activeMobileMenu"
                class="border-t border-white/[0.08] py-5 hidden"
            >
                <!-- BEGIN: First Child -->
                <template v-for="(menu, menuKey) in formattedMenu">
                    <li
                        v-if="menu == 'devider'"
                        :key="menu + menuKey"
                        class="menu__devider my-6"
                    ></li>
                    <li v-else :key="menu + menuKey">
                        <a
                            href="javascript:;"
                            class="menu"
                            :class="{
                                'menu--active': menu.active,
                                'menu--open': menu.activeDropdown,
                            }"
                            @click="linkTo(menu, menu.pageName)"
                        >
                            <div class="menu__icon">
                                <component :is="menu.icon"/>
                            </div>
                            <div class="menu__title">
                                {{ menu.title }}
                                <div
                                    v-if="menu.subMenu"
                                    class="menu__sub-icon"
                                    :class="{ 'transform rotate-180': menu.activeDropdown }"
                                >
                                    <ChevronDownIcon/>
                                </div>
                            </div>
                        </a>
                        <!-- BEGIN: Second Child -->
                        <transition @enter="enter" @leave="leave">
                            <ul v-if="menu.subMenu && menu.activeDropdown">
                                <li
                                    v-for="(subMenu, subMenuKey) in menu.subMenu"
                                    :key="subMenuKey"
                                >
                                    <a
                                        href="javascript:;"
                                        class="menu"
                                        :class="{ 'menu--active': subMenu.active }"
                                        @click="linkTo(subMenu, subMenu.pageName)"
                                    >
                                        <div class="menu__icon">
                                            <ActivityIcon/>
                                        </div>
                                        <div class="menu__title">
                                            {{ subMenu.title }}
                                            <div
                                                v-if="subMenu.subMenu"
                                                class="menu__sub-icon"
                                                :class="{
                          'transform rotate-180': subMenu.activeDropdown,
                        }"
                                            >
                                                <ChevronDownIcon/>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- BEGIN: Third Child -->
                                    <transition @enter="enter" @leave="leave">
                                        <ul v-if="subMenu.subMenu && subMenu.activeDropdown">
                                            <li
                                                v-for="(lastSubMenu, lastSubMenuKey) in subMenu.subMenu"
                                                :key="lastSubMenuKey"
                                            >
                                                <a
                                                    href="javascript:;"
                                                    class="menu"
                                                    :class="{ 'menu--active': lastSubMenu.active }"
                                                    @click="linkTo(lastSubMenu, lastSubMenu.pageName)"
                                                >
                                                    <div class="menu__icon">
                                                        <ZapIcon/>
                                                    </div>
                                                    <div class="menu__title">
                                                        {{ lastSubMenu.title }}
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </transition>
                                    <!-- END: Third Child -->
                                </li>
                            </ul>
                        </transition>
                        <!-- END: Second Child -->
                    </li>
                </template>
                <!-- END: First Child -->
            </ul>
        </transition>
    </div>
    <!-- END: Mobile Menu -->
</template>

<script setup>
import {usePage} from '@inertiajs/vue3'
import {computed, onMounted, ref, watch} from "vue";
import {helper as $h} from "@/utils/helper";
import {useTopMenuStore} from "@/store/menu";
import {
    activeMobileMenu,
    toggleMobileMenu,
    linkTo,
    enter,
    leave,
} from "./index";
import {nestedMenu} from "@/Layouts/TopMenu";

const formattedMenu = ref([]);
const sideMenuStore = useTopMenuStore();
const page = usePage()
const path = page.url;
const mobileMenu = computed(() => nestedMenu(sideMenuStore.menu, path));


watch(
    computed(() => page.url),
    () => {
        formattedMenu.value = $h.toRaw(mobileMenu.value);
    }
);

onMounted(() => {
    formattedMenu.value = $h.toRaw(mobileMenu.value);
});
</script>

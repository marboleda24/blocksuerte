<template>
    <div>
        <DarkModeSwitcher />
        <MainColorSwitcher/>
        <MobileMenu/>
        <TopBar />
        <LoadingSpinner/>

        <!-- BEGIN: Top Menu -->
        <nav class="top-nav">
            <ul>
                <li v-for="(menu, menuKey) in formattedMenu"
                    :key="menuKey"
                    v-permission:any="$h.gluePermission(menu.permission, '|')">
                    <a
                        href="javascript:void(0)"
                        class="top-menu"
                        @click="linkTo(menu, menu.pageName, $event)"
                    >
                        <div class="top-menu__icon">
                            <font-awesome-icon :icon="menu.icon" size="lg"/>
                        </div>
                        <div class="top-menu__title font-medium">
                            {{ menu.title }}
                            <font-awesome-icon v-if="menu.subMenu" class="top-menu__sub-icon" icon="chevron-down" />
                        </div>
                    </a>


                    <!-- BEGIN: Second Child -->
                    <ul v-if="menu.subMenu">
                        <li v-for="(subMenu, subMenuKey) in menu.subMenu"
                            :key="subMenuKey"
                            v-permission:any="$h.gluePermission(subMenu.permission, '|')">

                            <a href="javascript:void(0)"
                               class="top-menu"
                               @click="linkTo(subMenu, subMenu.pageName, $event)">

                                <div class="top-menu__icon">
                                    <font-awesome-icon :icon="subMenu.icon" v-if="subMenu.icon" size="lg"/>
                                    <font-awesome-icon icon="star" size="lg" v-else/>
                                </div>
                                <div class="top-menu__title font-medium">
                                    {{ subMenu.title }}
                                    <font-awesome-icon v-if="subMenu.subMenu" class="top-menu__sub-icon" icon="chevron-down"/>
                                </div>
                            </a>

                            <!-- BEGIN: Third Child -->
                            <ul v-if="subMenu.subMenu">
                                <li v-for="(lastSubMenu, lastSubMenuKey) in subMenu.subMenu"
                                    :key="lastSubMenuKey"
                                    v-permission:any="$h.gluePermission(lastSubMenu.permission, '|')">
                                    <a href="javascript:void(0)"
                                       class="top-menu"
                                       @click="linkTo(lastSubMenu, lastSubMenu.pageName, $event)">
                                        <div class="top-menu__icon">
                                            <font-awesome-icon :icon="lastSubMenu.icon" v-if="lastSubMenu.icon" size="lg"/>
                                            <font-awesome-icon icon="star" size="lg" v-else/>
                                        </div>
                                        <div class="top-menu__title font-medium">
                                            {{ lastSubMenu.title }}
                                            <font-awesome-icon v-if="lastSubMenu.subMenu" class="top-menu__sub-icon" icon="chevron-down"/>

                                        </div>
                                    </a>
                                    <ul v-if="$h.isset(lastSubMenu.subMenu)">
                                        <li v-for="(fourthlastSubMenu, fourthlastSubMenuKey) in lastSubMenu.subMenu"
                                            :key="fourthlastSubMenuKey"
                                            v-permission:any="$h.gluePermission(fourthlastSubMenu.permission, '|')">
                                            <a href="javascript:void(0)" class="top-menu" @click="linkTo(fourthlastSubMenu, fourthlastSubMenu.pageName, $event)" >
                                                <div class="top-menu__icon">
                                                    <font-awesome-icon :icon="fourthlastSubMenu.icon" v-if="fourthlastSubMenu.icon" size="lg"/>
                                                    <font-awesome-icon icon="star" size="lg" v-else/>
                                                </div>
                                                <div class="top-menu__title font-medium">
                                                    {{ fourthlastSubMenu.title }}
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <!-- END: Third Child -->
                        </li>
                    </ul>
                    <!-- END: Second Child -->
                </li>
                <!-- END: First Child -->
            </ul>
        </nav>
        <!-- END: Top Menu -->


        <div class="content content--top-nav">
            <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
                <!-- BEGIN: Application title -->
                <h2 class="text-lg font-medium mr-auto" >
                    <portal-target name="application-title" />
                </h2>
                <!-- END: Application title -->

                <!-- BEGIN: Application Buttons and actions -->
                <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                    <portal-target name="actions" />
                </div>
                <!-- END: Application Buttons and actions -->
            </div>

            <!-- BEGIN: Content -->
            <div class="intro-y gap-5 mt-5">
                <slot></slot>
            </div>
            <!-- END: Content -->
        </div>

    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { usePage } from '@inertiajs/vue3'
import { useTopMenuStore } from "@/store/menu";
import { helper as $h } from "@/utils/helper";
import MobileMenu from "@/Components/MobileMenu/Main.vue";
import DarkModeSwitcher from "@/Components/DarkModeSwitcher.vue";
import MainColorSwitcher from "@/Components/MainColorSwitcher.vue";
import dom from "@left4code/tw-starter/dist/js/dom";
import TopBar from "@/Components/TopBar.vue";
import LoadingSpinner from "@/GlobalComponents/LoadingSpinner.vue";
import {linkTo, nestedMenu} from "./index";

const formattedMenu = ref([]);
const topMenuStore = useTopMenuStore();
const page = usePage().url.value
const topMenu = computed(() => nestedMenu(topMenuStore.menu, page));

watch(
    computed(() => usePage().url.value),
    () => {
        formattedMenu.value = $h.toRaw(topMenu.value);
    }
);

onMounted(() => {
    dom("body").removeClass("error-page").removeClass("login").addClass("main");
    formattedMenu.value = $h.toRaw(topMenu.value);
});

</script>

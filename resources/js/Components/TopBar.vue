<template>
    <div class="top-bar-boxed border-b border-white/[0.08] -mt-7 md:mt-0 -mx-3 sm:-mx-8 md:mx-0 px-4 sm:px-8 md:px-6 mb-12 md:mb-4">
        <div class="h-full flex items-center">
            <!-- BEGIN: Logo -->
            <Link :href="route('dashboard')" class="-intro-x hidden md:flex items-center">
                <img alt="EVPIU" class="w-10" src="/img/favicon_192x192.png">
                <span class="text-white text-3xl font-medium ml-2">EVPIU </span>
            </Link>
            <!-- END: Logo -->

            <!-- BEGIN: Breadcrumb -->
            <div class="-intro-x h-full mr-auto">
                <ol class="breadcrumb breadcrumb-light">
                    <li class="breadcrumb-item font-bold mt-2">
                        <Link :href="route('dashboard')">{{ $page.props.app_version }}</Link>
                    </li>
                </ol>
            </div>

            <!-- BEGIN: Account Menu -->
            <div class="intro-x dropdown w-8 h-8">
                <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110"
                     role="button"
                     aria-expanded="false"
                     data-tw-toggle="dropdown">
                    <img :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                </div>
                <div class="dropdown-menu w-56">
                    <ul class="dropdown-content bg-primary/70 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                        <li class="p-2">
                            <div class="font-medium">
                                {{ $page.props.user.name }}
                            </div>
                            <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">
                                {{ $page.props.user.guid ? 'Usuario Active Directory' : 'Usuario Local' }}
                            </div>
                        </li>

                        <li><hr class="dropdown-divider border-white/[0.08]" /></li>

                        <li>
                            <Link :href="route('profile.show')" class="dropdown-item hover:bg-white/5">
                                <UserIcon class="w-4 h-4 mr-2" />
                                Perfil
                            </Link>
                        </li>

                        <li>
                            <Link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures" class="dropdown-item hover:bg-white/5">
                                <LockIcon class="w-4 h-4 mr-2" /> Api Tokens
                            </Link>
                        </li>

                        <li><hr class="dropdown-divider border-white/[0.08]" /></li>

                        <li>
                            <form @submit.prevent="logout">
                                <button type="submit" class="dropdown-item hover:bg-white/5 w-full">
                                    <ToggleRightIcon class="w-4 h-4 mr-2" />
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- END: Account Menu -->
        </div>
        <!-- END: Top Bar -->
    </div>
</template>

<script lang="jsx">
    import {defineComponent} from "vue";
    import {Link, router} from '@inertiajs/vue3';
    import { Inertia } from '@inertiajs/inertia'

    export default defineComponent({
        components: {
            Link
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },
            logout() {
                axios.post(route('logout')).then(response => {
                    router.get(route('pre-login'))
                })
            },
        },
    })
</script>

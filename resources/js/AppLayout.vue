<template>
    <div>
        <jet-banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <jet-application-mark class="block h-9 w-auto" />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <ul class="sm:flex">
                                    <NavLink v-for="nav in navigations" :nav="nav" :active=checkActive(nav) level="0">
                                        <span v-if="nav.name">
                                            <span v-if="typeof nav.name === 'object'">
                                                <span v-if="nav.name[locale.code]">
                                                    {{ nav.name[locale.code] }}
                                                </span>
                                                <span v-else>
                                                    [no translation] <span v-if="locale.country" :class="'fi fi-'+locale.country.code"></span>
                                                </span>
                                            </span>
                                            <span v-else>
                                                {{ nav.name }}
                                            </span>
                                        </span>
                                    </NavLink>
                                </ul>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Cache Dropdown -->
                            <div class="ml-3 relative" v-if="cache.hasOwnProperty('app') && helpers.stringToBoolean(cache.app)">
                                <jet-dropdown align="right" width="auto">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                <span>Cache</span>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Cache Management -->
                                        <form v-if="cache.hasOwnProperty('app') && helpers.stringToBoolean(cache.app)" @submit.prevent="cacheProcess('app')">
                                            <jet-dropdown-link as="button">
                                                <div class="flex items-center">
                                                    <span>Clear App's</span>
                                                </div>
                                            </jet-dropdown-link>
                                        </form>
                                        <form v-if="cache.hasOwnProperty('laravel') && helpers.stringToBoolean(cache.laravel)" @submit.prevent="cacheProcess('laravel')">
                                            <jet-dropdown-link as="button">
                                                <div class="flex items-center">
                                                    <span>Clear Laravel's</span>
                                                </div>
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>
                            <!-- Languages Dropdown -->
                            <div class="ml-3 relative">
                                <jet-dropdown align="right" width="auto">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                <span v-if="locale.country" :class="'fi fi-'+locale.country.code"></span>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Languages Management -->
                                        <template v-for="language in languages">
                                            <template v-if="language.code != locale.code" :key="language.code">
                                                <form @submit.prevent="updateLocale(language.code)">
                                                    <jet-dropdown-link as="button">
                                                        <div class="flex items-center">
                                                            <span v-if="language.country" :class="'fi fi-'+language.country.code"></span>
                                                        </div>
                                                    </jet-dropdown-link>
                                                </form>
                                            </template>
                                        </template>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Teams Dropdown -->
                            <div class="ml-3 relative" v-if="$page.props.jetstream.hasTeamFeatures && teamsEnabled">
                                <jet-dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <jet-dropdown-link :href="route('teams.show', $page.props.user.current_team)">
                                                    Team Settings
                                                </jet-dropdown-link>

                                                <jet-dropdown-link :href="route('teams.create')" v-if="$page.props.jetstream.canCreateTeams">
                                                    Create New Team
                                                </jet-dropdown-link>

                                                <div class="border-t border-gray-100"></div>

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <jet-dropdown-link as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id === $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <jet-dropdown-link :href="route('profile.show')">
                                            Profile
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                            API Access
                                            <span v-if="!apiData.access || !apiData.enabled" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-pink-600 bg-pink-200 uppercase last:mr-0 mr-1">
                                                Unauthorized
                                            </span>
                                            <span v-else class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-green-600 bg-green-200 uppercase last:mr-0 mr-1">
                                                Authorized
                                            </span>
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                Log Out
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <jet-responsive-nav-link v-for="nav in navigations" :href="getHref(nav)" :active="checkActive(nav)">
                            <span v-if="nav.name">
                                <span v-if="typeof nav.name === 'object'">
                                    <span v-if="nav.name[locale.code]">
                                        {{ nav.name[locale.code] }}
                                    </span>
                                    <span v-else>
                                        [no translation] <span v-if="locale.country" :class="'fi fi-'+locale.country.code"></span>
                                    </span>
                                </span>
                                <span v-else>
                                    {{ nav.name }}
                                </span>
                            </span>
                        </jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                API Access
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-pink-600 bg-pink-200 uppercase last:mr-0 mr-1" v-if="!apiData.access || !apiData.enabled">
                                    Unauthorized
                                </span>
                            </jet-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button">
                                    Log Out
                                </jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures && teamsEnabled">
                                <div class="border-t border-gray-200"></div>

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <jet-responsive-nav-link :href="route('teams.show', $page.props.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </jet-responsive-nav-link>

                                <jet-responsive-nav-link :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </jet-responsive-nav-link>

                                <div class="border-t border-gray-200"></div>

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <jet-responsive-nav-link as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id === $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </jet-responsive-nav-link>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import NavLink from '@/Pages/Projectbuilder/Navigations/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import { Helpers } from "Pub/js/Projectbuilder/Helpers/helpers"
    import {computed} from "vue"
    import {usePage, Link} from "@inertiajs/inertia-vue3"

    export default {
        components: {
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            NavLink,
            Link,
        },

        data() {
            return {
                showingNavigationDropdown: false,
                helpers: Helpers,
            }
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
                this.$inertia.post(route('logout'));
            },

            getHref(nav) {
                let href;
                switch(nav.type) {
                    case "route":
                        href = route(nav.destiny)
                        break
                    case "path":
                        href = window.location.origin + "/" + nav.destiny
                        break
                    default:
                        href = nav.destiny
                        break
                }
                return href
            },

            checkActive(nav) {
                let href = this.getHref(nav)
                let current = Helpers.refineURL(window.location.href)
                return (href === current)
            },

            updateLocale(locale) {
                this.$inertia.post(route('locale'), {
                    'locale': locale
                }, {
                    preserveState: false
                })
            },

            cacheProcess(type) {
                this.$inertia.post(route(type == 'laravel' ? 'clear-laravel-cache' : 'clear-cache'), {
                    preserveState: false
                })
            },
        },

        computed: {
            /* */
        },

        setup () {

            let navigations = computed(() => usePage().props.value.shared.navigations.firstlevel)
            let languages = computed(() => usePage().props.value.shared.languages)
            let apiData = computed(() => usePage().props.value.shared.api_data)
            const locale = computed(() => usePage().props.value.locale)
            const cache = computed(() => usePage().props.value.shared.cache)
            const teamsEnabled = computed(() => usePage().props.value.teams)

            return { navigations, apiData, locale, teamsEnabled, languages, cache }
        }
    }
</script>

<style scoped>
@import "/public/css/flag-icons.css";
</style>

<template>
    <li :class="liclasses">
        <Link :href="getHref" :class="aclasses">
            <slot></slot>
        </Link>
        <ul v-if="hasDescendants" :class="ulclasses">
            <NavLink v-for="subnav in nav.descendants" :nav="subnav" :active=false :level="addLevel">
                <span v-if="subnav.name">
                    <span v-if="typeof subnav.name === 'object'">
                        <span v-if="subnav.name[locale.code]">
                            {{ subnav.name[locale.code] }}
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
    </li>
</template>

<script>
import {Link, usePage} from "@inertiajs/inertia-vue3";
import {computed} from "vue";

export default {
    components: {
        Link,
    },

    props: {
        nav: Object,
        level: String,
        active: Boolean,
    },

    methods: {
        /* */
    },

    computed: {
        liclasses() {
            let classes = 'h-full inline-block relative text-gray-500 hover:text-gray-700 focus:text-gray-700 hover:bg-gray-100 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none active:text-gray-900'
            classes += this.isActive
            if (this.hasDescendants) {
                classes += ' dropdown'
            }
            if (this.isRoot) {
                classes += ' mr-4 pb-navigation-root'
            } else {
                classes += ' bg-white shadow-lg pb-navigation-child'
            }
            if (!this.isOdd) {
                classes += ' w-full'
            }
            return classes
        },

        isActive() {
            if (this.active) {
                return ' focus:border-indigo-700'
            } else {
                return ' border-transparent focus:border-gray-300'
            }
        },

        aclasses() {
            return 'items-center h-full sm:flex px-3 border-b-2 hover:ring-0 active:border-indigo-400 hover:border-gray-400 active:border-indigo-700'
        },

        ulclasses() {
            let classes = 'dropdown-menu h-full'
            if (this.isOdd) {
                classes += ' absolute inline-flex hidden'
            }
            return classes
        },

        isRoot() {
            return parseInt(this.level) === 0;

        },

        isOdd() {
            return parseInt(this.level) % 2 === 0;

        },

        getHref() {
            let href;
            switch(this.nav.type) {
                case "route":
                    href = route(this.nav.destiny)
                    break
                case "path":
                    href = window.location.origin + "/" + this.nav.destiny
                    break
                default:
                    href = this.nav.destiny
                    break
            }
            return href
        },

        hasDescendants() {
            return Object.keys(this.nav.descendants).length > 0;

        },

        addLevel() {
            return (parseInt(this.level) + 1).toString()
        },
    },

    setup () {

        const locale = computed(() => usePage().props.value.locale)

        return { locale }
    }
}
</script>

<style scoped>
@import "/public/css/flag-icons.css";
</style>

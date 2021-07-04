<template>
    <li :class="liclasses">
        <inertia-link :href="getHref" :class="aclasses">
            <slot></slot>
        </inertia-link>
        <ul v-if="hasDescendants" :class="ulclasses">
            <NavLink v-for="subnav in nav.descendants" :nav="subnav" active="false" :level="addLevel">
                {{ subnav.name }}
            </NavLink>
        </ul>
    </li>
</template>

<script>
export default {
    props: {
        nav: Object,
        level: Number
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
            let classes = 'items-center h-full sm:flex px-3 border-b-2 hover:ring-0 active:border-indigo-400 hover:border-gray-400 active:border-indigo-700'
            return classes
        },

        ulclasses() {
            let classes = 'dropdown-menu h-full'
            if (this.isOdd) {
                classes += ' absolute inline-flex hidden'
            }
            return classes
        },

        isRoot() {
            if (this.level == 0) {
                return true
            }
            return false
        },

        isOdd() {
            if (this.level % 2 == 0) {
                return true
            }
            return false
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
            console.log(this.nav.descendants)
            if (Object.keys(this.nav.descendants).length > 0) {
                return true
            }
            return false
        },

        addLevel() {
            return (this.level + 1)
        },
    },
}
</script>

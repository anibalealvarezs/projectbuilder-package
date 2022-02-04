<template>
    <th v-if="(allowed.update || allowed.delete) || (index !== 'actions')" scope="col"
        :class="buildThClasses()"
    >
        <PbDropdownLink
            v-if="field.hasOwnProperty('orderable') && helpers.stringToBoolean(field.orderable)"
            :href="buildOrderedRoute(field.key, (orderby.field === field.key) ? (orderby.order == 'asc' ? 'desc' : 'asc') : 'asc')"
            :cl="'block text-sm leading-9 text-gray-700 transition w-full h-full'"
        >
            {{ field.name }}
        </PbDropdownLink>
        <span v-else class="text-gray-800">{{ field.name }}</span>
    </th>
</template>

<script>
import { Helpers } from "Pub/js/Projectbuilder/projectbuilder"
import PbDropdownLink from "@/Pages/Projectbuilder/PbDropdownLink"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "Th",
    props: {
        field: Object,
        index: String,
        allowed: Object,
        pagination: Object,
        model: String,
    },
    components: {
        PbDropdownLink,
    },
    data() {
        return {
            helpers: Helpers,
        }
    },
    methods: {
        buildThClasses() {
            let clase = "border"
            clase += this.isCentered()
            clase += " " + this.field.style.width
            clase += (this.field.hasOwnProperty('orderable') && Helpers.stringToBoolean(this.field.orderable)) ? '  hover:bg-gray-300 p-0 m-0' : ' px-4 py-2'
            return clase
        },
        isCentered() {
            let ret = "";
            if (this.field.style.centered) {
                ret += " text-center"
            }
            return ret
        },
        getInertiaParams(field, order) {
            return [this.pagination.page ?? 1, this.pagination.per_page ?? 10, 'order', field, order]
        },
        buildOrderedRoute(field, order) {
            return Helpers.buildRoute(this.model + '.index.paginated', this.getInertiaParams(field, order))
        },
    },
    setup() {
        const orderby = computed(() => usePage().props.value.shared.orderby)
        return { orderby }
    }
}
</script>

<style scoped>

</style>

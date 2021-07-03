<template>
    <th v-if="(allowed.update || allowed.delete) || (index != 'actions')" scope="col"
        :class="buildThClasses()"
    >
        <span class="text-gray-800">{{ field.name }}</span>
    </th>
</template>

<script>
export default {
    name: "Th",
    props: {
        field: Object,
        index: String,
        allowed: Array,
    },
    methods: {
        buildThClasses() {
            let clase = "border px-4 py-2"
            clase += this.isCentered()
            clase += " " + this.field.style.width
            return clase
        },
        isCentered() {
            let ret = "";
            if (this.field.style.centered) {
                ret += " text-center"
            }
            return ret
        }
    }
}
</script>

<style scoped>

</style>

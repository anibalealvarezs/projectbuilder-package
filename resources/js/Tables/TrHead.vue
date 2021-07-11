<template>
    <tr class="bg-gray-200 border-4">
        <Th v-for="(field, index) in fields"
            :key="index"
            :index="index"
            :field="field"
            :allowed="allowed" />
    </tr>
</template>

<script>
import Th from "./Th"

export default {
    name: "TrHead",
    props: {
        fields: Object,
        allowed: Object,
    },
    components: {
        Th
    }
}
</script>

<style scoped>

</style>

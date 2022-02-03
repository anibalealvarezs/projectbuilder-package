<template>
    <tr
        v-if="['both', 'bottom'].includes(hlocation)"
        class="bg-gray-200 border-4"
    >
        <Th v-for="(field, index) in fields"
            :key="index"
            :index="index"
            :field="field"
            :allowed="allowed" />
    </tr>
    <tr class="bg-gray-200 border-4">
        <td colspan="100">
            <Pagination
                v-if="['both', 'bottom'].includes(plocation)"
                :pagination="pagination"
                :model="model"
            />
        </td>
    </tr>
</template>

<script>
import Th from "./Th"
import Pagination from "./Pagination";

export default {
    name: "TrFooter",
    props: {
        fields: Object,
        allowed: Object,
        pagination: Object,
        model: String,
        plocation: String,
        hlocation: String,
    },
    components: {
        Th,
        Pagination,
    }
}
</script>

<style scoped>

</style>

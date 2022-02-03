<template>
    <tr class="bg-gray-200 border-4">
        <td colspan="100">
            <Pagination
                v-if="['both', 'top'].includes(plocation)"
                :pagination="pagination"
                :model="model"
            />
        </td>
    </tr>
    <tr
        v-if="['both', 'top'].includes(hlocation)"
        class="bg-gray-200 border-4"
    >
        <Th v-for="(field, index) in fields"
            :key="index"
            :index="index"
            :field="field"
            :allowed="allowed" />
    </tr>
</template>

<script>
import Th from "./Th"
import Pagination from "./Pagination";

export default {
    name: "TrHead",
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

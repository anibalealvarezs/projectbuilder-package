<template>
    <tr class="bg-gray-200 border-4">
        <td colspan="100">
            <Pagination
                :pagination="pagination"
                :model="model"
            />
        </td>
    </tr>
</template>

<script>
import Pagination from "@/Pages/Projectbuilder/Tables/Pagination";

export default {
    name: "TrFooter",
    props: {
        pagination: Object,
        model: String,
    },
    components: {
        Pagination,
    }
}
</script>

<style scoped>

</style>

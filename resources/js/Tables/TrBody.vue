<template>
    <tr class="bg-white border-4 border-gray-200">
        <Td v-for="(field, index) in fields"
            :item="item"
            :field="field"
            :index="index"
            :hiddenid="hiddenid"
            @clicked-edit-item="onItemClicked"
        />
    </tr>
</template>

<script>
import Td from "./Td"

export default {
    name: "TrBody",
    components: {
        Td
    },
    props: {
        item: Object,
        fields: Object,
        hiddenid: String
    },
    methods: {
        onItemClicked(value) {
            this.$emit('clicked-edit-item', value);
        }
    }
}
</script>

<style scoped>

</style>

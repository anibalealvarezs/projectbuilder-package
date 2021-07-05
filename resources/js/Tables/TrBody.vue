<template>
    <tr :class="getClasses">
        <Td v-for="(field, index) in fields"
            :item="item"
            :field="field"
            :index="index"
            :hiddenid="hiddenid"
            :allowed="allowed"
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
        hiddenid: String,
        allowed: Array,
        draggable: Boolean
    },
    data() {
        return {
            classes: "bg-white border-4 border-gray-200"
        }
    },
    methods: {
        onItemClicked(value) {
            this.$emit('clicked-edit-item', value);
        }
    },
    computed: {
        getClasses() {
            return this.classes + (this.draggable ? " draggable" : "")
        }
    },
}
</script>

<style scoped>

</style>

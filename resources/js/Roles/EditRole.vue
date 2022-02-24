<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Role: {{ pbrole.name }}
            </h2>
        </template>

        <Edit :element="pbrole" title="Roles" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditRole",
    props: {
        pbrole: Object,
    },
}
</script>

<style scoped>

</style>

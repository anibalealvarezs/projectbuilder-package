<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Config: {{ config.name }}
            </h2>
        </template>

        <Edit :element="config" title="Configs" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        config: Object,
    },
}
</script>

<style scoped>

</style>

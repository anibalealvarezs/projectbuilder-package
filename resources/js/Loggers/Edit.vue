<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Logger: {{ logger.name }}
            </h2>
        </template>

        <Edit :element="logger" title="Loggers" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        logger: Object,
    },
}
</script>

<style scoped>

</style>

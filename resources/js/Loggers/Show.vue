<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Logger: {{ logger.name }}
            </h2>
        </template>

        <Show :element="logger" title="Loggers" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        logger: Object,
    },
}
</script>

<style scoped>

</style>

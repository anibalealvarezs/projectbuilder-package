<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Config: {{ config.name }}
            </h2>
        </template>

        <Show :element="config" title="Configs" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        config: Object,
    },
}
</script>

<style scoped>

</style>

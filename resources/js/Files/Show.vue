<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                File: {{ file.name }}
            </h2>
        </template>

        <Show :element="file" title="Files" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        file: Object,
    },
}
</script>

<style scoped>

</style>

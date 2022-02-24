<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Logger: {{ pblogger.name }}
            </h2>
        </template>

        <Show :element="pblogger" title="Loggers" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowLogger",
    props: {
        pblogger: Object,
    },
}
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Config: {{ pbconfig.name }}
            </h2>
        </template>

        <Show :element="pbconfig" title="Configs" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowConfig",
    props: {
        pbconfig: Object,
    },
}
</script>

<style scoped>

</style>

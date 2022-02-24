<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Navigation: {{ pbnavigation.name }}
            </h2>
        </template>

        <Show :element="pbnavigation" title="Navigations" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowNavigation",
    props: {
        pbnavigation: Object,
    },
}
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Navigation: {{ navigation.name }}
            </h2>
        </template>

        <Show :element="navigation" title="Navigations" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        navigation: Object,
    },
}
</script>

<style scoped>

</style>

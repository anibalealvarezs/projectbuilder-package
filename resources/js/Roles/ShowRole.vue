<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role: {{ pbrole.name }}
            </h2>
        </template>

        <Show :element="pbrole" title="Roles" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowRole",
    props: {
        pbrole: Object,
    },
}
</script>

<style scoped>

</style>

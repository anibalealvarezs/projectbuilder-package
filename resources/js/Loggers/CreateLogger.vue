<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Logger
            </h2>
        </template>

        <Create title="Logger" />
    </AppLayout>
</template>

<script>
import create from "Pub/js/Projectbuilder/Model/create"

export default {
    extends: create,
    name: "CreateLogger",
}
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Config
            </h2>
        </template>

        <Create title="Configs" />
    </AppLayout>
</template>

<script>
import create from "Pub/js/Projectbuilder/Model/create"

export default {
    extends: create,
}
</script>

<style scoped>

</style>

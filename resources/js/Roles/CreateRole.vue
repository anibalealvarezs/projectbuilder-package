<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Role
            </h2>
        </template>

        <Create title="Role" />
    </AppLayout>
</template>

<script>
import create from "Pub/js/Projectbuilder/Model/create"

export default {
    extends: create,
    name: "CreateRole",
}
</script>

<style scoped>

</style>

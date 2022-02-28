<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role: {{ role.name }}
            </h2>
        </template>

        <Show :element="role" title="Roles" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        role: Object,
    },
}
</script>

<style scoped>

</style>

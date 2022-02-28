<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User: {{ user.name }}
            </h2>
        </template>

        <Show :element="user" title="Users" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        user: Object,
    },
}
</script>

<style scoped>

</style>

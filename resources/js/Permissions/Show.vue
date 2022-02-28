<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permission: {{ permission.name }}
            </h2>
        </template>

        <Show :element="permission" title="Permissions" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    props: {
        permission: Object,
    },
}
</script>

<style scoped>

</style>

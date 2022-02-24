<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permission: {{ pbpermission.name }}
            </h2>
        </template>

        <Show :element="pbpermission" title="Permissions" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowPermission",
    props: {
        pbpermission: Object,
    },
}
</script>

<style scoped>

</style>

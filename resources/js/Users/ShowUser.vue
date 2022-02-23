<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User: {{ pbuser.name }}
            </h2>
        </template>

        <Show :element="pbuser" title="User" />
    </AppLayout>
</template>

<script>
import show from "Pub/js/Projectbuilder/Model/show"

export default {
    extends: show,
    name: "ShowUser",
    props: {
        pbuser: Object,
    },
}
</script>

<style scoped>

</style>

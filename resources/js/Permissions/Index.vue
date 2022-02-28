<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permissions
            </h2>
        </template>

        <Index :elements="permissions" title="Permissions" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        permissions: Object,
    },
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles
            </h2>
        </template>

        <Index :elements="roles" title="Roles" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        roles: Object,
    },
}
</script>

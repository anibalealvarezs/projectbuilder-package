<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <Index :elements="users" title="Users" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        users: Object,
    },
}
</script>

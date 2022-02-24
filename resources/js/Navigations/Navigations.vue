<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Navigations
            </h2>
        </template>

        <Index :elements="pbnavigations" title="Navigations" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    name: "Navigations",
    props: {
        pbnavigations: Object,
    },
}
</script>

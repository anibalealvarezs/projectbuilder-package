<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configs
            </h2>
        </template>

        <Index :elements="configs" title="Configs" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        configs: Object,
    },
}
</script>

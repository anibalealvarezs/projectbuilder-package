<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loggers
            </h2>
        </template>

        <Index :elements="loggers" title="Loggers" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        loggers: Object,
    },
}
</script>

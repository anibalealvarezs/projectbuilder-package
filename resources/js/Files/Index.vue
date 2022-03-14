<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Files
            </h2>
        </template>

        <Index :elements="files" title="Files" />
    </AppLayout>
</template>

<script>
import index from "Pub/js/Projectbuilder/Model/index"

export default {
    extends: index,
    props: {
        files: Object,
    },
}
</script>

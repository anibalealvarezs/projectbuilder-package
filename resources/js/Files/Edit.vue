<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit File: {{ file.name }}
            </h2>
        </template>

        <Edit :element="file" title="Files" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        file: Object,
    },
}
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Logger: {{ pblogger.name }}
            </h2>
        </template>

        <Edit :element="pblogger" title="Loggers" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditLogger",
    props: {
        pblogger: Object,
    },
}
</script>

<style scoped>

</style>

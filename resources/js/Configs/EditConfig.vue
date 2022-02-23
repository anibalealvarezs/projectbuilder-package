<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Config: {{ pbconfig.name }}
            </h2>
        </template>

        <Edit :element="pbconfig" title="Config" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditConfig",
    props: {
        pbconfig: Object,
    },
}
</script>

<style scoped>

</style>

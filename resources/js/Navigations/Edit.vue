<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Navigation: {{ navigation.name }}
            </h2>
        </template>

        <Edit :element="navigation" title="Navigations" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        navigation: Object,
    },
}
</script>

<style scoped>

</style>

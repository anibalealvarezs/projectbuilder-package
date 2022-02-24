<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Navigation: {{ pbnavigation.name }}
            </h2>
        </template>

        <Edit :element="pbnavigation" title="Navigations" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditNavigation",
    props: {
        pbnavigation: Object,
    },
}
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Permission: {{ permission.name }}
            </h2>
        </template>

        <Edit :element="permission" title="Permissions" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        permission: Object,
    },
}
</script>

<style scoped>

</style>

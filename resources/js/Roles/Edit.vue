<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Role: {{ role.name }}
            </h2>
        </template>

        <Edit :element="role" title="Roles" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        role: Object,
    },
}
</script>

<style scoped>

</style>

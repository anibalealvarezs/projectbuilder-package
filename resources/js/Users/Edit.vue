<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ user.name }}
            </h2>
        </template>

        <Edit :element="user" title="Users" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    props: {
        user: Object,
    },
}
</script>

<style scoped>

</style>

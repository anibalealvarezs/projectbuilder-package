<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ pbuser.name }}
            </h2>
        </template>

        <Edit :element="pbuser" title="Users" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditUser",
    props: {
        pbuser: Object,
    },
}
</script>

<style scoped>

</style>

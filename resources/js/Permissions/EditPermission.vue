<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Permission: {{ pbpermission.name }}
            </h2>
        </template>

        <Edit :element="pbpermission" title="Permissions" />
    </AppLayout>
</template>

<script>
import edit from "Pub/js/Projectbuilder/Model/edit"

export default {
    extends: edit,
    name: "EditPermission",
    props: {
        pbpermission: Object,
    },
}
</script>

<style scoped>

</style>

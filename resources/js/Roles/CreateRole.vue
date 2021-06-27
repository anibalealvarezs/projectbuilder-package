<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Role
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <RoleForm :data="{}" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"

    export default {
        name: "CreateRole",
        props: {
            page: Object
        },
        components: {
            RoleForm,
            AppLayout,
            Main
        }
    }
</script>

<style scoped>

</style>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Permission
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <PermissionForm :data="{}" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import PermissionForm from "@/Pages/Projectbuilder/Permissions/PermissionForm"

    export default {
        name: "CreatePermission",
        props: {
            page: Object
        },
        components: {
            PermissionForm,
            AppLayout,
            Main
        }
    }
</script>

<style scoped>

</style>

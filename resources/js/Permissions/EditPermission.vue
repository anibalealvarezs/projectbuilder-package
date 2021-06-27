<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Permission: {{ pbpermission.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <PermissionForm :data="setItem" :currentroles="currentroles" />
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
        name: "ShowPermission",
        props: {
            pbpermission: Object,
            page: Object,
            currentroles: Object
        },
        components: {
            PermissionForm,
            AppLayout,
            Main
        },
        computed: {
            setItem() {
                this.pbpermission.item = this.pbpermission.id
                return this.pbpermission
            },
        }
    }
</script>

<style scoped>

</style>

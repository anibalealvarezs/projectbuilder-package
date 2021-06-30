<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Role: {{ pbrole.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbrole }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <RoleForm :data="setItem" />
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
        name: "EditRole",
        props: {
            pbrole: Object,
            page: Object,
        },
        components: {
            RoleForm,
            AppLayout,
            Main
        },
        computed: {
            setItem() {
                this.pbrole.item = this.pbrole.id
                return this.pbrole
            },
        }
    }
</script>

<style scoped>

</style>

<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create User
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <UserForm :data="{}" />
                </div>
            </slot>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import UserForm from "@/Pages/Projectbuilder/Users/UserForm"

    export default {
        name: "CreateUser",
        props: {
            page: Object
        },
        components: {
            UserForm,
            AppLayout,
            Main
        }
    }
</script>

<style scoped>

</style>

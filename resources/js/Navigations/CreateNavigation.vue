<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Navigation
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <NavigationForm :data="{}" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"

    export default {
        name: "CreateNavigation",
        props: {
            page: Object
        },
        components: {
            NavigationForm,
            AppLayout,
            Main
        }
    }
</script>

<style scoped>

</style>

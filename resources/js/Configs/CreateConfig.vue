<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Config
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <ConfigForm :data="{}" />
                </div>
            </slot>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import ConfigForm from "@/Pages/Projectbuilder/Configs/ConfigForm"

    export default {
        name: "CreateConfig",
        props: {
            page: Object
        },
        components: {
            ConfigForm,
            AppLayout,
            Main
        }
    }
</script>

<style scoped>

</style>

<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <h1>Dashboard</h1>
                    <!-- Content -->
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <UserForm :data="{}" />
            </div>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import Main from "@/Pages/Projectbuilder/Main";

    export default {
        components: {
            AppLayout,
            Welcome,
            Main,
        },
    }
</script>

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
        </Main>
    </app-layout>
</template>

<script>
    import Welcome from '@/Jetstream/Welcome'
    import PbIndex from "Pub/js/Projectbuilder/pbindex"

    export default{
        extends: PbIndex,
        components: {
            Welcome,
        },
    }
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Navigation: {{ pbnavigation.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <h2>{{ pbnavigation.id }}. {{ pbnavigation.name }}</h2>
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import PbShow from "Pub/js/Projectbuilder/pbshow"

    export default {
        extends: PbShow,
        name: "ShowNavigation",
        props: {
            pbnavigation: Object,
        },
    }
</script>

<style scoped>

</style>

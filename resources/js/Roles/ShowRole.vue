<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role: {{ pbrole.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <h2>{{ pbrole.id }}. {{ pbrole.name }}</h2>
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import PbShow from "Pub/js/Projectbuilder/pbshow"

    export default {
        extends: PbShow,
        name: "ShowRole",
        props: {
            pbrole: Object,
        },
    }
</script>

<style scoped>

</style>

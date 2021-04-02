<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configs
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <inertia-link :href="route('configs.create')">
                        <Button type="button">
                            <slot>Create Config</slot>
                        </Button>
                    </inertia-link>
                    <TableConfigs :configs="pbconfigs"/>
                </div>
            </slot>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import TableConfigs from "@/Pages/Projectbuilder/Configs/TableConfigs"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"

    export default {
        props: {
            pbconfigs: Object,
            data: Object,
            errors: Object,
            page: Object
        },
        components: {
            Button,
            AppLayout,
            TableConfigs,
            Main
        }
    }
</script>

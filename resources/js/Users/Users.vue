<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <inertia-link :href="route('users.create')">
                        <Button type="button">
                            <slot>Create User</slot>
                        </Button>
                    </inertia-link>
                    <TableUsers :users="pbusers"/>
                </div>
            </slot>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import TableUsers from "@/Pages/Projectbuilder/Users/TableUsers"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"

    export default {
        props: {
            pbusers: Object,
            data: Object,
            errors: Object,
            page: Object
        },
        components: {
            Button,
            AppLayout,
            TableUsers,
            Main
        }
    }
</script>

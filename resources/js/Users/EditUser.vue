<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ pbuser.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbuser }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <UserForm :data="setItem" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import Main from "@/Pages/Projectbuilder/Main"
    import UserForm from "@/Pages/Projectbuilder/Users/UserForm"
    /* import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder"; */

    export default {
        name: "EditUser",
        props: {
            pbuser: Object,
            page: Object,
        },
        components: {
            UserForm,
            AppLayout,
            Main
        },
        computed: {
            setItem() {
                this.pbuser.item = this.pbuser.id
                return this.pbuser
            },
        }
    }
</script>

<style scoped>

</style>

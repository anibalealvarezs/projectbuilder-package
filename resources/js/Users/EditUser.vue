<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ pbuser.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <UserForm :data="setItem" :defaults="defaults" :required="required" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import UserForm from "@/Pages/Projectbuilder/Users/UserForm"
    import {computed} from "vue"
    import {usePage} from "@inertiajs/inertia-vue3"
    import PbEdit from "Pub/js/Projectbuilder/pbedit"

    export default {
        extends: PbEdit,
        name: "EditUser",
        props: {
            pbuser: Object,
        },
        components: {
            UserForm,
        },
        computed: {
            setItem() {
                this.pbuser.item = this.pbuser.id
                return this.pbuser
            },
        },
        setup () {
            const defaults = computed(() => usePage().props.value.shared.defaults)
            const required = computed(() => usePage().props.value.shared.required)

            return { defaults, required }
        }
    }
</script>

<style scoped>

</style>

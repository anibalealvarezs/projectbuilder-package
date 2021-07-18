<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Role
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <RoleForm :data="{}" :defaults="defaults" :required="required" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"
    import {computed} from "vue"
    import {usePage} from "@inertiajs/inertia-vue3"
    import PbCreate from "Pub/js/Projectbuilder/pbcreate"

    export default {
        extends: PbCreate,
        name: "CreateRole",
        components: {
            RoleForm,
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

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Permission: {{ pbpermission.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbpermission }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <PermissionForm :data="setItem" :defaults="defaults" :required="required" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import PermissionForm from "@/Pages/Projectbuilder/Permissions/PermissionForm"
    import {computed} from "vue"
    import {usePage} from "@inertiajs/inertia-vue3"
    import PbEdit from "Pub/js/Projectbuilder/pbedit"

    export default {
        extends: PbEdit,
        name: "EditPermission",
        props: {
            pbpermission: Object,
        },
        components: {
            PermissionForm,
        },
        computed: {
            setItem() {
                this.pbpermission.item = this.pbpermission.id
                return this.pbpermission
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

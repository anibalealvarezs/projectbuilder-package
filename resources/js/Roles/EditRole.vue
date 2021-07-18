<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Role: {{ pbrole.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbrole }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <RoleForm :data="setItem" :defaults="defaults" :required="required" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"
    import {computed} from "vue"
    import {usePage} from "@inertiajs/inertia-vue3"
    import PbEdit from "Pub/js/Projectbuilder/pbedit"

    export default {
        extends: PbEdit,
        name: "EditRole",
        props: {
            pbrole: Object,
        },
        components: {
            RoleForm,
        },
        computed: {
            setItem() {
                this.pbrole.item = this.pbrole.id
                return this.pbrole
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

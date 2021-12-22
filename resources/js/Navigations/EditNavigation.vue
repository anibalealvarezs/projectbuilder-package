<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Navigation: {{ pbnavigation.name }}
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbnavigation }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <NavigationForm :data="setItem(pbnavigation)" :defaults="defaults" :required="required" />
                </div>
            </slot>
        </Main>
    </AppLayout>
</template>

<script>
    import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"
    import {computed} from "vue"
    import {usePage} from "@inertiajs/inertia-vue3"
    import PbEdit from "Pub/js/Projectbuilder/pbedit"

    export default {
        extends: PbEdit,
        name: "EditNavigation",
        props: {
            pbnavigation: Object,
        },
        components: {
            NavigationForm,
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

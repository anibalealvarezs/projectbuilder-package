<template>
    <Main>
        <slot>
            <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                <Form
                    :data="setItem(element)"
                    :defaults="defaults"
                    :required="required"
                    :title="title"
                />
            </div>
        </slot>
    </Main>
</template>

<script>
import {computed} from "vue"
import {usePage} from "@inertiajs/inertia-vue3"
import PbEdit from "Pub/js/Projectbuilder/pbedit"

export default {
    extends: PbEdit,
    setup () {
        const defaults = computed(() => usePage().props.value.shared.defaults)
        const required = computed(() => usePage().props.value.shared.required)

        return { defaults, required }
    }
}
</script>

<style scoped>

</style>

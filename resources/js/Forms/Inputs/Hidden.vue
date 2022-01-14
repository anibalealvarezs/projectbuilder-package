<template>
    <input
        :value="(typeof value === 'object') ? (value[locale.code] ? value[locale.code] : '[no translation] ['+locale.code+']') : value"
        :id="'grid-'+ keyel +'-' + keyid"
        type="hidden"
        :required="isRequired(keyel)"
        @input="emitHiddenValue"
    >
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbInput,
    name: "Hidden",
    emits: [
        "input"
    ],
    methods: {
        emitHiddenValue(el) {
            if (this.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('input', el.target.value)
        },
    },
    setup() {
        const locale = computed(() => usePage().props.value.locale)

        return {locale}
    }
}
</script>

<style scoped>

</style>

<template>
    <input
        :value="value"
        :id="'grid-'+ keyel +'-' + keyid"
        type="hidden"
        :required="isRequired(keyel)"
        @input="emitHiddenValue"
    >
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"

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
}
</script>

<style scoped>

</style>

<template>
    <input
        :value="value"
        :id="'grid-'+ keyel +'-' + keyid"
        :type="type"
        :placeholder="type==='password' ? '******************' : keyel"
        :autoComplete="type==='password' ? 'new-password' : (keyel === 'email' ? 'email' : 'off')"
        class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 focus:border-gray-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        readonly
        :required="isRequired(keyel)"
        @mouseover="disableReadonly"
        @focus="disableReadonly"
        @input="emitInputValue"
    >
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"

export default {
    extends: PbInput,
    name: "Text",
    emits: [
        "input"
    ],
    methods: {
        emitInputValue(el) {
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

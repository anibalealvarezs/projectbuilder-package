<template>
    <textarea
        :value="value"
        :id="'grid-'+ keyel +'-' + keyid"
        :placeholder="keyel"
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        readonly
        :required="isRequired(keyel)"
        @mouseover="disableReadonly"
        @focus="disableReadonly"
        @keyup="emitTextareaValue"
    >
    </textarea>
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"

export default {
    extends: PbInput,
    name: "Textarea",
    emits: [
        "textarea"
    ],
    methods: {
        emitTextareaValue(el) {
            if (this.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('textarea', el.target.value)
        },
    },
}
</script>

<style scoped>

</style>

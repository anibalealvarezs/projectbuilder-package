<template>
    <textarea
        :value="(value && typeof value === 'object') ? (value[locale.code] ? value[locale.code] : '') : value"
        :id="'grid-'+ keyel +'-' + keyid"
        :placeholder="(value && typeof value === 'object' ? (!value[locale.code] ? '[no translation] ['+locale.code+']' : '') : keyel)"
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        readonly
        :required="isRequired(keyel)"
        @mouseover="disableReadonly"
        @focus="disableReadonly"
        @keyup="emitInputValue"
    >
    </textarea>
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"

export default {
    extends: PbInput,
    name: "Textarea",
}
</script>

<style scoped>

</style>

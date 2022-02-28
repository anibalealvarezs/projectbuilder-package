<template>
    <input
        :value="(value && typeof value === 'object') ? (value[locale.code] ? value[locale.code] : '') : value"
        :id="'grid-'+ keyel +'-' + keyid"
        :type="type"
        :placeholder="(value && typeof value === 'object' ? (!value[locale.code] ? '[no translation] ['+locale.code+']' : '') : type==='password' ? '******************' : keyel)"
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
}
</script>

<style scoped>

</style>

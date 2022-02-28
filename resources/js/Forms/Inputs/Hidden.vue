<template>
    <input
        :value="(typeof value === 'object') ? (value[locale.code] ? value[locale.code] : '') : value"
        :id="'grid-'+ keyel +'-' + keyid"
        type="hidden"
        :required="isRequired(keyel)"
        @input="emitInputValue"
    >
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"

export default {
    extends: PbInput,
    name: "Hidden",
}
</script>

<style scoped>

</style>

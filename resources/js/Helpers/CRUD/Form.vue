<template>
    <form @submit.prevent="submit" class="w-full max-w-lg">
        <div class="flex flex-wrap -mx-3 mb-6">
            <div v-for="(field, key) in form" class="w-full px-3 mb-6 md:mb-0">
                <!-- {{ key }} -->
                <label v-if="formconfig[key].type !== 'hidden'" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                       :for="'grid-'+ key +'-' + keyid">
                    {{ key }}
                </label>
                <SimpleInput
                    :value="form[key]"
                    :keyel="key"
                    :keyid="keyid"
                    :required="required"
                    :url="form.hasOwnProperty('url') ? form.url : ''"
                    @input="updateValue($event, key)"
                />
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-2 items-center justify-between">
            <!-- submit -->
            <div class="w-full px-3">
                <Button type="submit" :disabled="form.processing">{{ buttontext }}</Button>
            </div>
        </div>
    </form>
</template>

<script>
import PbForm from "Pub/js/Projectbuilder/pbform"

export default {
    extends: PbForm,
}
</script>

<style scoped>

</style>

<template>
    <form @submit.prevent="submit" class="w-full max-w-lg">
        <div class="flex flex-wrap -mx-3 mb-6">
            <div v-for="(field, key) in form" class="w-full px-3 mb-6 md:mb-0">
                <!-- {{ key }} -->
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                       :for="'grid-'+ key +'-' + keyid">
                    {{ key }}
                </label>
                <Input
                    :value="form[key]"
                    :keyel="key"
                    :keyid="keyid"
                    :required="required"
                    @input="updateValue($event, key)"
                    @click="updateValue($event, key)"
                    @select="updateValue($event, key)"
                    @textarea="updateValue($event, key)"
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
import {reactive} from 'vue'
import PbForm from "Pub/js/Projectbuilder/pbform"

export default {
    extends: PbForm,
    name: "NavigationForm",
    setup (props) {
        const form = reactive({
            name: props.data.name,
            type: props.data.type,
            parent: props.data.parent,
            destiny: props.data.destiny,
            module: props.data.module,
            status: props.data.status,
            permission: props.data.permission_id,
        })
        const directory = 'navigations'

        return { form, directory }
    }
}
</script>

<style scoped>

</style>

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
import {Helpers} from "Pub/js/Projectbuilder/Helpers/helpers"
import {usePage} from "@inertiajs/inertia-vue3";
import {computed} from "vue";

export default {
    extends: PbForm,
    setup(props) {
        const formconfig = computed(() => usePage().props.value.shared.formconfig)
        const fields = usePage().props.value.shared.formconfig
        const inputs = {}
        for (const [index, field] of Object.entries(fields)) {
            let key = field.hasOwnProperty('data_field') ? field.data_field : index
            switch(field.type) {
                case 'text':
                case 'textarea':
                case 'hidden':
                    inputs[index] = props.data[key]
                    break;
                case 'select':
                    inputs[index] = props.data[key] ? props.data[key].id : (props.defaults.hasOwnProperty(key) ? props.defaults[key].id : 0)
                    break;
                case 'select-multiple':
                    inputs[index] = Helpers.getModelIdsList(props.data[key])
                    break;
                case 'password':
                    inputs[index] = ''
                    break;
                default:
                    break;
            }
        }
        const form = reactive(inputs)
        const directory = props.title.toLowerCase()

        return {form, directory, formconfig}
    }
}
</script>

<style scoped>

</style>

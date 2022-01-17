<template>
    <div v-if="['text', 'password'].includes(formconfig[keyel].type)">
        <Text
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            :type="formconfig[keyel].type"
            :required="required"
            @input="emitInputValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'select-multiple'">
        <Checkbox
            :value="value"
            :list="formconfig[keyel].list"
            :keyel="keyel"
            :keyid="keyid"
            @click="emitCheckboxValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'select'">
        <Select
            :value="value"
            :list="formconfig[keyel].list"
            :keyel="keyel"
            :keyid="keyid"
            :required="required"
            @select="emitSelectValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'textarea'">
        <Textarea
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            :required="required"
            @textarea="emitTextareaValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'hidden'">
        <Hidden
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            @hidden="emitHiddenValue"
        />
    </div>
</template>

<script>
import {computed} from "vue"
import {usePage} from "@inertiajs/inertia-vue3"
import Text from "@/Pages/Projectbuilder/Forms/Inputs/Text"
import Checkbox from "@/Pages/Projectbuilder/Forms/Inputs/Checkbox"
import Select from "@/Pages/Projectbuilder/Forms/Inputs/Select"
import Textarea from "@/Pages/Projectbuilder/Forms/Inputs/Textarea"
import Hidden from "@/Pages/Projectbuilder/Forms/Inputs/Hidden"

export default {
    name: "Input",
    props: {
        keyel: String,
        keyid: String,
        value: undefined,
        required: Object,
        mime: String,
        url: String,
    },
    components: {
        Hidden,
        Textarea,
        Select,
        Text,
        Checkbox,
    },
    emits: [
        'click',
        'input',
        'select',
        'textarea',
    ],
    methods: {
        emitCheckboxValue(values) {
            this.$emit('click', values)
        },
        emitInputValue(value) {
            this.$emit('input', value)
        },
        emitSelectValue(value) {
            this.$emit('select', value)
        },
        emitTextareaValue(value) {
            this.$emit('textarea', value)
        },
        emitHiddenValue(value) {
            this.$emit('input', value)
        },
    },
    setup(props) {
        const formconfig = computed(() => usePage().props.value.shared.formconfig)

        return { formconfig }
    }
}
</script>

<style scoped>

</style>

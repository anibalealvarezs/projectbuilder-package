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
            @input="emitInputValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'select'">
        <Select
            :value="value"
            :list="formconfig[keyel].list"
            :keyel="keyel"
            :keyid="keyid"
            :required="required"
            @input="emitInputValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'textarea'">
        <Textarea
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            :required="required"
            @input="emitInputValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'hidden'">
        <Hidden
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            @input="emitInputValue"
        />
    </div>
    <div v-if="formconfig[keyel].type === 'file'">
        <File
            :value="value"
            :keyel="keyel"
            :keyid="keyid"
            :url="url"
            :required="required"
            @input="emitInputValue"
        />
    </div>
</template>

<script>
import {usePage} from "@inertiajs/inertia-vue3"
import Text from "@/Pages/Projectbuilder/Forms/Inputs/Text"
import Checkbox from "@/Pages/Projectbuilder/Forms/Inputs/Checkbox"
import Select from "@/Pages/Projectbuilder/Forms/Inputs/Select"
import Textarea from "@/Pages/Projectbuilder/Forms/Inputs/Textarea"
import Hidden from "@/Pages/Projectbuilder/Forms/Inputs/Hidden"
import File from "@/Pages/Projectbuilder/Forms/Inputs/File"

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
        File,
    },
    data() {
        return {
            formconfig: usePage().props.value.shared.formconfig,
        }
    },
    emits: [
        'input',
    ],
    methods: {
        emitInputValue(value) {
            this.$emit('input', value)
        },
    },
}
</script>

<style scoped>

</style>

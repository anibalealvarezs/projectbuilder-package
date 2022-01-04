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
    <!-- <div v-if="formconfig[keyel].type === 'file'">
        <div v-if="value" class="mt-2" v-show="!filePreview">
            <img :src="getFileUrl" alt="form.alt" class="h-40 w-40 object-cover">
        </div>
        <input
            :id="'grid-'+ keyel +'-' + keyid"
            ref="file"
            type="file"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
            :required="isRequired('file')"
            @change="updateFilePreview"
            @input="emitFileValue"
        >
    </div> -->
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
    data() {
        return {
            /* filePreview: null,
            ignoreFile: (!!this.value), */
        }
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
        /* emitFileValue(el) {
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.files[0] + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('input', el.target.files[0])
        },
        updateFilePreview() {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.filePreview = e.target.result;
            };

            reader.readAsDataURL(this.$refs.file.files[0]);
        }, */
    },
    computed: {
        /* getFileUrl() {
            return (this.mime.includes('image/') ? this.url : '#')
        }, */
    },
    setup(props) {
        const formconfig = computed(() => usePage().props.value.shared.formconfig)
        /* const modules = computed(() => usePage().props.value.modules) */

        return {formconfig /*, modules */}
    }
}
</script>

<style scoped>

</style>

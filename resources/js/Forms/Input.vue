<template>
    <div v-if="formconfig[keyel].type === 'text'">
        <input
            :value="value"
            :id="'grid-'+ keyel +'-' + keyid"
            type="text"
            :placeholder="keyel"
            :autoComplete="keyel === 'email' ? 'email' : 'off'"
            class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
            readonly
            :required="isRequired(keyel)"
            @mouseover="disableReadonly"
            @focus="disableReadonly"
            @input="emitInputValue"
        >
    </div>
    <div v-if="formconfig[keyel].type === 'password'">
        <input
            :value="value"
            :id="'grid-'+ keyel +'-' + keyid"
            type="password"
            placeholder="******************"
            autoComplete="new-password"
            class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            readonly
            :required="isRequired(keyel)"
            @mouseover="disableReadonly"
            @focus="disableReadonly"
            @input="emitInputValue"
        >
    </div>
    <div v-if="formconfig[keyel].type === 'select-multiple'">
        <div class="text-left" :id="'grid-'+ keyel +'-' + keyid" v-for="(el, k) in formconfig[keyel].list">
            <input
                :value="formconfig[keyel].list[k].id"
                type="checkbox"
                :id="'checkbox-'+ keyel +'-' + keyid + '-' + formconfig[keyel].list[k].id"
                class="appearance-none mx-4 px-4 py-3 mb-1 rounded bg-gray-200 text-gray-700 border border-gray-200 focus:outline-none focus:bg-white focus:border-gray-500"
                :checked="isChecked(value, formconfig[keyel].list, k)"
                @click="emitCheckboxValue"
            >
            <span>{{ formconfig[keyel].list[k].hasOwnProperty('alias') ? formconfig[keyel].list[k].alias : formconfig[keyel].list[k]['name'] }}</span>
        </div>
    </div>
    <div v-if="formconfig[keyel].type === 'select'">
        <select
            :id="'grid-'+ keyel +'-' + keyid"
            class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            :required="isRequired(keyel)"
            @change="emitSelectValue"
        >
            <option
                v-for="(el, k) in formconfig[keyel].list"
                :value="formconfig[keyel].list[k].id"
                :selected="isSelected(value, formconfig[keyel].list, k)"
                :data-iso="formconfig[keyel].list[k].hasOwnProperty('code') ? formconfig[keyel].list[k].code : ''"
            >
                {{ formconfig[keyel].list[k].hasOwnProperty('alias') ? formconfig[keyel].list[k].alias : formconfig[keyel].list[k]['name'] }}
            </option>
        </select>
    </div>
    <div v-if="formconfig[keyel].type === 'textarea'">
        <textarea
            :value="value"
            :id="'grid-'+ keyel +'-' + keyid"
            :placeholder="keyel"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
            readonly
            :required="isRequired(keyel)"
            @mouseover="disableReadonly"
            @focus="disableReadonly"
            @change="emitTextareaValue"
        >
        </textarea>
    </div>
</template>

<script>
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "Input",
    props: {
        keyel: String,
        keyid: String,
        value: undefined,
        required: Object,
    },
    emits: [
        'click',
        'input',
        'select',
        'textarea',
    ],
    methods: {
        disableReadonly(event) {
            document.getElementById(event.target.id).readOnly = false
        },
        isRequired(key) {
            return this.required.includes(key)
        },
        isEmptyField(value) {
            return !value
        },
        emitCheckboxValue(el) {
            let values = []
            this.formconfig[this.keyel].list.forEach(el => {
                let element = document.getElementById('checkbox-'+ this.keyel +'-' + this.keyid + '-' + el.id)
                if (element.checked) {
                    values.push(el.id)
                }
            })
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Checkbox click activated" + "\n" +
                    "Component: Input" + "\n" +
                    "Input ID: " + el.target.value + "\n" +
                    "Values to emit: (down)"
                )
                console.log(values)
            }
            this.$emit('click', values)
        },
        emitInputValue(el) {
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('input', el.target.value)
        },
        emitSelectValue(el) {
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('select', el.target.value)
        },
        emitTextareaValue(el) {
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('textarea', el.target.value)
        },
        isChecked(value, list, k) {
            return value.includes(list[k].id)
        },
        isSelected(value, list, k) {
            return value === list[k].id
        },
    },
    computed: {
        readonly() {
            return this.data.hasOwnProperty('item')
        },
    },
    setup(props) {
        const formconfig = computed(() => usePage().props.value.shared.formconfig)

        return {formconfig}
    }
}
</script>

<style scoped>

</style>

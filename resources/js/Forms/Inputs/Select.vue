<template>
    <select
        v-model="selected"
        :id="'grid-'+ keyel +'-' + keyid"
        class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        :required="isRequired(keyel)"
        @change="emitSelectValue"
    >
        <option
            v-for="(el, k) in list"
            :value="list[k].id"
            :data-iso="list[k].hasOwnProperty('code') ? list[k].code : ''"
        >
            {{ list[k].hasOwnProperty('alias') ?
                ((typeof list[k].alias === 'object') ?
                    (list[k].alias[locale.code] ? list[k].alias[locale.code] : '') :
                    list[k].alias
                ) :
                ((typeof list[k]['name'] === 'object') ?
                    (list[k]['name'][locale.code] ? list[k]['name'][locale.code] : '') :
                    list[k]['name']
                )
            }}
        </option>
    </select>
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbInput,
    name: "Select",
    emits: [
        "select"
    ],
    data() {
        return {
            selected: this.value,
        }
    },
    methods: {
        emitSelectValue(el) {
            if (this.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('select', el.target.value)
        },
    },
    setup() {
        const locale = computed(() => usePage().props.value.locale)

        return {locale}
    }
}
</script>

<style scoped>

</style>

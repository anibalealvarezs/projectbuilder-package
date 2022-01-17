<template>
    <div class="text-left" :id="'grid-'+ keyel +'-' + keyid" v-for="(el, k) in list">
        <input
            :value="list[k].id"
            type="checkbox"
            :id="'checkbox-'+ keyel +'-' + keyid + '-' + list[k].id"
            class="appearance-none mx-4 px-4 py-3 mb-1 rounded bg-gray-200 text-gray-700 border border-gray-200 focus:outline-none focus:bg-white focus:border-gray-500"
            :checked="isChecked(value, list[k].id)"
            @click="emitCheckboxValue"
        >
        <span>
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
        </span>
    </div>
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbInput,
    name: "Checkbox",
    emits: [
        "click"
    ],
    methods: {
        isChecked(value, el) {
            return value.includes(el)
        },
        emitCheckboxValue(el) {
            let values = []
            this.list.forEach(el => {
                let element = document.getElementById('checkbox-'+ this.keyel +'-' + this.keyid + '-' + el.id)
                if (element.checked) {
                    values.push(el.id)
                }
            })
            if (this.isDebugEnabled()) {
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
    },
    setup() {
        const locale = computed(() => usePage().props.value.locale)

        return {locale}
    }
}
</script>

<style scoped>

</style>

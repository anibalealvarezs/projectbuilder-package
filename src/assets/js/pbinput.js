import {Helpers} from "Pub/js/Projectbuilder/Helpers/helpers";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    props: {
        keyel: String,
        keyid: String,
        value: undefined,
        type: String,
        required: Object,
        list: Object,
    },
    data() {
        return {
            locale: usePage().props.value.locale,
        }
    },
    emits: [
        "input",
    ],
    methods: {
        isRequired(key) {
            return (this.required ? this.required.includes(key) : false)
        },
        disableReadonly(event) {
            document.getElementById(event.target.id).readOnly = false
        },
        isDebugEnabled() {
            Helpers.isDebugEnabled()
        },
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
            this.$emit('input', values)
        },
        emitInputValue(el) {
            if (this.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Input activated" + "\n" +
                    "Value to emit: " + el.target.value + "\n" +
                    "Component: Input"
                )
            }
            this.$emit('input', el.target.value)
        },
    },
}

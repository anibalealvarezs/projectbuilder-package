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
    methods: {
        isRequired(key) {
            return (this.required ? this.required.includes(key) : false)
        },
        disableReadonly(event) {
            document.getElementById(event.target.id).readOnly = false
        },
        isDebugEnabled() {
            return usePage().props.value.shared.debug_enabled
        }
    },
}

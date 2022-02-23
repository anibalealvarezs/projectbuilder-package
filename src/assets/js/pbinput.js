import {Helpers} from "Pub/js/Projectbuilder/Helpers/helpers";

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
            Helpers.isDebugEnabled()
        }
    },
}

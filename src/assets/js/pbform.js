import Button from "@/Jetstream/Button";

export default {
    props: {
        data: Object,
        keyid: String,
        defaults: Object,
        required: Object,
    },
    components: {
        Button
    },
    data() {
        return {
            buttontext: (this.data.item ? "Save" : "Create")
        }
    },
    methods: {
        disableReadonly(event) {
            document.getElementById(event.target.id).readOnly = false
        },
        isRequired(key) {
            return this.required.includes(key)
        },
        isEmptyField(value) {
            return (value ? false : true)
        }
    },
    computed: {
        readonly() {
            return this.data.hasOwnProperty('item')
        }
    },
}

import Button from "@/Jetstream/Button"
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import Input from "@/Pages/Projectbuilder/Forms/Input"
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    props: {
        data: Object,
        keyid: String,
        defaults: Object,
        required: Object,
    },
    components: {
        Button,
        Input,
    },
    data() {
        return {
            buttontext: (this.data.item ? "Save" : "Create"),
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
            return !value
        },
        updateValue(value, key) {
            this.form[key] = value
            if (usePage().props.value.shared.debug_enabled) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "After form updated" + "\n" +
                    "Component: UserForm"
                )
                console.log(this.form)
            }
        },
    },
    computed: {
        readonly() {
            return this.data.hasOwnProperty('item')
        },
        submit() {
            if (this.data.hasOwnProperty('item')) {
                Inertia.put("/"+this.directory+"/"+ this.data.item, this.form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            } else {
                Inertia.post("/"+this.directory, this.form, {
                    preserveScroll: true,
                    preserveState: false,
                    onSuccess: () => Swal.close()
                })
            }
        }
    },
}

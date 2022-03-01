import Button from "@/Jetstream/Button"
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import SimpleInput from "@/Pages/Projectbuilder/Forms/SimpleInput"
import {usePage} from "@inertiajs/inertia-vue3";
import {Helpers} from "Pub/js/Projectbuilder/Helpers/helpers";
import {reactive} from "vue";

export default {
    name: "Form",
    props: {
        data: Object,
        keyid: String,
        defaults: Object,
        required: Object,
        title: String,
    },
    components: {
        Button,
        SimpleInput,
    },
    data() {
        return {
            formconfig: usePage().props.value.shared.formconfig,
            directory: this.title.toLowerCase(),
            buttontext: (this.data.item ? "Save" : "Create"),
            form: reactive(this.buildForm(usePage().props.value.shared.formconfig)),
        }
    },
    methods: {
        disableReadonly(event) {
            document.getElementById(event.target.id).readOnly = false
        },
        isRequired(key) {
            return (this.required ? this.required.includes(key) : false)
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
        buildForm(fields) {
            const inputs = {}
            for (const [index, field] of Object.entries(fields)) {
                let key = field.hasOwnProperty('data_field') ? field.data_field : index
                switch(field.type) {
                    case 'text':
                    case 'textarea':
                    case 'hidden':
                        inputs[index] = this.data[key]
                        break;
                    case 'select':
                        inputs[index] = this.data[key] ? this.data[key].id : (this.defaults.hasOwnProperty(key) ? this.defaults[key].id : 0)
                        break;
                    case 'select-multiple':
                        inputs[index] = Helpers.getModelIdsList(this.data[key])
                        break;
                    case 'password':
                        inputs[index] = ''
                        break;
                    default:
                        inputs[index] = null
                        break;
                }
            }
            return inputs
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
    /* setup (props) {
        const locale = computed(() => usePage().props.value.locale)
        const formconfig = computed(() => usePage().props.value.shared.formconfig)
        const form = reactive({
            name: props.data.name,
            description: props.data.description,
            file: null,
            alt: ((props.data.alt === Object(props.data.alt) && locale && props.data.alt.hasOwnProperty(locale)) ? props.data.alt[locale] : props.data.alt),
            url: props.data.url,
            module: (props.data.module ? props.data.module : 'filemanager'),
            permission: (props.data.permission ? props.data.permission : (props.defaults.hasOwnProperty('permission') ? props.defaults.permission : 'public')),
        })
        const directory = 'files'
        return { form, directory, locale, formconfig }
    }, */
}

import AppLayout from "@/Pages/Projectbuilder/AppLayout";
import Main from "@/Pages/Projectbuilder/Main";
import Form from "@/Pages/Projectbuilder/Helpers/CRUD/Form"
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "Create",
    props: {
        page: Object,
        title: String,
    },
    components: {
        AppLayout,
        Main,
        Form,
    },
    data() {
        return {
            defaults: usePage().props.value.shared.defaults,
            required: usePage().props.value.shared.required,
        }
    },
}

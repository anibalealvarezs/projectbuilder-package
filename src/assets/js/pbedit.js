import AppLayout from "@/Pages/Projectbuilder/AppLayout";
import Main from "@/Pages/Projectbuilder/Main";
import Form from "@/Pages/Projectbuilder/Helpers/CRUD/Form"

export default {
    name: "Edit",
    props: {
        page: Object,
        element: Object,
        title: String,
    },
    components: {
        AppLayout,
        Main,
        Form,
    },
    methods: {
        setItem(element) {
            element.item = element.id
            return element
        },
    },
}

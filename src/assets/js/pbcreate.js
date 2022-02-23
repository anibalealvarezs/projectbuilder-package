import AppLayout from "@/Pages/Projectbuilder/AppLayout";
import Main from "@/Pages/Projectbuilder/Main";
import Form from "@/Pages/Projectbuilder/Helpers/CRUD/Form"

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
}

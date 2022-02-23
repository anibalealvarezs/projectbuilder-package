import AppLayout from "@/Pages/Projectbuilder/AppLayout";
import Main from "@/Pages/Projectbuilder/Main";

export default {
    name: "Show",
    props: {
        page: Object,
        element: Object,
        title: String,
    },
    components: {
        AppLayout,
        Main,
    },
}

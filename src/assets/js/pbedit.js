import AppLayout from "@/Pages/Projectbuilder/AppLayout";
import Main from "@/Pages/Projectbuilder/Main";

export default {
    props: {
        page: Object
    },
    components: {
        AppLayout,
        Main,
    },
    methods: {
        setItem(element) {
            element.item = element.id
            return element
        },
    },
}

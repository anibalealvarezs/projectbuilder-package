import Swal from "sweetalert2";
import {TableFields} from "Pub/js/Projectbuilder/Helpers/tablefields"
import Button from "@/Jetstream/Button"
import AppLayout from "@/Pages/Projectbuilder/AppLayout"
import Main from "@/Pages/Projectbuilder/Main"
import {usePage} from "@inertiajs/inertia-vue3";
import Table from "@/Pages/Projectbuilder/Helpers/CRUD/Table"
import Form from "@/Pages/Projectbuilder/Helpers/CRUD/Form"

export default {
    name: "Index",
    props: {
        elements: Object,
        title: String,
    },
    components: {
        Button,
        AppLayout,
        Main,
        Table,
        Form
    },
    data() {
        return {
            hiddenid: 0
        }
    },
    methods: {
        loadForm() {
            let swalConfig = TableFields.buildSwalLoadFormConfig({text: "Create", formitem: this.title})
            swalConfig['didOpen'] = () => {
                TableFields.appendToSwal(this.hiddenid)
            }
            swalConfig['willClose'] = () => {
                TableFields.removeFromSwal(this.hiddenid)
            }
            Swal.fire(swalConfig).then(
                () => {
                    if (usePage().props.value.shared.debug_enabled) {
                        console.log(
                            "[ProjectBuilder] DEBUG" + "\n" +
                            "Swal: Form - After firing" + "\n" +
                            "Component: PbIndex"
                        )
                    }
                }
            );
        },
        getRowPos(el) {
            return TableFields.getRowPos(this.sort, el)
        },
        getTablePositions(group) {
            let sort = [];
            document.querySelectorAll('#'+this.model+'-table-rows tr').forEach(function(value){
                if (value.dataset.group === group) {
                    sort.push(value.dataset.id)
                }
            })
            return sort
        },
    },
    computed: {
        buildHiddenIdTag() {
            this.hiddenid = TableFields.buildHiddenIdTag()
            return this.hiddenid
        }
    },
}

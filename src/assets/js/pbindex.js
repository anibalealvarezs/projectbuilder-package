import Swal from "sweetalert2";
import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder"
import Button from "@/Jetstream/Button"
import AppLayout from "@/Pages/Projectbuilder/AppLayout"
import Main from "@/Pages/Projectbuilder/Main"
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    components: {
        Button,
        AppLayout,
        Main,
    },
    data() {
        return {
            hiddenid: 0
        }
    },
    methods: {
        loadForm() {
            let swalConfig = Table.buildSwalLoadFormConfig({text: "Create", formitem: "role"})
            swalConfig['didOpen'] = () => {
                Table.appendToSwal(this.hiddenid)
            }
            swalConfig['willClose'] = () => {
                Table.removeFromSwal(this.hiddenid)
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
            return Table.getRowPos(this.sort, el)
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
            this.hiddenid = Table.buildHiddenIdTag()
            return this.hiddenid
        }
    },
}

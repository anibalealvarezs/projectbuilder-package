import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder";
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody";
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead";
import Container from "@/Pages/Projectbuilder/Tables/Container";
import Header from "@/Pages/Projectbuilder/Tables/Header";
import Body from "@/Pages/Projectbuilder/Tables/Body";

export default {
    props: {
        allowed: Array,
        model: String,
        sort: Boolean,
        showpos: Boolean,
        showid: Boolean,
        defaults: Object,
        required: Array,
    },
    components: {
        TrBody,
        TrHead,
        Container,
        Header,
        Body
    },
    data() {
        return {
            data: {},
            itemFormKey: 0
        }
    },
    methods: {
        onItemClicked(value) {
            let result = Table.onItemClicked(value, this.data, this.itemFormKey)
            this.data = result.data
            this.itemFormKey = result.key
        },
        getRowPos(el) {
            return Table.getRowPos(this.sort, el)
        },
        getTablePositions(group) {
            let sort = [];
            document.querySelectorAll('#'+this.model+'-table-rows tr').forEach(function(value){
                if (value.dataset.group == group) {
                    sort.push(value.dataset.id)
                }
            })
            return sort
        },
    },
    computed: {
        existsFormButton() {
            return Table.existsFormButton(this.fields.actions.buttons)
        },
        buildHiddenId() {
            return Table.buildHiddenId()
        },
        generateRandom() {
            return Table.generateRandom()
        },
    },
}

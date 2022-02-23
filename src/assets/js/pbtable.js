import {TableFields} from "Pub/js/Projectbuilder/Helpers/tablefields";
import {Helpers} from "Pub/js/Projectbuilder/Helpers/helpers";
import TrBody from "@/Pages/Projectbuilder/Helpers/Tables/TrBody";
import TrHead from "@/Pages/Projectbuilder/Helpers/Tables/TrHead";
import TrFooter from "@/Pages/Projectbuilder/Helpers/Tables/TrFooter";
import Container from "@/Pages/Projectbuilder/Helpers/Tables/Container";
import Header from "@/Pages/Projectbuilder/Helpers/Tables/Header";
import Body from "@/Pages/Projectbuilder/Helpers/Tables/Body";
import Footer from "@/Pages/Projectbuilder/Helpers/Tables/Footer";
import Sortable from "sortablejs";
import Form from "@/Pages/Projectbuilder/Helpers/CRUD/Form"

export default {
    name: "Table",
    props: {
        allowed: Object,
        model: String,
        sort: Boolean,
        showpos: Boolean,
        showid: Boolean,
        defaults: Object,
        required: Array,
        listing: Array,
        elements: Object,
        title: String,
    },
    components: {
        TrBody,
        TrHead,
        TrFooter,
        Container,
        Header,
        Body,
        Footer,
        Form,
    },
    data() {
        return {
            data: {},
            itemFormKey: 0
        }
    },
    methods: {
        onItemClicked(value) {
            let result = TableFields.onItemClicked(value, this.data, this.itemFormKey)
            this.data = result.data
            this.itemFormKey = result.key
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
        buildSortingOptions(that) {
            return Object.assign(
                {},
                TableFields.getSortingOptions(),
                {
                    onSort: function (e) {
                        let data = {
                            sortlist: that.getTablePositions(e.item.dataset.group),
                            _method: 'PUT',
                        }
                        that.$inertia.post(
                            Helpers.buildRoute(that.model+'.sort', e.item.dataset.group),
                            data,
                            {
                                preserveState: false,
                            }
                        )
                    },
                }
            );
        },
    },
    computed: {
        existsFormButton() {
            return TableFields.existsFormButton(this.fields.actions.buttons)
        },
        buildHiddenId() {
            return TableFields.buildHiddenId()
        },
        buildHiddenIdTag() {
            return TableFields.buildHiddenIdTag(this.data)
        },
        generateRandom() {
            return TableFields.generateRandom()
        },
        generateRandomTag() {
            return TableFields.generateRandomTag(this.data)
        },
    },
    mounted() {
        if (this.sort) {
            let that = this
            let sortingOptions = this.buildSortingOptions(that)

            Sortable.create(
                document.getElementById(this.model+'-table-rows'),
                sortingOptions
            )
        }
    },
}

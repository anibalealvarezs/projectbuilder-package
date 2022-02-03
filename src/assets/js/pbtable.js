import {TableFields as Table, Helpers} from "Pub/js/Projectbuilder/projectbuilder";
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody";
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead";
import TrFooter from "@/Pages/Projectbuilder/Tables/TrFooter";
import Container from "@/Pages/Projectbuilder/Tables/Container";
import Header from "@/Pages/Projectbuilder/Tables/Header";
import Body from "@/Pages/Projectbuilder/Tables/Body";
import Footer from "@/Pages/Projectbuilder/Tables/Footer";
import Sortable from "sortablejs";

export default {
    props: {
        allowed: Object,
        model: String,
        sort: Boolean,
        showpos: Boolean,
        showid: Boolean,
        defaults: Object,
        required: Array,
        listing: Array,
    },
    components: {
        TrBody,
        TrHead,
        TrFooter,
        Container,
        Header,
        Body,
        Footer,
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
                if (value.dataset.group === group) {
                    sort.push(value.dataset.id)
                }
            })
            return sort
        },
        buildSortingOptions(that) {
            return Object.assign(
                {},
                Table.getSortingOptions(),
                {
                    onSort: function (e) {
                        let data = {
                            sortlist: that.getTablePositions(e.item.dataset.group),
                            _method: 'PUT',
                        }
                        that.$inertia.post(
                            Helpers.buildRoute(that.directory+'.sort', e.item.dataset.group),
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
            return Table.existsFormButton(this.fields.actions.buttons)
        },
        buildHiddenId() {
            return Table.buildHiddenId()
        },
        buildHiddenIdTag() {
            return Table.buildHiddenIdTag(this.data)
        },
        generateRandom() {
            return Table.generateRandom()
        },
        generateRandomTag() {
            return Table.generateRandomTag(this.data)
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

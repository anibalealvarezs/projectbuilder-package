<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead :fields="fields" :allowed="allowed" />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="user in users" :item="user" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-pos="getRowPos(user)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <UserForm :data="data" :keyid="generateRandom" :key="itemFormKey" />
        </div>
    </Container>
</template>

<script>
import Container from "@/Pages/Projectbuilder/Tables/Container"
import Header from "@/Pages/Projectbuilder/Tables/Header"
import Body from "@/Pages/Projectbuilder/Tables/Body"
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead"
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody"
import UserForm from "@/Pages/Projectbuilder/Users/UserForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs";
/* import Sortable, { MultiDrag, Swap } from 'sortablejs'
Sortable.mount(new MultiDrag(), new Swap()); */

export default {
    name: "TableUsers",
    props: {
        users: Object,
        allowed: Array,
        model: String,
        sort: Boolean,
        showpos: Boolean,
        showid: Boolean,
    },
    components: {
        UserForm,
        TrBody,
        TrHead,
        Container,
        Header,
        Body
    },
    mounted() {
        if (this.sort) {
            let that = this
            let sortingOptions = Object.assign(
                {},
                Table.getSortingOptions(),
                {
                    onSort: function (e) {
                        let data = {
                            sortlist: that.getTablePositions(e.item.dataset.group)
                        }
                        that.$inertia.post(
                            'users/sort/'+e.item.dataset.group,
                            data,
                            {
                                preserveState: false,
                            }
                        )
                    },
                }
            );

            Sortable.create(
                document.getElementById(this.model+'-table-rows'),
                sortingOptions
            )
        }
    },
    setup(props) {
        const allowed = props.allowed
        const table = new Table(props.showid)
        table.customField(
            "name",
            "Name",
            {},
            {},
            {},
            {
                route: "users.show",
                id: true
            }
        )
        table.customField(
            "email",
            "Email",
        )
        table.customField(
            "roles",
            "Roles",
            {
                key: "name",
                href: {
                    route: "roles.show",
                    id: "id",
                },
            },
            {},
            {},
            {},
            'multiple',
        )
        table.customField(
            "country",
            "Country",
            {
                key: "name"
            }
        )
        table.customField(
            "lang",
            "Language",
            {
                key: "name"
            }
        )
        table.customField(
            "created_at",
            "Created at"
        )
        table.customField(
            "last_session",
            "Last Session"
        )
        table.pushActions({
            "update": {
                text: 'Update',
                style: 'secondary',
                method: 'PUT',
                route: "users.edit",
                formitem: "user",
                altforuser: {
                    key: 'id',
                    altroute: "profile.show"
                },
                allowed: allowed.update,
            },
            "delete": {
                text: 'Delete',
                style: 'danger',
                method: 'DELETE',
                route: "users.destroy",
                formitem: "user",
                altforuser: {},
                allowed: allowed.delete,
            }
        })
        let fields = table.fields
        return { fields }
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
</script>

<style scoped>

</style>

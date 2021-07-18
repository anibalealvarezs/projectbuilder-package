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
            <UserForm :data="data" :keyid="generateRandom" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import UserForm from "@/Pages/Projectbuilder/Users/UserForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs"
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
    name: "TableUsers",
    props: {
        users: Object,
    },
    components: {
        UserForm,
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
}
</script>

<style scoped>

</style>

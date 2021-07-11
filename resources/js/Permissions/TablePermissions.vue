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
                    <TrBody v-for="permission in permissions" :item="permission" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-pos="getRowPos(permission)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <PermissionForm :data="data" :keyid="generateRandom" :key="itemFormKey" />
        </div>
    </Container>
</template>

<script>
import Container from "@/Pages/Projectbuilder/Tables/Container"
import Header from "@/Pages/Projectbuilder/Tables/Header"
import Body from "@/Pages/Projectbuilder/Tables/Body"
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead"
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody"
import PermissionForm from "@/Pages/Projectbuilder/Permissions/PermissionForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import Sortable from "sortablejs";

export default {
    name: "TablePermissions",
    props: {
        permissions: Object,
        allowed: Object,
        model: String,
        sort: Boolean,
        showpos: Boolean,
        showid: Boolean,
    },
    components: {
        PermissionForm,
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
                            '/permissions/sort/'+e.item.dataset.group,
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
        )
        table.customField(
            "alias",
            "Alias",
        )
        table.customField(
            "module",
            "Module",
            {key: "name"},
        )
        table.pushActions({
            "update": {
                text: 'Update',
                style: 'secondary',
                method: 'PUT',
                route: "permissions.edit",
                formitem: "permission",
                altforpermission: {
                    key: 'id',
                    altroute: "profile.show"
                },
                allowed: allowed.update,
            },
            "delete": {
                text: 'Delete',
                style: 'danger',
                method: 'DELETE',
                route: "permissions.destroy",
                formitem: "permission",
                altforpermission: {},
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
        }
    }
}
</script>

<style scoped>

</style>

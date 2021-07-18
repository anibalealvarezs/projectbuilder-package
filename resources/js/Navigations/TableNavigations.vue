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
                    <TrBody v-for="navigation in navigations" :item="navigation" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-id="navigation.id" :data-group="navigation.parent" :data-pos="getRowPos(navigation)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <NavigationForm :data="data" :keyid="generateRandom" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs"
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
    name: "TableNavigations",
    props: {
        navigations: Object,
    },
    components: {
        NavigationForm,
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
                            '/navigations/sort/'+e.item.dataset.group,
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
        const table = new Table(props.showid, props.sort)
        table.customField(
            "name",
            "Name"
        )
        table.customField(
            "destiny",
            "Destiny"
        )
        table.customField(
            "type",
            "Type"
        )
        table.customField(
            "ascendant",
            "Parent",
            {key: "name"},
        )
        table.customField(
            "permission",
            "Permission",
            {key: "alias"},
        )
        if (props.showpos) {
            table.customField(
                "position",
                "Position",
                {},
                {
                    centered: true,
                },
            )
        }
        table.customField(
            "status",
            "Status",
            {},
            {
                centered: true,
            },
            {},
            {},
            'single',
            true,
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
                route: "navigations.edit",
                formitem: "navigation",
                altforuser: {},
                allowed: allowed.update,
            },
            "delete": {
                text: 'Delete',
                style: 'danger',
                method: 'DELETE',
                route: "navigations.destroy",
                formitem: "navigation",
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

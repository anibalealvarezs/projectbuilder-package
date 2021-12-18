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
                    <TrBody v-for="role in roles" :item="role" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-pos="getRowPos(role)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <RoleForm :data="data" :keyid="generateRandom" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs"
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
    name: "TableRoles",
    props: {
        roles: Object,
    },
    components: {
        RoleForm,
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
                            '/roles/sort/'+e.item.dataset.group,
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
        const table = new Table(props.showid)
        const listing = props.listing
        for (const [k, v] of Object.entries(listing)) {
            if ((v.key != 'item') && (v.key != 'actions') && (v.key != 'sorthandle')) {
                table.customField(v.key, v.name, v.arrval, v.style, v.buttons, v.href, v.size, v.status)
            } else if (v.key == 'actions') {
                table.pushActions(v.buttons);
            }
        }
        let fields = table.fields
        return { fields }
    },
}
</script>

<style scoped>

</style>

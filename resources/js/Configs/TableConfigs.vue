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
                    <TrBody v-for="config in configs" :item="config" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-pos="getRowPos(config)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <ConfigForm :data="data" :keyid="generateRandom" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import ConfigForm from "@/Pages/Projectbuilder/Configs/ConfigForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs"
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
    name: "TableConfigs",
    props: {
        configs: Object,
    },
    components: {
        ConfigForm,
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
                            '/configs/sort/'+e.item.dataset.group,
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

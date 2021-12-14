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
                    <TrBody v-for="logger in loggers" :item="logger" :fields="fields" :hiddenid="buildHiddenId" :allowed="allowed" :data-pos="getRowPos(logger)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <LoggerForm :data="data" :keyid="generateRandom" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import LoggerForm from "@/Pages/Projectbuilder/Loggers/LoggerForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import Sortable from "sortablejs"
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
    name: "TableLoggers",
    props: {
        loggers: Object,
    },
    components: {
        LoggerForm,
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
                            '/loggers/sort/'+e.item.dataset.group,
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
            "severity",
            "Severity"
        )
        table.customField(
            "code",
            "Code"
        )
        table.customField(
            "message",
            "Message",
        )
        table.customField(
            "object_type",
            "Object Type",
        )
        table.customField(
            "object_id",
            "Object ID"
        )
        table.customField(
            "user",
            "User",
            {
                key: "name",
                href: {
                    route: "users.show",
                    id: "id",
                },
            },
        )
        table.customField(
            "module",
            "Module",
            {
                key: "name",
            },
        )
        table.customField(
            "created_at",
            "Created at"
        )
        table.pushActions({
            "update": {
                text: 'Edit',
                style: 'secondary',
                method: 'PUT',
                route: "loggers.edit",
                formitem: "logger",
                altforuser: {},
                allowed: allowed.update,
            },
            "delete": {
                text: 'Delete',
                style: 'danger',
                method: 'DELETE',
                route: "loggers.destroy",
                formitem: "logger",
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

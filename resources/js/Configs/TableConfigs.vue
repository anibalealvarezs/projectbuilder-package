<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead :fields="fields" />
                </slot>
            </Header>
            <Body>
                <slot>
                    <TrBody v-for="config in configs" :item="config" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <ConfigForm :data="data" :keyid="generateRandom" :key="itemFormKey" />
        </div>
    </Container>
</template>

<script>
import Container from "@/Pages/Projectbuilder/Tables/Container"
import Header from "@/Pages/Projectbuilder/Tables/Header"
import Body from "@/Pages/Projectbuilder/Tables/Body"
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead"
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody"
import ConfigForm from "@/Pages/Projectbuilder/Configs/ConfigForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"

export default {
    name: "TableConfigs",
    props: {
        configs: Object
    },
    components: {
        ConfigForm,
        TrBody,
        TrHead,
        Container,
        Header,
        Body
    },
    setup() {
        const table = new Table
        table.customField(
            "name",
            "Name"
        )
        table.customField(
            "configkey",
            "Key"
        )
        table.customField(
            "configvalue",
            "Value"
        )
        table.customField(
            "description",
            "Description"
        )
        table.pushActions({
            "update": {
                route: "configs.edit",
                formitem: "config",
                altforuser: {}
            },
            "delete": {
                route: "configs.destroy",
                formitem: "config",
                altforuser: {}
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
        }
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

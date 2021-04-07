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
                    <TrBody v-for="navigation in navigations" :item="navigation" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <NavigationForm :data="data" :keyid="generateRandom" :key="itemFormKey" />
        </div>
    </Container>
</template>

<script>
import Container from "@/Pages/Projectbuilder/Tables/Container"
import Header from "@/Pages/Projectbuilder/Tables/Header"
import Body from "@/Pages/Projectbuilder/Tables/Body"
import TrHead from "@/Pages/Projectbuilder/Tables/TrHead"
import TrBody from "@/Pages/Projectbuilder/Tables/TrBody"
import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"

export default {
    name: "TableNavigations",
    props: {
        navigations: Object
    },
    components: {
        NavigationForm,
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
            "destiny",
            "Destiny"
        )
        table.customField(
            "type",
            "Type"
        )
        table.customField(
            "parent",
            "Parent"
        )
        table.customField(
            "module",
            "Module"
        )
        table.pushActions({
            "update": {
                route: "navigations.edit",
                formitem: "navigation",
                altforuser: {}
            },
            "delete": {
                route: "navigations.destroy",
                formitem: "navigation",
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

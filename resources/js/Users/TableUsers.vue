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
                    <TrBody v-for="user in users" :item="user" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onItemClicked" />
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
import { TableFields as Table } from "../../../../../public/js/Projectbuilder/projectbuilder"

export default {
    name: "TableUsers",
    props: {
        users: Object
    },
    components: {
        UserForm,
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
            "Name",
            {},
            {},
            {route: "users.show", id: true}
        )
        table.customField(
            "email",
            "Email"
        )
        table.customField(
            "last_session",
            "Last Session"
        )
        table.customField(
            "created_at",
            "Created at"
        )
        table.pushActions({
            "update": {
                route: "users.edit",
                formitem: "user",
                altforuser: {
                    key: 'id',
                    altroute: "profile.show"
                }
            },
            "delete": {
                route: "users.destroy",
                formitem: "user",
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

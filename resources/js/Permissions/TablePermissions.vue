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
                    <TrBody v-for="permission in permissions" :item="permission" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onItemClicked" />
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

export default {
    name: "TablePermissions",
    props: {
        permissions: Object,
    },
    components: {
        PermissionForm,
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
            {},
            {route: "permissions.show", id: true}
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
            },
            "delete": {
                text: 'Delete',
                style: 'danger',
                method: 'DELETE',
                route: "permissions.destroy",
                formitem: "permission",
                altforpermission: {},
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

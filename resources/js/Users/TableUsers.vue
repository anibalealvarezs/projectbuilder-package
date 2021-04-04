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
                    <TrBody v-for="user in users" :item="user" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onUserClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">
            <UserForm :data="data" :key="itemFormKey" />
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
import ProjectBuilder from "../../../../../public/js/Projectbuilder/projectbuilder"
// import { ProjectBuilder } from "../../../../../public/js/projectbuilder"
// import * as _ProjectBuilder from "../../../../../public/js/projectbuilder"
// import { customField, pushActions } from "../../../../../public/js/projectbuilder"
// const {defineFields, customField} = require("../../../../../public/js/projectbuilder")
// const { ProjectBuilder } = require("../../../../../public/js/projectbuilder")
// const ProjectBuilder = require("../../../../../public/js/projectbuilder").default
// import defineFields from "../../../../../public/js/projectbuilder"
// var ProjectBuilder = require('../../../../../public/js/projectbuilder');

// import {defineFields, customField, pushActions} from "../../../../../packages/anibalealvarezs/projectbuilder-package/src/assets/js/projectbuilder"
// import ProjectBuilder from "../../../../../packages/anibalealvarezs/projectbuilder-package/src/assets/js/projectbuilder"

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
        const projectbuilder = new ProjectBuilder
        console.log(projectbuilder.fields)
        projectbuilder.customField(
            "name",
            "Name",
            {},
            {},
            {route: "users.show", id: true}
        )
        projectbuilder.customField(
            "email",
            "Email"
        )
        projectbuilder.customField(
            "last_session",
            "Last Session"
        )
        projectbuilder.customField(
            "created_at",
            "Created at"
        )
        projectbuilder.pushActions({
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
        let fields = projectbuilder.fields
        console.log(fields)
        return { fields }
    },
    data() {
        return {
            data: {},
            itemFormKey: 0
        }
    },
    methods: {
        onUserClicked(value) {
            for(let i in value) {
                if (i == "id") {
                    this.data['item'] = value[i]
                } else {
                    this.data[i] = value[i]
                }
            }
            this.itemFormKey += 1
        }
    },
    computed: {
        existsFormButton() {
            let b = this.fields.actions.buttons;
            if (b) {
                for (const [k, v] of Object.entries(b)) {
                    if ( v.enabled && (v.type === "form")) {
                        return true
                    }
                }
            }
            return false
        },
        buildHiddenId() {
            return (this.existsFormButton ?
                    'hidden-form-' + Math.floor((Math.random() * 999999999) + 1) :
                        "")
        }
    }
}
</script>

<style scoped>
    .infinite-hidden {
        position: fixed;
        top: 999999px;
        letf: 999999px;
    }
</style>

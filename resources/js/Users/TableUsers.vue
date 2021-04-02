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
    data() {
        return {
            fields: {
                "item" : {
                    name: "#",
                    style: {
                        centered: true,
                        bold: true,
                        width: "w-20",
                    },
                    buttons: [],
                    href: {}
                },
                "name" : {
                    name: "Name",
                    style: {
                        centered: false,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    href: {
                        route: "users.show",
                        id: true
                    }
                },
                "email" : {
                    name: "Email",
                    style: {
                        centered: false,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    href: {}
                },
                "last_session" : {
                    name: "Last Session",
                    style: {
                        centered: true,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    href: {}
                },
                "created_at" : {
                    name: "Created at",
                    style: {
                        centered: true,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    key: "created_at",
                    href: {}
                },
                "actions" : {
                    name: "Actions",
                    style: {
                        centered: true,
                        bold: false,
                        width: "w-60",
                    },
                    buttons: [
                        {
                            text: "Update",
                            route: "users.edit",
                            id: true,
                            callback: null,
                            style: "secondary",
                            type: "form",
                            formitem: "user",
                            method: "PUT"
                        },
                        {
                            text: "Delete",
                            route: "users.destroy",
                            id: true,
                            callback: null,
                            style: "danger",
                            type: "form",
                            formitem: "user",
                            method: "DELETE"
                        }
                    ],
                    href: {}
                }
            },
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
                return b.some((e) => {
                    return e.type === "form"
                });
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

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
                    <TrBody v-for="config in configs" :item="config" :fields="fields" :hiddenid="buildHiddenId" @clicked-edit-item="onConfigClicked" />
                </slot>
            </Body>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenId" class="infinite-hidden">>
            <ConfigForm :data="data" :key="itemFormKey" />
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
                    href: {}
                },
                "configkey" : {
                    name: "Key",
                    style: {
                        centered: false,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    href: {}
                },
                "configvalue" : {
                    name: "Value",
                    style: {
                        centered: false,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
                    href: {}
                },
                "description" : {
                    name: "Description",
                    style: {
                        centered: false,
                        bold: false,
                        width: "",
                    },
                    buttons: [],
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
                            route: "configs.edit",
                            id: true,
                            callback: null,
                            style: "secondary",
                            type: "form",
                            formitem: "config",
                            method: "PUT"
                        },
                        {
                            text: "Delete",
                            route: "configs.destroy",
                            id: true,
                            callback: null,
                            style: "danger",
                            type: "form",
                            formitem: "config",
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
        onConfigClicked(value) {
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

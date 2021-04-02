<template>
    <td scope="row"
        :class="buildTdClasses()">
        <div v-for="(button, i) in field.buttons" :key="i" class="float-left">
            <inertia-link
                v-if="button.type == 'link'"
                :href="buildRoute(button.route, item.id)"
            >
                <Button
                    v-if="button.style == 'primary'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </Button>
                <SecondaryButton
                    v-if="button.style == 'secondary'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </SecondaryButton>
                <DangerButton
                    v-if="button.style == 'danger'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </DangerButton>
            </inertia-link>
            <form
                v-if="button.type == 'form'"
                :action="buildRoute(button.route, item.id)"
                method="post"
            >
                <Button
                    v-if="button.style == 'primary'"
                    @click="processAction(button, item)"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </Button>
                <SecondaryButton
                    v-if="button.style == 'secondary'"
                    @click="processAction(button, item)"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </SecondaryButton>
                <DangerButton
                    v-if="button.style == 'danger'"
                    @click="processAction(button, item)"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </DangerButton>
            </form>
        </div>
        <inertia-link
            v-if="field.href.hasOwnProperty('route')"
            :href="buildRoute(field.href.route, item.id)"
        >
            <span
                :class="buildSpanClasses()"
            >
                {{ cellValue }}
            </span>
        </inertia-link>
        <span
            v-if="!field.href.hasOwnProperty('route')"
            :class="buildSpanClasses()"
        >
            {{ cellValue }}
        </span>
    </td>
</template>

<script>
import Button from "@/Jetstream/Button"
import SecondaryButton from "@/Jetstream/SecondaryButton"
import DangerButton from "@/Jetstream/DangerButton"
import Swal from "sweetalert2"
import UserForm from "@/Pages/Projectbuilder/Users/UserForm"

export default {
    name: "Td",
    components: {
        DangerButton,
        SecondaryButton,
        Button,
        UserForm
    },
    props: {
        item: Object,
        field: Object,
        index: String,
        hiddenid: String
    },
    data() {
        return {
            form: {
                title: null,
                body: null,
            }
        }
    },
    computed: {
        fixKey() {
            if (this.index == "item") {
                return "id"
            }
            return this.index
        },
        cellValue() {
            return this.item[this.fixKey]
        }
    },
    methods: {
        buildUserForm() {

        },
        buildSpanClasses() {
            let clase = ""
            clase += this.isBold()
            clase += this.isCentered()
            return clase
        },
        buildTdClasses() {
            let clase = "border px-4 py-2"
            clase += this.isCentered()
            return clase
        },
        isBold() {
            if (this.field.style.bold) {
                return " font-semibold";
            }
            return ""
        },
        isCentered() {
            let ret = "";
            if (this.field.style.centered) {
                ret += " text-center"
            }
            return ret
        },
        buildRoute(r, id) {
            if (id) {
                return route(r, id)
            }
            return route(r)
        },
        buildButtonClasses(color) {
            return "mx-1"
        },
        processAction(b, u) {
            switch(b.text) {
                case "Create":
                case "Update":
                    this.loadForm(b, u)
                    break
                case "Delete":
                    this.confirmAndSubmit(b, u.id)
                    break
                default:
                    break
            }
        },
        loadForm(b, i) {
            Swal.fire({
                title: b.text + ' ' + b.formitem,
                html: '<div id="formmodal" class="p-12 sm:px-20 bg-white border-b border-gray-200"></div>',
                confirmButtonText: b.text,
                showCloseButton: false,
                showCancelButton: false,
                showConfirmButton: false,
                width: 800,
                didOpen: () => {
                    let hidden = document.getElementById(this.hiddenid);
                    let formodal = document.getElementById('formmodal');
                    formodal.append(hidden.childNodes[0]);
                    this.$emit('clicked-edit-item', i);
                },
                willClose: () => {
                    let hidden = document.getElementById(this.hiddenid);
                    let formodal = document.getElementById('formmodal');
                    hidden.append(formodal.childNodes[0]);
                    this.$emit('clicked-edit-item', {});
                }
            });
        },
        confirmAndSubmit(b, i) {
            let data = {
                id: i
            }
            Swal.fire({
                title: b.text + ' ' + b.formitem,
                text: 'Are you sure you want to proceed?',
                icon: 'warning',
                confirmButtonText: b.text
            }).then((result) => {
                if (result['isConfirmed']){
                    data._method = b.method;
                    this.$inertia.post(this.buildRoute(b.route, i), data)
                }
            })
        }
    }
}
</script>

<style scoped>

</style>

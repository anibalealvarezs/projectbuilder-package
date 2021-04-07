<template>
    <td scope="row"
        :class="buildTdClasses()">
        <div v-for="(button, i) in field.buttons" :key="i" class="float-left">
            <!-- BUTTON HREF -->
            <inertia-link
                v-if="button.type == 'link'"
                :href="buildRoute(button.route, item.id)"
            >
                <!-- Primary Button -->
                <Button
                    v-if="button.style == 'primary'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </Button>
                <!-- Secondary Button -->
                <SecondaryButton
                    v-if="button.style == 'secondary'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </SecondaryButton>
                <!-- Danger Button -->
                <DangerButton
                    v-if="button.style == 'danger'"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </DangerButton>
            </inertia-link>
            <!-- BUTTON FORM -->
            <form
                v-if="button.type == 'form'"
                :action="buildRoute(button.route, item.id)"
                method="post"
            >
                <!-- Primary Button -->
                <Button
                    v-if="button.style == 'primary'"
                    @click="processAction(button, item)"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </Button>
                <!-- Secondary Button -->
                <SecondaryButton
                    v-if="button.style == 'secondary'"
                    @click="processAction(button, item)"
                    type="button"
                    :class="buildButtonClasses(button.color)"
                >
                    {{ button.text }}
                </SecondaryButton>
                <!-- Danger Button -->
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
        <!-- HREF -->
        <inertia-link
            v-if="field.href.hasOwnProperty('route')"
            :href="buildRoute(field.href.route, item.id)"
        >
            <!-- HREF CONTENT -->
            <span
                :class="buildSpanClasses()"
            >
                {{ cellValue }}
            </span>
        </inertia-link>
        <!-- NO HREF CONTENT -->
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
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"

export default {
    name: "Td",
    components: {
        DangerButton,
        SecondaryButton,
        Button
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
            return Table.fixKey(this.index)
        },
        cellValue() {
            if (this.field.arrval.hasOwnProperty('key')) {
                let obj = Object.assign({}, this.item[this.fixKey]);
                return obj[this.field.arrval.key]
            }
            return this.item[this.fixKey]
        }
    },
    methods: {
        buildSpanClasses() {
            return Table.buildSpanClasses(this.field.style.bold, this.field.style.centered)
        },
        buildTdClasses() {
            return Table.buildTdClasses(this.field.style.centered)
        },
        isBold() {
            return Table.isBold(this.field.style.bold)
        },
        isCentered() {
            return Table.isCentered(this.field.style.centered)
        },
        buildRoute(r, id) {
            if (id) {
                return route(r, id)
            }
            return route(r)
        },
        buildButtonClasses() {
            return "mx-1"
        },
        processAction(b, i) {
            switch(b.text) {
                case "Create":
                case "Update":
                    let action = true;
                    if (b.altforuser.hasOwnProperty('altroute')) {
                        if (i[b.altforuser.key] == this.user[b.altforuser.key]) {
                            action = false;
                            window.location.href = route(b.altforuser.altroute);
                        }
                    }
                    if (action) {
                        this.loadForm(b, i)
                    }
                    break
                case "Delete":
                    this.confirmAndSubmit(b, i.id)
                    break
                default:
                    break
            }
        },
        loadForm(b, i) {
            let swalConfig = Table.buildSwalLoadFormConfig(b)
            swalConfig['didOpen'] = () => {
                Table.appendToSwal(this.hiddenid)
                this.$emit('clicked-edit-item', i)
            }
            swalConfig['willClose'] = () => {
                Table.removeFromSwal(this.hiddenid)
                this.$emit('clicked-edit-item', {})
            }
            Swal.fire(swalConfig);
        },
        confirmAndSubmit(b, i) {
            let data = { id: i }
            let swalConfig = Table.buildSwalConfirmAndSubmitConfig(b)
            Swal.fire(swalConfig)
                .then((result) => {
                    if (result['isConfirmed']){
                        data._method = b.method;
                        // Aquí deberé modificar para recargar pantalla en edición de navigations
                        this.$inertia.post(this.buildRoute(b.route, i), data)
                    }
                })
        }
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user)
        return { user }
    }
}
</script>

<style scoped>

</style>

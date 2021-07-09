<template>
    <td v-if="(allowed.update || allowed.delete) || (index != 'actions')" scope="row"
        :class="buildTdClasses()">
        <div v-if="index == 'actions'">
            <!-- Actions Dropdown -->
            <div class="relative">
                <JetDropdown align="right" width="60">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <Button type="button">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </Button>
                        </span>
                    </template>

                    <template #content>
                        <div class="w-60">
                            <!-- Actions -->
                            <div v-for="(button, i) in field.buttons" class="space-y-1" :key="i">
                                <JetDropdownLink
                                    v-if="(button.type == 'link') && ((i != 'update') || item.crud.editable) && ((i != 'delete') || item.crud.deletable)"
                                    :href="buildRoute(button.route, item.id)"
                                >
                                    <div :class="buildSpanClasses()">
                                        <div>{{ button.text }}</div>
                                    </div>
                                </JetDropdownLink>
                                <form
                                    v-if="(button.type == 'form') && ((i != 'update') || item.crud.editable) && ((i != 'delete') || item.crud.deletable)"
                                    :action="buildRoute(button.route, item.id)"
                                    @submit.prevent="submit"
                                    method="post"
                                >
                                    <JetDropdownLink
                                        as="button"
                                        @click="processAction(button, item)"
                                    >
                                        <div :class="buildSpanClasses()">
                                            <div>{{ button.text }}</div>
                                        </div>
                                    </JetDropdownLink>
                                </form>
                            </div>
                        </div>
                    </template>
                </JetDropdown>
            </div>
        </div>
        <!-- SIZE -->
        <div v-if="field.size == 'single'" :class="(field.href.hasOwnProperty('route') ? 'bg-gray-200' : '')">
            <!-- HREF -->
            <JetDropdownLink
                v-if="field.href.hasOwnProperty('route')"
                :href="buildRoute(field.href.route, item.id)"
            >
                <!-- HREF CONTENT -->
                <span
                    :class="buildSpanClasses()"
                >
                    {{ cellValue }}
                    <!-- <svg class="h-4 w-4"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 12h14l-3 -3m0 6l3 -3" />
                    </svg> -->
                </span>
            </JetDropdownLink>
            <!-- NO HREF CONTENT -->
            <span
                v-if="!field.href.hasOwnProperty('route')"
                :class="buildSpanClasses()"
            >
                {{ cellValue }}
            </span>
        </div>
        <div v-if="field.size == 'multiple'">
            <div v-for="cv in cellValue" :class="(field.arrval.hasOwnProperty('href') ? 'bg-gray-200 space-y-1' : 'space-y-1')">
                <!-- HREF -->
                <JetDropdownLink
                    v-if="field.arrval.hasOwnProperty('href')"
                    :href="buildRoute(field.arrval.href.route, cv[field.arrval.href.id])"
                >
                    <!-- HREF CONTENT -->
                    <span
                        class="inline-flex items-center"
                    >
                        {{ (field.arrval.hasOwnProperty('key') ? cv[field.arrval.key] : cv) }}
                        <!-- <svg class="h-4 w-4"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 12h14l-3 -3m0 6l3 -3" />
                        </svg> -->
                    </span>
                </JetDropdownLink>
                <!-- NO HREF CONTENT -->
                <span
                    class="inline-flex items-center"
                    v-if="!field.arrval.hasOwnProperty('href')"
                >
                    {{ (field.arrval.hasOwnProperty('key') ? cv[field.arrval.key] : cv) }}
                </span>
            </div>
        </div>
    </td>
</template>

<script>
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Button from "@/Jetstream/Button"
import Swal from "sweetalert2"
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"

export default {
    name: "Td",
    components: {
        JetDropdown,
        JetDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
        Button,
    },
    props: {
        item: Object,
        field: Object,
        index: String,
        hiddenid: String,
        allowed: Array,
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
            if (this.field.arrval.hasOwnProperty('key') && (this.field.size == 'single')) {
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

<template>
    <td v-if="((allowed.update && item.crud.editable) || (allowed.delete && item.crud.deletable)) || (index !== 'actions')" scope="row"
        :class="(index === 'sorthandle' ? buildHandlerClasses() : buildTdClasses())">
        <div v-if="index === 'actions'">
            <!-- Actions Dropdown -->
            <div class="relative">
                <PbDropdown align="right" width="60" :top="'-mt-6'" :right="'right-14'">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <Button type="button">
                                ...
                            </Button>
                        </span>
                    </template>

                    <template #content>
                        <div class="w-60">
                            <!-- Actions -->
                            <div v-for="(button, i) in field.buttons" class="space-y-1" :key="i">
                                <PbDropdownLink
                                    v-if="(button.type === 'link') && ((i !== 'update') || item.crud.editable) && ((i !== 'delete') || item.crud.deletable)"
                                    :href="buildRoute(button.route, item.id)"
                                >
                                    <div :class="buildSpanClasses()">
                                        <div>{{ button.text }}</div>
                                    </div>
                                </PbDropdownLink>
                                <form
                                    v-if="(button.type === 'form') && ((i !== 'update') || item.crud.editable) && ((i !== 'delete') || item.crud.deletable)"
                                    :action="buildRoute(button.route, item.id)"
                                    @submit.prevent="submit"
                                    method="post"
                                >
                                    <PbDropdownLink
                                        as="button"
                                        @click="processAction(button, item)"
                                    >
                                        <div :class="buildSpanClasses()">
                                            <div>{{ button.text }}</div>
                                        </div>
                                    </PbDropdownLink>
                                </form>
                            </div>
                        </div>
                    </template>
                </PbDropdown>
            </div>
        </div>
        <!-- SIZE -->
        <div v-if="field.size === 'single'" :class="(!!field.href.hasOwnProperty('route') || !!field.href.hasOwnProperty('custom') ? 'bg-gray-200' : '')">
            <!-- HREF -->
            <!-- route -->
            <PbDropdownLink
                v-if="!!field.href.hasOwnProperty('route')"
                :href="buildRoute(field.href.route, item.id)"
            >
                <!-- HREF CONTENT -->
                <span
                    class="inline-flex items-center"
                >
                    <Icon
                        v-if="field.href.hasOwnProperty('text') && field.href.text === '#'"
                        type="get-into"
                    />
                    <span v-else>
                        {{ field.href.hasOwnProperty('text') ? field.href.text : cellValue }}
                    </span>
                </span>
            </PbDropdownLink>
            <!-- custom -->
            <PbDropdownLink
                v-else-if="!!field.href.hasOwnProperty('custom')"
                :href="field.href.custom === 'self' ? cellValue : '#'"
                :as="'a'"
                :target="field.href.hasOwnProperty('target') ? field.href.target : '_self'"
            >
                <!-- HREF CONTENT -->
                <span
                    class="inline-flex items-center"
                >
                    <Icon
                        v-if="field.href.hasOwnProperty('text') && field.href.text === '#'"
                        type="get-into"
                    />
                    <span v-else>
                        {{ field.href.hasOwnProperty('text') ? field.href.text : cellValue }}
                    </span>
                </span>
            </PbDropdownLink>
            <!-- NO HREF -->
            <div v-else>
                <div v-if="field.status">
                    <span
                        :class="buildSpanClasses()"
                    >
                        <!-- ENABLED -->
                        <Icon
                            v-if="cellValue === 1"
                            type="check"
                            :classes="['pb-icon-check', 'pb-status-enabled']"
                            :clickable=true
                            @click="disableRow(item.id)"
                        />
                        <!-- DISABLED -->
                        <Icon
                            v-else
                            type="close"
                            :classes="['pb-icon-close', 'pb-status-disabled']"
                            :clickable=true
                            @click="enableRow(item.id)"
                        />
                    </span>
                </div>
                <div v-else-if="index === 'sorthandle'">
                    <span
                        :class="buildSpanClasses()"
                    >
                        <Icon type="sort" />
                    </span>
                </div>
                <span
                    v-else
                    :class="buildSpanClasses()"
                >
                    <span v-if="cellValue">
                        <span v-if="typeof cellValue === 'object'" :class="cellValue[locale.code] ? '' : 'opacity-25 italic'">
                            {{ (cellValue[locale.code] ? cellValue[locale.code] : '[no translation]') }} <span v-if="!cellValue[locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                        </span>
                        <span v-else>
                            {{ cellValue }}
                        </span>
                    </span>
                </span>
            </div>
        </div>
        <div v-if="field.size === 'multiple'">
            <div v-for="cv in cellValue" :class="(field.arrval.hasOwnProperty('href') ? 'bg-gray-200 space-y-1 mb-px' : 'space-y-1')">
                <!-- HREF -->
                <!-- route -->
                <PbDropdownLink
                    v-if="field.arrval.hasOwnProperty('href') && field.arrval.href.hasOwnProperty('route')"
                    :href="buildRoute(field.arrval.href.route, cv[field.arrval.href.id])"
                >
                    <!-- HREF CONTENT -->
                    <span
                        v-if="field.arrval.hasOwnProperty('key')"
                        class="inline-flex items-center"
                    >
                        <span v-if="cv[field.arrval.key]">
                            <span v-if="typeof cv[field.arrval.key] === 'object'">
                                {{ cv[field.arrval.key][locale.code] ? cv[field.arrval.key][locale.code] : '[no translation]' }}  <span v-if="!cv[field.arrval.key][locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                            </span>
                            <span v-else>
                                <Icon
                                    v-if="field.arrval.href.hasOwnProperty('text') && field.arrval.href.text === '#'"
                                    type="get-into"
                                />
                                <span v-else>
                                    {{ field.arrval.href.hasOwnProperty('text') ? field.arrval.href.text : cv[field.arrval.key] }}
                                </span>
                            </span>
                        </span>
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center"
                    >
                        {{ typeof cv === 'object' ? (cv[locale.code] ? cv[locale.code] : '[no translation]') : cv }} <span v-if="!cv[locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                    </span>
                </PbDropdownLink>
                <!-- custom -->
                <PbDropdownLink
                    v-else-if="field.arrval.hasOwnProperty('href') && field.arrval.href.hasOwnProperty('custom')"
                    :href="field.arrval.href.custom === 'self' ? (cv[field.arrval.key] ? cv[field.arrval.key] : '#') : '#'"
                    :target="field.arrval.href.hasOwnProperty('target') ? field.arrval.href.target : '_self'"
                    :as="'a'"
                >
                    <!-- HREF CONTENT -->
                    <span
                        v-if="field.arrval.hasOwnProperty('key')"
                        class="inline-flex items-center"
                    >
                        <span v-if="cv[field.arrval.key]">
                            <span v-if="typeof cv[field.arrval.key] === 'object'">
                                {{ cv[field.arrval.key][locale.code] ? cv[field.arrval.key][locale.code] : '[no translation]' }}  <span v-if="!cv[field.arrval.key][locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                            </span>
                            <span v-else>
                                <Icon
                                    v-if="field.arrval.href.hasOwnProperty('text') && field.arrval.href.text === '#'"
                                    type="get-into"
                                />
                                <span v-else>
                                    {{ field.arrval.href.hasOwnProperty('text') ? field.arrval.href.text : cv[field.arrval.key] }}
                                </span>
                            </span>
                        </span>
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center"
                    >
                        {{ typeof cv === 'object' ? (cv[locale.code] ? cv[locale.code] : '[no translation]') : cv }} <span v-if="!cv[locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                    </span>
                </PbDropdownLink>
                <!-- NO HREF -->
                <span
                    class="inline-flex items-center"
                    v-else
                >
                    <span v-if="field.arrval.hasOwnProperty('key')">
                        <span v-if="cv[field.arrval.key]">
                            <span v-if="typeof cv[field.arrval.key] === 'object'">
                                {{ cv[field.arrval.key][locale.code] ? cv[field.arrval.key][locale.code] : '[no translation]' }}  <span v-if="!cv[field.arrval.key][locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                            </span>
                            <span v-else>
                                {{ cv[field.arrval.key] }}
                            </span>
                        </span>
                    </span>
                    <span v-else>
                        {{ typeof cv === 'object' ? (cv[locale.code] ? cv[locale.code] : '[no translation]') : cv }} <span v-if="!cv[locale.code] && locale.country" :class="'fi fi-'+locale.country.code"></span>
                    </span>
                </span>
            </div>
        </div>
    </td>
</template>

<script>
import PbDropdown from '@/Pages/Projectbuilder/PbDropdown'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Button from "@/Jetstream/Button"
import Swal from "sweetalert2"
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { TableFields as Table, Helpers } from "Pub/js/Projectbuilder/projectbuilder"
import PbDropdownLink from "@/Pages/Projectbuilder/PbDropdownLink"
import Icon from "@/Pages/Projectbuilder/Icons/Icon"

export default {
    name: "Td",
    components: {
        PbDropdown,
        PbDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
        Button,
        Icon,
    },
    props: {
        item: Object,
        field: Object,
        index: String,
        hiddenid: String,
        allowed: Object,
    },
    emits: [
        'clicked-edit-item'
    ],
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
            if (this.field.arrval.hasOwnProperty('key') && (this.field.size === 'single')) {
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
        buildHandlerClasses() {
            return Table.buildHandlerClasses()
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
            return Helpers.buildRoute(r, id)
        },
        buildButtonClasses() {
            return "mx-1"
        },
        processAction(b, i) {
            switch(b.text) {
                case "Create":
                case "Edit":
                    let action = true;
                    if (b.altformodel.hasOwnProperty('altroute')) {
                        if (i[b.altformodel.key] === this.user[b.altformodel.key]) {
                            action = false;
                            window.location.href = route(b.altformodel.altroute);
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
                        this.$inertia.post(Helpers.buildRoute(b.route, i), data)
                    }
                })
        },
        disableRow(el) {
            if (Helpers.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Click received" + "\n" +
                    "Disable: " + el + "\n" +
                    "Component: Td"
                )
            }
            let data = {
                id: el,
                _method: 'PUT',
            }
            this.$inertia.post(Helpers.buildRoute(this.model+'.disable', el), data, {
                preserveScroll: true,
                preserveState: false,
            })
        },
        enableRow(el) {
            if (Helpers.isDebugEnabled()) {
                console.log(
                    "[ProjectBuilder] DEBUG" + "\n" +
                    "Click received" + "\n" +
                    "Enable: " + el + "\n" +
                    "Component: Td"
                )
            }
            let data = {
                id: el,
                _method: 'PUT',
            }
            this.$inertia.post(Helpers.buildRoute(this.model+'.enable', el), data, {
                preserveScroll: true,
                preserveState: false,
            })
        },
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user)
        const locale = computed(() => usePage().props.value.locale)
        const model = computed(() => usePage().props.value.shared.model)
        return { user, locale, model }
    }
}
</script>

<style scoped>
@import "/public/css/flag-icons.css";
</style>

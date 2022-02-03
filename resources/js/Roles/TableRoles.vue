<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="roles"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="role in (roles.hasOwnProperty('data') ? roles.data : roles)" :item="role" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-pos="getRowPos(role)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="roles.hasOwnProperty('data') && roles.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="roles"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <RoleForm :data="data" :keyid="generateRandomTag" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import PbTable from "Pub/js/Projectbuilder/pbtable"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbTable,
    name: "TableRoles",
    props: {
        roles: Object,
    },
    components: {
        RoleForm,
    },
    setup(props) {
        const fields = new Table(props.showid, props.sort).buildTableFields(props.listing)
        const plocation = computed(() => usePage().props.value.shared.pagination.location)
        const hlocation = computed(() => usePage().props.value.shared.heading.location)
        const directory = 'roles'
        return { fields, directory, plocation, hlocation }
    },
}
</script>

<style scoped>

</style>

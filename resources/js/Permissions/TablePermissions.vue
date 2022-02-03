<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="permissions"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="permission in (permissions.hasOwnProperty('data') ? permissions.data : permissions)" :item="permission" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-pos="getRowPos(permission)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="permissions.hasOwnProperty('data') && permissions.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="permissions"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <PermissionForm :data="data" :keyid="generateRandomTag" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import PermissionForm from "@/Pages/Projectbuilder/Permissions/PermissionForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import PbTable from "Pub/js/Projectbuilder/pbtable"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
  extends: PbTable,
    name: "TablePermissions",
    props: {
        permissions: Object,
    },
    components: {
        PermissionForm,
    },
    setup(props) {
        const fields = new Table(props.showid, props.sort).buildTableFields(props.listing)
        const plocation = computed(() => usePage().props.value.shared.pagination.location)
        const hlocation = computed(() => usePage().props.value.shared.heading.location)
        const directory = 'permissions'
        return { fields, directory, plocation, hlocation }
    },
}
</script>

<style scoped>

</style>

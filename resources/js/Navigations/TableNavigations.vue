<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="navigations"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="navigation in (navigations.hasOwnProperty('data') ? navigations.data : navigations)" :item="navigation" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-id="navigation.id" :data-group="navigation.parent" :data-pos="getRowPos(navigation)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="navigations.hasOwnProperty('data') && navigations.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="navigations"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <NavigationForm :data="data" :keyid="generateRandomTag" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import PbTable from "Pub/js/Projectbuilder/pbtable"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbTable,
    name: "TableNavigations",
    props: {
        navigations: Object,
    },
    components: {
        NavigationForm,
    },
    setup(props) {
        const fields = new Table(props.showid, props.sort).buildTableFields(props.listing)
        const plocation = computed(() => usePage().props.value.shared.pagination.location)
        const hlocation = computed(() => usePage().props.value.shared.heading.location)
        const directory = 'navigations'
        return { fields, directory, plocation, hlocation }
    },
}
</script>

<style scoped>

</style>

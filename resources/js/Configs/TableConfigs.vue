<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="configs"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="config in (configs.hasOwnProperty('data') ? configs.data : configs)" :item="config" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-pos="getRowPos(config)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="configs.hasOwnProperty('data') && configs.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="configs"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <ConfigForm :data="data" :keyid="generateRandomTag" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import ConfigForm from "@/Pages/Projectbuilder/Configs/ConfigForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import PbTable from "Pub/js/Projectbuilder/pbtable"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbTable,
    name: "TableConfigs",
    props: {
        configs: Object,
    },
    components: {
        ConfigForm,
    },
    setup(props) {
        const fields = new Table(props.showid, props.sort).buildTableFields(props.listing)
        const plocation = computed(() => usePage().props.value.shared.pagination.location)
        const hlocation = computed(() => usePage().props.value.shared.heading.location)
        const directory = 'configs'
        return { fields, directory, plocation, hlocation }
    },
}
</script>

<style scoped>

</style>

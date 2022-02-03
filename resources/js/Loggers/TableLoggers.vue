<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="loggers"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
                <slot>
                    <TrBody v-for="logger in (loggers.hasOwnProperty('data') ? loggers.data : loggers)" :item="logger" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-pos="getRowPos(logger)" @clicked-edit-item="onItemClicked" />
                </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="loggers.hasOwnProperty('data') && loggers.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="loggers"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <LoggerForm :data="data" :keyid="generateRandomTag" :key="itemFormKey" :defaults="defaults" :required="required" />
        </div>
    </Container>
</template>

<script>
import LoggerForm from "@/Pages/Projectbuilder/Loggers/LoggerForm"
import { TableFields as Table } from "Pub/js/Projectbuilder/projectbuilder"
import PbTable from "Pub/js/Projectbuilder/pbtable"
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    extends: PbTable,
    name: "TableLoggers",
    props: {
        loggers: Object,
    },
    components: {
        LoggerForm,
    },
    setup(props) {
        const fields = new Table(props.showid, props.sort).buildTableFields(props.listing)
        const plocation = computed(() => usePage().props.value.shared.pagination.location)
        const hlocation = computed(() => usePage().props.value.shared.heading.location)
        const directory = 'loggers'
        return { fields, directory, plocation, hlocation }
    },
}
</script>

<style scoped>

</style>

<template>
    <Container>
        <slot>
            <Header>
                <slot>
                    <TrHead
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="elements"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Header>
            <Body :id="model+'-table-rows'">
            <slot>
                <TrBody v-for="element in (elements.hasOwnProperty('data') ? elements.data : elements)" :item="element" :fields="fields" :hiddenid="buildHiddenIdTag" :allowed="allowed" :data-id="(sort ? element.id : 0)" :data-group="(sort ? element.parent : 0)" :data-pos="getRowPos(element)" @clicked-edit-item="onItemClicked" />
            </slot>
            </Body>
            <Footer>
                <slot>
                    <TrFooter
                        v-if="elements.hasOwnProperty('data') && elements.data.length > 0"
                        :fields="fields"
                        :allowed="allowed"
                        :pagination="elements"
                        :model="model"
                        :plocation="plocation"
                        :hlocation="hlocation"
                    />
                </slot>
            </Footer>
        </slot>
        <div v-if="existsFormButton" :id="buildHiddenIdTag" class="infinite-hidden">
            <Form
                :data="data"
                :keyid="generateRandomTag"
                :key="itemFormKey"
                :defaults="defaults"
                :required="required"
                :title="title"
            />
        </div>
    </Container>
</template>

<script>
import PbTable from "Pub/js/Projectbuilder/pbtable"

export default {
    extends: PbTable,
}
</script>

<style scoped>

</style>

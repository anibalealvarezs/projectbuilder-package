<template>
    <Main>
        <slot>
            <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                <Button
                    v-if="allowed.create"
                    @click="loadForm"
                    type="button"
                >
                    <slot>Create {{ title }}</slot>
                </Button>
                <Table
                    :allowed="allowed"
                    :defaults="defaults"
                    :listing="listing"
                    :model="model"
                    :required="required"
                    :showid="showid"
                    :showpos="showpos"
                    :sort="sort"
                    :elements="elements"
                    :title="title"
                />
            </div>
        </slot>
        <div :id="buildHiddenIdTag" class="infinite-hidden">
            <Form
                :data="{}"
                :defaults="defaults"
                :required="required"
                :title="title"
            />
        </div>
    </Main>
</template>

<script>
import PbIndex from "Pub/js/Projectbuilder/pbindex"

export default {
    extends: PbIndex,
    name: "Index",
}
</script>

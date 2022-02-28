<template>
    <Main>
        <slot>
            <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                <Form
                    :data="setItem(element)"
                    :defaults="defaults"
                    :required="required"
                    :title="title"
                />
            </div>
        </slot>
    </Main>
</template>

<script>
import PbEdit from "Pub/js/Projectbuilder/pbedit"

export default {
    extends: PbEdit,
    name: "Edit",
}
</script>

<style scoped>

</style>

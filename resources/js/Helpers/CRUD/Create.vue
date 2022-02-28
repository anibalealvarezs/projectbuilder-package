<template>
    <Main>
        <slot>
            <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                <Form
                    :data="{}"
                    :defaults="defaults"
                    :required="required"
                    :title="title"
                />
            </div>
        </slot>
    </Main>
</template>

<script>
import PbCreate from "Pub/js/Projectbuilder/pbcreate"

export default {
    extends: PbCreate,
    name: "Create",
}
</script>

<style scoped>

</style>

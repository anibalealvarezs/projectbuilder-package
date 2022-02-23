<template>
    <Main>
        <slot>
            <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                <h2>{{ element.id }}. {{ element.name }}</h2>
            </div>
        </slot>
    </Main>
</template>

<script>
import PbShow from "Pub/js/Projectbuilder/pbshow"

export default {
    extends: PbShow,
}
</script>

<style scoped>

</style>

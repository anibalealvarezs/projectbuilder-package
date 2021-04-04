<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Main",
    setup() {
        let projectBuilder = document.createElement('script');
        projectBuilder.setAttribute('src',"/js/projectbuilder.js");
        document.head.appendChild(projectBuilder);
    },
}
</script>

<style scoped>

</style>

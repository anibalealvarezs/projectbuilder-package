<template>
    <form :action="getAction" method="POST" class="w-full max-w-lg">
        <input type="hidden" name="_method" :value="getMethod">
        <input type="hidden" name="_token" :value="csrf">
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- name -->
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-name">
                    Name
                </label>
                <input id="grid-name" name="name" type="text" :value="data.name" placeholder="Name" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">
            </div>
            <!-- key -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-key">
                    Key
                </label>
                <input id="grid-key" name="configkey" type="text" :value="data.configkey" placeholder="Key" :readonly="readonly" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">
            </div>
            <!-- value -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-value">
                    Value
                </label>
                <input id="grid-value" name="configvalue" type="text" :value="data.configvalue" placeholder="Value" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">
            </div>
            <!-- description -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-description">
                    Description
                </label>
                <textarea id="grid-description" name="description" placeholder="description" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">{{ data.description }}</textarea>
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-2 items-center justify-between">
            <!-- submit -->
            <div class="w-full md:w-1/2 px-3">
                <Button type="submit">{{ buttontext }}</Button>
            </div>
        </div>
    </form>
</template>

<script>
import Button from "@/Jetstream/Button"

export default {
    name: "ConfigForm",
    props: {
        data: Object,
    },
    components: {
        Button
    },
    data() {
        return {
            buttontext: (this.data.item ? "Save" : "Create")
        }
    },
    methods: {
        /* */
    },
    computed: {
        getAction() {
            return (this.data.item ? "/configs/"+ this.data.item : "/configs")
        },
        getMethod() {
            return (this.data.item ? "PUT" : "POST")
        },
        csrf() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        },
        readonly() {
            return this.data.hasOwnProperty('item')
        }
    }
}
</script>

<style scoped>

</style>

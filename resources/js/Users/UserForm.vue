<template>
    <form :action="getAction" method="POST" class="w-full max-w-lg">
        <input type="hidden" name="_method" :value="getMethod">
        <input type="hidden" name="_token" :value="csrf">
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- name -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-name">
                    Name
                </label>
                <input id="grid-name" name="name" type="text" :value="data.name" placeholder="Name" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">
            </div>
            <!-- email -->
            <div class="w-full md:w-1/2 px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-email">
                    Email
                </label>
                <input id="grid-email" name="email" type="text" :value="data.email" placeholder="email@email.com" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white">
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- password -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                    Password
                </label>
                <input id="grid-password" name="password" type="password" placeholder="******************" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
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
    name: "UserForm",
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
            return (this.data.item ? "/users/"+ this.data.item : "/users")
        },
        getMethod() {
            return (this.data.item ? "PUT" : "POST")
        },
        csrf() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    }
}
</script>

<style scoped>

</style>

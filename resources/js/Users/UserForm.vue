<template>
    <form @submit.prevent="submit" class="w-full max-w-lg">
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- name -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-name-' + keyid">
                    Name
                </label>
                <input
                    v-model="form.name"
                    :id="'grid-name-' + keyid"
                    name="name"
                    type="text"
                    placeholder="Name"
                    class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="readonly"
                    @mouseover="disableReadonly"
                >
            </div>
            <!-- email -->
            <div class="w-full md:w-1/2 px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-email-' + keyid">
                    Email
                </label>
                <input
                    v-model="form.email"
                    :id="'grid-email-' + keyid"
                    name="email"
                    type="text"
                    placeholder="email@email.com"
                    class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="readonly"
                    @mouseover="disableReadonly"
                >
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- password -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-password-' + keyid">
                    Password
                </label>
                <input
                    v-model="form.password"
                    :id="'grid-password-' + keyid"
                    name="password"
                    type="password"
                    placeholder="******************"
                    class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    readonly="readonly"
                    @mouseover="disableReadonly"
                >
            </div>
        </div>
        <SelectCountries :options="data" />
        <div class="flex flex-wrap -mx-3 mb-2 items-center justify-between">
            <!-- submit -->
            <div class="w-full md:w-1/2 px-3">
                <Button type="submit" :disabled="form.processing">{{ buttontext }}</Button>
            </div>
        </div>
    </form>
</template>

<script>
import Button from "@/Jetstream/Button"
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import SelectCountries from "@/Pages/Projectbuilder/Countries/SelectCountries";

export default {
    name: "UserForm",
    props: {
        data: Object,
        keyid: String
    },
    components: {
        SelectCountries,
        Button
    },
    data() {
        return {
            buttontext: (this.data.item ? "Save" : "Create")
        }
    },
    methods: {
        disableReadonly(event) {
            document.getElementById(event.toElement.id).readOnly = false
        }
    },
    computed: {
        readonly() {
            return this.data.hasOwnProperty('item')
        }
    },
    setup (props) {
        const form = reactive({
            name: props.data.name,
            email: props.data.email,
            password: ""
        })

        function submit() {
            if (props.data.hasOwnProperty('item')) {
                Inertia.put("/users/"+ props.data.item, form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            } else {
                Inertia.post("/users", form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            }
        }

        return { form, submit }
    }
}
</script>

<style scoped>

</style>

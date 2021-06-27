<template>
    <form @submit.prevent="submit" class="w-full max-w-lg">
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- name -->
            <div class="w-full px-3">
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
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- alias -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-alias-' + keyid">
                    Alias
                </label>
                <input
                    v-model="form.alias"
                    :id="'grid-alias-' + keyid"
                    name="alias"
                    type="text"
                    placeholder="Alias"
                    class="temp-readonly appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="readonly"
                    @mouseover="disableReadonly"
                >
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- roles -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-roles-' + keyid">
                    Roles
                </label>
                <select
                    v-model="form.roles"
                    :id="'grid-roles-' + keyid"
                    name="roles"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-auto leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Assign it to roles"
                    multiple="true"
                >
                    <option v-for="role in roles" :value="role.id">
                        {{ role.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-2 items-center justify-between">
            <!-- submit -->
            <div class="w-full px-3">
                <Button type="submit" :disabled="form.processing">{{ buttontext }}</Button>
            </div>
        </div>
    </form>
</template>

<script>
import Button from "@/Jetstream/Button"
import {computed, reactive} from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import {usePage} from "@inertiajs/inertia-vue3";
/* import {usePage} from "@inertiajs/inertia-vue3"; */

export default {
    name: "PermissionForm",
    props: {
        data: Object,
        keyid: String,
        currentroles: Array
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
            roles: props.currentroles,
            alias: props.data['alias'],
        })

        function submit() {
            if (props.data.hasOwnProperty('item')) {
                Inertia.put("/permissions/"+ props.data.item, form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            } else {
                Inertia.post("/permissions", form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            }
        }

        const roles = computed(() => usePage().props.value.shared.roles)

        return { form, submit, roles }
    }
}
</script>

<style scoped>

</style>

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
                    :readonly="!isEmptyField(form.name)"
                    :required="isRequired('name')"
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
                    :readonly="!isEmptyField(form.email)"
                    :required="isRequired('email')"
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
                    :readonly="!isEmptyField(form.password)"
                    :required="isRequired('password')"
                    @mouseover="disableReadonly"
                >
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- role -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-role-' + keyid">
                    Role
                </label>
                <select
                    v-model="form.roles"
                    :id="'grid-role-' + keyid"
                    name="role"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-auto leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select role"
                    multiple="true"
                    :required="isRequired('role')"
                >
                    <option v-for="role in roles" :value="role.name">
                        {{ role.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- country -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-country-' + keyid">
                    Country
                </label>
                <select
                    v-model="form.country"
                    :id="'grid-country-' + keyid"
                    name="country"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select country"
                    :required="isRequired('country')"
                >
                    <option v-for="country in countries" :value="country.id">
                        {{ country.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- language -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-language-' + keyid">
                    Language
                </label>
                <select
                    v-model="form.lang"
                    :id="'grid-language-' + keyid"
                    name="lang"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select language"
                    :required="isRequired('lang')"
                >
                    <option v-for="language in languages" :value="language.id" :data-iso="language.code">
                        {{ language.name }}
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
import {computed, reactive} from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import {usePage} from "@inertiajs/inertia-vue3"
import PbForm from "Pub/js/Projectbuilder/pbform"

export default {
    extends: PbForm,
    name: "UserForm",
    setup (props) {
        let rolesList = [];
        if (props.data.roles) {
            for (const [k, v] of Object.entries(props.data.roles)) {
                rolesList.push(v.name);
            }
        }

        const form = reactive({
            name: props.data.name,
            email: props.data.email,
            lang: (props.data.lang ? props.data.lang.id : (props.defaults.hasOwnProperty('lang') ? props.defaults.lang.id : 0)),
            country: (props.data.country ? props.data.country.id : (props.defaults.hasOwnProperty('country') ? props.defaults.country.id : 0)),
            roles: rolesList,
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

        const languages = computed(() => usePage().props.value.shared.languages)
        const countries = computed(() => usePage().props.value.shared.countries)
        const roles = computed(() => usePage().props.value.shared.roles)

        return { form, submit, languages, countries, roles }
    }
}
</script>

<style scoped>

</style>

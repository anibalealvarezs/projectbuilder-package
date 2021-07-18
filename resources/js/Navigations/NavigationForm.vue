<template>
    <form @submit.prevent="submit" class="w-full max-w-lg">
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- name -->
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-name-' + keyid">
                    Name
                </label>
                <input
                    v-model="form.name"
                    :id="'grid-name-' + keyid"
                    name="name"
                    type="text"
                    placeholder="Name"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="true"
                    :required="isRequired('name')"
                    @mouseover="disableReadonly"
                    @focus="disableReadonly"
                >
            </div>
            <!-- destiny -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-destiny-' + keyid">
                    Destiny
                </label>
                <textarea
                    v-model="form.destiny"
                    :id="'grid-destiny-' + keyid"
                    name="destiny"
                    placeholder="Destiny"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="true"
                    :required="isRequired('destiny')"
                    @mouseover="disableReadonly"
                    @focus="disableReadonly"
                >
                </textarea>
            </div>
            <!-- type -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-type-' + keyid">
                    Type
                </label>
                <select
                    v-model="form.type"
                    :id="'grid-type-' + keyid"
                    name="type"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select Type"
                    :required="isRequired('type')"
                >
                    <option v-for="type in ['route', 'path', 'custom']" :value="type">
                        {{ type }}
                    </option>
                </select>
            </div>
            <!-- parent -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-parent-' + keyid">
                    Parent
                </label>
                <select
                    v-model="form.parent"
                    :id="'grid-parent-' + keyid"
                    name="parent"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select parent"
                    :required="isRequired('parent')"
                >
                    <option value="0">[None]</option>
                    <option v-for="navigation in navigations" :value="navigation.id">
                        {{ navigation.id }}. {{ navigation.name }}
                    </option>
                </select>
            </div>
            <!-- permissions -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-permissions-' + keyid">
                    Permission
                </label>
                <select
                    v-model="form.permission"
                    :id="'grid-permissions-' + keyid"
                    name="permissions"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Assign permissions"
                    :required="isRequired('permissions')"
                >
                    <option v-for="permission in permissions" :value="permission.id">
                        {{ permission.alias }}
                    </option>
                </select>
            </div>
            <!-- status -->
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-status-' + keyid">
                    Status
                </label>
                <select
                    v-model="form.status"
                    :id="'grid-status-' + keyid"
                    name="status"
                    class="appearance-none w-full md:w-1/1 px-4 py-3 mb-3 block rounded bg-gray-200 text-gray-700 border border-gray-200 overflow-hidden leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    placeholder="Select status"
                    :required="isRequired('status')"
                >
                    <option value="1">
                        Enabled
                    </option>
                    <option value="0">
                        Disabled
                    </option>
                </select>
            </div>
            <!-- module -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-module-' + keyid">
                    Module
                </label>
                <input
                    v-model="form.module"
                    :id="'grid-module-' + keyid"
                    name="module"
                    type="text"
                    placeholder="Module"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="true"
                    :required="isRequired('module')"
                    @mouseover="disableReadonly"
                    @focus="disableReadonly"
                >
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
import {usePage} from "@inertiajs/inertia-vue3";
import { Helpers } from "Pub/js/Projectbuilder/projectbuilder"
import PbForm from "Pub/js/Projectbuilder/pbform"

export default {
    extends: PbForm,
    name: "NavigationForm",
    setup (props) {
        const form = reactive({
            name: props.data.name,
            type: props.data.type,
            parent: props.data.parent,
            destiny: props.data.destiny,
            module: props.data.module,
            status: props.data.status,
            permission: props.data.permission_id,
        })

        function submit() {
            if (props.data.hasOwnProperty('item')) {
                Inertia.put("/navigations/"+ props.data.item, form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            } else {
                Inertia.post("/navigations", form, {
                    preserveScroll: true,
                    preserveState: false,
                    onSuccess: () => Swal.close()
                })
            }
        }

        function removeIdsFromNavigations(navs) {
            let n = proxyNavsToObject(navs)
            let navigations = []
            let excluded = [props.data.item]
            excluded = excluded.concat(getNavChildren(n, props.data.item))
            for (const [k, v] of Object.entries(n)) {
                if (!excluded.includes(v.id)) {
                    navigations.push(v)
                }
            }
            return navigations
        }

        function proxyNavsToObject(navs) {
            let navigations = {}
            let objNavs = Helpers.proxyToObject(navs.value);
            for (const [k, v] of Object.entries(objNavs)) {
                navigations[k] = Helpers.proxyToObject(v)
            }
            return navigations
        }

        function getNavChildren(navs, el) {
            let arr = []
            for (const [k, v] of Object.entries(navs)) {
                if (v.parent == el) {
                    arr.push(v.id)
                    arr = arr.concat(getNavChildren(navs, v.id))
                }
            }
            return arr
        }

        let navigations = removeIdsFromNavigations(
            computed(() => usePage().props.value.shared.navigations.full)
        )
        const permissions = computed(() => usePage().props.value.shared.permissionsall)

        return { form, submit, navigations, permissions }
    }
}
</script>

<style scoped>

</style>

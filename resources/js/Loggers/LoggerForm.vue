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
            <!-- key -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-key-' + keyid">
                    Key
                </label>
                <input
                    v-model="form.loggerkey"
                    :id="'grid-key-' + keyid"
                    name="loggerkey"
                    type="text"
                    placeholder="Key"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    :readonly="!isEmptyField(data.loggerkey)"
                    :required="isRequired('loggerkey')"
                >
            </div>
            <!-- value -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-value-' + keyid">
                    Value
                </label>
                <input
                    v-model="form.loggervalue"
                    :id="'grid-value-' + keyid"
                    name="loggervalue"
                    type="text"
                    placeholder="Value"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="true"
                    :required="isRequired('loggervalue')"
                    @mouseover="disableReadonly"
                    @focus="disableReadonly"
                >
            </div>
            <!-- description -->
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" :for="'grid-description-' + keyid">
                    Description
                </label>
                <textarea
                    v-model="form.description"
                    :id="'grid-description-' + keyid"
                    name="description"
                    placeholder="description"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    readonly="true"
                    :required="isRequired('description')"
                    @mouseover="disableReadonly"
                    @focus="disableReadonly"
                >
                </textarea>
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
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Swal from "sweetalert2"
import PbForm from "Pub/js/Projectbuilder/pbform"

export default {
    extends: PbForm,
    name: "LoggerForm",
    setup (props) {
        const form = reactive({
            name: props.data.name,
            /* configkey: props.data.configkey,
            configvalue: props.data.configvalue,
            description: props.data.description */
        })

        function submit() {
            if (props.data.hasOwnProperty('item')) {
                Inertia.put("/loggers/"+ props.data.item, form, {
                    preserveScroll: true,
                    onSuccess: () => Swal.close()
                })
            } else {
                Inertia.post("/loggers", form, {
                    preserveScroll: true,
                    preserveState: false,
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

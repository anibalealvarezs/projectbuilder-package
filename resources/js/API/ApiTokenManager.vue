<template>
    <div>
        <!-- API Token alert -->
        <div v-if="!apiData.enabled">
            <div class="flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3" role="alert">
                <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/></svg>
                <p>API service is currently DISABLED.</p>
            </div>
        </div>
        <div v-if="!apiData.access">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">UNAUTHORIZED: </strong>
                <span class="block sm:inline">You don't have permission to connect throught API.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
            </div>
        </div>
        <div v-if="!apiData.enabled && apiData.access">
            <jet-section-border />
        </div>
        <!-- Generate API Token -->
        <jet-form-section v-if="apiData.access" @submitted="createApiToken">
            <template #title>
                Create API Token
            </template>

            <template #description>
                API tokens allow third-party services to authenticate with our application on your behalf.
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="name" value="Name" />
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="createApiTokenForm.name" autofocus />
                    <jet-input-error :message="createApiTokenForm.errors.name" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div class="col-span-6" v-if="availablePermissions.length > 0">
                    <jet-label for="permissions" value="Permissions" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions" :key="permission">
                            <label class="flex items-center">
                                <jet-checkbox :value="permission" v-model:checked="createApiTokenForm.permissions"/>
                                <span class="ml-2 text-sm text-gray-600">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <jet-action-message :on="createApiTokenForm.recentlySuccessful" class="mr-3">
                    Created.
                </jet-action-message>

                <jet-button :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">
                    Create
                </jet-button>
            </template>
        </jet-form-section>

        <div v-if="tokens.length > 0">
            <jet-section-border />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <jet-action-section>
                    <template #title>
                        Manage API Tokens
                    </template>

                    <template #description>
                        You may delete any of your existing tokens if they are no longer needed.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between" v-for="token in tokens" :key="token.id">
                                <div>
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-400" v-if="token.last_used_ago">
                                        Last used {{ token.last_used_ago }}
                                    </div>

                                    <button class="cursor-pointer ml-6 text-sm text-gray-400 underline"
                                        @click="manageApiTokenPermissions(token)"
                                        v-if="availablePermissions.length > 0"
                                    >
                                        Permissions
                                    </button>

                                    <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmApiTokenDeletion(token)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </jet-action-section>
            </div>
        </div>

        <!-- Token Value Modal -->
        <jet-dialog-modal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Please copy your new API token. For your security, it won't be shown again.
                </div>

                <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500" v-if="$page.props.jetstream.flash.token">
                    {{ $page.props.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="displayingToken = false">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <!-- API Token Permissions Modal -->
        <jet-dialog-modal :show="managingPermissionsFor" @close="managingPermissionsFor = null">
            <template #title>
                API Token Permissions
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions" :key="permission">
                        <label class="flex items-center">
                            <jet-checkbox :value="permission" v-model:checked="updateApiTokenForm.permissions"/>
                            <span class="ml-2 text-sm text-gray-600">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="managingPermissionsFor = null">
                    Cancel
                </jet-secondary-button>

                <jet-button class="ml-2" @click="updateApiToken" :class="{ 'opacity-25': updateApiTokenForm.processing }" :disabled="updateApiTokenForm.processing">
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>

        <!-- Delete Token Confirmation Modal -->
        <jet-confirmation-modal :show="apiTokenBeingDeleted" @close="apiTokenBeingDeleted = null">
            <template #title>
                Delete API Token
            </template>

            <template #content>
                Are you sure you would like to delete this API token?
            </template>

            <template #footer>
                <jet-secondary-button @click="apiTokenBeingDeleted = null">
                    Cancel
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click="deleteApiToken" :class="{ 'opacity-25': deleteApiTokenForm.processing }" :disabled="deleteApiTokenForm.processing">
                    Delete
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetActionSection from '@/Jetstream/ActionSection.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import {usePage} from "@inertiajs/inertia-vue3";

    export default defineComponent({
        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetCheckbox,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },

        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        data() {
            return {
                createApiTokenForm: this.$inertia.form({
                    name: '',
                    permissions: this.defaultPermissions,
                }),

                updateApiTokenForm: this.$inertia.form({
                    permissions: []
                }),

                deleteApiTokenForm: this.$inertia.form(),

                displayingToken: false,
                managingPermissionsFor: null,
                apiTokenBeingDeleted: null,

                apiData: usePage().props.value.shared.api_data,
            }
        },

        methods: {
            createApiToken() {
                this.createApiTokenForm.post(route('api-tokens.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.displayingToken = true
                        this.createApiTokenForm.reset()
                    }
                })
            },

            manageApiTokenPermissions(token) {
                this.updateApiTokenForm.permissions = token.abilities

                this.managingPermissionsFor = token
            },

            updateApiToken() {
                this.updateApiTokenForm.put(route('api-tokens.update', this.managingPermissionsFor), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.managingPermissionsFor = null),
                })
            },

            confirmApiTokenDeletion(token) {
                this.apiTokenBeingDeleted = token
            },

            deleteApiToken() {
                this.deleteApiTokenForm.delete(route('api-tokens.destroy', this.apiTokenBeingDeleted), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.apiTokenBeingDeleted = null),
                })
            },
        },
    })
</script>

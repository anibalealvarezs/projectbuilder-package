<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                API Tokens
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ApiTokenManager
                    :tokens="tokens"
                    :available-permissions="availablePermissions"
                    :default-permissions="defaultPermissions" />
            </div>
        </div>
    </AppLayout>
</template>

<script>
    import ApiTokenManager from '@/Pages/Projectbuilder/API/ApiTokenManager.vue'
    import PbIndex from "Pub/js/Projectbuilder/pbindex"

    export default{
        extends: PbIndex,
        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        components: {
            ApiTokenManager,
        },
    }
</script>

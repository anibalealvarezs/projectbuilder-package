<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permissions
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbpermissions }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <Button
                        @click="loadForm"
                        type="button"
                    >
                        <slot>Create Permission</slot>
                    </Button>
                    <TablePermissions :permissions="pbpermissions" />
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <PermissionForm :data="{}" :currentroles="currentroles" />
            </div>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import TablePermissions from "@/Pages/Projectbuilder/Permissions/TablePermissions"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"
    import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder";
    import Swal from "sweetalert2";
    import PermissionForm from "@/Pages/Projectbuilder/Permissions/PermissionForm"
    import {computed} from "vue";
    import {usePage} from "@inertiajs/inertia-vue3";

    export default {
        name: "Permissions",
        props: {
            pbpermissions: Object,
            currentroles: Object
        },
        components: {
            Button,
            AppLayout,
            TablePermissions,
            Main,
            PermissionForm
        },
        data() {
            return {
                hiddenid: 0
            }
        },
        methods: {
            loadForm() {
                let swalConfig = Table.buildSwalLoadFormConfig({text: "Create", formitem: "permission"})
                swalConfig['didOpen'] = () => {
                    Table.appendToSwal(this.hiddenid)
                }
                swalConfig['willClose'] = () => {
                    Table.removeFromSwal(this.hiddenid)
                }
                Swal.fire(swalConfig);
            }
        },
        computed: {
            buildHiddenId() {
                this.hiddenid = Table.buildHiddenId()
                return this.hiddenid
            }
        },
    }
</script>

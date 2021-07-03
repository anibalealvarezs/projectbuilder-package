<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbroles }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <Button
                        v-if="allowed.create"
                        @click="loadForm"
                        type="button"
                    >
                        <slot>Create Role</slot>
                    </Button>
                    <TableRoles :roles="pbroles" :allowed="allowed" />
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <RoleForm :data="{}" />
            </div>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import TableRoles from "@/Pages/Projectbuilder/Roles/TableRoles"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"
    import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder";
    import Swal from "sweetalert2";
    import RoleForm from "@/Pages/Projectbuilder/Roles/RoleForm"
    import {computed} from "vue";
    import {usePage} from "@inertiajs/inertia-vue3";

    export default {
        name: "Roles",
        props: {
            pbroles: Object,
        },
        components: {
            Button,
            AppLayout,
            TableRoles,
            Main,
            RoleForm
        },
        data() {
            return {
                hiddenid: 0
            }
        },
        methods: {
            loadForm() {
                let swalConfig = Table.buildSwalLoadFormConfig({text: "Create", formitem: "role"})
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
        setup (props) {
            const allowed = computed(() => usePage().props.value.shared.allowed)

            return { allowed }
        }
    }
</script>

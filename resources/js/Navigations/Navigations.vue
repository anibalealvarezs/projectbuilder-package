<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Navigations
            </h2>
        </template>

        <Main>
            <slot>
                <!-- <div>
                    {{ pbnavigations }}
                </div> -->
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <Button
                        @click="loadForm"
                        type="button"
                    >
                        <slot>Create Navigation</slot>
                    </Button>
                    <TableNavigations :navigations="pbnavigations"/>
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <NavigationForm :data="{}" />
            </div>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import TableNavigations from "@/Pages/Projectbuilder/Navigations/TableNavigations"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"
    import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder";
    import Swal from "sweetalert2";
    import NavigationForm from "@/Pages/Projectbuilder/Navigations/NavigationForm"

    export default {
        name: "Navigations",
        props: {
            pbnavigations: Object
        },
        components: {
            Button,
            AppLayout,
            TableNavigations,
            Main,
            NavigationForm
        },
        data() {
            return {
                hiddenid: 0
            }
        },
        methods: {
            loadForm() {
                let swalNavigation = Table.buildSwalLoadFormConfig({text: "Create", formitem: "navigation"})
                swalNavigation['didOpen'] = () => {
                    Table.appendToSwal(this.hiddenid)
                }
                swalNavigation['willClose'] = () => {
                    Table.removeFromSwal(this.hiddenid)
                }
                Swal.fire(swalNavigation);
            }
        },
        computed: {
            buildHiddenId() {
                this.hiddenid = Table.buildHiddenId()
                return this.hiddenid
            }
        }
    }
</script>

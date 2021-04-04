<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <Button
                        @click="loadForm"
                        type="button"
                    >
                        <slot>Create User</slot>
                    </Button>
                    <TableUsers :users="pbusers"/>
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <UserForm :data="{}" />
            </div>
        </Main>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import TableUsers from "@/Pages/Projectbuilder/Users/TableUsers"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"
    import {TableFields as Table} from "../../../../../public/js/Projectbuilder/projectbuilder";
    import Swal from "sweetalert2";
    import UserForm from "@/Pages/Projectbuilder/Users/UserForm"

    export default {
        props: {
            pbusers: Object
        },
        components: {
            Button,
            AppLayout,
            TableUsers,
            Main,
            UserForm
        },
        data() {
            return {
                hiddenid: 0
            }
        },
        methods: {
            loadForm() {
                let swalConfig = Table.buildSwalLoadFormConfig({text: "Create", formitem: "user"})
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
        }
    }
</script>

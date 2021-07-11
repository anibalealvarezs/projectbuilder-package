<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configs
            </h2>
        </template>

        <Main>
            <slot>
                <div class="p-12 sm:px-20 bg-white border-b border-gray-200">
                    <Button
                        v-if="allowed.create"
                        @click="loadForm"
                        type="button"
                    >
                        <slot>Create Config</slot>
                    </Button>
                    <TableConfigs :configs="pbconfigs" :allowed="allowed" :sort="sort" :showpos="showpos" :showid="showid" :model="model" />
                </div>
            </slot>
            <div :id="buildHiddenId" class="infinite-hidden">
                <ConfigForm :data="{}" />
            </div>
        </Main>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Pages/Projectbuilder/AppLayout'
    import TableConfigs from "@/Pages/Projectbuilder/Configs/TableConfigs"
    import Button from "@/Jetstream/Button"
    import Main from "@/Pages/Projectbuilder/Main"
    import {TableFields as Table} from "Pub/js/Projectbuilder/projectbuilder";
    import Swal from "sweetalert2";
    import ConfigForm from "@/Pages/Projectbuilder/Configs/ConfigForm"
    import {computed} from "vue";
    import {usePage} from "@inertiajs/inertia-vue3";

    export default {
        name: "Configs",
        props: {
            pbconfigs: Object
        },
        components: {
            Button,
            AppLayout,
            TableConfigs,
            Main,
            ConfigForm
        },
        data() {
            return {
                hiddenid: 0
            }
        },
        methods: {
            loadForm() {
                let swalConfig = Table.buildSwalLoadFormConfig({text: "Create", formitem: "config"})
                swalConfig['didOpen'] = () => {
                    Table.appendToSwal(this.hiddenid)
                }
                swalConfig['willClose'] = () => {
                    Table.removeFromSwal(this.hiddenid)
                }
                Swal.fire(swalConfig);
            },
            getRowPos(el) {
                return Table.getRowPos(this.sort, el)
            },
            getTablePositions(group) {
                let sort = [];
                document.querySelectorAll('#'+this.model+'-table-rows tr').forEach(function(value){
                    if (value.dataset.group == group) {
                        sort.push(value.dataset.id)
                    }
                })
                return sort
            },
        },
        computed: {
            buildHiddenId() {
                this.hiddenid = Table.buildHiddenId()
                return this.hiddenid
            }
        },
        setup (props) {
            const allowed = computed(() => usePage().props.value.shared.allowed)
            const sort = computed(() => usePage().props.value.shared.sort)
            const showpos = computed(() => usePage().props.value.shared.showpos)
            const showid = computed(() => usePage().props.value.shared.showid)
            const model = computed(() => usePage().props.value.shared.model)

            return { allowed, sort, model, showpos, showid }
        }
    }
</script>

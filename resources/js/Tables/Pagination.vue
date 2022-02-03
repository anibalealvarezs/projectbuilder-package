<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <PbDropdownLink v-if="pagination.prev_page_url"
               :href="buildPaginatedRoute(pagination.prev_page_url)"
               :cl="'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-white hover:bg-gray-700 hover:border-gray-700'"
            >
                Previous
            </PbDropdownLink>
            <PbDropdownLink v-if="pagination.next_page_url"
               :href="buildPaginatedRoute(pagination.next_page_url)"
               :cl="'ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-white hover:bg-gray-700 hover:border-gray-700'"
            >
                Next
            </PbDropdownLink>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ pagination.from }}</span>
                    to
                    <span class="font-medium">{{ pagination.to }}</span>
                    of
                    <span class="font-medium">{{ pagination.total }}</span>
                    results
                </p>
            </div>
            <div v-if="pagination.prev_page_url || pagination.next_page_url">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <div v-for="link in pagination.links">
                        <PbDropdownLink
                            v-if="link.label.includes('Previous') && link.url"
                            :href="buildPaginatedRoute(pagination.first_page_url)"
                            :cl="'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:text-white hover:bg-gray-700 hover:border-gray-700'"
                            :ccl="'float-left'"
                        >
                            <span class="sr-only">First</span>
                            <!-- Heroicon name: solid/chevron-left -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <!-- Heroicon name: solid/chevron-left -->
                            <svg class="h-5 w-5 -ml-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </PbDropdownLink>
                        <PbDropdownLink
                            v-if="link.label.includes('Previous') && link.url"
                           :href="buildPaginatedRoute(link.url) ?? '#'"
                            :cl="'relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:text-white hover:bg-gray-700 hover:border-gray-700'"
                            :ccl="'float-left'"
                        >
                            <span class="sr-only">Previous</span>
                            <!-- Heroicon name: solid/chevron-left -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </PbDropdownLink>
                        <PbDropdownLink
                            v-if="link.label.includes('Next') && link.url"
                            :href="buildPaginatedRoute(link.url) ?? '#'"
                            :cl="'relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:text-white hover:bg-gray-700 hover:border-gray-700'"
                            :ccl="'float-left'"
                        >
                            <span class="sr-only">Next</span>
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </PbDropdownLink>
                        <PbDropdownLink
                            v-if="link.label.includes('Next') && link.url"
                            :href="buildPaginatedRoute(pagination.last_page_url) ?? '#'"
                            :cl="'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:text-white hover:bg-gray-700 hover:border-gray-700'"
                            :ccl="'float-left'"
                        >
                            <span class="sr-only">Next</span>
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="h-5 w-5 -ml-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </PbDropdownLink>
                        <PbDropdownLink
                            v-if="helpers.stringToBoolean(link.active)"
                            :href="buildPaginatedRoute(link.url) ?? '#'"
                            aria-current="page"
                            :cl="'z-10 bg-gray-800 hover:bg-gray-700 active:bg-gray-900 border-gray-800 text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium'"
                        >
                            {{ link.label }}
                        </PbDropdownLink>
                        <PbDropdownLink
                            v-if="Number.isInteger(parseInt(link.label)) && !helpers.stringToBoolean(link.active)"
                            :href="buildPaginatedRoute(link.url) ?? '#'"
                            :cl="'bg-white border-gray-300 text-gray-500 hover:text-white hover:bg-gray-700 hover:border-gray-700 relative inline-flex items-center px-4 py-2 border text-sm font-medium'"
                        >
                            {{ link.label }}
                        </PbDropdownLink>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import {Helpers} from "Pub/js/Projectbuilder/projectbuilder";
import PbDropdownLink from "@/Pages/Projectbuilder/PbDropdownLink"

export default {
    name: "Pagination",
    components: {
        PbDropdownLink
    },
    data() {
        return {
            helpers: Helpers,
        }
    },
    props: {
        pagination: Object,
        model: String,
    },
    methods: {
        getInertiaId(link) {
            let array = link.split('page=');
            return [array[array.length - 1], this.pagination.per_page ?? 10];
        },
        buildPaginatedRoute(link) {
            return Helpers.buildRoute(this.model + '.index.paginated', this.getInertiaId(link))
        },
    }
}
</script>

<style scoped>

</style>

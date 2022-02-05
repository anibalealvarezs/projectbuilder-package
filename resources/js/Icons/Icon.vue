<template>
    <svg
        v-if="type === 'check'"
        :class="'h-5 w-5' + classesString"
        viewBox="0 0 24 24"
        :stroke="color ?? 'rgba(52, 211, 153, 1)'"
        fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        @click="clickAction"
    >
        <circle cx="12" cy="12" r="11"></circle>
        <polyline points="6 13 9 16 17 8"></polyline>
    </svg>
    <svg
        v-if="type === 'close'"
        :class="'h-5 w-5' + classesString"
        viewBox="0 0 78.67 78.67"
        :fill="color ?? '#ef4444'"
        @click="clickAction"
    >
        <path d="M55.18,25a3.12,3.12,0,0,0-4.41,0L39.33,36.41,27.9,25a3.12,3.12,0,0,0-4.41,4.41L34.93,40.82,23.49,52.25a3.12,3.12,0,0,0,4.41,4.41L39.34,45.23,50.77,56.66a3.11,3.11,0,0,0,4.4,0,3.11,3.11,0,0,0,0-4.41L43.74,40.82,55.18,29.38A3.11,3.11,0,0,0,55.18,25Z"></path>
        <path d="M39.34,0A39.34,39.34,0,1,0,78.67,39.34,39.39,39.39,0,0,0,39.34,0Zm0,72.44a33.11,33.11,0,1,1,33.09-33.1A33.15,33.15,0,0,1,39.34,72.44Z"></path>
    </svg>
    <svg
        v-if="type === 'sort'"
        :class="'h-5 w-5' + classesString"
        viewBox="0 0 20 20"
        :fill="color ?? 'currentColor'"
        xmlns="http://www.w3.org/2000/svg"
        @click="clickAction"
    >
        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" data-v-4ba735d4=""></path>
    </svg>
    <svg
        v-if="type === 'get-into'"
        :class="'h-5 w-5' + classesString"
        viewBox="0 0 24 24"
        :stroke="color ?? 'currentColor'"
        width="24" height="24" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
        @click="clickAction"
    >
        <path stroke="none" d="M0 0h24v24H0z" />
        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
        <path d="M7 12h14l-3 -3m0 6l3 -3" />
    </svg>
    <svg
        v-if="type === 'arrow-down'"
        :class="'h-3 w-3' + classesString"
        viewBox="0 0 32 32"
        :stroke="color ?? 'currentColor'"
    >
        <path d="M14.77,23.795L5.185,14.21c-0.879-0.879-0.879-2.317,0-3.195l0.8-0.801c0.877-0.878,2.316-0.878,3.194,0  l7.315,7.315l7.316-7.315c0.878-0.878,2.317-0.878,3.194,0l0.8,0.801c0.879,0.878,0.879,2.316,0,3.195l-9.587,9.585  c-0.471,0.472-1.104,0.682-1.723,0.647C15.875,24.477,15.243,24.267,14.77,23.795z" :fill="color ?? 'currentColor'"/>
    </svg>
    <svg
        v-if="type === 'arrow-up'"
        :class="'h-3 w-3' + classesString"
        viewBox="0 0 32 32"
        :stroke="color ?? 'currentColor'"
    >
        <path d="M18.221,7.206l9.585,9.585c0.879,0.879,0.879,2.317,0,3.195l-0.8,0.801c-0.877,0.878-2.316,0.878-3.194,0  l-7.315-7.315l-7.315,7.315c-0.878,0.878-2.317,0.878-3.194,0l-0.8-0.801c-0.879-0.878-0.879-2.316,0-3.195l9.587-9.585  c0.471-0.472,1.103-0.682,1.723-0.647C17.115,6.524,17.748,6.734,18.221,7.206z" :fill="color ?? 'currentColor'"/>
    </svg>
</template>

<script>
import Swal from "sweetalert2"

export default {
    name: "Icon",
    props: {
        type: String,
        classes: Array,
        color: String,
        clickable: Boolean,
        confirm: Boolean,
    },
    data() {
        return {
            classesString: this.buildClassesString(),
        };
    },
    emits: ["click"],
    methods: {
        buildClassesString() {
            let classes = this.classes ?? [];
            let array = [];
            classes.forEach((className) => {
                array.push(' ' + className);
            });
            if (this.clickable) {
                array.push(' cursor-pointer hover:opacity-50');
            }
            return array.join('');
        },
        clickAction() {
            if (this.clickable) {
                if (this.confirm) {
                    let swalConfig = {
                        title: 'Confirm',
                        text: 'Are you sure you want to proceed?',
                        icon: 'warning',
                        confirmButtonText: button.text
                    }
                    Swal.fire(swalConfig)
                        .then((result) => {
                            if (result['isConfirmed']){
                                this.$emit('click');
                            }
                        })
                } else {
                    this.$emit('click');
                }
            }
        },
    },
}
</script>

<style scoped>

</style>

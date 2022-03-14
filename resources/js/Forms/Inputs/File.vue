<template>
    <div
        :class="'mt-2 mb-2 w-full h-auto bg-center bg-cover bg-no-repeat bg-clip-padding p-6 border rounded' + (!disabled ? ' border-4 border-dashed' : '')"
        :style="'background-image: url('+fileUrl+')'"
        v-on:drop="dropHandler"
        v-on:dragover="dragOverHandler"
    >
        <div v-if="!disabled" class="bg-gray-200 border rounded opacity-50 as-input h-48">
            <input
                :id="'grid-'+ keyel +'-' + keyid"
                ref="file"
                type="file"
                class="appearance-none block w-full h-40 opacity-0 cursor-pointer h-full"
                :required="isRequired('file')"
                @change="processInputFile"
            >
            <span
                class="relative as-message px-2 py-1 bg-gray-800 text-gray-200 inline-block opacity-50 cursor-pointer"
                @click="triggerClick"
            > Drag & Drop / Click</span>
        </div>
        <div v-else class="h-40"></div>
    </div>
</template>

<script>
import PbInput from "Pub/js/Projectbuilder/pbinput"
export default {
    extends: PbInput,
    name: "File",
    props: {
        url: String,
    },
    data() {
        return {
            disabled: !!this.url,
            fileUrl: !!this.url ? this.url : null,
        }
    },
    methods: {
        updateFilePreview(file) {
            let url = "#";
            if (file && file.type.includes('image/')) {
                url = URL.createObjectURL(file)
            }
            this.fileUrl = url
        },
        processInputFile() {
            let file = this.$refs.file.files[0]
            this.updatePreviewAndEmit(file)
        },
        updatePreviewAndEmit(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.updateFilePreview(file)
            };
            reader.readAsDataURL(file)
            this.emitFileValue(file)
        },
        dropHandler(ev) {
            ev.preventDefault();
            if (this.disabled) {
                return
            }
            let file = ev.dataTransfer.files
            this.setFile(file);
            this.processInputFile()
        },
        dragOverHandler(ev) {
            ev.preventDefault();
        },
        setFile(files) {
            document.getElementById('grid-'+ this.keyel +'-' + this.keyid).files = files;
        },
        triggerClick(ev) {
            ev.target.previousElementSibling.click();
        },
    },
}
</script>

<style scoped>
.as-message {
    transform: translate(0, -50%);
    bottom: 50%;
}
.as-input:hover {
    opacity: 0.65;
}
</style>

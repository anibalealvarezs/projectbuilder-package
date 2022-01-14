<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div class="col-span-6 sm:col-span-4" v-if="$page.props.jetstream.managesProfilePhotos">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            ref="photo"
                            @change="updatePhotoPreview">

                <jet-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div class="mt-2" v-show="! photoPreview">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" v-show="photoPreview">
                    <span class="block rounded-full w-20 h-20"
                          :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <jet-secondary-button class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </jet-secondary-button>

                <jet-secondary-button type="button" class="mt-2" @click.prevent="deletePhoto" v-if="user.profile_photo_path">
                    Remove Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Name" />
                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- Roles -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="roles" value="Roles" />
                <jet-input id="roles" type="text" class="mt-1 block w-full bg-gray-200" v-model="rolesList" disabled />
            </div>

            <!-- Locale -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="language" value="Language" />
                <select
                    name="language"
                    id="language"
                    class="mt-1 block w-full rounded text-gray-700 border border-gray-300"
                    v-model="form.language"
                >
                    <option
                        v-for="language in languages"
                        :value="language.id"
                    >
                        {{ typeof language.name === 'object' ?
                            (language.name[locale.code] ?
                                language.name[locale.code] :
                                (language.name['en'] ?
                                    language.name['en'] :
                                    (language.name['es'] ?
                                        language.name['es'] :
                                        '[no translation] ['+locale.code+']'
                                    )
                                )
                            ) :
                            language.name
                        }}
                    </option>
                </select>
            </div>

            <!-- Country -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="country" value="Country" />
                <select
                    name="country"
                    id="country"
                    class="mt-1 block w-full rounded text-gray-700 border border-gray-300"
                    v-model="form.country"
                >
                    <option
                        v-for="country in countries"
                        :value="country.id"
                    >
                        {{ typeof country.name === 'object' ?
                            (country.name[locale.code] ?
                                country.name[locale.code] :
                                (country.name['en'] ?
                                    country.name['en'] :
                                    (country.name['es'] ?
                                        country.name['es'] :
                                        '[no translation] ['+locale.code+']'
                                    )
                                )
                        ) :
                        country.name
                        }}
                    </option>
                </select>
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import {computed} from "vue";
    import {usePage} from "@inertiajs/inertia-vue3";

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton
        },

        props: [
            'user',
            'roles',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'PUT',
                    name: this.user.name,
                    email: this.user.email,
                    photo: null,
                    language: this.user.language_id,
                    country: this.user.country_id,
                }),

                photoPreview: null,

                rolesList: this.roles.map(role => (typeof role.alias === 'object' ? (role.alias[this.locale.code] ? role.alias[this.locale.code] : '[no translation] ['+this.locale.code+']') : role.alias)).join(' | '),
            }
        },

        methods: {
            updateProfileInformation() {
                if (this.$refs.photo) {
                    this.form.photo = this.$refs.photo.files[0]
                }

                this.form.post(route('user-profile-information.update'), {
                    errorBag: 'updateProfileInformation',
                    preserveScroll: true
                });
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updatePhotoPreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.photo.files[0]);
            },

            deletePhoto() {
                this.$inertia.delete(route('current-user-photo.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => (this.photoPreview = null),
                });
            },
        },

        setup () {

            const locale = computed(() => usePage().props.value.locale)
            const languages = computed(() => usePage().props.value.shared.languages)
            const countries = computed(() => usePage().props.value.shared.countries)

            return { languages, countries, locale }
        }
    }
</script>

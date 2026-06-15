<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { PageProps } from '@/types';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const page = usePage<PageProps>();
const user = page.props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    slug: user.slug,
    timezone: user.timezone,
});

const publicUrlPreview = computed(() => `${window.location.origin}/u/${form.slug || '...'}`);
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account information, public URL and time zone.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="slug" value="Public URL slug" />
                <div class="relative mt-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">/u/</span>
                    <TextInput
                        id="slug"
                        type="text"
                        class="block w-full pl-9"
                        v-model="form.slug"
                        required
                    />
                </div>
                <p class="mt-1.5 truncate font-mono text-xs text-gray-500 dark:text-gray-500">
                    {{ publicUrlPreview }}
                </p>
                <InputError class="mt-2" :message="form.errors.slug" />
            </div>

            <div>
                <InputLabel for="timezone" value="Time zone" />
                <TextInput
                    id="timezone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.timezone"
                    required
                />
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-500">
                    All bookings show in this time zone unless the customer's browser overrides.
                </p>
                <InputError class="mt-2" :message="form.errors.timezone" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-emerald-600 dark:text-emerald-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

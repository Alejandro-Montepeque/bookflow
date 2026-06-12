<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const detectedTimezone = (() => {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    } catch {
        return 'UTC';
    }
})();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    timezone: detectedTimezone,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign up — BookFlow" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                Create your account
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Start accepting paid bookings in minutes. No credit card required.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Full name" />
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

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div>
                <InputLabel for="timezone" value="Time zone" />
                <div class="relative mt-1">
                    <TextInput
                        id="timezone"
                        type="text"
                        class="block w-full pl-10"
                        v-model="form.timezone"
                        required
                    />
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                    </svg>
                </div>
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Detected from your browser — you can change it later.
                </p>
                <InputError class="mt-2" :message="form.errors.timezone" />
            </div>

            <PrimaryButton
                class="w-full justify-center !py-3 !text-sm"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Creating account…' : 'Sign up' }}
            </PrimaryButton>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                Already have an account?
                <Link
                    :href="route('login')"
                    class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    Log in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>

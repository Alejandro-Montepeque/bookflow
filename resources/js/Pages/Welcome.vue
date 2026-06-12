<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="BookFlow — Effortless bookings for service businesses" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-emerald-50 text-gray-900 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 dark:text-gray-100">
        <!-- Nav -->
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <div class="flex items-center gap-2 text-xl font-bold">
                <span class="inline-block size-3 rounded-sm bg-indigo-500" />
                BookFlow
            </div>

            <nav v-if="canLogin" class="flex items-center gap-4 text-sm">
                <Link
                    :href="route('login')"
                    class="rounded-md px-3 py-1.5 transition hover:text-indigo-600 dark:hover:text-indigo-400"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-white shadow-sm transition hover:bg-indigo-500"
                >
                    Get started
                </Link>
            </nav>
        </header>

        <!-- Hero -->
        <main class="mx-auto max-w-5xl px-6 pb-24 pt-12 text-center lg:pt-20">
            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                Bookings without the
                <span class="bg-gradient-to-r from-indigo-500 to-emerald-500 bg-clip-text text-transparent">
                    back-and-forth
                </span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                BookFlow is the Calendly alternative for small service businesses. Publish your services,
                share your link, and accept paid bookings — all in one place.
            </p>

            <div class="mt-10 flex items-center justify-center gap-4">
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="rounded-lg bg-indigo-600 px-5 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    Start for free
                </Link>
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="rounded-lg border border-gray-300 bg-white px-5 py-3 text-base font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-600"
                >
                    I already have an account
                </Link>
            </div>

            <!-- Feature grid -->
            <div class="mt-24 grid gap-6 text-left sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-3 inline-flex size-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                        📅
                    </div>
                    <h3 class="text-lg font-semibold">Multi-service scheduling</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Offer different services (consultations, classes, sessions) with their own durations and prices.
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-3 inline-flex size-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                        💳
                    </div>
                    <h3 class="text-lg font-semibold">Built-in payments</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Get paid upfront with Stripe Checkout. No-shows are a thing of the past.
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-3 inline-flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300">
                        ✉️
                    </div>
                    <h3 class="text-lg font-semibold">Automated reminders</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Confirmation and reminder emails go out automatically — no more chasing.
                    </p>
                </div>
            </div>
        </main>

        <footer class="border-t border-gray-200 py-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-500">
            BookFlow — Built with Laravel, Inertia &amp; Vue 3
        </footer>
    </div>
</template>

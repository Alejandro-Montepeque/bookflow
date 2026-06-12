<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import { computed, ref } from 'vue';

const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user);

const bookingUrl = computed(() => {
    if (!user.value?.slug) return '';
    return `${window.location.origin}/u/${user.value.slug}`;
});

const copied = ref(false);
async function copyBookingUrl(): Promise<void> {
    try {
        await navigator.clipboard.writeText(bookingUrl.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        // no-op: clipboard might be blocked in some browsers
    }
}

interface StatCard {
    label: string;
    value: string;
    hint: string;
    tone: 'indigo' | 'emerald' | 'amber';
    icon: 'calendar' | 'briefcase' | 'wallet';
}

const stats: StatCard[] = [
    { label: 'Upcoming bookings', value: '—', hint: 'In the next 30 days', tone: 'indigo', icon: 'calendar' },
    { label: 'Active services', value: '—', hint: 'Published and bookable', tone: 'emerald', icon: 'briefcase' },
    { label: 'Revenue this month', value: '—', hint: 'Paid bookings', tone: 'amber', icon: 'wallet' },
];

const toneRing: Record<StatCard['tone'], string> = {
    indigo: 'bg-indigo-500/10 text-indigo-600 ring-1 ring-indigo-500/20 dark:bg-indigo-400/10 dark:text-indigo-300 dark:ring-indigo-400/30',
    emerald: 'bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/30',
    amber: 'bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/30',
};
</script>

<template>
    <Head title="Dashboard — BookFlow" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Welcome back, {{ user.name }}
                </p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Hero card with booking URL -->
                <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-emerald-500 p-6 text-white shadow-lg sm:p-8">
                    <div aria-hidden="true" class="pointer-events-none absolute -top-12 -right-12 size-48 rounded-full bg-white/10 blur-3xl" />
                    <div aria-hidden="true" class="pointer-events-none absolute bottom-0 left-1/3 size-40 rounded-full bg-white/5 blur-3xl" />

                    <div class="relative grid gap-6 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-white/70">
                                Your public booking page
                            </p>
                            <h1 class="mt-1 text-xl font-semibold sm:text-2xl">
                                Share your link to start taking bookings
                            </h1>
                            <div class="mt-4 flex items-center gap-2">
                                <div class="flex-1 truncate rounded-lg bg-white/15 px-4 py-2.5 font-mono text-sm backdrop-blur">
                                    {{ bookingUrl || 'Loading…' }}
                                </div>
                                <button
                                    type="button"
                                    @click="copyBookingUrl"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-gray-100"
                                >
                                    <svg v-if="!copied" viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" />
                                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" />
                                    </svg>
                                    <svg v-else viewBox="0 0 24 24" class="size-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6L9 17l-5-5" />
                                    </svg>
                                    {{ copied ? 'Copied' : 'Copy' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Stats grid -->
                <section>
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        At a glance
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ stat.label }}
                                    </p>
                                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                                        {{ stat.value }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                        {{ stat.hint }}
                                    </p>
                                </div>
                                <div :class="['grid size-10 place-items-center rounded-lg', toneRing[stat.tone]]">
                                    <svg v-if="stat.icon === 'calendar'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path d="M3 9h18M8 3v4M16 3v4" />
                                    </svg>
                                    <svg v-else-if="stat.icon === 'briefcase'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="7" width="18" height="13" rx="2" />
                                        <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 13h18" />
                                    </svg>
                                    <svg v-else viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 12V8a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2v-4" />
                                        <circle cx="17" cy="14" r="1.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Quick actions -->
                <section>
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Quick actions
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Link
                            :href="route('services.create')"
                            class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-700"
                        >
                            <div class="grid size-11 shrink-0 place-items-center rounded-lg bg-indigo-500/10 text-indigo-600 ring-1 ring-indigo-500/20 dark:bg-indigo-400/10 dark:text-indigo-300">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Create a service</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Set duration, price and availability</p>
                            </div>
                            <svg class="size-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-gray-600 dark:group-hover:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>

                        <Link
                            :href="route('services.index')"
                            class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-emerald-700"
                        >
                            <div class="grid size-11 shrink-0 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="7" width="18" height="13" rx="2" />
                                    <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 13h18" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Manage services</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Edit, deactivate, or remove existing services</p>
                            </div>
                            <svg class="size-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-gray-600 dark:group-hover:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </section>

                <!-- Empty state — recent activity -->
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white/50 p-10 text-center dark:border-gray-700 dark:bg-gray-900/40">
                    <div class="mx-auto grid size-14 place-items-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                        <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v6l4 2" />
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                    </div>
                    <h4 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">No bookings yet</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Publish your first service and share your link to start receiving bookings.
                    </p>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { formatDuration, formatPrice } from '@/utils/format';

interface PublicService {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    duration_minutes: number;
    price_cents: number;
    currency: string;
    color: string;
}

const props = defineProps<{
    provider: { name: string; slug: string; timezone: string };
    services: PublicService[];
}>();

const initials = props.provider.name
    .split(/\s+/)
    .map(w => w.charAt(0))
    .slice(0, 2)
    .join('')
    .toUpperCase();
</script>

<template>
    <Head :title="`${provider.name} on BookFlow`" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <header class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto flex max-w-4xl items-center gap-3 px-6 py-4">
                <span class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="3" />
                        <path d="M3 9h18" />
                        <path d="M7 14l3 3 7-7" />
                    </svg>
                </span>
                <p class="text-sm font-semibold">BookFlow</p>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-6 py-10">
            <!-- Provider hero -->
            <section class="rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-emerald-500 p-8 text-white shadow-lg">
                <div class="mx-auto grid size-16 place-items-center rounded-full bg-white/20 text-xl font-bold backdrop-blur">
                    {{ initials }}
                </div>
                <h1 class="mt-4 text-center text-3xl font-bold tracking-tight">{{ provider.name }}</h1>
                <p class="mt-1 text-center text-sm text-white/80">
                    Times shown in {{ provider.timezone }}
                </p>
            </section>

            <!-- Services list -->
            <section class="mt-8">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    Available services
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pick one to see the available time slots.
                </p>

                <div v-if="!services.length" class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ provider.name }} hasn't published any services yet. Check back soon!
                    </p>
                </div>

                <div v-else class="mt-4 grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="service in services"
                        :key="service.id"
                        :href="route('public.booking.show', [provider.slug, service.slug])"
                        class="group block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700"
                    >
                        <div class="h-1.5" :style="{ backgroundColor: service.color }" />
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ service.name }}</h3>
                                <span class="shrink-0 text-base font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatPrice(service.price_cents, service.currency) }}
                                </span>
                            </div>
                            <p v-if="service.description" class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ service.description }}
                            </p>
                            <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-500">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                    {{ formatDuration(service.duration_minutes) }}
                                </span>
                                <span class="inline-flex items-center gap-1 font-medium text-indigo-600 transition group-hover:gap-2 dark:text-indigo-400">
                                    Book
                                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14M12 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>
        </main>
    </div>
</template>

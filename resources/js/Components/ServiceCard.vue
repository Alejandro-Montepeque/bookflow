<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps, Service } from '@/types';
import { formatDuration, formatPrice } from '@/utils/format';

interface ServiceWithCounts extends Service {
    bookings_count?: number;
    availability_rules_count?: number;
}

const props = defineProps<{
    service: ServiceWithCounts;
}>();

const emit = defineEmits<{
    (e: 'request-delete', service: ServiceWithCounts): void;
}>();

const page = usePage<PageProps>();

function copyPublicUrl(): void {
    const userSlug = page.props.auth.user?.slug;
    if (!userSlug) return;
    const url = `${window.location.origin}/u/${userSlug}/${props.service.slug}`;
    void navigator.clipboard.writeText(url);
}
</script>

<template>
    <article class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700">
        <!-- Colored accent strip -->
        <div class="h-1.5" :style="{ backgroundColor: service.color }" />

        <div class="p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="truncate text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ service.name }}
                        </h3>
                        <span
                            v-if="!service.is_active"
                            class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                        >
                            Inactive
                        </span>
                    </div>
                    <p
                        v-if="service.description"
                        class="mt-1 line-clamp-2 text-sm text-gray-600 dark:text-gray-400"
                    >
                        {{ service.description }}
                    </p>
                </div>

                <div class="text-right shrink-0">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ formatPrice(service.price_cents, service.currency) }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-500">
                        {{ formatDuration(service.duration_minutes) }}
                    </p>
                </div>
            </div>

            <!-- Stats row -->
            <dl class="mt-4 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M3 9h18M8 3v4M16 3v4" />
                    </svg>
                    <span>{{ service.bookings_count ?? 0 }} bookings</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    <span>{{ service.availability_rules_count ?? 0 }} rules</span>
                </div>
            </dl>

            <!-- Actions -->
            <div class="mt-5 flex items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                <div class="flex items-center gap-1 text-xs">
                    <button
                        type="button"
                        @click="copyPublicUrl"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                        title="Copy public booking URL"
                    >
                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" />
                        </svg>
                        Copy link
                    </button>
                </div>

                <div class="flex items-center gap-1">
                    <Link
                        :href="route('services.edit', service.id)"
                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Edit
                    </Link>
                    <button
                        type="button"
                        @click="emit('request-delete', service)"
                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                    >
                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </article>
</template>

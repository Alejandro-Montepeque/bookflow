<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { PageProps } from '@/types';
import { formatDateTime, formatDuration } from '@/utils/format';

interface BookingPayload {
    id: number;
    customer_name: string;
    customer_email: string;
    starts_at: string;
    ends_at: string;
    status: string;
    timezone: string;
    cancellation_token: string;
    notes: string | null;
    is_cancellable: boolean;
}

const props = defineProps<{
    booking: BookingPayload;
    service: { name: string; color: string; duration_minutes: number };
    provider: { name: string };
}>();

const page = usePage<PageProps>();
const flashSuccess = computed(() => page.props.flash?.success);

const isCancelled = computed(() => props.booking.status === 'cancelled');
const isPast = computed(() => new Date(props.booking.starts_at).getTime() < Date.now());

const processing = ref(false);

function confirmCancel(): void {
    processing.value = true;
    router.post(
        route('public.booking.cancel.store', props.booking.cancellation_token),
        {},
        {
            onFinish: () => (processing.value = false),
            preserveScroll: true,
        }
    );
}
</script>

<template>
    <Head title="Cancel booking — BookFlow" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <main class="mx-auto flex min-h-screen max-w-2xl items-center px-6 py-12">
            <div class="w-full overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
                <!-- Header -->
                <div
                    class="p-8 text-center"
                    :class="isCancelled
                        ? 'bg-gray-100 dark:bg-gray-950'
                        : ''"
                    :style="!isCancelled ? { background: `linear-gradient(135deg, ${service.color}, ${service.color}cc)` } : {}"
                >
                    <div
                        class="mx-auto grid size-14 place-items-center rounded-full"
                        :class="isCancelled
                            ? 'bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-400'
                            : 'bg-white/20 text-white'"
                    >
                        <svg v-if="isCancelled" viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <h1
                        class="mt-4 text-2xl font-bold"
                        :class="isCancelled ? 'text-gray-900 dark:text-gray-100' : 'text-white'"
                    >
                        {{ isCancelled ? 'Booking cancelled' : 'Cancel your booking?' }}
                    </h1>
                    <p
                        class="mt-1 text-sm"
                        :class="isCancelled ? 'text-gray-600 dark:text-gray-400' : 'text-white/90'"
                    >
                        <template v-if="isCancelled">
                            We've notified {{ provider.name }}.
                        </template>
                        <template v-else-if="isPast">
                            This booking has already happened.
                        </template>
                        <template v-else>
                            Let {{ provider.name }} know you can't make it.
                        </template>
                    </p>
                </div>

                <!-- Body -->
                <div class="space-y-6 p-8">
                    <div
                        v-if="flashSuccess"
                        class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300"
                    >
                        <svg viewBox="0 0 24 24" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                        {{ flashSuccess }}
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Service</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ service.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">With</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ provider.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">When</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ formatDateTime(booking.starts_at) }}
                            </dd>
                            <dd class="text-xs text-gray-500 dark:text-gray-500">{{ booking.timezone }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Duration</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ formatDuration(service.duration_minutes) }}
                            </dd>
                        </div>
                    </dl>

                    <!-- Action area -->
                    <div
                        v-if="booking.is_cancellable"
                        class="flex flex-col items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end"
                    >
                        <button
                            type="button"
                            @click="confirmCancel"
                            :disabled="processing"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 disabled:opacity-50 sm:w-auto"
                        >
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12" />
                            </svg>
                            {{ processing ? 'Cancelling…' : 'Cancel my booking' }}
                        </button>
                    </div>

                    <div
                        v-else-if="isCancelled"
                        class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-400"
                    >
                        This booking has been cancelled.
                    </div>

                    <div
                        v-else
                        class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-900/20 dark:text-amber-300"
                    >
                        This booking can no longer be cancelled.
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

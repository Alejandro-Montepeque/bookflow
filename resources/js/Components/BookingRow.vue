<script setup lang="ts">
import type { Booking, Payment, Service } from '@/types';
import {
    bookingStatusClasses,
    formatDateTime,
    formatDuration,
    formatPrice,
} from '@/utils/format';

interface BookingWithService extends Omit<Booking, 'service' | 'payment'> {
    service?: Pick<Service, 'id' | 'name' | 'color' | 'duration_minutes'>;
    payment?: Pick<Payment, 'id' | 'status' | 'amount_cents' | 'currency'> | null;
}

const props = defineProps<{
    booking: BookingWithService;
    canCancel: boolean;
    canComplete: boolean;
}>();

const emit = defineEmits<{
    (e: 'cancel', booking: BookingWithService): void;
    (e: 'complete', booking: BookingWithService): void;
}>();

function statusLabel(status: BookingWithService['status']): string {
    return status.charAt(0).toUpperCase() + status.slice(1);
}
</script>

<template>
    <article class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
        <!-- Left: service + customer + status -->
        <div class="flex items-start gap-3 min-w-0 flex-1">
            <span
                class="mt-1 inline-block size-3 shrink-0 rounded-full"
                :style="{ backgroundColor: booking.service?.color ?? '#6366f1' }"
                :title="booking.service?.name"
            />
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <h4 class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ booking.service?.name ?? 'Service' }}
                    </h4>
                    <span
                        :class="['inline-flex shrink-0 rounded-full px-2 py-0.5 text-xs font-medium', bookingStatusClasses(booking.status)]"
                    >
                        {{ statusLabel(booking.status) }}
                    </span>
                </div>
                <p class="mt-1 truncate text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">{{ booking.customer_name }}</span>
                    <span class="text-gray-400 dark:text-gray-500">· {{ booking.customer_email }}</span>
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                    {{ formatDateTime(booking.starts_at) }}
                    <span class="text-gray-300 dark:text-gray-700">·</span>
                    {{ booking.service ? formatDuration(booking.service.duration_minutes) : '' }}
                    <span v-if="booking.payment?.status === 'succeeded'" class="ml-2 inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg viewBox="0 0 24 24" class="size-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                        Paid {{ formatPrice(booking.payment.amount_cents, booking.payment.currency) }}
                    </span>
                </p>
                <p
                    v-if="booking.notes"
                    class="mt-2 line-clamp-2 rounded-md bg-gray-50 px-2.5 py-1.5 text-xs text-gray-600 dark:bg-gray-950/50 dark:text-gray-400"
                >
                    {{ booking.notes }}
                </p>
            </div>
        </div>

        <!-- Right: actions -->
        <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
            <button
                v-if="canComplete"
                type="button"
                @click="emit('complete', booking)"
                class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800/50 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
            >
                <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
                Mark completed
            </button>
            <button
                v-if="canCancel"
                type="button"
                @click="emit('cancel', booking)"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-red-50 hover:text-red-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            >
                <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6L6 18M6 6l12 12" />
                </svg>
                Cancel
            </button>
        </div>
    </article>
</template>

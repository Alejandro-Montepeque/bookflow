<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
}

const props = defineProps<{
    booking: BookingPayload;
    service: { name: string; color: string; duration_minutes: number };
    provider: { name: string };
}>();

const cancelUrl = computed(() => `${window.location.origin}/cancel/${props.booking.cancellation_token}`);

const copied = ref(false);
async function copyCancelLink(): Promise<void> {
    try {
        await navigator.clipboard.writeText(cancelUrl.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        // no-op
    }
}
</script>

<template>
    <Head title="Booking confirmed — BookFlow" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <main class="mx-auto flex min-h-screen max-w-2xl items-center px-6 py-12">
            <div class="w-full overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
                <div class="p-8 text-center" :style="{ background: `linear-gradient(135deg, ${service.color}, ${service.color}cc)` }">
                    <div class="mx-auto grid size-14 place-items-center rounded-full bg-white/20 text-white">
                        <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-white">Booking confirmed</h1>
                    <p class="mt-1 text-sm text-white/90">A confirmation email is on its way to you.</p>
                </div>

                <div class="space-y-6 p-8">
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

                    <div v-if="booking.notes" class="rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-950/50">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Your notes</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ booking.notes }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-950/50">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Need to cancel?</p>
                        <div class="mt-2 flex items-center gap-2">
                            <code class="flex-1 truncate rounded bg-white px-2 py-1.5 font-mono text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                {{ cancelUrl }}
                            </code>
                            <button
                                type="button"
                                @click="copyCancelLink"
                                class="rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                {{ copied ? 'Copied' : 'Copy' }}
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                            Keep this link safe — you can cancel your booking anytime by visiting it.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BookingRow from '@/Components/BookingRow.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { Booking, PageProps, Payment, Service } from '@/types';

type Tab = 'upcoming' | 'past' | 'cancelled';

interface BookingWithService extends Booking {
    service?: Pick<Service, 'id' | 'name' | 'color' | 'duration_minutes'>;
    payment?: Pick<Payment, 'id' | 'status' | 'amount_cents' | 'currency'> | null;
}

interface ServiceOption {
    id: number;
    name: string;
    color: string;
}

const props = defineProps<{
    bookings: BookingWithService[];
    tab: Tab;
    counts: Record<Tab, number>;
    services: ServiceOption[];
    filters: { service_id: number | null };
}>();

const page = usePage<PageProps>();
const flashSuccess = computed(() => page.props.flash?.success);

const tabs: { value: Tab; label: string }[] = [
    { value: 'upcoming', label: 'Upcoming' },
    { value: 'past', label: 'Past' },
    { value: 'cancelled', label: 'Cancelled' },
];

const selectedServiceId = ref<number | null>(props.filters.service_id);

function changeTab(tab: Tab): void {
    router.get(
        route('bookings.index'),
        {
            tab,
            ...(selectedServiceId.value ? { service_id: selectedServiceId.value } : {}),
        },
        { preserveScroll: true, preserveState: true }
    );
}

function changeService(): void {
    router.get(
        route('bookings.index'),
        {
            tab: props.tab,
            ...(selectedServiceId.value ? { service_id: selectedServiceId.value } : {}),
        },
        { preserveScroll: true, preserveState: true }
    );
}

// Action confirmations
const pendingCancel = ref<BookingWithService | null>(null);
const pendingComplete = ref<BookingWithService | null>(null);
const actionProcessing = ref(false);

function requestCancel(booking: BookingWithService): void {
    pendingCancel.value = booking;
}
function requestComplete(booking: BookingWithService): void {
    pendingComplete.value = booking;
}

function performCancel(): void {
    if (!pendingCancel.value) return;
    actionProcessing.value = true;
    router.patch(route('bookings.cancel', pendingCancel.value.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            actionProcessing.value = false;
            pendingCancel.value = null;
        },
    });
}

function performComplete(): void {
    if (!pendingComplete.value) return;
    actionProcessing.value = true;
    router.patch(route('bookings.complete', pendingComplete.value.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            actionProcessing.value = false;
            pendingComplete.value = null;
        },
    });
}

function canCancelBooking(booking: BookingWithService): boolean {
    return ['pending', 'confirmed'].includes(booking.status);
}

function canCompleteBooking(booking: BookingWithService): boolean {
    return booking.status === 'confirmed';
}

const emptyMessage = computed(() => {
    switch (props.tab) {
        case 'upcoming': return 'No upcoming bookings. Share your booking link to start receiving them.';
        case 'past': return 'No past bookings yet.';
        case 'cancelled': return 'No cancelled bookings.';
    }
});
</script>

<template>
    <Head title="Bookings — BookFlow" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        Bookings
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage upcoming appointments, mark completed sessions, or cancel.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Flash -->
                <div
                    v-if="flashSuccess"
                    class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300"
                >
                    <svg viewBox="0 0 24 24" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                    {{ flashSuccess }}
                </div>

                <!-- Tabs + service filter -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <nav class="-mb-px inline-flex gap-1 rounded-lg bg-white p-1 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
                        <button
                            v-for="t in tabs"
                            :key="t.value"
                            type="button"
                            @click="changeTab(t.value)"
                            class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition"
                            :class="t.value === tab
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'"
                        >
                            {{ t.label }}
                            <span
                                class="rounded-full px-1.5 py-0.5 text-xs font-semibold"
                                :class="t.value === tab
                                    ? 'bg-white/20 text-white'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                            >
                                {{ counts[t.value] }}
                            </span>
                        </button>
                    </nav>

                    <select
                        v-if="services.length > 0"
                        v-model.number="selectedServiceId"
                        @change="changeService"
                        class="rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                    >
                        <option :value="null">All services</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <!-- Empty state -->
                <div
                    v-if="!bookings.length"
                    class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900/40"
                >
                    <div class="mx-auto grid size-14 place-items-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M3 9h18M8 3v4M16 3v4" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Nothing here yet
                    </h3>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">
                        {{ emptyMessage }}
                    </p>
                </div>

                <!-- List -->
                <div v-else class="space-y-3">
                    <BookingRow
                        v-for="booking in bookings"
                        :key="booking.id"
                        :booking="booking"
                        :can-cancel="canCancelBooking(booking)"
                        :can-complete="canCompleteBooking(booking)"
                        @cancel="requestCancel"
                        @complete="requestComplete"
                    />
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="pendingCancel !== null"
            title="Cancel this booking?"
            :message="pendingCancel ? `${pendingCancel.customer_name} will no longer hold this slot. We'll mark it as cancelled in your records.` : ''"
            confirm-label="Yes, cancel booking"
            variant="danger"
            :processing="actionProcessing"
            @close="pendingCancel = null"
            @confirm="performCancel"
        />

        <ConfirmDialog
            :show="pendingComplete !== null"
            title="Mark booking as completed?"
            :message="pendingComplete ? `Mark the session with ${pendingComplete.customer_name} as completed.` : ''"
            confirm-label="Yes, mark completed"
            variant="primary"
            :processing="actionProcessing"
            @close="pendingComplete = null"
            @confirm="performComplete"
        />
    </AuthenticatedLayout>
</template>

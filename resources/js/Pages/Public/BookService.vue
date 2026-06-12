<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { formatDuration, formatPrice } from '@/utils/format';

interface Provider {
    name: string;
    slug: string;
    timezone: string;
}

interface ServiceSummary {
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
    provider: Provider;
    service: ServiceSummary;
    slots: Record<string, string[]>;
    range: { from: string; to: string };
}>();

const availableDates = computed(() => Object.keys(props.slots).sort());

// Pre-select the first available date, if any.
const selectedDate = ref<string | null>(availableDates.value[0] ?? null);
const selectedTime = ref<string | null>(null);

const timesForSelectedDate = computed(() =>
    selectedDate.value ? props.slots[selectedDate.value] ?? [] : []
);

function selectDate(date: string): void {
    selectedDate.value = date;
    selectedTime.value = null;
}

function selectTime(time: string): void {
    selectedTime.value = time;
    // Sync to form payload as an ISO datetime in the provider's timezone.
    if (selectedDate.value) {
        const isoLocal = `${selectedDate.value}T${time}:00`;
        form.starts_at = isoLocal;
    }
}

const form = useForm({
    customer_name: '',
    customer_email: '',
    starts_at: '',
    notes: '',
});

function submit(): void {
    form.post(route('public.booking.store', [props.provider.slug, props.service.slug]));
}

function prettyDate(iso: string): string {
    const [y, m, d] = iso.split('-').map(Number);
    return new Date(y, m - 1, d).toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
}

const grid = computed(() => {
    const dates: { key: string; label: string; weekday: string; hasSlots: boolean }[] = [];
    const start = new Date(props.range.from);
    const end = new Date(props.range.to);
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
        const key = d.toISOString().slice(0, 10);
        dates.push({
            key,
            label: String(d.getDate()),
            weekday: d.toLocaleDateString(undefined, { weekday: 'short' }),
            hasSlots: !!props.slots[key]?.length,
        });
    }
    return dates;
});
</script>

<template>
    <Head :title="`${service.name} — ${provider.name}`" />

    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <header class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto flex max-w-5xl items-center gap-3 px-6 py-4">
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

        <main class="mx-auto max-w-5xl px-6 py-10">
            <!-- Provider + service info -->
            <section class="grid gap-6 sm:grid-cols-[1fr_2fr]">
                <div class="rounded-2xl p-6 text-white shadow-lg" :style="{ background: `linear-gradient(135deg, ${service.color}, ${service.color}cc)` }">
                    <div class="grid size-14 place-items-center rounded-full bg-white/20 text-lg font-bold">
                        {{ provider.name.charAt(0) }}
                    </div>
                    <p class="mt-4 text-xs uppercase tracking-wider text-white/70">Provider</p>
                    <h2 class="text-lg font-semibold">{{ provider.name }}</h2>

                    <div class="mt-6 border-t border-white/20 pt-4">
                        <h3 class="text-sm font-medium text-white/80">{{ service.name }}</h3>
                        <p v-if="service.description" class="mt-1 text-xs leading-relaxed text-white/80">
                            {{ service.description }}
                        </p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-2xl font-bold">{{ formatPrice(service.price_cents, service.currency) }}</span>
                            <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-medium">
                                {{ formatDuration(service.duration_minutes) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Calendar + time slots -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold">Pick a date &amp; time</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        All times shown in {{ provider.timezone }}.
                    </p>

                    <div v-if="!availableDates.length" class="mt-6 rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        No availability in the next 30 days. Check back soon!
                    </div>

                    <template v-else>
                        <!-- Date grid -->
                        <div class="mt-5 grid grid-cols-7 gap-1.5">
                            <button
                                v-for="d in grid"
                                :key="d.key"
                                type="button"
                                :disabled="!d.hasSlots"
                                @click="selectDate(d.key)"
                                class="flex aspect-square flex-col items-center justify-center rounded-lg text-xs transition"
                                :class="[
                                    d.key === selectedDate
                                        ? 'bg-indigo-600 font-semibold text-white shadow'
                                        : d.hasSlots
                                            ? 'border border-gray-200 text-gray-700 hover:border-indigo-500 hover:text-indigo-600 dark:border-gray-700 dark:text-gray-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300'
                                            : 'border border-transparent text-gray-300 dark:text-gray-700'
                                ]"
                            >
                                <span class="text-[10px] uppercase tracking-wide opacity-70">{{ d.weekday.slice(0,3) }}</span>
                                <span class="text-sm font-medium">{{ d.label }}</span>
                            </button>
                        </div>

                        <!-- Time slots -->
                        <div v-if="selectedDate" class="mt-6">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ prettyDate(selectedDate) }}
                            </p>
                            <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4">
                                <button
                                    v-for="time in timesForSelectedDate"
                                    :key="time"
                                    type="button"
                                    @click="selectTime(time)"
                                    class="rounded-md border px-3 py-2 text-sm transition"
                                    :class="time === selectedTime
                                        ? 'border-indigo-600 bg-indigo-600 font-semibold text-white'
                                        : 'border-gray-200 text-gray-700 hover:border-indigo-500 hover:text-indigo-600 dark:border-gray-700 dark:text-gray-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300'"
                                >
                                    {{ time }}
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- Booking form (revealed after slot is picked) -->
            <section
                v-if="selectedTime"
                class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <h3 class="text-base font-semibold">Your details</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    You'll receive a confirmation email with a link to cancel if needed.
                </p>

                <form @submit.prevent="submit" class="mt-5 space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <InputLabel for="name" value="Full name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.customer_name"
                                required
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.customer_name" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.customer_email"
                                required
                                autocomplete="email"
                            />
                            <InputError class="mt-2" :message="form.errors.customer_email" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="notes" value="Notes (optional)" />
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                            placeholder="Anything the provider should know about?"
                        />
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>

                    <InputError class="mt-2" :message="form.errors.starts_at" />

                    <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ selectedDate ? prettyDate(selectedDate) : '' }}</span>
                            at <span class="font-medium">{{ selectedTime }}</span>
                        </p>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Booking…' : 'Confirm booking' }}
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>

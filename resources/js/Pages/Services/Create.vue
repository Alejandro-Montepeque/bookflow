<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const COLOR_SWATCHES = [
    '#6366f1', '#10b981', '#f59e0b', '#ec4899',
    '#3b82f6', '#8b5cf6', '#ef4444', '#14b8a6',
] as const;

const SUPPORTED_CURRENCIES = ['USD', 'EUR', 'GBP', 'MXN', 'COP', 'ARS'] as const;

const form = useForm({
    name: '',
    description: '',
    duration_minutes: 30,
    price_cents: 5000,
    currency: 'USD' as (typeof SUPPORTED_CURRENCIES)[number],
    color: '#6366f1',
    buffer_minutes: 0,
    is_active: true,
});

// User types dollars in the price input; we keep cents in the form payload.
const priceDollars = computed({
    get: () => (form.price_cents / 100).toFixed(2),
    set: (v: string) => {
        const parsed = Math.round(parseFloat(v) * 100);
        form.price_cents = Number.isFinite(parsed) ? Math.max(0, parsed) : 0;
    },
});

function submit(): void {
    form.post(route('services.store'));
}
</script>

<template>
    <Head title="New service — BookFlow" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('services.index')"
                    class="grid size-9 place-items-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800"
                    aria-label="Back to services"
                >
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        New service
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Configure what customers will book.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <form @submit.prevent="submit" class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Basic info card -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Basic information
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Shown to customers on your public booking page.
                    </p>

                    <div class="mt-5 space-y-5">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="e.g. 30-min Strategy Call"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                                placeholder="What customers can expect from this service."
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                    </div>
                </section>

                <!-- Pricing + duration card -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Pricing &amp; duration
                    </h3>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <InputLabel for="price" value="Price" />
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                                <TextInput
                                    id="price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="block w-full pl-7"
                                    v-model="priceDollars"
                                    required
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.price_cents" />
                        </div>

                        <div>
                            <InputLabel for="currency" value="Currency" />
                            <select
                                id="currency"
                                v-model="form.currency"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                            >
                                <option v-for="c in SUPPORTED_CURRENCIES" :key="c" :value="c">{{ c }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.currency" />
                        </div>

                        <div>
                            <InputLabel for="duration" value="Duration (minutes)" />
                            <TextInput
                                id="duration"
                                type="number"
                                min="5"
                                max="480"
                                class="mt-1 block w-full"
                                v-model.number="form.duration_minutes"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.duration_minutes" />
                        </div>

                        <div>
                            <InputLabel for="buffer" value="Buffer between bookings (minutes)" />
                            <TextInput
                                id="buffer"
                                type="number"
                                min="0"
                                max="240"
                                class="mt-1 block w-full"
                                v-model.number="form.buffer_minutes"
                            />
                            <InputError class="mt-2" :message="form.errors.buffer_minutes" />
                        </div>
                    </div>
                </section>

                <!-- Appearance + status -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Appearance
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Color used in your dashboard and on the public booking page.
                    </p>

                    <div class="mt-5">
                        <InputLabel value="Color" />
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="swatch in COLOR_SWATCHES"
                                :key="swatch"
                                type="button"
                                :style="{ backgroundColor: swatch }"
                                @click="form.color = swatch"
                                class="size-9 rounded-lg ring-offset-2 transition hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:ring-offset-gray-900"
                                :class="form.color === swatch ? 'ring-2 ring-gray-900 dark:ring-white' : 'ring-1 ring-gray-200 dark:ring-gray-700'"
                                :aria-label="`Use color ${swatch}`"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.color" />
                    </div>

                    <div class="mt-6">
                        <label class="flex items-start gap-3">
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="mt-0.5 rounded border-gray-300 bg-white text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-offset-gray-900"
                            />
                            <span>
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Active</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    When inactive, this service is hidden from your public booking page.
                                </span>
                            </span>
                        </label>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('services.index')">
                        <SecondaryButton type="button">Cancel</SecondaryButton>
                    </Link>
                    <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                        {{ form.processing ? 'Creating…' : 'Create service' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

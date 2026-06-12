<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import type { AvailabilityRule, DayOfWeek } from '@/types';
import { dayOfWeekLabel } from '@/utils/format';
import { computed } from 'vue';

const props = defineProps<{
    serviceId: number;
    initialRules: AvailabilityRule[];
}>();

interface RuleDraft {
    day_of_week: DayOfWeek;
    start_time: string;
    end_time: string;
}

function normalizeTime(value: string | null | undefined): string {
    if (!value) return '09:00';
    // Backend can serialize "09:00" or "09:00:00" depending on cast; strip seconds.
    return value.slice(0, 5);
}

function toDraft(rule: AvailabilityRule): RuleDraft {
    return {
        day_of_week: rule.day_of_week,
        start_time: normalizeTime(rule.start_time),
        end_time: normalizeTime(rule.end_time),
    };
}

const form = useForm<{ rules: RuleDraft[] }>({
    rules: props.initialRules.map(toDraft),
});

const days: { value: DayOfWeek; label: string }[] = ([1, 2, 3, 4, 5, 6, 0] as DayOfWeek[]).map(d => ({
    value: d,
    label: dayOfWeekLabel(d),
}));

function addRule(): void {
    form.rules.push({ day_of_week: 1, start_time: '09:00', end_time: '17:00' });
}

function addWeekdayDefaults(): void {
    const weekdays: DayOfWeek[] = [1, 2, 3, 4, 5];
    for (const day of weekdays) {
        const exists = form.rules.some(r => r.day_of_week === day);
        if (!exists) {
            form.rules.push({ day_of_week: day, start_time: '09:00', end_time: '17:00' });
        }
    }
}

function removeRule(index: number): void {
    form.rules.splice(index, 1);
}

function fieldError(field: string): string | undefined {
    return (form.errors as Record<string, string>)[field];
}

function submit(): void {
    form.put(route('services.availability.sync', props.serviceId), {
        preserveScroll: true,
    });
}

const hasRules = computed(() => form.rules.length > 0);
</script>

<template>
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    Availability
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Define the time windows when customers can book this service.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    v-if="!hasRules"
                    type="button"
                    @click="addWeekdayDefaults"
                    class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Use Mon–Fri 9–17
                </button>
                <button
                    type="button"
                    @click="addRule"
                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Add rule
                </button>
            </div>
        </div>

        <!-- Rules list -->
        <div v-if="hasRules" class="mt-5 space-y-2">
            <div
                v-for="(rule, index) in form.rules"
                :key="index"
                class="grid grid-cols-[1fr_auto_1fr_1fr_auto] items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-800 dark:bg-gray-950/50 sm:gap-3"
            >
                <select
                    v-model.number="rule.day_of_week"
                    class="rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                >
                    <option v-for="d in days" :key="d.value" :value="d.value">{{ d.label }}</option>
                </select>

                <span class="text-xs text-gray-500 dark:text-gray-500">from</span>

                <input
                    v-model="rule.start_time"
                    type="time"
                    class="rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                    required
                />

                <input
                    v-model="rule.end_time"
                    type="time"
                    class="rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                    required
                />

                <button
                    type="button"
                    @click="removeRule(index)"
                    class="grid size-8 place-items-center rounded-md text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                    aria-label="Remove rule"
                >
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                    </svg>
                </button>

                <!-- Per-row errors span the full width below the row -->
                <div v-if="fieldError(`rules.${index}.end_time`) || fieldError(`rules.${index}.start_time`) || fieldError(`rules.${index}.day_of_week`)" class="col-span-5">
                    <InputError :message="fieldError(`rules.${index}.start_time`) ?? fieldError(`rules.${index}.end_time`) ?? fieldError(`rules.${index}.day_of_week`)" />
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div
            v-else
            class="mt-5 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-950/40"
        >
            <p class="text-sm text-gray-600 dark:text-gray-400">
                No availability rules yet. Add one above, or use the Mon–Fri 9–17 preset.
            </p>
        </div>

        <!-- Save action -->
        <div v-if="form.isDirty || hasRules" class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-500">
                Changes are saved separately from the service info above.
            </p>
            <PrimaryButton
                @click="submit"
                :disabled="form.processing"
                :class="{ 'opacity-50': form.processing }"
            >
                {{ form.processing ? 'Saving…' : 'Save availability' }}
            </PrimaryButton>
        </div>
    </section>
</template>

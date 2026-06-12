<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import type { PageProps, Service } from '@/types';
import { ref, computed } from 'vue';

interface ServiceWithCounts extends Service {
    bookings_count?: number;
    availability_rules_count?: number;
}

const props = defineProps<{
    services: ServiceWithCounts[];
}>();

const page = usePage<PageProps>();
const flashSuccess = computed(() => page.props.flash?.success);

const deleting = ref<ServiceWithCounts | null>(null);
const deleteProcessing = ref(false);

function confirmDelete(service: ServiceWithCounts): void {
    deleting.value = service;
}

function cancelDelete(): void {
    if (deleteProcessing.value) return;
    deleting.value = null;
}

function performDelete(): void {
    if (!deleting.value) return;
    deleteProcessing.value = true;
    router.delete(route('services.destroy', deleting.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteProcessing.value = false;
            deleting.value = null;
        },
    });
}
</script>

<template>
    <Head title="Services — BookFlow" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        Services
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Publish bookable services and start accepting reservations.
                    </p>
                </div>
                <Link
                    :href="route('services.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 dark:focus:ring-offset-gray-900"
                >
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    New service
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Flash message -->
                <div
                    v-if="flashSuccess"
                    class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300"
                >
                    <svg viewBox="0 0 24 24" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                    {{ flashSuccess }}
                </div>

                <!-- Empty state -->
                <div
                    v-if="!props.services.length"
                    class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900/40"
                >
                    <div class="mx-auto grid size-14 place-items-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="7" width="18" height="13" rx="2" />
                            <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 13h18" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        No services yet
                    </h3>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">
                        Publish your first service to start accepting paid bookings.
                    </p>
                    <Link
                        :href="route('services.create')"
                        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                    >
                        Create your first service
                    </Link>
                </div>

                <!-- Grid of services -->
                <div
                    v-else
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <ServiceCard
                        v-for="service in props.services"
                        :key="service.id"
                        :service="service"
                        @request-delete="confirmDelete"
                    />
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="deleting !== null"
            title="Delete this service?"
            :message="deleting ? `“${deleting.name}” and all of its availability rules will be removed permanently. Existing bookings will also be cancelled.` : ''"
            confirm-label="Yes, delete"
            variant="danger"
            :processing="deleteProcessing"
            @close="cancelDelete"
            @confirm="performDelete"
        />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

withDefaults(
    defineProps<{
        show: boolean;
        title: string;
        message?: string;
        confirmLabel?: string;
        cancelLabel?: string;
        variant?: 'danger' | 'primary';
        processing?: boolean;
    }>(),
    {
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        variant: 'danger',
        processing: false,
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ title }}
            </h3>
            <p
                v-if="message"
                class="mt-2 text-sm text-gray-600 dark:text-gray-400"
            >
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end gap-2">
                <SecondaryButton @click="emit('close')" :disabled="processing">
                    {{ cancelLabel }}
                </SecondaryButton>
                <DangerButton
                    v-if="variant === 'danger'"
                    @click="emit('confirm')"
                    :disabled="processing"
                    :class="{ 'opacity-50': processing }"
                >
                    {{ processing ? 'Working…' : confirmLabel }}
                </DangerButton>
                <PrimaryButton
                    v-else
                    @click="emit('confirm')"
                    :disabled="processing"
                    :class="{ 'opacity-50': processing }"
                >
                    {{ processing ? 'Working…' : confirmLabel }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

import type { BookingStatus, DayOfWeek, PaymentStatus } from '@/types';

// Cents → "$50.00" / "€42.00", honouring locale separators.
export function formatPrice(cents: number, currency = 'USD', locale = 'en-US'): string {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(cents / 100);
}

// "Jun 12, 2026"
export function formatDate(value: string | Date, locale = 'en-US'): string {
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
}

// "Jun 12, 2026, 2:30 PM"
export function formatDateTime(value: string | Date, locale = 'en-US'): string {
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(value));
}

// "2:30 PM"
export function formatTime(value: string | Date, locale = 'en-US'): string {
    return new Intl.DateTimeFormat(locale, {
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(value));
}

// 90 → "1h 30m" — readable badge under a service card.
export function formatDuration(minutes: number): string {
    if (minutes < 60) return `${minutes}m`;
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return mins === 0 ? `${hours}h` : `${hours}h ${mins}m`;
}

const DAY_LABELS: Record<DayOfWeek, string> = {
    0: 'Sunday',
    1: 'Monday',
    2: 'Tuesday',
    3: 'Wednesday',
    4: 'Thursday',
    5: 'Friday',
    6: 'Saturday',
};

export function dayOfWeekLabel(day: DayOfWeek): string {
    return DAY_LABELS[day];
}

// Tailwind classes per status — kept here so badges stay consistent across pages.
const BOOKING_STATUS_CLASSES: Record<BookingStatus, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    confirmed: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
};

export function bookingStatusClasses(status: BookingStatus): string {
    return BOOKING_STATUS_CLASSES[status];
}

const PAYMENT_STATUS_CLASSES: Record<PaymentStatus, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    processing: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    succeeded: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
};

export function paymentStatusClasses(status: PaymentStatus): string {
    return PAYMENT_STATUS_CLASSES[status];
}

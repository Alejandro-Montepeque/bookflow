export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    created_at?: string;
    updated_at?: string;
}

// Carbon convention: 0 = Sunday … 6 = Saturday.
export type DayOfWeek = 0 | 1 | 2 | 3 | 4 | 5 | 6;

export interface AvailabilityRule {
    id: number;
    service_id: number;
    day_of_week: DayOfWeek;
    start_time: string;
    end_time: string;
    created_at?: string;
    updated_at?: string;
}

export interface Service {
    id: number;
    user_id: number;
    name: string;
    slug: string;
    description: string | null;
    duration_minutes: number;
    price_cents: number;
    currency: string;
    color: string;
    buffer_minutes: number;
    is_active: boolean;
    formatted_price?: string;
    created_at?: string;
    updated_at?: string;
    user?: User;
    availability_rules?: AvailabilityRule[];
    bookings?: Booking[];
}

export type BookingStatus =
    | 'pending'
    | 'confirmed'
    | 'cancelled'
    | 'completed';

export interface Booking {
    id: number;
    service_id: number;
    customer_name: string;
    customer_email: string;
    starts_at: string;
    ends_at: string;
    status: BookingStatus;
    timezone: string;
    notes: string | null;
    cancellation_token: string;
    created_at?: string;
    updated_at?: string;
    service?: Service;
    payment?: Payment | null;
}

export type PaymentStatus =
    | 'pending'
    | 'processing'
    | 'succeeded'
    | 'failed'
    | 'refunded';

export interface Payment {
    id: number;
    booking_id: number;
    stripe_payment_intent_id: string | null;
    stripe_checkout_session_id: string | null;
    amount_cents: number;
    currency: string;
    status: PaymentStatus;
    paid_at: string | null;
    created_at?: string;
    updated_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash?: {
        success?: string;
        error?: string;
    };
};

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

interface Feature {
    title: string;
    description: string;
    icon: 'calendar' | 'card' | 'mail' | 'clock' | 'globe' | 'sparkles';
    tone: 'indigo' | 'emerald' | 'amber' | 'rose' | 'sky' | 'violet';
}

const features: Feature[] = [
    {
        title: 'Multi-service scheduling',
        description: 'Offer consultations, classes or sessions with their own duration, price and color.',
        icon: 'calendar',
        tone: 'indigo',
    },
    {
        title: 'Paid bookings upfront',
        description: 'Charge with Stripe Checkout at the moment of booking. No-shows become rare.',
        icon: 'card',
        tone: 'emerald',
    },
    {
        title: 'Automated reminders',
        description: 'Confirmation and reminder emails go out automatically. You focus on the work.',
        icon: 'mail',
        tone: 'amber',
    },
    {
        title: 'Recurring availability',
        description: 'Define weekly rules once (Mon–Fri 9–17h) and slots are generated for you.',
        icon: 'clock',
        tone: 'sky',
    },
    {
        title: 'Time-zone aware',
        description: 'Customers see your slots in their own zone. No more "what time was that for me?"',
        icon: 'globe',
        tone: 'violet',
    },
    {
        title: 'Built for solo founders',
        description: 'Quick to set up, easy to share, no bloat. Add your services and ship in minutes.',
        icon: 'sparkles',
        tone: 'rose',
    },
];

const toneClasses: Record<Feature['tone'], string> = {
    indigo: 'bg-indigo-500/10 text-indigo-600 ring-1 ring-indigo-500/20 dark:bg-indigo-400/10 dark:text-indigo-300 dark:ring-indigo-400/30',
    emerald: 'bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/30',
    amber: 'bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/30',
    sky: 'bg-sky-500/10 text-sky-600 ring-1 ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/30',
    violet: 'bg-violet-500/10 text-violet-600 ring-1 ring-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/30',
    rose: 'bg-rose-500/10 text-rose-600 ring-1 ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/30',
};
</script>

<template>
    <Head title="BookFlow — Effortless bookings for service businesses" />

    <div class="relative min-h-screen overflow-hidden bg-white text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <!-- Decorative blobs -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-32 -left-32 size-96 rounded-full bg-indigo-500/30 blur-3xl dark:bg-indigo-500/20" />
            <div class="absolute top-40 right-0 size-96 rounded-full bg-emerald-400/30 blur-3xl dark:bg-emerald-400/15" />
            <div class="absolute bottom-0 left-1/2 size-[28rem] -translate-x-1/2 rounded-full bg-violet-400/20 blur-3xl dark:bg-violet-500/10" />
        </div>

        <!-- Subtle grid pattern -->
        <div
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgba(0,0,0,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(0,0,0,0.04)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_at_top,black_50%,transparent_80%)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
        />

        <!-- Nav -->
        <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <Link href="/" class="flex items-center gap-2 text-lg font-bold tracking-tight">
                <span class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/30">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="3" />
                        <path d="M3 9h18" />
                        <path d="M7 14l3 3 7-7" />
                    </svg>
                </span>
                BookFlow
            </Link>

            <nav v-if="canLogin" class="flex items-center gap-1 text-sm">
                <Link
                    :href="route('login')"
                    class="rounded-md px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="rounded-md bg-gray-900 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    Get started →
                </Link>
            </nav>
        </header>

        <!-- Hero -->
        <main class="relative z-10 mx-auto max-w-6xl px-6">
            <section class="pt-12 text-center lg:pt-20">
                <div class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white/60 px-4 py-1.5 text-xs font-medium text-gray-600 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-400">
                    <span class="size-2 animate-pulse rounded-full bg-emerald-500" />
                    New — Time-zone aware booking flows
                </div>

                <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-7xl">
                    Bookings without the
                    <span class="relative inline-block">
                        <span class="bg-gradient-to-r from-indigo-500 via-violet-500 to-emerald-500 bg-clip-text text-transparent">
                            back-and-forth
                        </span>
                        <svg
                            class="absolute -bottom-3 left-0 w-full text-indigo-500/40"
                            viewBox="0 0 200 8"
                            fill="none"
                            preserveAspectRatio="none"
                            aria-hidden="true"
                        >
                            <path d="M0 4 Q 50 0, 100 4 T 200 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                </h1>

                <p class="mx-auto mt-8 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                    BookFlow is the Calendly alternative for small service businesses. Publish your services,
                    share your link, and accept paid bookings — all in one place.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="group inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-gray-900/20 transition hover:translate-y-[-1px] hover:bg-gray-800 hover:shadow-xl dark:bg-white dark:text-gray-900 dark:shadow-white/10 dark:hover:bg-gray-100"
                    >
                        Start for free
                        <svg class="size-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </Link>
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3.5 text-base font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                    >
                        I already have an account
                    </Link>
                </div>

                <p class="mt-6 text-xs text-gray-500 dark:text-gray-500">
                    No credit card required · Free during beta
                </p>
            </section>

            <!-- Hero "screenshot" mockup -->
            <section class="mt-24 lg:mt-32">
                <div class="relative mx-auto max-w-5xl">
                    <!-- Glow under the mockup -->
                    <div aria-hidden="true" class="absolute inset-x-12 -bottom-8 h-24 rounded-full bg-gradient-to-r from-indigo-500/30 via-violet-500/30 to-emerald-500/30 blur-3xl" />

                    <div class="relative rounded-2xl border border-gray-200 bg-white/80 p-1 shadow-2xl backdrop-blur dark:border-gray-800 dark:bg-gray-900/80 dark:shadow-black/40">
                        <!-- Window chrome -->
                        <div class="flex items-center gap-2 border-b border-gray-200 px-4 py-3 dark:border-gray-800">
                            <span class="size-2.5 rounded-full bg-red-400" />
                            <span class="size-2.5 rounded-full bg-amber-400" />
                            <span class="size-2.5 rounded-full bg-emerald-400" />
                            <div class="ml-4 flex h-6 flex-1 items-center justify-center rounded-md bg-gray-100 px-3 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                bookflow.app / u / alejandro
                            </div>
                        </div>

                        <!-- Fake booking UI -->
                        <div class="grid gap-6 p-6 sm:grid-cols-[1fr_2fr]">
                            <!-- Provider panel -->
                            <div class="rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 p-5 text-white">
                                <div class="mb-3 grid size-12 place-items-center rounded-full bg-white/20 text-lg font-bold">
                                    A
                                </div>
                                <p class="text-xs uppercase tracking-wider text-white/70">Provider</p>
                                <h3 class="text-lg font-semibold">Alejandro Montepeque</h3>
                                <p class="mt-3 text-sm text-white/90">30-min Consultation</p>
                                <p class="mt-1 text-2xl font-bold">$50.00</p>
                                <div class="mt-4 flex items-center gap-2 text-xs text-white/80">
                                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" stroke-linecap="round" />
                                    </svg>
                                    30 minutes
                                </div>
                            </div>

                            <!-- Calendar mockup -->
                            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-950">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-sm font-semibold">June 2026</p>
                                    <div class="flex gap-1">
                                        <button type="button" class="grid size-7 place-items-center rounded-md border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800">‹</button>
                                        <button type="button" class="grid size-7 place-items-center rounded-md border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800">›</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
                                    <div v-for="d in ['Mo','Tu','We','Th','Fr','Sa','Su']" :key="d" class="py-1 text-gray-400">{{ d }}</div>
                                    <div v-for="n in [1,2,3,4,5,6,7,8,9,10,11,12,13,14]" :key="n" class="aspect-square grid place-items-center rounded-md text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800" :class="{ 'bg-indigo-600 text-white font-semibold hover:bg-indigo-500': n === 12 }">
                                        {{ n }}
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                                    <button v-for="t in ['9:00 AM','9:30 AM','10:00 AM','10:30 AM','11:00 AM','11:30 AM']" :key="t" type="button" class="rounded-md border border-gray-200 px-2 py-1.5 text-gray-700 hover:border-indigo-500 hover:text-indigo-600 dark:border-gray-700 dark:text-gray-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300">
                                        {{ t }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Feature grid -->
            <section class="mt-32 lg:mt-40">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Everything you need to take bookings</h2>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        No plugins, no add-ons. The features below come built-in.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-gray-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900/60 dark:hover:border-gray-700"
                    >
                        <div :class="['mb-4 inline-flex size-11 items-center justify-center rounded-lg', toneClasses[feature.tone]]">
                            <!-- calendar -->
                            <svg v-if="feature.icon === 'calendar'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="16" rx="2" />
                                <path d="M3 9h18M8 3v4M16 3v4" />
                            </svg>
                            <!-- card -->
                            <svg v-else-if="feature.icon === 'card'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                <path d="M2 10h20M6 15h4" />
                            </svg>
                            <!-- mail -->
                            <svg v-else-if="feature.icon === 'mail'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="M3 7l9 6 9-6" />
                            </svg>
                            <!-- clock -->
                            <svg v-else-if="feature.icon === 'clock'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            <!-- globe -->
                            <svg v-else-if="feature.icon === 'globe'" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                            </svg>
                            <!-- sparkles -->
                            <svg v-else viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z" />
                                <path d="M19 14l.7 2.1L22 17l-2.3.9L19 20l-.7-2.1L16 17l2.3-.9L19 14z" />
                            </svg>
                        </div>

                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ feature.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ feature.description }}</p>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="my-32 lg:my-40">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-emerald-500 p-10 text-center text-white shadow-2xl">
                    <div aria-hidden="true" class="absolute inset-0 -z-10 opacity-20 [background:radial-gradient(circle_at_30%_30%,white,transparent_40%),radial-gradient(circle_at_70%_70%,white,transparent_40%)]" />

                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Start taking bookings today</h2>
                    <p class="mx-auto mt-4 max-w-xl text-white/90">
                        Free during beta. Set up your first bookable service in under 5 minutes.
                    </p>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="mt-8 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-base font-semibold text-gray-900 shadow-lg transition hover:bg-gray-100"
                    >
                        Create your account
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-gray-200 py-8 dark:border-gray-800">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-6 text-sm text-gray-500 sm:flex-row dark:text-gray-500">
                <p>© 2026 BookFlow</p>
                <p>Built with Laravel · Inertia · Vue 3 · Tailwind</p>
            </div>
        </footer>
    </div>
</template>

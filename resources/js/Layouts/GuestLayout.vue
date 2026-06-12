<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
            <!-- Left brand panel — hidden on mobile -->
            <aside class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-600 via-violet-600 to-emerald-500 p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <!-- Decorative grid pattern -->
                <div
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-0 opacity-20 [background-image:linear-gradient(to_right,white_1px,transparent_1px),linear-gradient(to_bottom,white_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_70%)]"
                />
                <!-- Decorative blobs -->
                <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-white/20 blur-3xl" />
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 -right-24 size-96 rounded-full bg-emerald-300/30 blur-3xl" />

                <!-- Logo / brand -->
                <Link href="/" class="relative flex items-center gap-2 text-lg font-bold">
                    <span class="grid size-9 place-items-center rounded-lg bg-white/15 backdrop-blur">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="3" />
                            <path d="M3 9h18" />
                            <path d="M7 14l3 3 7-7" />
                        </svg>
                    </span>
                    BookFlow
                </Link>

                <!-- Quote -->
                <div class="relative max-w-md space-y-6">
                    <svg viewBox="0 0 24 24" class="size-8 text-white/40" fill="currentColor">
                        <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                    </svg>
                    <p class="text-2xl font-medium leading-relaxed text-white/95">
                        "I stopped chasing clients to confirm times. They book, they pay, I show up.
                        BookFlow saved me 5 hours a week."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 place-items-center rounded-full bg-white/20 font-bold backdrop-blur">M</div>
                        <div>
                            <p class="text-sm font-semibold">María Castaneda</p>
                            <p class="text-xs text-white/70">Career coach · San Salvador</p>
                        </div>
                    </div>
                </div>

                <!-- Footer of left panel -->
                <p class="relative text-xs text-white/60">© 2026 BookFlow</p>
            </aside>

            <!-- Right form panel -->
            <main class="flex min-h-screen flex-col">
                <!-- Mobile-only logo bar -->
                <div class="border-b border-gray-200 bg-white px-6 py-4 lg:hidden dark:border-gray-800 dark:bg-gray-900">
                    <Link href="/" class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-gray-100">
                        <span class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white">
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="16" rx="3" />
                                <path d="M3 9h18" />
                                <path d="M7 14l3 3 7-7" />
                            </svg>
                        </span>
                        BookFlow
                    </Link>
                </div>

                <div class="flex flex-1 items-center justify-center px-6 py-12">
                    <div class="w-full max-w-md">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

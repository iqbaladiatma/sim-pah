<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-pail-ivory pt-6 sm:pt-0 dark:bg-gray-900 font-sans text-gray-900 antialiased"
    >
        <div class="mb-6 flex flex-col items-center">
            <Link href="/" class="flex flex-col items-center">
                <ApplicationLogo
                    class="h-20 w-20 fill-current text-pail-gold mb-2 drop-shadow-sm"
                />
                <span
                    class="text-2xl font-bold tracking-wider text-gray-800 dark:text-gray-100"
                    >SIM <span class="text-pail-gold">PAH</span></span
                >
                <span
                    class="text-xs text-gray-500 tracking-widest uppercase mt-1"
                    >Sistem Informasi Manajemen</span
                >
            </Link>
        </div>

        <div
            class="w-full overflow-hidden bg-white px-8 py-8 shadow-xl border border-gray-100 sm:max-w-md sm:rounded-2xl dark:bg-gray-800 dark:border-gray-700"
        >
            <slot />
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; {{ new Date().getFullYear() }} Pondok Pesantren Abu Hurairah
        </div>
    </div>
</template>

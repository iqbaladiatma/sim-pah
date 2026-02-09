<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center p-4 sm:p-6 relative overflow-hidden transition-colors duration-500">
        <!-- Dashboard-style Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05] z-0 overflow-hidden">
            <div class="absolute inset-0" style="background-image: radial-gradient(#000 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>
        </div>

        <!-- Premium Background Blobs -->
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-pail-gold opacity-10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-pail-gold opacity-10 rounded-full blur-[140px]"></div>

        <div class="w-full max-w-md relative z-10 flex flex-col items-center">
            <!-- Branding Header -->
            <div class="mb-8 sm:mb-12 text-center">
                <Link href="/" class="flex flex-col items-center gap-4">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 flex items-center justify-center transition-all duration-500">
                        <ApplicationLogo class="h-16 w-16 sm:h-24 sm:w-24 fill-current text-pail-gold transition-all duration-500" />
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <span class="text-3xl sm:text-4xl font-black tracking-tighter text-gray-900 dark:text-white leading-none transition-all duration-300">
                            SIM <span class="text-pail-gold">PAH</span>
                        </span>
                        <span class="text-[8px] sm:text-[10px] font-black text-pail-gold uppercase tracking-[0.4em] mt-2 opacity-60 transition-all duration-300">
                            Edisi Mataram
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Login Card (Glassmorphism) -->
            <div class="w-full bg-white/70 dark:bg-gray-800/70 backdrop-blur-2xl px-6 py-8 sm:px-10 sm:py-12 rounded-[2rem] sm:rounded-[3rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.15)] border border-white dark:border-gray-700/50 transition-all duration-500">
                <slot />
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center">
                <p class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-[0.3em] leading-relaxed">
                    &copy; {{ new Date().getFullYear() }} Pondok Pesantren Abu Hurairah<br/>
                    <span class="opacity-50">Keamanan Logistik & Infrastruktur</span>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-pail-gold {
    background-color: #C9A658;
}
.text-pail-gold {
    color: #C9A658;
}
</style>

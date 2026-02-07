<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        503: "503: Service Unavailable",
        500: "500: Server Error",
        404: "404: Page Not Found",
        403: "403: Forbidden",
        419: "419: Page Expired",
    }[props.status];
});

const description = computed(() => {
    return {
        503: "Maaf, sistem sedang sibuk atau dalam pemeliharaan rutin. Silakan coba kembali sesaat lagi.",
        500: "Terjadi kesalahan pada server kami. Tim teknis telah dinotifikasi mengenai masalah ini.",
        404: "Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.",
        403: "Akses ditolak. Anda tidak memiliki izin yang cukup untuk mengakses protokol ini.",
        419: "Sesi Anda telah berakhir karena terlalu lama tidak ada aktivitas. Silakan refresh halaman.",
    }[props.status];
});

const icon = computed(() => {
    return {
        503: "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z",
        500: "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z",
        404: "M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        403: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z",
        419: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
    }[props.status];
});
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex items-center justify-center p-6 relative overflow-hidden font-sans">
        <!-- Neural Background Decoration -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-pail-gold opacity-[0.05] rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500 opacity-[0.05] rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-2xl w-full text-center relative z-10">
            <!-- Glitchy Error Code -->
            <div class="relative inline-block mb-12">
                <span class="text-[12rem] md:text-[16rem] font-black text-gray-900/5 dark:text-white/5 leading-none select-none tracking-tighter italic">
                    {{ status }}
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl flex items-center justify-center text-pail-gold transform -rotate-12 hover:rotate-0 transition-transform duration-700">
                        <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="icon"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Error Content -->
            <div class="space-y-6">
                <div class="flex items-center justify-center gap-4 mb-2">
                    <div class="w-12 h-[2px] bg-pail-gold"></div>
                    <h2 class="text-xs font-black text-pail-gold uppercase tracking-[0.5em] italic">System Error Protocol</h2>
                    <div class="w-12 h-[2px] bg-pail-gold"></div>
                </div>

                <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                    {{ title.split(': ')[1] }}
                </h1>
                
                <p class="text-sm md:text-base text-gray-500 dark:text-gray-400 font-bold leading-relaxed max-w-lg mx-auto uppercase tracking-tight">
                    {{ description }}
                </p>

                <div class="pt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link 
                        href="/dashboard"
                        class="px-8 py-4 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-black rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl hover:scale-105 transition-all w-full sm:w-auto text-center"
                    >
                        Kembali ke Dashboard
                    </Link>
                    <button 
                        @click="window.location.reload()"
                        class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-50 transition-all w-full sm:w-auto"
                    >
                        Muat Ulang Halaman
                    </button>
                </div>

                <!-- Debug Info -->
                <div class="pt-20 opacity-20 group hover:opacity-100 transition-opacity">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.4em] mb-4">SIM PAH - Integrated Security Node</p>
                    <div class="flex items-center justify-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Neural Link Heartbeat: Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes glitch {
    0% { transform: translate(0); }
    20% { transform: translate(-2px, 2px); }
    40% { transform: translate(-2px, -2px); }
    60% { transform: translate(2px, 2px); }
    80% { transform: translate(2px, -2px); }
    100% { transform: translate(0); }
}

.font-black:hover {
    animation: glitch 0.3s cubic-bezier(.25,.46,.45,.94) both infinite;
    animation-play-state: paused;
}

.max-w-2xl:hover .font-black {
    animation-play-state: running;
}
</style>

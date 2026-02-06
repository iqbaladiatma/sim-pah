<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Identitas Node | SIM PAH" />

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-16 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-16 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                Verifikasi <span class="text-pail-gold">Node</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Menunggu Autentikasi
                </p>
            </div>
        </div>

        <div class="mb-10 p-8 bg-gray-50/50 dark:bg-gray-900/40 rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 text-center">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight leading-loose">
                Identitas node baru terdeteksi. Silakan verifikasi <span class="text-pail-gold">Identitas Komunikasi</span> Anda melalui link aman yang telah dikirimkan ke email Anda. <br/>
                <span class="opacity-50 italic">Tidak menerima sinyal? Kirim ulang permintaan di bawah.</span>
            </p>
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-8 p-6 bg-green-500/10 border border-green-500/20 rounded-2xl text-[10px] font-black text-green-600 uppercase tracking-widest text-center"
        >
            Payload verifikasi baru telah dikirimkan ke node utama Anda.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <PrimaryButton
                class="w-full h-18 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.3em] border border-pail-gold/20"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Mengirimkan...</span>
                <span v-else class="flex items-center gap-3">
                    Kirim Ulang Sinyal Verifikasi
                    <ZapIcon className="w-4 h-4" />
                </span>
            </PrimaryButton>

            <div class="flex items-center justify-center">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-colors decoration-2 underline-offset-8 underline"
                >
                    Terminasi Sesi
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Keamanan | SIM PAH" />

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-16 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-16 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                Override <span class="text-pail-gold">Keamanan</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Protokol Area Terbatas
                </p>
            </div>
        </div>

        <div class="mb-10 p-6 bg-red-500/5 dark:bg-red-900/10 rounded-2xl border border-red-500/10 text-center">
            <p class="text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-tight leading-relaxed">
                Ini adalah zona keamanan tinggi. Silakan verifikasi <span class="text-pail-gold">Kunci Keamanan Terenkripsi</span> Anda untuk memfasilitasi override manual.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div class="group relative">
                <InputLabel for="password" value="Verifikasi Kunci Keamanan" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
                <div class="relative">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-600 transition-colors group-focus-within:text-pail-gold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-[10px] focus:ring-pail-gold/5 rounded-full shadow-sm h-16 pl-14 pr-6 text-sm font-bold transition-all placeholder:text-gray-300 dark:placeholder:text-gray-600 border-2"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="••••••••••••"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full h-18 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.3em] border border-pail-gold/20"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memverifikasi identitas...</span>
                    <span v-else class="flex items-center gap-3">
                        Otorisasi Override
                        <ZapIcon className="w-4 h-4" />
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

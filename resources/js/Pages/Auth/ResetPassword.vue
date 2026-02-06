<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Kunci Identitas | SIM PAH" />

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-16 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-16 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                Inisialisasi <span class="text-pail-gold">Kredensial</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Pemrograman Ulang Sedang Berlangsung
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div class="group relative">
                <InputLabel for="email" value="Identitas Target (Email)" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
                <div class="relative">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-600">
                        <UserIcon className="w-5 h-5" />
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full bg-gray-50/50 dark:bg-gray-900/40 border-gray-100 dark:border-gray-700 opacity-60 rounded-full h-14 pl-14 shadow-sm text-sm font-bold border-2"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        readonly
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-6">
                <div class="group relative">
                    <InputLabel for="password" value="Kunci Keamanan Baru" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
                    <div class="relative">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-600 transition-colors group-focus-within:text-pail-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-[10px] focus:ring-pail-gold/5 rounded-full shadow-sm h-16 pl-14 pr-6 text-sm font-bold transition-all border-2"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            autofocus
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="group relative">
                    <InputLabel for="password_confirmation" value="Konfirmasi Kunci Baru" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
                    <div class="relative">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-600 transition-colors group-focus-within:text-pail-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-[10px] focus:ring-pail-gold/5 rounded-full shadow-sm h-16 pl-14 pr-6 text-sm font-bold transition-all border-2"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-6">
                <PrimaryButton
                    class="w-full h-18 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.3em] border border-pail-gold/20"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memprogram ulang node...</span>
                    <span v-else class="flex items-center gap-3">
                        Inisialisasi Kunci Baru
                        <ZapIcon className="w-4 h-4" />
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

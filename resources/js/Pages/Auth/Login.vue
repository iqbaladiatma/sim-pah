<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InstitutionSelect from "@/Components/InstitutionSelect.vue";
import SparklesIcon from "@/Components/Icons/SparklesIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    institutions: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
    institution_id: null,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login | SIM PAH" />

        <div v-if="status" class="mb-8 p-6 bg-green-500/10 border border-green-500/20 rounded-[2rem] text-[10px] font-black text-green-600 uppercase tracking-[0.2em] text-center backdrop-blur-md">
            {{ status }}
        </div>

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-20 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-20 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                <span class="text-pail-gold">Login</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Protokol Keamanan Aktif
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-10">
            <!-- Institution Selection Hub -->
            <div class="bg-gray-50/80 dark:bg-gray-900/40 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 relative group transition-all duration-700 hover:border-pail-gold/30 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-pail-gold opacity-[0.03] rounded-full blur-3xl group-hover:opacity-10 transition-opacity"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <InputLabel for="institution" value="Identitas Lembaga" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400" />
                        <span class="text-[9px] font-black text-pail-gold/40 uppercase tracking-widest">Wajib untuk Lembaga-Lembaga</span>
                    </div>
                    
                    <InstitutionSelect 
                        v-model="form.institution_id"
                        :institutions="institutions"
                    />
                    <InputError class="mt-2" :message="form.errors.institution_id" />
                    
                    <div class="mt-5 flex items-start gap-4 p-4 bg-white/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100/50 dark:border-gray-700/50">
                        <div class="w-8 h-8 rounded-xl bg-pail-gold/10 flex items-center justify-center shrink-0">
                            <SparklesIcon className="w-4 h-4 text-pail-gold" />
                        </div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tight leading-relaxed">
                            Personil yang terasosiasi dengan lembaga tertentu wajib mengidentifikasi <span class="text-pail-gold">Node Institusi</span> mereka. Akun administrator melewati protokol ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Credentials Block -->
            <div class="space-y-6">
                <div class="group relative">
                    <InputLabel for="email" value="Identitas Akses (Email)" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
                    <div class="relative">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-600 transition-colors group-focus-within:text-pail-gold">
                            <UserIcon className="w-5 h-5" />
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-[10px] focus:ring-pail-gold/5 rounded-full shadow-sm h-16 pl-14 pr-6 text-sm font-bold transition-all placeholder:text-gray-300 dark:placeholder:text-gray-600 border-2"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="credential@abu-hurairah.id"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="group relative">
                    <div class="flex items-center justify-between mb-3 px-2">
                        <InputLabel for="password" value="Kunci Keamanan Terenkripsi" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400" />
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-[9px] font-black text-pail-gold hover:text-yellow-600 uppercase tracking-widest transition-colors decoration-2 underline-offset-4 hover:underline"
                        >
                            Lupa Kunci?
                        </Link>
                    </div>
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
                            placeholder="••••••••••••"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="flex items-center justify-between px-2">
                <label class="flex items-center group cursor-pointer">
                    <div class="relative flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="w-6 h-6 rounded-full border-gray-200 dark:border-gray-700 text-pail-gold focus:ring-pail-gold/20 transition-all group-hover:border-pail-gold/50 cursor-pointer" />
                    </div>
                    <span class="ms-4 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pertahankan Sesi Aktif</span>
                </label>
            </div>

            <div class="pt-6">
                <PrimaryButton
                    class="w-full h-20 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.4em] border border-pail-gold/20 group/btn overflow-hidden relative"
                    :disabled="form.processing"
                >
                    <div class="absolute inset-0 bg-pail-gold opacity-0 group-hover/btn:opacity-5 transition-opacity"></div>
                    <span v-if="form.processing" class="flex items-center gap-4">
                        <svg class="animate-spin h-5 w-5 text-pail-gold" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memverifikasi...
                    </span>
                    <span v-else class="flex items-center gap-4 text-pail-gold">
                        Login
                        <ZapIcon className="w-5 h-5 group-hover/btn:scale-125 transition-transform" />
                    </span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-6">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] leading-relaxed">
                    Akses Ditolak? <br/>
                    <span class="text-pail-gold/60">Hubungi Pusat Intelijen URT.</span>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

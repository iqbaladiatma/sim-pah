<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InstitutionSelect from "@/Components/InstitutionSelect.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import SparklesIcon from "@/Components/Icons/SparklesIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

const props = defineProps({
    institutions: {
        type: Array,
        default: () => []
    },
});

const form = useForm({
    name: "",
    email: "",
    phone: "",
    institution_id: null,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Pendaftaran Personil | SIM PAH" />

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-16 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-16 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                Portal <span class="text-pail-gold">Pendaftaran</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Registrasi Node Baru
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <InputLabel for="name" value="Nama Lengkap" class="text-[10px] uppercase font-black text-gray-400 mb-2 px-1" />
                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/5 rounded-full shadow-sm h-14 px-5 text-sm font-bold transition-all border-2"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="John Doe"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="group">
                    <InputLabel for="email" value="Email Institusi" class="text-[10px] uppercase font-black text-gray-400 mb-2 px-1" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/5 rounded-full shadow-sm h-14 px-5 text-sm font-bold transition-all border-2"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="identity@abu-hurairah.id"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="group">
                <InputLabel for="phone" value="Node Komunikasi (WhatsApp/HP)" class="text-[10px] uppercase font-black text-gray-400 mb-2 px-1" />
                <TextInput
                    id="phone"
                    type="text"
                    class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/5 rounded-full shadow-sm h-14 px-5 text-sm font-bold transition-all border-2"
                    v-model="form.phone"
                    placeholder="+62 812 XXXX XXXX"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="p-6 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border border-gray-100 dark:border-gray-700/50">
                <InputLabel for="institution_id" value="Penempatan Node Institusi" class="text-[10px] uppercase font-black text-gray-400 mb-4 px-1" />
                <InstitutionSelect 
                    v-model="form.institution_id"
                    :institutions="institutions"
                />
                <InputError class="mt-2" :message="form.errors.institution_id" />
                <div class="mt-4 flex items-start gap-4 p-4 bg-white/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100/50 dark:border-gray-700/50">
                    <SparklesIcon className="w-4 h-4 text-pail-gold mt-0.5 shrink-0" />
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tight leading-relaxed">
                        Pemetaan <span class="text-pail-gold">Institusi</span> yang akurat sangat krusial untuk manajemen sumber daya dan kompartementalisasi data.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <InputLabel for="password" value="Kunci Keamanan" class="text-[10px] uppercase font-black text-gray-400 mb-2 px-1" />
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/5 rounded-full shadow-sm h-14 px-5 text-sm font-bold transition-all border-2"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="group">
                    <InputLabel for="password_confirmation" value="Verifikasi Kunci" class="text-[10px] uppercase font-black text-gray-400 mb-2 px-1" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full bg-white/50 dark:bg-gray-800/50 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/5 rounded-full shadow-sm h-14 px-5 text-sm font-bold transition-all border-2"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-8 space-y-4">
                <PrimaryButton
                    class="w-full h-18 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.3em] border border-pail-gold/20"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses Pendaftaran...</span>
                    <span v-else class="flex items-center gap-3">
                        Eksekusi Pendaftaran
                        <ZapIcon className="w-4 h-4" />
                    </span>
                </PrimaryButton>
                
                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-pail-gold transition-colors"
                    >
                        Sudah memiliki node aktif? <span class="text-pail-gold decoration-2 underline underline-offset-4">Masuk</span>
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Pemulihan Identitas | SIM PAH" />

        <!-- Intelligence Hub Header -->
        <div class="mb-12 relative text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-20">
                <div class="w-16 h-[1px] bg-pail-gold"></div>
                <DiamondIcon className="w-3 h-3 text-pail-gold" />
                <div class="w-16 h-[1px] bg-pail-gold"></div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-tight">
                Pemulihan <span class="text-pail-gold">Identitas</span>
            </h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                    Menunggu Input Kredensial
                </p>
            </div>
        </div>

        <div class="mb-10 p-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-700/50">
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight leading-relaxed">
                Kehilangan kunci keamanan? Masukkan <span class="text-pail-gold">Identitas Intelijen</span> Anda yang terdaftar dan kami akan mengirimkan link pemulihan ke node Anda.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-8 p-6 bg-green-500/10 border border-green-500/20 rounded-2xl text-[10px] font-black text-green-600 uppercase tracking-widest text-center"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div class="group relative">
                <InputLabel for="email" value="ID Email Terdaftar" class="text-[10px] uppercase tracking-[0.3em] font-black text-gray-400 mb-3 px-2" />
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

            <div class="pt-4 flex flex-col gap-4">
                <PrimaryButton
                    class="w-full h-18 bg-gray-900 dark:bg-black hover:bg-black dark:hover:bg-gray-900 text-pail-gold font-black rounded-full shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center text-xs uppercase tracking-[0.3em] border border-pail-gold/20 group/btn"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mengirimkan...</span>
                    <span v-else class="flex items-center gap-3">
                        Inisiasi Pemulihan
                        <ZapIcon className="w-4 h-4" />
                    </span>
                </PrimaryButton>
                
                <Link :href="route('login')" class="text-center text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-pail-gold transition-colors">
                    Kembali ke Gerbang
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

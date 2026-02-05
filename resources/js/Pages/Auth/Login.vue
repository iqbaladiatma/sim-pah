<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InstitutionSelect from "@/Components/InstitutionSelect.vue";

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

console.log('Login Props Institutions:', props.institutions);

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
        <Head title="Masuk | SIM PAH" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="mb-10 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                SIM <span class="text-pail-gold font-black italic">URT</span> PAH
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Pondok Pesantren Abu Hurairah - Mataram
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-gray-50/50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                <InputLabel for="institution" value="Pilih Lembaga / Unit Kerja" class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-2" />
                <InstitutionSelect 
                    v-model="form.institution_id"
                    :institutions="institutions"
                />
                <InputError class="mt-2" :message="form.errors.institution_id" />
                <p class="mt-2 text-[10px] text-gray-500 italic leading-tight">
                    * User Lembaga wajib memilih unit yang sesuai. Admin biarkan kosong. ({{ institutions.length }} Lembaga tersedia)
                </p>
            </div>

            <div>
                <InputLabel for="email" value="Email Address" class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-1" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-gray-200 focus:border-pail-gold focus:ring-pail-gold rounded-lg shadow-sm"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@abu-hurairah.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-1" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-pail-gold hover:text-yellow-600 font-medium"
                    >
                        Lupa?
                    </Link>
                </div>
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border-gray-200 focus:border-pail-gold focus:ring-pail-gold rounded-lg shadow-sm"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-pail-gold focus:ring-pail-gold h-4 w-4 rounded" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ingat sesi saya</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-3 bg-pail-gold hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 text-white font-bold rounded-lg shadow-lg shadow-pail-gold/20 transform transition active:scale-95 disabled:opacity-50 flex justify-center text-sm uppercase tracking-widest"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Memproses...' : 'Masuk Ke Sistem' }}
                </PrimaryButton>
            </div>

            <div class="text-center pt-2">
                <p class="text-xs text-gray-500">
                    Belum punya akses? Hubungi Admin URT Mataram.
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

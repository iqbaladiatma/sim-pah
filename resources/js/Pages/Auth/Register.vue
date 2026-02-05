<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    institutions: Array, // Passed from controller
});

const form = useForm({
    name: "",
    email: "",
    phone: "",
    institution_id: "",
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
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Registrasi Karyawan
            </h2>
            <p class="text-sm text-gray-500">
                Buat akun baru untuk akses sistem.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="No. WhatsApp / HP" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="institution_id" value="Lembaga / Bagian" />
                <select
                    id="institution_id"
                    v-model="form.institution_id"
                    class="mt-1 block w-full border-gray-300 focus:border-pail-gold focus:ring-pail-gold rounded-md shadow-sm bg-gray-50 focus:bg-white text-sm"
                >
                    <option value="">- Pilih Lembaga -</option>
                    <option
                        v-for="inst in institutions"
                        :key="inst.id"
                        :value="inst.id"
                    >
                        {{ inst.code }} - {{ inst.name }}
                    </option>
                </select>
                <InputError
                    class="mt-2"
                    :message="form.errors.institution_id"
                />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-8 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600 underline hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:text-gray-100"
                >
                    Sudah punya akun?
                </Link>

                <PrimaryButton
                    class="ml-4 w-1/3 justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

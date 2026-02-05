<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Sistem Informasi URT
            </h2>
            <p class="text-sm text-gray-500">
                Silakan login untuk melanjutkan.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="user@sim-pah.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 focus:bg-white"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="********"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block flex justify-between items-center">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Ingat saya</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-pail-gold hover:text-yellow-600 underline"
                >
                    Lupa Password?
                </Link>
            </div>

            <div class="mt-8">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk
                </PrimaryButton>

                <div class="mt-4 text-center">
                    <span class="text-xs text-gray-500"
                        >Belum punya akun?
                    </span>
                    <Link
                        :href="route('register')"
                        class="text-xs font-bold text-pail-gold hover:text-yellow-600 underline"
                    >
                        Daftar disini
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

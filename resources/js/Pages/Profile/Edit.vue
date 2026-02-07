<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Page Header -->
                <div class="flex items-center justify-between px-4 sm:px-0 mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-wider">
                            Pengaturan Akun
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Kelola informasi profil dan keamanan akun Anda
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Profile Information -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl border border-gray-100 dark:border-gray-700">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>

                    <!-- Update Password -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl border border-gray-100 dark:border-gray-700">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <!-- Delete User -->
                    <div class="md:col-span-2 p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl border border-red-100 dark:border-red-900/30">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

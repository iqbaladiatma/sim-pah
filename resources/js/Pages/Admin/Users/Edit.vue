<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
    institutions: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "", // Empty for edit
    role: props.user.role,
    institution_id: props.user.institution_id || "",
    phone: props.user.phone || "",
});

const submit = () => {
    form.put(route("admin.users.update", props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <header class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Update Informasi User</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Ubah detail pengguna. Kosongkan password jika tidak ingin mengubahnya.
                                </p>
                            </div>
                            <Link :href="route('admin.users.index')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 font-bold text-sm">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-xl">
                            <div class="mb-4">
                                <InputLabel for="name" value="Nama Lengkap" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="email" value="Email Address" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="password" value="Password (Opsional)" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    placeholder="Biarkan kosong jika tidak ingin mengubah"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <InputLabel for="role" value="Role / Peran" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    >
                                        <option value="super admin">Super Admin</option>
                                        <option value="admin">Admin</option>
                                        <option value="lembaga">Lembaga</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.role" />
                                </div>
                                
                                <div>
                                    <InputLabel for="institution_id" value="Lembaga (Opsional)" />
                                    <select
                                        id="institution_id"
                                        v-model="form.institution_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    >
                                        <option value="">- Pilih Lembaga -</option>
                                        <option :value="null">Bukan Lembaga</option>
                                        <option
                                            v-for="inst in institutions"
                                            :key="inst.id"
                                            :value="inst.id"
                                        >
                                            {{ inst.code }} - {{ inst.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.institution_id" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <InputLabel for="phone" value="No. Telepon (Opsional)" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <PrimaryButton :disabled="form.processing">
                                    Simpan Perubahan
                                </PrimaryButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Tersimpan.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

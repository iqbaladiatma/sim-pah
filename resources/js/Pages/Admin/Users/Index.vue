<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    users: Object,
    institutions: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    role: "karyawan",
    institution_id: "",
    phone: "",
});

const isModalOpen = ref(false);
const editingUser = ref(null);

const openModal = (user = null) => {
    if (user) {
        editingUser.value = user;
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
        form.institution_id = user.institution_id;
        form.phone = user.phone;
        form.password = ""; // Don't fill password on edit
    } else {
        editingUser.value = null;
        form.reset();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingUser.value) {
        form.put(route("admin.users.update", editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.users.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteUser = (id) => {
    if (confirm("Yakin ingin menghapus user ini?")) {
        form.delete(route("admin.users.destroy", id));
    }
};
</script>

<template>
    <Head title="Manajemen User" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add Button -->
                <div class="mb-6 flex justify-end">
                    <button
                        @click="openModal()"
                        class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600 transition"
                    >
                        + Tambah User
                    </button>
                </div>

                <!-- Table -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Email
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr v-for="user in users.data" :key="user.id">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-bold"
                                    >
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 capitalize">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-blue-100 text-blue-800':
                                                    user.role === 'admin',
                                                'bg-green-100 text-green-800':
                                                    user.role === 'karyawan',
                                            }"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{
                                            user.institution
                                                ? user.institution.name
                                                : "-"
                                        }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            @click="openModal(user)"
                                            class="text-blue-600 hover:text-blue-900 mr-2"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6"
            >
                <h3
                    class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100"
                >
                    {{ editingUser ? "Edit User" : "Tambah User Baru" }}
                </h3>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Nama</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="form.errors.name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Email</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="form.errors.email"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            :required="!editingUser"
                        />
                        <div
                            v-if="form.errors.password"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.password }}
                        </div>
                        <p
                            v-if="editingUser"
                            class="text-xs text-gray-500 mt-1"
                        >
                            Kosongkan jika tidak ingin mengubah password.
                        </p>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Role</label
                        >
                        <select
                            v-model="form.role"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="karyawan">Karyawan</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div
                            v-if="form.errors.role"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.role }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Lembaga</label
                        >
                        <select
                            v-model="form.institution_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
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
                        <div
                            v-if="form.errors.institution_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.institution_id }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600"
                            :disabled="form.processing"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

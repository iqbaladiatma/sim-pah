<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    requests: Object,
});

const adminForm = useForm({
    status: "approved",
    admin_note: "",
});

const isAdminModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const selectedRequest = ref(null);

const createForm = useForm({
    type: "utilitas",
    title: "",
    description: "",
    estimated_cost: 0,
    photo_evidence: null,
});

const openCreateModal = () => {
    createForm.reset();
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
    createForm.reset();
    createForm.clearErrors();
};

const submitCreate = () => {
    createForm.post(route("requests.store"), {
        onSuccess: () => closeCreateModal(),
    });
};

const onFileChange = (e) => {
    createForm.photo_evidence = e.target.files[0];
};

const openAdminModal = (req) => {
    selectedRequest.value = req;
    adminForm.status = "approved";
    adminForm.admin_note = "";
    isAdminModalOpen.value = true;
};

const closeAdminModal = () => {
    isAdminModalOpen.value = false;
    selectedRequest.value = null;
    adminForm.reset();
};

const submitAdminAction = () => {
    if (selectedRequest.value) {
        adminForm.put(route("admin.requests.update", selectedRequest.value.id), {
            onSuccess: () => closeAdminModal(),
        });
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "approved":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};
</script>

<template>
    <Head title="Manajemen Pengajuan (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen Pengajuan (Utilitas, B7, Darurat)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add Button -->
                <div class="mb-6 flex justify-end">
                    <button
                        @click="openCreateModal()"
                        class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600 transition shadow-sm font-bold"
                    >
                        + Buat Pengajuan Baru
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
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Judul
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Est. Biaya
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Status
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
                                <tr v-for="req in requests.data" :key="req.id">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ req.user.institution?.code || 'ADMIN' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap capitalize"
                                    >
                                        {{ req.type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold">
                                            {{ req.title }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 truncate max-w-xs"
                                        >
                                            {{ req.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        Rp
                                        {{
                                            new Intl.NumberFormat(
                                                "id-ID",
                                            ).format(req.estimated_cost)
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="getStatusColor(req.status)"
                                        >
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a
                                            v-if="req.photo_evidence"
                                            :href="`/storage/${req.photo_evidence}`"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-900 mr-4 text-sm"
                                            >Lihat Foto</a
                                        >
                                        <button
                                            v-if="req.status === 'pending'"
                                            @click="openAdminModal(req)"
                                            class="text-green-600 hover:text-green-900 font-bold"
                                        >
                                            Proses
                                        </button>
                                        <span
                                            v-if="req.admin_note"
                                            class="block text-xs text-gray-500 mt-1 max-w-[150px] ml-auto"
                                            >Note: {{ req.admin_note }}</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Admin Action Modal -->
        <div
            v-if="isAdminModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6"
            >
                <h3
                    class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100"
                >
                    Proses Pengajuan
                </h3>
                <p class="mb-4 text-sm text-gray-500">
                    {{ selectedRequest?.title }}
                </p>

                <form @submit.prevent="submitAdminAction">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Keputusan</label
                        >
                        <select
                            v-model="adminForm.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="approved">Setujui</option>
                            <option value="rejected">Tolak</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Catatan Admin</label
                        >
                        <textarea
                            v-model="adminForm.admin_note"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Opsional"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeAdminModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            :disabled="adminForm.processing"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Modal -->
        <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6"
            >
                <h3
                    class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100"
                >
                    Buat Pengajuan Baru (Admin)
                </h3>

                <form @submit.prevent="submitCreate">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Tipe Pengajuan</label
                        >
                        <select
                            v-model="createForm.type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="utilitas">
                                Utilitas (Air, Listrik, Internet)
                            </option>
                            <option value="b7">B7 (Beras, Bumbu, dll)</option>
                            <option value="darurat">Darurat</option>
                        </select>
                        <div
                            v-if="createForm.errors.type"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ createForm.errors.type }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Judul</label
                        >
                        <input
                            v-model="createForm.title"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="createForm.errors.title"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ createForm.errors.title }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Deskripsi</label
                        >
                        <textarea
                            v-model="createForm.description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        ></textarea>
                        <div
                            v-if="createForm.errors.description"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ createForm.errors.description }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Estimasi Biaya</label
                        >
                        <input
                            v-model="createForm.estimated_cost"
                            type="number"
                            min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="createForm.errors.estimated_cost"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ createForm.errors.estimated_cost }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Foto Bukti</label
                        >
                        <input
                            @input="onFileChange"
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300"
                            required
                        />
                        <div
                            v-if="createForm.errors.photo_evidence"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ createForm.errors.photo_evidence }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeCreateModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600 font-bold"
                            :disabled="createForm.processing"
                        >
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    institutions: Object,
});

const form = useForm({
    name: "",
    code: "",
    description: "",
});

const isModalOpen = ref(false);
const editingInstitution = ref(null);

const openModal = (institution = null) => {
    if (institution) {
        editingInstitution.value = institution;
        form.name = institution.name;
        form.code = institution.code;
        form.description = institution.description;
    } else {
        editingInstitution.value = null;
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
    if (editingInstitution.value) {
        form.put(
            route("admin.institutions.update", editingInstitution.value.id),
            {
                onSuccess: () => closeModal(),
            },
        );
    } else {
        form.post(route("admin.institutions.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteInstitution = (id) => {
    if (confirm("Yakin ingin menghapus lembaga ini?")) {
        form.delete(route("admin.institutions.destroy", id));
    }
};
</script>

<template>
    <Head title="Manajemen Lembaga" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen Lembaga (Institutions)
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
                        + Tambah Lembaga
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
                                        Kode
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Nama Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Deskripsi
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
                                <tr
                                    v-for="inst in institutions.data"
                                    :key="inst.id"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-bold"
                                    >
                                        {{ inst.code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ inst.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ inst.description || "-" }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            @click="openModal(inst)"
                                            class="text-blue-600 hover:text-blue-900 mr-2"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteInstitution(inst.id)"
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
                    {{
                        editingInstitution
                            ? "Edit Lembaga"
                            : "Tambah Lembaga Baru"
                    }}
                </h3>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Nama Lembaga</label
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
                            >Kode Unit</label
                        >
                        <input
                            v-model="form.code"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="form.errors.code"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.code }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Deskripsi</label
                        >
                        <textarea
                            v-model="form.description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        ></textarea>
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

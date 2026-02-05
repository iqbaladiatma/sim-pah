<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

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

const importForm = useForm({
    file: null,
});

const isImportModalOpen = ref(false);

const openImportModal = () => {
    isImportModalOpen.value = true;
};

const closeImportModal = () => {
    isImportModalOpen.value = false;
    importForm.reset();
};

const handleImport = () => {
    importForm.post(route("admin.institutions.import"), {
        onSuccess: () => closeImportModal(),
    });
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
                <div class="mb-6 flex justify-end gap-2">
                    <button
                        @click="openImportModal"
                        class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-sm font-bold text-sm"
                    >
                        📂 Impor Excel
                    </button>
                    <button
                        @click="openModal()"
                        class="px-4 py-2 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition shadow-sm font-bold text-sm"
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
                <!-- Pagination -->
                <Pagination :links="institutions.links" />
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

        <!-- Import Modal -->
        <div
            v-if="isImportModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md p-8 border border-gray-100 dark:border-gray-700"
            >
                <h3 class="text-xl font-black mb-6 text-gray-900 dark:text-white">Impor Lembaga</h3>
                <form @submit.prevent="handleImport">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input
                            @input="importForm.file = $event.target.files[0]"
                            type="file"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                            required
                        />
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl mb-6 text-[10px] text-gray-500 leading-relaxed border border-gray-100 dark:border-gray-800">
                        <p class="font-black text-gray-700 dark:text-gray-300 uppercase mb-2">Format Headers:</p>
                        <code class="block bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 font-mono">name, code, description</code>
                        <p class="mt-2 text-red-500 font-bold italic">* Pastikan kode lembaga unik.</p>
                        <a :href="route('admin.institutions.template')" class="mt-4 block text-center py-2 px-4 bg-blue-50 text-blue-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition border border-blue-100">
                             📥 Download Contoh CSV
                        </a>
                    </div>
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" @click="closeImportModal" class="px-6 py-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 font-bold transition">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-lg shadow-green-600/20 transition" :disabled="importForm.processing">Mulai Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

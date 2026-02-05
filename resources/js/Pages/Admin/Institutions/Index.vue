<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";

const props = defineProps({
    institutions: Object,
});

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
        router.delete(route("admin.institutions.destroy", id));
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
                <div class="mb-6 flex justify-end gap-3">
                    <button
                        @click="openImportModal"
                        class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Impor Excel
                    </button>
                    <Link
                        :href="route('admin.institutions.create')"
                        class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Lembaga
                    </Link>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
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
                                        class="px-6 py-3 text-right text-xs font-extrabold text-gray-500 uppercase tracking-wider">
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
                                        <Link
                                            :href="route('admin.institutions.edit', inst.id)"
                                            class="text-blue-600 hover:text-blue-900 font-semibold mr-2 underline">
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteInstitution(inst.id)"
                                            class="text-red-600 hover:text-red-900 font-semibold underline">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="inst in institutions.data" :key="inst.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <span class="px-2 py-0.5 bg-pail-gold/10 text-pail-gold font-bold text-xs rounded mb-2 inline-block">{{ inst.code }}</span>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ inst.name }}</h3>
                                </div>
                            </div>

                            <!-- Description -->
                            <div v-if="inst.description" class="mb-4">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">Deskripsi</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ inst.description }}</p>
                            </div>
                            <div v-else class="mb-4">
                                <p class="text-sm text-gray-400 italic">Tidak ada deskripsi</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link :href="route('admin.institutions.edit', inst.id)" class="flex-1 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 font-semibold text-sm text-center">
                                    Edit
                                </Link>
                                <button @click="deleteInstitution(inst.id)" class="flex-1 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 font-semibold text-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="institutions.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada data lembaga.</p>
                    </div>
                </div>
                <!-- Pagination -->
                <Pagination :links="institutions.links" />
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
                        <a :href="route('admin.institutions.template')" class="mt-4 flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs uppercase tracking-wide hover:bg-blue-100 transition-all duration-200 border border-blue-100">
                            <DownloadIcon className="w-4 h-4" /> Download Template CSV
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

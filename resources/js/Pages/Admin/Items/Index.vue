<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import RocketIcon from "@/Components/Icons/RocketIcon.vue";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";

const props = defineProps({
    items: Object,
});

const deleteItem = (id) => {
    if (confirm("Yakin ingin menghapus barang ini?")) {
        router.delete(route("admin.items.destroy", id));
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
    importForm.post(route("admin.items.import"), {
        onSuccess: () => closeImportModal(),
    });
};
</script>

<template>
    <Head title="Manajemen Barang (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Manajemen Seluruh Barang (Inventaris)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-[95%] sm:px-6 lg:px-8">
                <!-- Action Buttons -->
                <div class="mb-6 flex justify-end gap-3">
                    <button
                        @click="openImportModal"
                        class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Impor Excel
                    </button>
                    <Link
                        :href="route('admin.items.create')"
                        class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Barang
                    </Link>
                </div>

                <!-- Desktop Table View (hidden on mobile/tablet) -->
                <div class="hidden lg:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto p-2">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-4 py-4 text-center">No.</th>
                                    <th class="px-4 py-4 text-left">Lembaga</th>
                                    <th class="px-4 py-4 text-left font-black text-rose-500">RUANGAN</th>
                                    <th class="px-4 py-4 text-left">JENIS BARANG</th>
                                    <th class="px-4 py-4 text-left">TANGGAL PEMBUKUAN</th>
                                    <th class="px-4 py-4 text-center">KUANTITAS</th>
                                    <th class="px-4 py-4 text-left">SUMBER PEROLEHAN</th>
                                    <th class="px-4 py-4 text-center">KEADAAN</th>
                                    <th class="px-4 py-4 text-left">PENANGGUNG JAWAB</th>
                                    <th class="px-4 py-4 text-left">KETERANGAN</th>
                                    <th class="px-4 py-4 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(item, index) in items.data" :key="item.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-[11px]">
                                    <td class="px-4 py-4 text-center text-gray-400 font-mono">{{ index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-pail-gold">{{ item.institution?.code || '-' }}</span>
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-gray-700 dark:text-gray-300 capitalize text-nowrap">{{ item.room?.name || '-' }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-900 dark:text-white uppercase">{{ item.name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ item.purchase_date || '-' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-1 rounded-lg font-semibold bg-gray-50 text-gray-900">
                                            {{ item.stock }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 italic">{{ item.source }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full font-semibold uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-500">{{ item.responsible_person || '-' }}</td>
                                    <td class="px-4 py-4 text-gray-400 max-w-[150px] truncate" :title="item.note">{{ item.note || '-' }}</td>
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        <Link :href="route('admin.items.edit', item.id)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition mr-1 font-semibold underline">Edit</Link>
                                        <button @click="deleteItem(item.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition font-semibold underline">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td colspan="11" class="px-6 py-20 text-center text-gray-400 italic font-medium">Belum ada data inventaris.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile/tablet) -->
                <div class="lg:hidden space-y-4">
                    <div v-for="(item, index) in items.data" :key="item.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-pail-gold/10 text-pail-gold font-bold text-xs rounded">{{ item.institution?.code }}</span>
                                        <span class="px-2 py-0.5 rounded-full font-semibold uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400'">
                                            {{ item.condition }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base uppercase">{{ item.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ item.room?.name || '-' }}</p>
                                </div>
                            </div>

                            <!-- Info Grid -->
                            <div class="space-y-2.5 mb-4">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.purchase_date || '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Kuantitas</span>
                                    <span class="px-2.5 py-1 rounded-lg font-semibold text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">{{ item.stock }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Sumber</span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 italic text-right">{{ item.source }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Penanggung Jawab</span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 text-right">{{ item.responsible_person || '-' }}</span>
                                </div>
                                <div v-if="item.note" class="py-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">Keterangan</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ item.note }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link :href="route('admin.items.edit', item.id)" class="flex-1 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 font-semibold text-sm text-center">
                                    Edit
                                </Link>
                                <button @click="deleteItem(item.id)" class="flex-1 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 font-semibold text-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="items.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada data inventaris.</p>
                    </div>
                </div>
                <!-- Pagination -->
                <Pagination :links="items.links" />
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white text-center flex items-center justify-center gap-2">
                    <RocketIcon className="w-5 h-5 text-pail-gold" /> Import Inventory
                </h3>
                <form @submit.prevent="handleImport">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input
                            @input="importForm.file = $event.target.files[0]"
                            type="file"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition shadow-sm"
                            required
                        />
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl mb-8 text-[10px] text-gray-500 leading-relaxed border border-gray-100 dark:border-gray-800">
                        <p class="font-black text-gray-700 dark:text-gray-300 uppercase mb-3 text-center border-b pb-2">📋 Format Header Kolom:</p>
                        <div class="space-y-1 font-mono text-[9px] text-center">
                            <code>lembaga_kode, ruangan, jenis_barang, tanggal_pembukuan_pembelian, kuantitas, sumber_perolehan_barang, keadaan_barang, penanggung_jawab, keterangan</code>
                        </div>
                        <a :href="route('admin.items.template')" class="mt-4 flex items-center justify-center gap-2 py-3 px-4 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs uppercase tracking-wide hover:bg-blue-100 transition-all duration-200 border border-blue-100 shadow-sm">
                            <DownloadIcon className="w-4 h-4" /> Download Template (.csv)
                        </a>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-2xl hover:bg-green-700 font-black shadow-lg shadow-green-600/20 transition uppercase tracking-widest" :disabled="importForm.processing">Mulai Impor Sekarang</button>
                        <button type="button" @click="closeImportModal" class="w-full py-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 font-bold transition">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import RocketIcon from "@/Components/Icons/RocketIcon.vue";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import { formatRupiah } from "@/Utils/format";

const props = defineProps({
    items: Object,
    stats: Object,
});

// Delete Confirmation Modal State
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

const deleteItem = (id) => {
    itemToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route("admin.items.destroy", itemToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            itemToDelete.value = null;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    itemToDelete.value = null;
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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Gudang Inventaris
                </h2>
                <div class="flex items-center gap-4">
                    <button
                        @click="openImportModal"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Import
                    </button>
                    <Link
                        :href="route('admin.items.create')"
                        class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Barang
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-[95%] sm:px-6 lg:px-8 space-y-10">
                
                <!-- Stats Dynamic Carousel -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.2em] text-[10px] mb-3 relative z-10">Total Unit</h3>
                        <div class="flex items-end gap-2 relative z-10">
                            <div class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">{{ stats.total_items }}</div>
                             <span class="text-[10px] font-black text-gray-300 uppercase mb-1">SKU</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.2em] text-[10px] mb-3 relative z-10">Stok Fisik</h3>
                        <div class="flex items-end gap-2 relative z-10 text-blue-600">
                            <div class="text-5xl font-black tracking-tighter leading-none">{{ stats.total_stock }}</div>
                            <span class="text-[10px] font-black text-blue-200 uppercase mb-1">Items</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50/50 rounded-full group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.2em] text-[10px] mb-3 relative z-10 text-red-500">Kritis</h3>
                        <div class="flex items-end gap-2 relative z-10 text-red-600">
                            <div class="text-5xl font-black tracking-tighter leading-none">{{ stats.low_stock }}</div>
                            <span class="text-[10px] font-black text-red-200 uppercase mb-1">Low</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-10 rounded-[3rem] shadow-2xl shadow-pail-gold/30 relative overflow-hidden group hover:-translate-y-1 transition-all duration-500">
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <h3 class="text-white/70 font-black uppercase tracking-[0.2em] text-[10px] mb-3 relative z-10">Kapitalitas Aset</h3>
                        <div class="text-2xl font-black text-white font-mono tracking-tighter relative z-10">{{ formatRupiah(stats.total_value) }}</div>
                    </div>
                </div>

                <!-- Desktop Table Master -->
                <div class="hidden lg:block bg-white dark:bg-gray-800 shadow-2xl rounded-[3.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
                    <div class="p-10">
                        <table class="min-w-full border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.3em]">
                                    <th class="px-6 py-4 text-left">Unit</th>
                                    <th class="px-6 py-4 text-left">Lokasi</th>
                                    <th class="px-6 py-4 text-left">Identitas Barang</th>
                                    <th class="px-6 py-4 text-center">Stok</th>
                                    <th class="px-6 py-4 text-center">Kondisi</th>
                                    <th class="px-6 py-4 text-right">Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in items.data" :key="item.id" class="group transition-all hover:translate-x-1 duration-300">
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-l-[2rem] border-y border-l border-gray-50 dark:border-gray-800/50">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-pail-gold uppercase tracking-tighter mb-1">{{ item.institution?.code || '-' }}</span>
                                            <span class="text-[9px] font-bold text-gray-400 truncate max-w-[100px]">{{ item.institution?.name || '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <span class="font-black text-gray-600 dark:text-gray-400 text-[11px] uppercase tracking-tight">{{ item.room?.name || '-' }}</span>
                                    </td>
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 dark:text-white text-sm uppercase tracking-tighter mb-1">{{ item.name }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-white dark:bg-gray-800 px-2 py-0.5 rounded border border-gray-100 dark:border-gray-700">PJ: {{ item.responsible_person || '-' }}</span>
                                                <span class="text-[9px] font-bold text-gray-300 italic">{{ item.purchase_date || '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50 text-center">
                                        <div class="inline-flex flex-col items-center justify-center w-14 h-14 rounded-2xl border-2 transition-all group-hover:scale-110" 
                                            :class="item.stock <= item.min_stock ? 'bg-red-50 border-red-200 text-red-600 shadow-lg shadow-red-500/10' : 'bg-white border-gray-100 text-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-white'">
                                            <span class="font-black font-mono text-xl leading-none">{{ item.stock }}</span>
                                            <span class="text-[8px] font-black uppercase mt-1">QTY</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50 text-center">
                                        <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] border shadow-sm"
                                            :class="item.condition === 'Baik' ? 
                                                    'bg-green-50 text-green-600 border-green-100' : 
                                                    'bg-orange-50 text-orange-600 border-orange-100'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-r-[2rem] border-y border-r border-gray-50 dark:border-gray-800/50 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link 
                                                :href="route('admin.items.edit', item.id)"
                                                class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 text-gray-400 flex items-center justify-center hover:bg-pail-gold hover:text-white transition-all shadow-sm border border-gray-100 dark:border-gray-700 group/btn"
                                            >
                                                <svg class="w-5 h-5 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </Link>
                                            <button 
                                                @click="deleteItem(item.id)"
                                                class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 text-red-300 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100 dark:border-gray-700 group/btn"
                                            >
                                                <svg class="w-5 h-5 group-hover/btn:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td colspan="6" class="px-6 py-40 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <PackageIcon className="w-16 h-16 mb-6 opacity-20" />
                                            <p class="font-black uppercase tracking-[0.3em] text-sm">Gudang Aset Kosong</p>
                                            <p class="text-xs text-gray-300 font-bold mt-2">Daftarkan barang inventaris untuk memulai tracking.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Premium Card Engine -->
                <div class="lg:hidden space-y-8">
                    <div v-for="item in items.data" :key="item.id" class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="p-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="px-4 py-2 rounded-2xl bg-gray-900 text-pail-gold text-[10px] font-black uppercase tracking-widest shadow-lg">
                                    {{ item.institution?.code || '-' }}
                                </div>
                                <span class="px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest border shadow-sm"
                                    :class="item.condition === 'Baik' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-orange-50 text-orange-600 border-orange-100'">
                                    {{ item.condition }}
                                </span>
                            </div>

                            <div class="mb-8">
                                <h3 class="font-black text-gray-900 dark:text-white text-2xl leading-tight mb-2 uppercase tracking-tighter">{{ item.name }}</h3>
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-pail-gold"></div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ item.room?.name || 'AREA BELUM TERDEFINISI' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 mb-10">
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                                    <span class="text-[9px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Kapasitas Stok</span>
                                    <div class="flex items-end gap-2">
                                        <span class="text-3xl font-black font-mono tracking-tighter" :class="item.stock <= item.min_stock ? 'text-red-500' : 'text-gray-900 dark:text-white'">{{ item.stock }}</span>
                                         <span class="text-[9px] font-black text-gray-300 uppercase mb-1.5">Item</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                                    <span class="text-[9px] font-black text-gray-400 uppercase block mb-2 tracking-widest">PJ Aset</span>
                                    <span class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tighter truncate block">{{ item.responsible_person || '-' }}</span>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <Link 
                                    :href="route('admin.items.edit', item.id)"
                                    class="flex-1 py-5 bg-pail-gold text-white rounded-[1.5rem] hover:bg-yellow-600 transition-all text-center font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-pail-gold/20"
                                >
                                    Update Aset
                                </Link>
                                <button 
                                    @click="deleteItem(item.id)"
                                    class="w-16 h-16 bg-red-50 text-red-500 rounded-[1.5rem] flex items-center justify-center border border-red-100 shadow-lg shadow-red-500/5 group-active:scale-95 transition-transform"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Engine -->
                <Pagination :links="items.links" />
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white text-center flex items-center justify-center gap-2">
                    <RocketIcon className="w-5 h-5 text-pail-gold" /> Impor Inventaris
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

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Hapus Barang"
            message="Apakah Anda yakin ingin menghapus barang ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua riwayat terkait."
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            variant="danger"
            @confirm="confirmDelete"
            @close="closeDeleteModal"
        />
    </AuthenticatedLayout>
</template>

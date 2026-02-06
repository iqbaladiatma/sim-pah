<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";

const props = defineProps({
    rooms: Object,
    stats: {
        type: Object,
        default: () => ({ total: 0, latest: '-' }),
    },
});

const deleteRoom = (id) => {
    if (confirm("Yakin ingin menghapus ruangan ini?")) {
        router.delete(route("admin.rooms.destroy", id));
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
    importForm.post(route("admin.rooms.import"), {
        onSuccess: () => closeImportModal(),
    });
};
</script>

<template>
    <Head title="Manajemen Ruangan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Master Data Ruangan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-white shadow-lg shadow-blue-600/20 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                        <h3 class="text-white/80 font-bold uppercase tracking-wider text-xs mb-1 relative z-10">Total Ruangan</h3>
                        <div class="text-3xl font-black relative z-10">{{ stats.total }}</div>
                        <div class="text-white/60 text-xs mt-2 relative z-10">Ruangan Terdata</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                         <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Penambahan Terakhir</h3>
                         <div class="text-lg font-bold text-gray-800 dark:text-white truncate">{{ stats.latest }}</div>
                         <div class="text-gray-400 text-xs mt-1">Ruangan Baru</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mb-6 flex justify-end gap-3">
                    <button
                        @click="openImportModal"
                        class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Impor Excel
                    </button>
                    <Link
                        :href="route('admin.rooms.create')"
                        class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Ruangan
                    </Link>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-left">No</th>
                                    <th class="px-6 py-4 text-left">Lembaga</th>
                                    <th class="px-6 py-4 text-left">Nama Ruangan</th>
                                    <th class="px-6 py-4 text-left">Deskripsi / Lokasi</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(room, index) in rooms.data" :key="room.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                    <td class="px-6 py-4 font-bold text-gray-500">
                                        {{ (rooms.current_page - 1) * rooms.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-pail-gold">{{ room.institution?.code }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ room.name }}</td>
                                    <td class="px-6 py-4 text-gray-500 italic">{{ room.description || '-' }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <Link :href="route('admin.rooms.edit', room.id)" class="text-blue-600 hover:text-blue-900 font-semibold mr-3 underline">Edit</Link>
                                        <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-900 font-semibold underline">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="rooms.data.length === 0">
                                    <td colspan="4" class="px-6 py-20 text-center text-gray-400 italic font-medium text-sm">Belum ada data ruangan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="room in rooms.data" :key="room.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <span class="px-2 py-0.5 bg-pail-gold/10 text-pail-gold font-bold text-xs rounded mb-2 inline-block">{{ room.institution?.code }}</span>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ room.name }}</h3>
                                </div>
                            </div>

                            <!-- Description -->
                            <div v-if="room.description" class="mb-4">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">Deskripsi / Lokasi</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">{{ room.description }}</p>
                            </div>
                            <div v-else class="mb-4">
                                <p class="text-sm text-gray-400 italic">Tidak ada deskripsi</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link :href="route('admin.rooms.edit', room.id)" class="flex-1 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 font-semibold text-sm text-center">
                                    Edit
                                </Link>
                                <button @click="deleteRoom(room.id)" class="flex-1 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 font-semibold text-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="rooms.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada data ruangan.</p>
                    </div>
                </div>
                <!-- Pagination -->
                <Pagination :links="rooms.links" />
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white text-center flex items-center justify-center gap-2">
                    <FolderIcon className="w-5 h-5 text-green-600" /> Import Master Ruangan
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
                        <p class="font-black text-gray-700 dark:text-gray-300 uppercase mb-3 text-center border-b pb-2">📋 Format Kolom:</p>
                        <div class="space-y-1 font-mono text-[9px] text-center">
                            <code>institution_code, name, description</code>
                        </div>
                        <a :href="route('admin.rooms.template')" class="mt-4 flex items-center justify-center gap-2 py-3 px-4 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs uppercase tracking-wide hover:bg-blue-100 transition-all duration-200 border border-blue-100 shadow-sm">
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

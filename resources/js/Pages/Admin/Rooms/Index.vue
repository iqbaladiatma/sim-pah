<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import XIcon from "@/Components/Icons/XIcon.vue";
import SparklesIcon from "@/Components/Icons/SparklesIcon.vue";
import RocketIcon from "@/Components/Icons/RocketIcon.vue";

const props = defineProps({
    rooms: Object,
    stats: {
        type: Object,
        default: () => ({ total: 0, latest: '-' }),
    },
});

// Delete Confirmation Modal State
const showDeleteModal = ref(false);
const roomToDelete = ref(null);

const deleteRoom = (id) => {
    roomToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route("admin.rooms.destroy", roomToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            roomToDelete.value = null;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    roomToDelete.value = null;
};
</script>

<template>
    <Head title="Manajemen Ruangan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 sm:gap-0">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Master Data Ruangan
                </h2>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                    <Link
                        :href="route('admin.rooms.import_page')"
                        class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Import Excel
                    </Link>
                    <Link
                        :href="route('admin.rooms.create')"
                        class="px-6 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Ruangan
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-10">
                
                <!-- Physical Infrastructure Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.3em] text-[8px] sm:text-[10px] mb-3 relative z-10">Kapasitas Ruangan</h3>
                        <div class="flex items-end gap-2 relative z-10 text-blue-600">
                            <div class="text-3xl sm:text-5xl font-black tracking-tighter leading-none">{{ stats.total }}</div>
                            <span class="text-[8px] sm:text-[10px] font-black text-blue-200 uppercase mb-1">Total</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.3em] text-[8px] sm:text-[10px] mb-3 relative z-10">Aktivitas Terkini</h3>
                        <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white truncate uppercase tracking-tighter relative z-10 h-10 sm:h-12 flex items-center leading-tight">
                            {{ stats.latest }}
                        </div>
                    </div>

                    <div class="hidden md:block bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-10 rounded-[3rem] shadow-2xl shadow-pail-gold/30 relative overflow-hidden group hover:-translate-y-1 transition-all duration-500 col-span-2">
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center justify-between h-full relative z-10">
                            <div>
                                <h3 class="text-white/70 font-black uppercase tracking-[0.3em] text-[10px] mb-3">Asset Mapping</h3>
                                <div class="text-3xl font-black text-white uppercase tracking-tighter leading-none">Pemetaan Geografis</div>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-[1.5rem] flex items-center justify-center backdrop-blur-md border border-white/30 shadow-xl group-hover:-rotate-12 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table Master -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-[3.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-pail-gold opacity-5 rounded-full blur-3xl"></div>
                    <div class="p-10 relative z-10">
                        <table class="min-w-full border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.3em]">
                                    <th class="px-8 py-4 text-center">No</th>
                                    <th class="px-8 py-4 text-left">Lembaga Pemilik</th>
                                    <th class="px-8 py-4 text-left">Nama Ruangan / Area</th>
                                    <th class="px-8 py-4 text-left">Detail Geografis</th>
                                    <th class="px-8 py-4 text-right">Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(room, index) in rooms.data" :key="room.id" class="group transition-all hover:translate-x-1 duration-300">
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-l-[2rem] border-y border-l border-gray-50 dark:border-gray-800/50 text-center font-black text-gray-300">
                                        {{ (rooms.current_page - 1) * rooms.per_page + index + 1 }}
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="flex items-center gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-pail-gold"></span>
                                            <span class="text-xs font-black text-pail-gold uppercase tracking-[0.2em]">{{ room.institution?.code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="font-black text-gray-900 dark:text-white uppercase tracking-tighter text-sm">{{ room.name }}</div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="text-[11px] text-gray-400 font-bold italic line-clamp-1 max-w-xs">{{ room.description || "Catatan lokasi belum tersedia." }}</div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-r-[2rem] border-y border-r border-gray-50 dark:border-gray-800/50 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link :href="route('admin.rooms.edit', room.id)" class="px-6 py-3 bg-gray-900 text-white rounded-full hover:bg-pail-gold transition-all shadow-xl font-black text-[10px] uppercase tracking-widest">Edit</Link>
                                            <button @click="deleteRoom(room.id)" class="px-6 py-3 bg-red-50 text-red-600 rounded-full hover:bg-red-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-widest border border-red-100">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="rooms.data.length === 0">
                                    <td colspan="5" class="px-8 py-32 text-center text-gray-300 font-black uppercase tracking-[0.3em] text-xs">
                                        Belum ada infrastruktur fisik yang terdaftar.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Premium Card Engine -->
                <div class="md:hidden space-y-6">
                    <div v-for="room in rooms.data" :key="room.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="p-6 sm:p-10">
                            <div class="flex items-center justify-between mb-6">
                                <div class="px-3 py-1.5 rounded-xl bg-gray-900 text-pail-gold text-[8px] font-black uppercase tracking-[0.2em] shadow-lg">
                                    {{ room.institution?.code }}
                                </div>
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                            </div>

                            <div class="mb-6">
                                <h3 class="font-black text-gray-900 dark:text-white text-xl leading-tight mb-2 uppercase tracking-tighter">{{ room.name }}</h3>
                                <p class="text-[10px] text-gray-500 font-bold italic leading-relaxed">{{ room.description || "Detail lokasi sedang dalam proses pendokumentasian." }}</p>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <Link :href="route('admin.rooms.edit', room.id)" class="flex-1 py-4 bg-pail-gold text-white rounded-full transition-all text-center font-black text-[9px] uppercase tracking-[0.2em] shadow-lg shadow-pail-gold/20">
                                    Edit Area
                                </Link>
                                <button @click="deleteRoom(room.id)" class="flex-1 py-4 bg-red-50 text-red-600 rounded-full transition-all text-center font-black text-[9px] uppercase tracking-[0.2em] border border-red-100">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Empty State -->
                    <div v-if="rooms.data.length === 0" class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 p-24 text-center relative overflow-hidden">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-gray-50 opacity-50 rounded-full blur-3xl"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mb-8 border border-gray-100 dark:border-gray-700 shadow-inner">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            <p class="text-gray-400 font-black uppercase tracking-[0.3em] text-sm italic">Pusat Infrastruktur Kosong</p>
                            <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase">Definisikan area untuk mengorganisir aset.</p>
                        </div>
                    </div>
                </div>

                <!-- Pagination Blueprint -->
                <div class="mt-8 flex justify-center">
                    <Pagination :links="rooms.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Hapus Ruangan"
            message="Apakah Anda yakin ingin menghapus ruangan ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            variant="danger"
            @confirm="confirmDelete"
            @close="closeDeleteModal"
        />
    </AuthenticatedLayout>
</template>

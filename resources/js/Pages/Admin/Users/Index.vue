<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import Pagination from "@/Components/Pagination.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    users: Object,
    stats: Object,
});

// Delete Confirmation Modal State
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const deleteUser = (id) => {
    userToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route("admin.users.destroy", userToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            userToDelete.value = null;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    userToDelete.value = null;
};
</script>

<template>
    <Head title="Manajemen User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 md:w-2 md:h-10 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            Manajemen Pengguna
                        </h2>
                        <p class="text-[8px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.users.create')"
                        class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah User
                    </Link>
                </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 md:w-2 md:h-10 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            Manajemen Pengguna
                        </h2>
                        <p class="text-[8px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.users.create')"
                        class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah User
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Total User</h3>
                        <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total }}</div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Admin Pusat</h3>
                        <div class="text-3xl font-black text-blue-600 tracking-tighter">{{ stats.admins }}</div>
                        <div class="text-3xl font-black text-blue-600 tracking-tighter">{{ stats.admins }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">User Lembaga</h3>
                        <div class="text-3xl font-black text-green-600 tracking-tighter">{{ stats.lembaga }}</div>
                        <div class="text-3xl font-black text-green-600 tracking-tighter">{{ stats.lembaga }}</div>
                    </div>
                    <div class="hidden md:block bg-gradient-to-br from-gray-900 to-black p-6 rounded-3xl shadow-xl col-span-1">
                    <div class="hidden md:block bg-gradient-to-br from-gray-900 to-black p-6 rounded-3xl shadow-xl col-span-1">
                        <div class="flex flex-col justify-center h-full">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Keamanan</h3>
                            <div class="text-xs font-black text-white uppercase tracking-widest">Akses Berlapis</div>
                            <div class="text-xs font-black text-white uppercase tracking-widest">Akses Berlapis</div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-6 text-left">No</th>
                                    <th class="px-6 py-6 text-left">Pengguna</th>
                                    <th class="px-6 py-6 text-left">Role & Lembaga</th>
                                    <th class="px-6 py-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(user, index) in users.data" :key="user.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all group">
                                    <td class="px-6 py-5 font-black text-gray-300">
                                        {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-center font-black text-base shadow-sm border border-gray-200 dark:border-gray-600 group-hover:bg-pail-gold group-hover:text-white group-hover:border-pail-gold transition-all duration-500">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900 dark:text-white uppercase tracking-tight text-xs">{{ user.name }}</div>
                                                <div class="text-[10px] text-gray-400 font-medium">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="px-2.5 py-0.5 inline-flex text-[8px] font-black uppercase tracking-widest rounded-lg w-fit border shadow-sm transition-all"
                                                :class="{
                                                    'bg-purple-50 text-purple-700 border-purple-100': user.role === 'super admin',
                                                    'bg-blue-50 text-blue-700 border-blue-100': user.role === 'admin',
                                                    'bg-green-50 text-green-700 border-green-100': user.role === 'lembaga',
                                                }">
                                                {{ user.role }}
                                            </span>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                {{ user.institution ? user.institution.name : "ADMINISTRATOR" }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('admin.users.edit', user.id)" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-black transition-all shadow-lg font-black text-[9px] uppercase tracking-widest">Edit</Link>
                                            <button @click="deleteUser(user.id)" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-widest border border-red-100">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-6">
                    <div v-for="user in users.data" :key="user.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-pail-gold text-white flex items-center justify-center text-xl font-black shadow-xl shadow-pail-gold/20">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-black text-gray-900 dark:text-white text-lg uppercase tracking-tight truncate leading-none mb-1">{{ user.name }}</h3>
                                    <p class="text-[10px] text-gray-400 font-medium truncate">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <div>
                                    <span class="text-[8px] uppercase font-black text-gray-400 tracking-widest block mb-1.5">Role Akses</span>
                                    <span class="px-2.5 py-0.5 inline-flex text-[8px] font-black uppercase tracking-widest rounded-lg transition-all border shadow-sm"
                                        :class="{
                                            'bg-purple-50 text-purple-700 border-purple-100': user.role === 'super admin',
                                            'bg-blue-50 text-blue-700 border-blue-100': user.role === 'admin',
                                            'bg-green-50 text-green-700 border-green-100': user.role === 'lembaga',
                                        }">
                                        {{ user.role }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[8px] uppercase font-black text-gray-400 tracking-widest block mb-1.5">Penempatan</span>
                                    <span class="text-[9px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest leading-tight block">
                                        {{ user.institution ? user.institution.name : "ADMIN PUSAT" }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <Link :href="route('admin.users.edit', user.id)" class="flex-1 py-3.5 bg-gray-900 text-white rounded-xl transition text-center font-black text-[9px] uppercase tracking-widest shadow-xl shadow-black/10">
                                    Edit
                                </Link>
                                <button @click="deleteUser(user.id)" class="flex-1 py-3.5 bg-red-50 text-red-600 rounded-xl transition text-center font-black text-[9px] uppercase tracking-widest border border-red-100">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <Pagination :links="users.links" />
                </div>

                <div class="mt-8 flex justify-center">
                    <Pagination :links="users.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Hapus Pengguna"
            message="Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            variant="danger"
            @confirm="confirmDelete"
            @close="closeDeleteModal"
        />
    </AuthenticatedLayout>
</template>

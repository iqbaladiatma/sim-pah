<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    users: Object,
    stats: Object,
});

const deleteUser = (id) => {
    if (confirm("Yakin ingin menghapus user ini?")) {
        router.delete(route("admin.users.destroy", id));
    }
};
</script>

<template>
    <Head title="Manajemen User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Manajemen Pengguna
                </h2>
                <Link
                    :href="route('admin.users.create')"
                    class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Total User</h3>
                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Admin Pusat</h3>
                        <div class="text-4xl font-black text-blue-600 tracking-tighter">{{ stats.admins }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">User Lembaga</h3>
                        <div class="text-4xl font-black text-green-600 tracking-tighter">{{ stats.lembaga }}</div>
                    </div>
                    <div class="hidden md:block bg-gradient-to-br from-gray-900 to-black p-8 rounded-[2.5rem] shadow-xl col-span-1">
                        <div class="flex flex-col justify-center h-full">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Keamanan</h3>
                            <div class="text-sm font-black text-white uppercase tracking-widest">Akses Berlapis</div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
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
                                    <td class="px-6 py-8 font-black text-gray-300">
                                        {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-[1.25rem] bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-center font-black text-lg shadow-sm border border-gray-200 dark:border-gray-600 group-hover:bg-pail-gold group-hover:text-white group-hover:border-pail-gold transition-all duration-500">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ user.name }}</div>
                                                <div class="text-xs text-gray-400 font-medium">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-8">
                                        <div class="flex flex-col gap-2">
                                            <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-xl w-fit border shadow-sm transition-all"
                                                :class="{
                                                    'bg-purple-50 text-purple-700 border-purple-100': user.role === 'super admin',
                                                    'bg-blue-50 text-blue-700 border-blue-100': user.role === 'admin',
                                                    'bg-green-50 text-green-700 border-green-100': user.role === 'lembaga',
                                                }">
                                                {{ user.role }}
                                            </span>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                {{ user.institution ? user.institution.name : "ADMINISTRATOR" }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-8 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link :href="route('admin.users.edit', user.id)" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-black transition-all shadow-lg font-black text-[10px] uppercase tracking-widest">Edit</Link>
                                            <button @click="deleteUser(user.id)" class="px-5 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-widest border border-red-100">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-6">
                    <div v-for="user in users.data" :key="user.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-8">
                            <div class="flex items-center gap-5 mb-8">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-pail-gold text-white flex items-center justify-center text-2xl font-black shadow-xl shadow-pail-gold/20">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-black text-gray-900 dark:text-white text-xl uppercase tracking-tight truncate leading-none mb-1">{{ user.name }}</h3>
                                    <p class="text-xs text-gray-400 font-medium truncate">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 mb-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                                <div>
                                    <span class="text-[9px] uppercase font-black text-gray-400 tracking-widest block mb-2">Role Akses</span>
                                    <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-xl transition-all border shadow-sm"
                                        :class="{
                                            'bg-purple-50 text-purple-700 border-purple-100': user.role === 'super admin',
                                            'bg-blue-50 text-blue-700 border-blue-100': user.role === 'admin',
                                            'bg-green-50 text-green-700 border-green-100': user.role === 'lembaga',
                                        }">
                                        {{ user.role }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[9px] uppercase font-black text-gray-400 tracking-widest block mb-2">Penempatan</span>
                                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest leading-tight block">
                                        {{ user.institution ? user.institution.name : "ADMIN PUSAT" }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <Link :href="route('admin.users.edit', user.id)" class="flex-1 py-4 bg-gray-900 text-white rounded-2xl transition text-center font-black text-[10px] uppercase tracking-widest shadow-xl shadow-black/10">
                                    Edit
                                </Link>
                                <button @click="deleteUser(user.id)" class="flex-1 py-4 bg-red-50 text-red-600 rounded-2xl transition text-center font-black text-[10px] uppercase tracking-widest border border-red-100">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

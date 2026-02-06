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
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-pail-gold shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Total User</h3>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-blue-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Admin Pusat</h3>
                        <div class="text-3xl font-black text-blue-600">{{ stats.admins }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-green-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">User Lembaga</h3>
                        <div class="text-3xl font-black text-green-600">{{ stats.lembaga }}</div>
                    </div>
                </div>

                <!-- Add Button Container -->
                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Manajemen User</h3>
                        <p class="text-sm text-gray-500">Kelola data pengguna sistem dan hak akses mereka</p>
                    </div>
                    <Link :href="route('admin.users.create')" class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah User
                    </Link>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-left">No</th>
                                    <th class="px-6 py-4 text-left">User</th>
                                    <th class="px-6 py-4 text-left">Role & Lembaga</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(user, index) in users.data" :key="user.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                    <td class="px-6 py-4 font-bold text-gray-500">
                                        {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-pail-gold/10 text-pail-gold flex items-center justify-center font-black">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">{{ user.name }}</div>
                                                <div class="text-xs text-gray-500">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 py-0.5 inline-flex text-[10px] font-black uppercase rounded-lg w-fit transition-colors"
                                                :class="{
                                                    'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': user.role === 'super admin',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': user.role === 'admin',
                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': user.role === 'lembaga',
                                                }">
                                                {{ user.role }}
                                            </span>
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                                {{ user.institution ? user.institution.name : "-" }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <Link :href="route('admin.users.edit', user.id)" class="text-blue-600 hover:text-blue-900 font-semibold mr-3 underline">Edit</Link>
                                        <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900 font-semibold underline">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="user in users.data" :key="user.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-pail-gold text-white flex items-center justify-center text-xl font-black shadow-lg shadow-pail-gold/20">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base truncate">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-5 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 block mb-1">Role</span>
                                    <span class="px-2 py-0.5 inline-flex text-[10px] font-black uppercase rounded-lg transition-colors shadow-sm"
                                        :class="{
                                            'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': user.role === 'super admin',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': user.role === 'admin',
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': user.role === 'lembaga',
                                        }">
                                        {{ user.role }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 block mb-1">Lembaga</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-200 truncate block">
                                        {{ user.institution ? user.institution.name : "-" }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link :href="route('admin.users.edit', user.id)" class="flex-1 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 font-semibold text-sm text-center">
                                    Edit
                                </Link>
                                <button @click="deleteUser(user.id)" class="flex-1 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 font-semibold text-sm">
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

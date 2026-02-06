<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";

const props = defineProps({
    sessions: Array,
    stats: {
        type: Object,
        default: () => ({ total_online: 0, admins_online: 0, lembaga_online: 0 }),
    },
});

// Kick Confirmation Modal State
const showKickModal = ref(false);
const sessionToKick = ref(null);

const kickSession = (sessionId) => {
    sessionToKick.value = sessionId;
    showKickModal.value = true;
};

const confirmKick = () => {
    router.delete(route('admin.online_users.kick', sessionToKick.value), {
        preserveScroll: true,
        onSuccess: () => {
            sessionToKick.value = null;
        }
    });
};

const closeKickModal = () => {
    showKickModal.value = false;
    sessionToKick.value = null;
};

const getRelativeTime = (lastActivity) => {
    const now = new Date();
    const activityDate = new Date(lastActivity);
    const diffMs = now - activityDate;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit yang lalu`;
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} jam yang lalu`;
    return activityDate.toLocaleDateString('id-ID');
};
</script>

<template>
    <Head title="User Online" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                User yang sedang Online
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-6 text-white shadow-lg shadow-green-600/20">
                        <h3 class="text-white/80 font-bold uppercase tracking-wider text-xs mb-1">Total Online</h3>
                        <div class="text-3xl font-black">{{ stats.total_online }}</div>
                        <div class="text-white/60 text-xs mt-2">🟢 Active Users</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-blue-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Admin Online</h3>
                        <div class="text-3xl font-black text-blue-600">{{ stats.admins_online }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-purple-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Lembaga Online</h3>
                        <div class="text-3xl font-black text-purple-600">{{ stats.lembaga_online }}</div>
                    </div>
                </div>

                <!-- Online Users List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Daftar User Aktif</h3>
                        
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                        <th class="px-6 py-4 text-left">User</th>
                                        <th class="px-6 py-4 text-left">Role & Lembaga</th>
                                        <th class="px-6 py-4 text-left">IP Address</th>
                                        <th class="px-6 py-4 text-left">Browser</th>
                                        <th class="px-6 py-4 text-left">Last Activity</th>
                                        <th class="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    <tr v-for="session in sessions" :key="session.id" 
                                        class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm"
                                        :class="{ 'bg-yellow-50 dark:bg-yellow-900/10': session.is_current }">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white"
                                                    :class="{
                                                        'bg-gradient-to-br from-purple-500 to-purple-700': session.user?.role === 'super admin',
                                                        'bg-gradient-to-br from-blue-500 to-blue-700': session.user?.role === 'admin',
                                                        'bg-gradient-to-br from-green-500 to-green-700': session.user?.role === 'lembaga',
                                                    }">
                                                    {{ session.user?.name?.charAt(0) || '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                        {{ session.user?.name || 'Unknown' }}
                                                        <span v-if="session.is_current" class="px-2 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 text-[9px] font-black uppercase rounded">YOU</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ session.user?.email || '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-black uppercase rounded-lg w-fit transition-colors"
                                                    :class="{
                                                        'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': session.user?.role === 'super admin',
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': session.user?.role === 'admin',
                                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': session.user?.role === 'lembaga',
                                                    }">
                                                    {{ session.user?.role || '-' }}
                                                </span>
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                                    {{ session.user?.institution?.name || '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">
                                                {{ session.ip_address }}
                                            </code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg">{{ session.browser.icon }}</span>
                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ session.browser.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ getRelativeTime(session.last_activity) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button v-if="!session.is_current" 
                                                @click="kickSession(session.id)"
                                                class="px-3 py-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition shadow-sm font-bold text-xs">
                                                🚫 Keluarkan
                                            </button>
                                            <span v-else class="text-xs text-gray-400 italic">Sesi Saat Ini</span>
                                        </td>
                                    </tr>
                                    <tr v-if="sessions.length === 0">
                                        <td colspan="6" class="px-6 py-20 text-center text-gray-400 italic font-medium">Tidak ada user yang sedang online.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="md:hidden space-y-4">
                            <div v-for="session in sessions" :key="session.id" 
                                class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700"
                                :class="{ 'border-yellow-400 dark:border-yellow-600': session.is_current }">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-white text-lg"
                                        :class="{
                                            'bg-gradient-to-br from-purple-500 to-purple-700': session.user?.role === 'super admin',
                                            'bg-gradient-to-br from-blue-500 to-blue-700': session.user?.role === 'admin',
                                            'bg-gradient-to-br from-green-500 to-green-700': session.user?.role === 'lembaga',
                                        }">
                                        {{ session.user?.name?.charAt(0) || '?' }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            {{ session.user?.name || 'Unknown' }}
                                            <span v-if="session.is_current" class="px-2 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 text-[9px] font-black uppercase rounded">YOU</span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ session.user?.email || '-' }}</div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-xs font-medium text-gray-500">Role</span>
                                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded-lg"
                                            :class="{
                                                'bg-purple-100 text-purple-700': session.user?.role === 'super admin',
                                                'bg-blue-100 text-blue-700': session.user?.role === 'admin',
                                                'bg-green-100 text-green-700': session.user?.role === 'lembaga',
                                            }">
                                            {{ session.user?.role || '-' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-xs font-medium text-gray-500">IP Address</span>
                                        <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded text-[10px] font-mono">{{ session.ip_address }}</code>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-xs font-medium text-gray-500">Browser</span>
                                        <span class="text-sm">{{ session.browser.icon }} {{ session.browser.name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-xs font-medium text-gray-500">Last Activity</span>
                                        <span class="text-xs text-gray-600 flex items-center gap-1">
                                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                            {{ getRelativeTime(session.last_activity) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <button v-if="!session.is_current" 
                                    @click="kickSession(session.id)"
                                    class="w-full py-2.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition font-bold text-sm shadow-md">
                                    🚫 Keluarkan Sesi
                                </button>
                                <div v-else class="w-full py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-full text-center text-sm italic">
                                    Sesi Saat Ini
                                </div>
                            </div>
                            <div v-if="sessions.length === 0" class="p-16 text-center text-gray-400 italic font-medium">Tidak ada user yang sedang online.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kick Confirmation Modal -->
        <ConfirmModal
            :show="showKickModal"
            title="Keluarkan User"
            message="Apakah Anda yakin ingin mengeluarkan user ini dari sistem? User akan logout secara paksa."
            confirm-text="Ya, Keluarkan"
            cancel-text="Batal"
            variant="warning"
            @confirm="confirmKick"
            @close="closeKickModal"
        />
    </AuthenticatedLayout>
</template>

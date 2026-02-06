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
    stats: {
        type: Object,
        default: () => ({ total: 0, new_this_month: 0 }),
    },
});

const importForm = useForm({
    file: null,
});

// ... rest of logic
</script>

<template>
    <Head title="Manajemen Lembaga" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Manajemen Lembaga
                </h2>
                <div class="flex items-center gap-4">
                    <button
                        @click="openImportModal"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <FolderIcon className="w-4 h-4" /> Import Excel
                    </button>
                    <Link
                        :href="route('admin.institutions.create')"
                        class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-xs uppercase tracking-widest flex items-center gap-2"
                    >
                        <PlusIcon className="w-4 h-4" /> Tambah Lembaga
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-10">
                
                <!-- Advanced Stats Intelligence -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.3em] text-[10px] mb-3 relative z-10">Institutional Hub</h3>
                        <div class="flex items-end gap-2 relative z-10">
                            <div class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">{{ stats.total }}</div>
                            <span class="text-[10px] font-black text-gray-300 uppercase mb-1">Units</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <h3 class="text-gray-400 font-black uppercase tracking-[0.3em] text-[10px] mb-3 relative z-10">Growth Rate</h3>
                        <div class="flex items-end gap-2 relative z-10 text-green-500">
                            <div class="text-5xl font-black tracking-tighter leading-none">+{{ stats.new_this_month }}</div>
                            <span class="text-[10px] font-black text-green-200 uppercase mb-1">Mtd</span>
                        </div>
                    </div>

                    <div class="hidden md:block bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-10 rounded-[3rem] shadow-2xl shadow-pail-gold/30 relative overflow-hidden group hover:-translate-y-1 transition-all duration-500 col-span-2">
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center justify-between h-full relative z-10">
                            <div>
                                <h3 class="text-white/70 font-black uppercase tracking-[0.3em] text-[10px] mb-3">System Core Status</h3>
                                <div class="text-3xl font-black text-white uppercase tracking-tighter">Operational Excellence</div>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-[1.5rem] flex items-center justify-center backdrop-blur-md border border-white/30 shadow-xl group-hover:rotate-12 transition-transform">
                                <CheckIcon className="w-8 h-8 text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table Master -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-[3.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-pail-gold opacity-5 rounded-full blur-3xl"></div>
                    <div class="p-10 relative z-10">
                        <table class="min-w-full border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.3em]">
                                    <th class="px-8 py-4 text-center">Ref</th>
                                    <th class="px-8 py-4 text-left">Internal ID</th>
                                    <th class="px-8 py-4 text-left">Nama Lembaga / Unit Kerjas</th>
                                    <th class="px-8 py-4 text-left">Summary</th>
                                    <th class="px-8 py-4 text-right">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(inst, index) in institutions.data" :key="inst.id" class="group transition-all hover:translate-x-1 duration-300">
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-l-[2rem] border-y border-l border-gray-50 dark:border-gray-800/50 text-center font-black text-gray-300">
                                        {{ (institutions.current_page - 1) * institutions.per_page + index + 1 }}
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-pail-gold"></span>
                                            <span class="text-xs font-black text-pail-gold uppercase tracking-[0.2em]">#{{ inst.code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="font-black text-gray-900 dark:text-white uppercase tracking-tighter text-sm">{{ inst.name }}</div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-y border-gray-50 dark:border-gray-800/50">
                                        <div class="text-[11px] text-gray-400 font-bold italic line-clamp-1 max-w-xs">{{ inst.description || "Operational Unit Profile Default" }}</div>
                                    </td>
                                    <td class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 rounded-r-[2rem] border-y border-r border-gray-50 dark:border-gray-800/50 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link
                                                :href="route('admin.institutions.edit', inst.id)"
                                                class="px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-pail-gold transition-all shadow-xl font-black text-[10px] uppercase tracking-widest"
                                            >
                                                Edit Profile
                                            </Link>
                                            <button
                                                @click="deleteInstitution(inst.id)"
                                                class="px-6 py-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-widest border border-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Premium Card Engine -->
                <div class="md:hidden space-y-8">
                    <div v-for="inst in institutions.data" :key="inst.id" class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="p-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="px-4 py-2 rounded-2xl bg-gray-900 text-pail-gold text-[10px] font-black uppercase tracking-[0.2em] shadow-lg">
                                    {{ inst.code }}
                                </div>
                                <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                            </div>

                            <div class="mb-8">
                                <h3 class="font-black text-gray-900 dark:text-white text-2xl leading-tight mb-2 uppercase tracking-tighter">{{ inst.name }}</h3>
                                <p class="text-xs text-gray-500 font-bold italic leading-relaxed">{{ inst.description || "Operational profile not configured for this institution." }}</p>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Link :href="route('admin.institutions.edit', inst.id)" class="flex-1 py-5 bg-pail-gold text-white rounded-[1.5rem] transition-all text-center font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-pail-gold/20">
                                    Edit unit
                                </Link>
                                <button @click="deleteInstitution(inst.id)" class="flex-1 py-5 bg-red-50 text-red-600 rounded-[1.5rem] transition-all text-center font-black text-[10px] uppercase tracking-[0.2em] border border-red-100">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Empty State -->
                    <div v-if="institutions.data.length === 0" class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 p-24 text-center relative overflow-hidden">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-gray-50 opacity-50 rounded-full blur-3xl"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mb-8 border border-gray-100 dark:border-gray-700 shadow-inner">
                                <FolderIcon className="w-10 h-10 text-gray-200" />
                            </div>
                            <p class="text-gray-400 font-black uppercase tracking-[0.3em] text-sm italic">Institutional Data Empty</p>
                            <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase">Please initialize units to proceed.</p>
                        </div>
                    </div>
                </div>

                <!-- Pagination Blueprint -->
                <div class="mt-8 flex justify-center">
                    <Pagination :links="institutions.links" />
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div
            v-if="isImportModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xl p-4"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl w-full max-w-lg p-12 border border-white/20 relative overflow-hidden"
            >
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-60 h-60 bg-pail-gold opacity-10 rounded-full blur-[80px]"></div>
                
                <header class="relative z-10 mb-10">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Import Lembaga</h3>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-widest italic">Migrasi data massal via file excel.</p>
                </header>

                <form @submit.prevent="handleImport" class="relative z-10 space-y-8">
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Unggah Berkas (.xlsx, .xls, .csv)</label>
                        <input
                            @input="importForm.file = $event.target.files[0]"
                            type="file"
                            class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-4 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-pail-gold file:text-white hover:file:bg-yellow-600 transition-all cursor-pointer bg-white dark:bg-gray-800 border-2 border-dashed border-gray-100 dark:border-gray-700 p-4 rounded-2xl"
                            required
                        />
                    </div>

                    <div class="p-8 bg-pail-gold/5 rounded-[2rem] border border-pail-gold/10 text-[11px] text-gray-600 dark:text-gray-400 leading-relaxed">
                        <div class="flex items-center gap-3 mb-4">
                            <DownloadIcon className="w-5 h-5 text-pail-gold" />
                            <span class="font-black text-pail-gold uppercase tracking-wider">Panduan Format</span>
                        </div>
                        <p class="mb-4 font-medium italic">Gunakan header kolom berikut pada baris pertama file Anda:</p>
                        <code class="block bg-white dark:bg-gray-900 p-4 rounded-xl border border-pail-gold/20 font-mono font-bold text-pail-gold mb-6 shadow-sm">name, code, description</code>
                        
                        <a :href="route('admin.institutions.template')" class="flex items-center justify-center gap-3 py-4 px-6 bg-white dark:bg-gray-900 text-pail-gold rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-pail-gold hover:text-white transition-all border border-pail-gold/20 shadow-sm">
                            <DownloadIcon className="w-4 h-4" /> Download Template CSV
                        </a>
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <button type="submit" class="flex-1 py-5 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs" :disabled="importForm.processing">Eksekusi Import</button>
                        <button type="button" @click="closeImportModal" class="px-10 py-5 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 font-black transition-all uppercase tracking-widest text-xs">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

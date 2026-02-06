<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import XIcon from "@/Components/Icons/XIcon.vue";
import SparklesIcon from "@/Components/Icons/SparklesIcon.vue";
import RocketIcon from "@/Components/Icons/RocketIcon.vue";

const importForm = useForm({
    file: null,
});

const handleImport = () => {
    importForm.post(route("admin.rooms.import"));
};
</script>

<template>
    <Head title="Import Ruangan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Import Ruangan Massal
                </h2>
                <Link
                    :href="route('admin.rooms.index')"
                    class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl hover:bg-gray-200 transition-all font-black text-xs uppercase tracking-widest flex items-center gap-2"
                >
                    <XIcon className="w-4 h-4" /> Batal & Kembali
                </Link>
            </div>
        </template>

        <div class="pt-12 pb-24">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Premium Import Canvas -->
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl relative overflow-hidden border border-gray-100 dark:border-gray-700/50">
                    <!-- Dynamic Accent Background -->
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-pail-gold opacity-10 rounded-full blur-[100px] animate-pulse"></div>
                    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500 opacity-5 rounded-full blur-[100px]"></div>

                    <div class="relative z-10 p-10 lg:p-14">
                        <!-- Intelligence Header -->
                        <header class="mb-12">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="px-3 py-1 bg-pail-gold/10 rounded-full border border-pail-gold/20 flex items-center gap-2">
                                    <SparklesIcon className="w-3 h-3 text-pail-gold" />
                                    <span class="text-[9px] font-black text-pail-gold uppercase tracking-[0.2em]">Data Engine</span>
                                </div>
                            </div>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none text-center">Import Ruangan</h3>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.3em] mt-3 opacity-60 italic text-center">Pemetaan lokasi massal via excel.</p>
                        </header>

                        <form @submit.prevent="handleImport" class="space-y-10">
                            <!-- Advanced File Upload Box -->
                            <div class="group relative">
                                <div class="absolute -inset-1 bg-gradient-to-r from-pail-gold/20 to-blue-500/20 rounded-[2.5rem] blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                                <div class="relative bg-white dark:bg-gray-900/80 p-16 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-inner flex flex-col items-center justify-center border-dashed border-2 hover:border-pail-gold transition-colors">
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center mb-6 shadow-sm border border-gray-100 dark:border-gray-700 group-hover:scale-110 transition-transform duration-500">
                                        <PlusIcon className="w-10 h-10 text-gray-300 group-hover:text-pail-gold transition-colors" />
                                    </div>
                                    <p class="text-[12px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Seret atau Pilih Berkas Excel/CSV</p>
                                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-tight">Format yang didukung: XLSX, XLS, CSV</p>
                                    <input
                                        @input="importForm.file = $event.target.files[0]"
                                        type="file"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        required
                                    />
                                    <div v-if="importForm.file" class="mt-8 px-6 py-3 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-2xl text-[11px] font-black uppercase tracking-wider flex items-center gap-3 border border-green-100 dark:border-green-800 animate-bounce">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        {{ importForm.file.name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Integrated Blueprint & Template -->
                            <div class="bg-gray-50 dark:bg-gray-900/40 p-10 lg:p-12 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-pail-gold/10 flex items-center justify-center">
                                        <DownloadIcon className="w-6 h-6 text-pail-gold" />
                                    </div>
                                    <div>
                                        <span class="text-[12px] font-black text-pail-gold uppercase tracking-[0.25em]">Skema Protokol</span>
                                        <p class="text-[12px] text-gray-400 font-bold italic mt-1 leading-none">Gunakan urutan kolom yang presisi:</p>
                                    </div>
                                </div>
                                
                                <code class="block bg-white dark:bg-gray-900 p-8 rounded-3xl border border-pail-gold/20 font-mono font-black text-pail-gold mb-10 shadow-sm text-center text-sm tracking-tighter">
                                    institution_code, name, description
                                </code>
                                
                                <a :href="route('admin.rooms.template')" class="group/temp flex items-center justify-between py-6 px-10 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:border-pail-gold hover:translate-y-[-2px] hover:shadow-xl">
                                    <div class="flex items-center gap-5">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl group-hover/temp:bg-pail-gold/10 transition-colors">
                                            <DownloadIcon className="w-5 h-5 text-gray-400 group-hover/temp:text-pail-gold" />
                                        </div>
                                        <span>Dapatkan Template Resmi (.CSV)</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover/temp:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>

                            <!-- Execution Engine -->
                            <div class="pt-6">
                                <button 
                                    type="submit" 
                                    class="group/btn w-full py-8 bg-gray-900 dark:bg-black text-pail-gold rounded-full font-black shadow-[0_30px_60px_-15px_rgba(0,0,0,0.4)] transition-all hover:bg-black dark:hover:bg-gray-900 uppercase tracking-[0.4em] text-sm relative overflow-hidden flex items-center justify-center gap-6"
                                    :disabled="importForm.processing"
                                >
                                    <div class="absolute inset-0 bg-pail-gold opacity-0 group-hover/btn:opacity-10 transition-opacity"></div>
                                    <span v-if="!importForm.processing" class="flex items-center gap-6 relative z-10 transition-all group-hover/btn:scale-105">
                                        Meluncurkan Import Data
                                        <RocketIcon className="w-6 h-6 group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform" />
                                    </span>
                                    <span v-else class="flex items-center gap-6 relative z-10">
                                        <svg class="animate-spin h-6 w-6 text-pail-gold" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Proses Integrasi Sedang Berjalan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

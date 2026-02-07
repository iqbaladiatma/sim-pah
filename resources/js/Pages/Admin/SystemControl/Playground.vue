<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

// Components
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import InstitutionSelect from "@/Components/InstitutionSelect.vue";

// Icons
import InboxIcon from "@/Components/Icons/InboxIcon.vue";

const activeTab = ref('dashboard');
const showConfirmModal = ref(false);
const processing = ref(false);

const labs = [
    { id: 'dashboard', label: 'Dashboard Hub', desc: 'Stats, Cards, & Metrics', icon: 'D' },
    { id: 'data', label: 'Data & Lists', desc: 'Tables, Items, & Logs', icon: 'L' },
    { id: 'forms', label: 'Form Engine', desc: 'Inputs, Selects, & Validation', icon: 'F' },
    { id: 'system', label: 'System UI', desc: 'Alerts, Modals, & Badges', icon: 'S' },
];

// Simulated Data for Playground
const sandboxData = ref({
    title: 'Proposal Pembangunan Ruang Lab Komputer',
    status: 'pending',
    cost: 25000000,
    institution: 'SMA Abu Hurairah'
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};
</script>

<template>
    <Head title="Developer Playground" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.system_control.index')" class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-pail-gold border border-gray-100 dark:border-gray-700 transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </Link>
                        <div>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-1">DESIGN SYSTEM LAB</h2>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-pail-gold animate-pulse"></span>
                                <p class="text-[10px] font-black text-pail-gold uppercase tracking-[0.3em] opacity-60 italic">Real-time Component Stress Test</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex gap-4">
                    <div class="px-6 py-3 bg-gray-900 text-white rounded-2xl border border-white/5 shadow-2xl flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <div class="w-6 h-6 rounded-full bg-pail-gold border-2 border-gray-900"></div>
                            <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-gray-900"></div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">2 Devs Online</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto py-8">
            <!-- Laboratory Navigation -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-20 px-4">
                <button 
                    v-for="lab in labs" 
                    :key="lab.id"
                    @click="activeTab = lab.id"
                    class="p-6 bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-500 text-left group relative overflow-hidden"
                    :class="activeTab === lab.id ? 'ring-2 ring-pail-gold shadow-2xl shadow-pail-gold/10' : 'hover:shadow-xl hover:-translate-y-1'"
                >
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full opacity-50 group-hover:scale-125 transition-transform"></div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-900 flex items-center justify-center text-pail-gold mb-6 border border-pail-gold/20 relative z-10 transition-transform group-hover:rotate-6">
                        <span class="text-xl font-black">{{ lab.icon }}</span>
                    </div>
                    <h4 class="font-black text-gray-900 dark:text-white uppercase tracking-tighter text-lg leading-none mb-1 relative z-10">{{ lab.label }}</h4>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest relative z-10">{{ lab.desc }}</p>
                    
                    <div v-if="activeTab === lab.id" class="absolute bottom-4 right-6">
                        <div class="w-2 h-2 rounded-full bg-pail-gold animate-ping"></div>
                    </div>
                </button>
            </div>

            <div class="space-y-20 px-4">
                <!-- DASHBOARD HUB -->
                <section v-if="activeTab === 'dashboard'" class="animate-fade-in space-y-12">
                    <div class="flex items-center gap-6 px-4">
                        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.5em]">Strategic Metrics Cluster</h2>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Standard Blue Stat -->
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden transition-all duration-500 hover:shadow-2xl">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Unit Organisasi</h4>
                            <div class="flex items-end gap-3 leading-none mb-2">
                                <div class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">28</div>
                                <span class="text-xs font-black text-blue-500 uppercase mb-2">Lembaga</span>
                            </div>
                            <div class="w-12 h-1 bg-blue-500/20 rounded-full"></div>
                        </div>

                        <!-- Red Alert Stat -->
                        <div class="bg-gradient-to-br from-red-500 to-red-600 p-8 rounded-[3rem] shadow-2xl shadow-red-500/30 group hover:scale-[1.03] transition-all duration-500 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-full bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <h4 class="text-[10px] font-black text-red-100 uppercase tracking-[0.3em] mb-4 italic">Kritis Operasional</h4>
                            <div class="text-5xl font-black text-white tracking-tighter mb-4 leading-none">12</div>
                            <div class="flex items-center gap-3 text-[10px] font-black text-white uppercase tracking-widest">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                Stok Habis Terdeteksi
                            </div>
                        </div>

                        <!-- Gold Premium Stat -->
                        <div class="bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-8 rounded-[3rem] shadow-2xl shadow-pail-gold/30 flex flex-col justify-between group hover:-translate-y-2 transition-all duration-500">
                             <div>
                                <h4 class="text-[10px] font-black text-white/70 uppercase tracking-[0.3em] mb-4">Total Aset Aktif</h4>
                                <div class="text-4xl font-black text-white tracking-tighter leading-none mb-1">1,402</div>
                                <p class="text-[9px] text-black/30 font-black uppercase tracking-widest">SKUs Terverifikasi</p>
                             </div>
                             <div class="mt-8 flex items-center justify-between">
                                 <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white backdrop-blur-md">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                 </div>
                                 <span class="text-[9px] font-black text-white uppercase tracking-widest font-mono">+12.4% MoM</span>
                             </div>
                        </div>

                        <!-- Dark Intelligence Stat -->
                        <div class="bg-gray-900 dark:bg-black p-8 rounded-[3rem] shadow-2xl flex flex-col justify-center border border-white/5 relative group overflow-hidden">
                             <div class="absolute inset-0 bg-pail-gold/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                             <div class="relative z-10">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-pail-gold/10 border border-pail-gold/20 flex items-center justify-center text-pail-gold">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[9px] font-black text-pail-gold/70 uppercase tracking-[0.3em]">Traffic</h4>
                                        <div class="text-2xl font-black text-white tracking-tighter">82 Online</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-500/10 border border-green-500/20 rounded-full w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_10px_#22c55e]"></span>
                                    <p class="text-[8px] text-green-500 font-black uppercase tracking-widest leading-none">Neural Link Healthy</p>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Financial Architecture Cluster -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white dark:bg-gray-800 p-10 rounded-[4rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row items-center gap-10 group hover:shadow-2xl transition-all">
                             <div class="shrink-0">
                                 <div class="w-32 h-32 rounded-full border-[10px] border-gray-50 flex items-center justify-center text-3xl font-black text-gray-900 relative">
                                      <svg class="absolute -inset-2 w-[calc(100%+16px)] h-[calc(100%+16px)] text-pail-gold transform -rotate-90">
                                          <circle class="opacity-10" cx="72" cy="72" r="64" stroke="currentColor" stroke-width="10" fill="transparent" />
                                          <circle class="transition-all duration-1000" cx="72" cy="72" r="64" stroke="currentColor" stroke-width="10" fill="transparent" stroke-dasharray="402" :stroke-dashoffset="402 * 0.3" stroke-linecap="round" />
                                      </svg>
                                      70%
                                 </div>
                             </div>
                             <div class="text-center sm:text-left">
                                 <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.5em] mb-4">Efisiensi Budgeting</h4>
                                 <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">{{ formatRupiah(840200000) }}</div>
                                 <p class="text-sm text-gray-500 font-bold leading-relaxed max-w-xs uppercase tracking-tight">Terutilisasi untuk pengadaan sarana & prasarana semester genap.</p>
                             </div>
                        </div>

                        <div class="bg-gray-900 rounded-[4rem] p-10 flex flex-col justify-between relative overflow-hidden group">
                             <div class="absolute inset-0 bg-gradient-to-br from-pail-gold/10 via-transparent to-transparent"></div>
                             <div class="relative z-10 flex items-start justify-between">
                                 <div>
                                     <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-2">Pusat Logistik</h3>
                                     <p class="text-[10px] font-black text-pail-gold uppercase tracking-[0.3em]">Arsip Laporan Keuangan</p>
                                 </div>
                                 <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white backdrop-blur-md">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                 </div>
                             </div>
                             <div class="relative z-10 mt-10 grid grid-cols-2 gap-4">
                                 <div class="p-5 bg-white/5 rounded-3xl border border-white/5">
                                     <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Inflow</p>
                                     <p class="text-lg font-black text-green-500 font-mono tracking-tighter">+8.2M</p>
                                 </div>
                                 <div class="p-5 bg-white/5 rounded-3xl border border-white/5">
                                     <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Outflow</p>
                                     <p class="text-lg font-black text-red-500 font-mono tracking-tighter">-3.1M</p>
                                 </div>
                             </div>
                        </div>
                    </div>
                </section>

                <!-- DATA & LISTS -->
                <section v-if="activeTab === 'data'" class="animate-fade-in space-y-12">
                     <div class="flex items-center gap-6 px-4">
                        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.5em]">Inventory & Activity Pattern</h2>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                    </div>

                    <!-- Complex List Item -->
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-4 sm:p-8 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6 px-4">Standard Proposal Row</h4>
                            <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-8 group hover:bg-white dark:hover:bg-gray-900 border border-transparent hover:border-gray-100 dark:hover:border-gray-700 transition-all cursor-pointer shadow-none hover:shadow-2xl">
                                <div class="flex items-center gap-6 flex-1 w-full">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-900 flex flex-col items-center justify-center border-2 border-pail-gold/30 shadow-xl shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-[8px] font-black text-pail-gold uppercase leading-none mb-1 opacity-70">FEB</span>
                                        <span class="text-2xl font-black text-white leading-none tracking-tighter">07</span>
                                    </div>
                                    <div class="min-w-0">
                                         <span class="px-3 py-1 bg-white dark:bg-gray-800 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] border border-gray-100 dark:border-gray-700 mb-2 inline-block">ID: #RE-4012</span>
                                         <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-2 truncate group-hover:text-pail-gold transition-colors">{{ sandboxData.title }}</h4>
                                         <div class="flex items-center gap-4">
                                              <div class="flex items-center gap-2">
                                                   <div class="w-1.5 h-1.5 rounded-full bg-pail-gold animate-pulse"></div>
                                                   <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ sandboxData.institution }}</span>
                                              </div>
                                              <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                              <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori: Sarpras</span>
                                         </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-10 shrink-0 w-full md:w-auto border-t md:border-t-0 pt-6 md:pt-0 border-gray-100">
                                     <div class="text-right">
                                         <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.2em] mb-1">Estimasi</p>
                                         <p class="text-2xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(sandboxData.cost) }}</p>
                                     </div>
                                     <div class="px-5 py-2 rounded-2xl bg-yellow-50 text-yellow-600 text-[10px] font-black uppercase tracking-widest border border-yellow-100 flex items-center gap-2">
                                         <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                         Reviewing
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Pulse -->
                    <div class="max-w-xl space-y-6">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] px-4">Neural Activity Pulse</h4>
                        <div class="bg-gray-900 rounded-[3rem] p-10 space-y-10 relative overflow-hidden group">
                             <div class="absolute left-[31px] top-10 bottom-10 w-[1px] bg-white/10 italic"></div>
                             
                             <div v-for="i in 3" :key="i" class="flex gap-8 relative group/item">
                                 <div class="w-10 h-10 rounded-full bg-gray-800 border-2 border-white/10 flex items-center justify-center shrink-0 relative z-10 group-hover/item:border-pail-gold transition-colors">
                                     <div class="w-2 h-2 rounded-full bg-pail-gold shadow-[0_0_10px_rgba(201,166,88,0.5)]"></div>
                                 </div>
                                 <div class="flex-1">
                                     <p class="text-xs font-bold text-gray-400 uppercase tracking-tight leading-relaxed">
                                         <span class="text-white font-black">Super Admin</span> 
                                         <span class="mx-1 lowercase opacity-50">baru saja menjalankan</span>
                                         <span :class="i===1 ? 'text-red-500' : 'text-pail-gold'">{{ i===1 ? 'system.maintenance_mode_on' : 'system.cache_cleared' }}</span>
                                     </p>
                                     <div class="flex items-center gap-3 mt-2">
                                         <span class="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] font-mono">22:04:12</span>
                                         <span class="w-1 h-1 rounded-full bg-white/10"></span>
                                         <span class="text-[8px] font-black text-pail-gold/30 uppercase tracking-widest italic font-mono">{{ i===1 ? 'CRITICAL' : 'ROUTINE' }}</span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </section>

                <!-- FORMS ENGINE -->
                <section v-if="activeTab === 'forms'" class="animate-fade-in space-y-12 px-4">
                    <div class="flex items-center gap-6">
                        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.5em]">Input Protocol Interface</h2>
                        <div class="flex-1 h-px bg-gray-100 dark:border-gray-800"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-10 bg-white dark:bg-gray-800 p-10 rounded-[4rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                             <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">Standard Fieldset</h3>
                             <div class="space-y-6">
                                <div class="space-y-2">
                                    <InputLabel value="Judul Laporan / Agenda" />
                                    <TextInput class="block w-full" v-model="sandboxData.title" placeholder="Misal: Pemeliharaan AC Ruang Lab" />
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight italic">Binding: {{ sandboxData.title }}</p>
                                </div>
                                <div class="space-y-2">
                                    <InputLabel value="Estimasi Anggaran" />
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400 text-xs">Rp</span>
                                        <TextInput type="number" class="block w-full pl-12" v-model="sandboxData.cost" />
                                    </div>
                                </div>
                             </div>
                        </div>

                        <div class="space-y-10 bg-white dark:bg-gray-800 p-10 rounded-[4rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                             <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">Search Intelligence Select</h3>
                             <div class="space-y-8">
                                <div class="space-y-3">
                                    <InputLabel value="Pilih Lembaga Terintegrasi" />
                                    <InstitutionSelect @update:modelValue="(v) => sandboxData.institution = v" />
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-center">
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Active Selector Data:</p>
                                         <p class="text-xs font-black text-pail-gold tracking-tighter">{{ sandboxData.institution }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <InputLabel value="Searchable Global Item" />
                                    <SearchableSelect :options="[{id: 1, name: 'Komputer Core i7'}, {id: 2, name: 'Printer Epson L3110'}]" placeholder="Cari item di seluruh gudang..." />
                                </div>
                             </div>
                        </div>
                    </div>
                </section>

                <!-- SYSTEM UI -->
                <section v-if="activeTab === 'system'" class="animate-fade-in space-y-12 px-4">
                     <div class="flex items-center gap-6">
                        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.5em]">Interaction & Feedback Matrix</h2>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Buttons Matrix -->
                        <div class="p-10 bg-white dark:bg-gray-800 rounded-[4rem] border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
                             <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Component Actions</h3>
                             <div class="flex flex-wrap gap-4">
                                <PrimaryButton @click="processing = true; setTimeout(() => processing = false, 2000)" :processing="processing">
                                    Breeze Primary
                                </PrimaryButton>
                                <SecondaryButton>Breeze Secondary</SecondaryButton>
                                <DangerButton @click="showConfirmModal = true">Breeze Danger</DangerButton>
                             </div>

                             <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] pt-6">Security & Error Protocols</h4>
                             <div class="grid grid-cols-2 gap-4">
                                <Link :href="route('admin.system_control.playground.error', 404)" class="px-6 py-4 bg-red-500/10 border border-red-500/20 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-red-500 hover:text-white transition-all">
                                    Simulate 404 (Not Found)
                                </Link>
                                <Link :href="route('admin.system_control.playground.error', 403)" class="px-6 py-4 bg-gray-900 border border-white/5 text-pail-gold rounded-2xl text-[10px] font-black uppercase tracking-widest text-center hover:scale-105 transition-all">
                                    Simulate 403 (Forbidden)
                                </Link>
                             </div>
                             
                             <!-- Special Custom Button -->
                             <button class="w-full px-8 py-5 bg-gray-900 text-pail-gold rounded-[2rem] font-black text-xs uppercase tracking-[0.3em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center justify-center gap-4 group">
                                 <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                 Ultra Premium Action
                             </button>
                        </div>

                        <!-- Badge System -->
                        <div class="p-10 bg-white dark:bg-gray-800 rounded-[4rem] border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
                              <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Status Indicators</h3>
                              <div class="grid grid-cols-2 gap-4">
                                  <div class="px-5 py-3 rounded-2xl bg-green-50 text-green-600 border border-green-100 flex items-center gap-3">
                                      <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_#22c55e]"></span>
                                      <span class="text-[10px] font-black uppercase tracking-widest">Authorized</span>
                                  </div>
                                  <div class="px-5 py-3 rounded-2xl bg-red-50 text-red-600 border border-red-100 flex items-center gap-3">
                                      <span class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444]"></span>
                                      <span class="text-[10px] font-black uppercase tracking-widest">Rejected</span>
                                  </div>
                                  <div class="px-5 py-3 rounded-2xl bg-gray-900 text-pail-gold border border-white/5 flex items-center gap-3">
                                      <span class="w-2 h-2 rounded-full bg-pail-gold animate-pulse"></span>
                                      <span class="text-[10px] font-black uppercase tracking-widest">Synchronizing</span>
                                  </div>
                                  <div class="px-5 py-3 rounded-2xl bg-gray-50 text-gray-400 border border-gray-200 flex items-center gap-3">
                                      <div class="flex gap-1">
                                          <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                          <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                          <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                      </div>
                                      <span class="text-[10px] font-black uppercase tracking-widest">Archived</span>
                                  </div>
                              </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Real Modal Testing -->
        <ConfirmModal 
            :show="showConfirmModal" 
            title="Penghapusan Data Lab"
            message="Apakah Anda yakin ingin menghapus data simulasi ini? Tindakan ini akan menguji fungsionalitas modal konfirmasi premium kami."
            confirm-text="Ya, Hapus Saja"
            @close="showConfirmModal = false"
            @confirm="showConfirmModal = false"
        />

    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

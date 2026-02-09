<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import UploadIcon from "@/Components/Icons/UploadIcon.vue";

const props = defineProps({
    institutions: Array,
});

const form = useForm({
    institution_id: "",
    type: "",
    title: "",
    description: "",
    estimated_cost: 0,
    photo_evidence: null,
});

const costDisplay = computed({
    get: () => formatRupiah(form.estimated_cost),
    set: (val) => {
        form.estimated_cost = parseRupiah(val);
    }
});

const fileInput = ref(null);
const previewUrl = ref(null);
const isDragging = ref(false);

const handleFile = (file) => {
    if (file && file.type.startsWith('image/')) {
        form.photo_evidence = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const onFileChange = (e) => {
    const file = e.target.files[0];
    handleFile(file);
};

const onDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    handleFile(file);
};

const removeFile = () => {
    form.photo_evidence = null;
    previewUrl.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submit = () => {
    form.post(route("admin.requests.store"));
};

const requestTypes = [
    { id: 'utilitas', label: 'Utilitas', desc: 'Listrik, Air, Internet', icon: 'bolt', color: 'text-yellow-500', bg: 'bg-yellow-50 dark:bg-yellow-900/20' },
    { id: 'barang_habis_pakai', label: 'Barang Habis Pakai', desc: 'ATK, Kebersihan, Dapur', icon: 'box', color: 'text-blue-500', bg: 'bg-blue-50 dark:bg-blue-900/20' },
    { id: 'pemeliharaan', label: 'Pemeliharaan', desc: 'Perbaikan Aset, Servis', icon: 'tool', color: 'text-orange-500', bg: 'bg-orange-50 dark:bg-orange-900/20' },
    { id: 'darurat', label: 'Darurat', desc: 'Insiden, Bencana', icon: 'alert', color: 'text-red-500', bg: 'bg-red-50 dark:bg-red-900/20' },
];

const selectType = (typeId) => {
    form.type = typeId;
};
</script>

<template>
    <Head title="Buat Pengajuan Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 font-sans">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                        Buat Pengajuan Baru
                    </h2>
                    <p class="text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Operational Portal</p>
                </div>
                <Link :href="route('admin.requests.index')" class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-gray-200 transition-all group">
                    <span class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center group-hover:bg-pail-gold group-hover:text-white transition-colors">
                        &larr;
                    </span>
                    <span>Kembali</span>
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-20">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Section 1: Institution Selection -->
                    <div class="relative z-30">
                        <!-- Background & Decoration Layer (Clipped) -->
                        <div class="absolute inset-0 bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200/50 dark:shadow-none overflow-hidden z-0">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-pail-gold/5 rounded-bl-[100px]"></div>
                        </div>

                        <!-- Content Layer (Visible Overflow for Dropdowns) -->
                        <div class="relative z-10 p-8 sm:p-10">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-gray-900 text-pail-gold flex items-center justify-center text-sm">1</span>
                                Pilih Lembaga
                            </h3>
                            
                            <div class="max-w-xl">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Lembaga / Unit Pengaju</label>
                                <SearchableSelect
                                    v-model="form.institution_id"
                                    :options="institutions"
                                    placeholder="Cari atau pilih lembaga..."
                                    :customLabel="(opt) => `${opt.code} - ${opt.name}`"
                                    class="premium-select"
                                />
                                <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.institution_id }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Request Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 sm:p-10 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200/50 dark:shadow-none relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-8 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-gray-900 text-pail-gold flex items-center justify-center text-sm">2</span>
                                Detail Pengajuan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- Type Selection Cards -->
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Kategori Pengajuan</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div 
                                            v-for="type in requestTypes" 
                                            :key="type.id"
                                            @click="selectType(type.id)"
                                            class="cursor-pointer rounded-2xl p-4 border-2 transition-all duration-300 relative group overflow-hidden"
                                            :class="form.type === type.id 
                                                ? 'border-pail-gold bg-pail-gold/5 ring-2 ring-pail-gold/20' 
                                                : 'border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-gray-50/50 dark:bg-gray-900'"
                                        >
                                            <div class="mb-3 w-10 h-10 rounded-xl flex items-center justify-center transition-colors" :class="type.bg">
                                                <!-- Icons -->
                                                <svg v-if="type.icon === 'bolt'" class="w-5 h-5" :class="type.color" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                <svg v-else-if="type.icon === 'box'" class="w-5 h-5" :class="type.color" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                <svg v-else-if="type.icon === 'tool'" class="w-5 h-5" :class="type.color" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                <svg v-else-if="type.icon === 'alert'" class="w-5 h-5" :class="type.color" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">{{ type.label }}</h4>
                                            <p class="text-[9px] text-gray-400 font-medium leading-tight">{{ type.desc }}</p>
                                            
                                            <!-- Checkmark -->
                                            <div v-if="form.type === type.id" class="absolute top-2 right-2 w-5 h-5 bg-pail-gold rounded-full flex items-center justify-center animate-in zoom-in duration-300">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.type" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.type }}</div>
                                </div>

                                <!-- Title -->
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Judul Pengajuan</label>
                                    <input v-model="form.title" type="text" 
                                        class="w-full h-14 border-gray-100 rounded-xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-pail-gold focus:border-pail-gold px-5 transition-all placeholder-gray-300" 
                                        placeholder="Contoh: Perbaikan AC Ruang Kantor URT" required />
                                    <div v-if="form.errors.title" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.title }}</div>
                                </div>

                                <!-- Description -->
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Deskripsi Lengkap & Justifikasi</label>
                                    <textarea v-model="form.description" rows="5" 
                                        class="w-full border-gray-100 rounded-2xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-pail-gold focus:border-pail-gold p-5 leading-relaxed transition-all placeholder-gray-300" 
                                        placeholder="Jelaskan detail kebutuhan, alasan pengajuan, dan urgensi..." required></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Cost & Evidence -->
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 sm:p-10 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200/50 dark:shadow-none relative overflow-hidden">
                        <div class="relative z-10">
                             <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-8 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-gray-900 text-pail-gold flex items-center justify-center text-sm">3</span>
                                Anggaran & Bukti
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                                
                                <!-- Cost Input -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Estimasi Biaya</label>
                                    <div class="relative group">
                                        <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-black text-lg group-focus-within:text-pail-gold transition-colors">Rp</div>
                                        <input v-model="costDisplay" type="text" 
                                            class="w-full h-20 border-gray-100 rounded-2xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-3xl font-black font-mono tracking-tighter text-gray-900 dark:text-white pl-16 pr-6 focus:ring-2 focus:ring-pail-gold focus:border-pail-gold transition-all" 
                                            placeholder="0"
                                            required />
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2 ml-1 italic">* Masukkan estimasi kasar jika belum ada penawaran resmi.</p>
                                    <div v-if="form.errors.estimated_cost" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.estimated_cost }}</div>
                                </div>

                                <!-- File Upload Drag & Drop -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Bukti Foto / Dokumen</label>
                                    
                                    <!-- Upload Zone -->
                                    <div 
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="onDrop"
                                        class="relative w-full h-48 rounded-2xl border-2 border-dashed transition-all duration-300 flex flex-col items-center justify-center text-center cursor-pointer overflow-hidden group"
                                        :class="isDragging ? 'border-pail-gold bg-pail-gold/5' : 'border-gray-200 dark:border-gray-700 hover:border-pail-gold hover:bg-gray-50 dark:hover:bg-gray-900'"
                                    >
                                        <input 
                                            ref="fileInput"
                                            type="file" 
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                            @change="onFileChange"
                                            accept="image/*"
                                        />

                                        <!-- Preview -->
                                        <div v-if="previewUrl" class="absolute inset-0 z-10 bg-gray-100 flex items-center justify-center">
                                            <img :src="previewUrl" class="w-full h-full object-cover" />
                                            <button @click.prevent="removeFile" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 shadow-lg z-30 transform hover:scale-110 transition-transform">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <!-- Placeholder Content -->
                                        <div v-else class="flex flex-col items-center pointer-events-none p-4">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-gray-400 group-hover:text-pail-gold">
                                                <UploadIcon class="w-6 h-6" />
                                            </div>
                                            <p class="text-xs font-bold text-gray-700 dark:text-white mb-1 uppercase tracking-wide">
                                                <span class="text-pail-gold">Upload</span> atau drag file
                                            </p>
                                            <p class="text-[10px] text-gray-400">PNG, JPG, JPEG (Max. 2MB)</p>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.photo_evidence" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1 tracking-widest">{{ form.errors.photo_evidence }}</div>
                                </div>

                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-center gap-4 mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                             <button 
                                type="submit" 
                                class="w-full sm:flex-1 py-4 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 font-black shadow-lg shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs flex items-center justify-center gap-2 group" 
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                                <span>{{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}</span>
                                <svg v-if="!form.processing" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                             <Link :href="route('admin.requests.index')" 
                                class="w-full sm:w-auto px-8 py-4 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-800 font-black transition-all text-xs uppercase tracking-[0.2em] text-center">
                                Batal
                            </Link>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


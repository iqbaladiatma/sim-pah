<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";

const form = useForm({
    type: "utilitas",
    title: "",
    description: "",
    estimated_cost: 0,
    photo_evidence: null,
});

const fileName = ref("");

const costDisplay = computed({
    get: () => formatRupiah(form.estimated_cost),
    set: (val) => {
        form.estimated_cost = parseRupiah(val);
    }
});

const onFileChange = (e) => {
    form.photo_evidence = e.target.files[0];
    fileName.value = e.target.files[0]?.name || "";
};

const submit = () => {
    form.post(route("requests.store"));
};

const types = [
    { value: 'utilitas', label: 'UTILITAS (LISTRIK, AIR, DSB)', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { value: 'barang_habis_pakai', label: 'BARANG HABIS PAKAI (B7)', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { value: 'darurat', label: 'DARURAT / EMERGENCY', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
];
</script>

<template>
    <Head title="Buat Pengajuan Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter">
                        Buat <span class="text-pail-gold">Pengajuan</span> Baru
                    </h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2">
                        <span class="w-8 h-[1px] bg-pail-gold"></span>
                        Formulir Pengadaan & Perbaikan
                    </p>
                </div>
                <Link :href="route('requests.index')" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-2xl hover:bg-gray-200 font-black text-[10px] uppercase tracking-widest transition-all border border-transparent hover:border-gray-200">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-8 pb-20">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 relative">
                    <!-- Accent Decoration -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-pail-gold/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    
                    <div class="p-10 md:p-16 relative z-10">
                        <form @submit.prevent="submit" class="space-y-10">
                            <!-- Type Selection (Visual Cards) -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Pilih Kategori Pengajuan</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button 
                                        v-for="t in types" 
                                        :key="t.value"
                                        type="button" 
                                        @click="form.type = t.value"
                                        class="p-6 rounded-[2rem] border-2 transition-all duration-300 text-left group"
                                        :class="form.type === t.value 
                                            ? 'border-pail-gold bg-pail-gold/5 shadow-lg shadow-pail-gold/10' 
                                            : 'border-gray-100 dark:border-gray-700 hover:border-gray-200'"
                                    >
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110"
                                             :class="form.type === t.value ? 'bg-pail-gold text-white shadow-lg shadow-pail-gold/30' : 'bg-gray-100 dark:bg-gray-900 text-gray-400'">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="t.icon"></path></svg>
                                        </div>
                                        <span class="text-[9px] font-black uppercase tracking-widest leading-tight block" :class="form.type === t.value ? 'text-pail-gold' : 'text-gray-400 group-hover:text-gray-600'">{{ t.label }}</span>
                                    </button>
                                </div>
                                <div v-if="form.errors.type" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.type }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Title -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Judul Pengajuan</label>
                                    <input 
                                        v-model="form.title" 
                                        type="text" 
                                        class="w-full border-gray-100 dark:border-gray-700 rounded-[1.5rem] bg-gray-50/50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-pail-gold focus:border-pail-gold font-bold py-4 px-6 transition-all" 
                                        placeholder="Tuliskan nama barang atau keperluan..." 
                                        required 
                                    />
                                    <div v-if="form.errors.title" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.title }}</div>
                                </div>

                                <!-- Cost -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Estimasi Biaya (Pagu)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                            <span class="text-xs font-black text-pail-gold uppercase">Rp</span>
                                        </div>
                                        <input 
                                            v-model="costDisplay" 
                                            type="text" 
                                            class="w-full border-gray-100 dark:border-gray-700 rounded-[1.5rem] bg-gray-50/50 dark:bg-gray-900 text-xl font-mono font-black tracking-tighter focus:ring-2 focus:ring-pail-gold focus:border-pail-gold py-4 pl-14 pr-6 transition-all text-gray-900 dark:text-white shadow-inner" 
                                            required 
                                        />
                                    </div>
                                    <div v-if="form.errors.estimated_cost" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.estimated_cost }}</div>
                                </div>

                                <!-- File Upload -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Foto Bukti / Referensi</label>
                                    <div class="relative group">
                                        <input 
                                            type="file" 
                                            @change="onFileChange" 
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                            accept="image/*"
                                        />
                                        <div class="w-full border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-[1.5rem] bg-gray-50/50 dark:bg-gray-900 py-4 px-6 flex items-center gap-4 group-hover:border-pail-gold/50 transition-all duration-300">
                                            <div class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 truncate">
                                                    {{ fileName || 'Klik Untuk Unggah Foto' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.photo_evidence" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.photo_evidence }}</div>
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Deskripsi Detail Keperluan</label>
                                    <textarea 
                                        v-model="form.description" 
                                        rows="5" 
                                        class="w-full border-gray-100 dark:border-gray-700 rounded-[2rem] bg-gray-50/50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-pail-gold focus:border-pail-gold font-bold p-6 transition-all" 
                                        placeholder="Jelaskan secara detail alasan pengajuan dan spesifikasi barang jika ada..." 
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="pt-8 border-t border-gray-50 dark:border-gray-700">
                                <button 
                                    type="submit" 
                                    class="w-full md:w-auto px-12 py-5 bg-gray-900 text-pail-gold rounded-[2rem] hover:bg-black font-black text-[12px] uppercase tracking-[0.3em] shadow-2xl shadow-black/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-4 disabled:opacity-50" 
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="w-4 h-4 border-2 border-pail-gold border-t-transparent rounded-full animate-spin"></span>
                                    KIRIM PENGAJUAN SEKARANG
                                </button>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-6 text-center md:text-left">
                                    Setelah dikirim, pengajuan akan masuk ke tahap Tinjaun oleh Admin URT.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

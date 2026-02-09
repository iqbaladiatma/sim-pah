<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";

const props = defineProps({
    request: Object,
});

const form = useForm({
    _method: 'PUT',
    type: props.request.type || "utilitas",
    title: props.request.title || "",
    description: props.request.description || "",
    estimated_cost: props.request.estimated_cost || 0,
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
    form.post(route("requests.update", props.request.id), {
        preserveScroll: true,
    });
};

const types = [
    { value: 'utilitas', label: 'UTILITAS (LISTRIK, AIR, DSB)', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { value: 'barang_habis_pakai', label: 'BARANG HABIS PAKAI (B7)', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { value: 'darurat', label: 'DARURAT / EMERGENCY', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.133-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
];

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Tinjauan',
        processed: 'Diproses',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        completed: 'Selesai'
    };
    return labels[status] || status;
};
</script>

<template>
    <Head title="Edit Pengajuan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter">
                        Edit <span class="text-pail-gold">Pengajuan</span> Saya
                    </h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2">
                        <span class="w-8 h-[1px] bg-pail-gold"></span>
                        Status Saat Ini: {{ getStatusLabel(request.status) }}
                    </p>
                </div>
                <Link :href="route('requests.index')" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-2xl hover:bg-gray-200 font-black text-[10px] uppercase tracking-widest transition-all border border-transparent hover:border-gray-200">
                    &larr; Kembali
                </Link>
            </div>
        </template>

        <div class="pt-8 pb-20">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 relative">
                    
                    <div v-if="request.status !== 'pending'" class="p-16 text-center">
                        <div class="w-24 h-24 rounded-[2.5rem] bg-rose-50 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-4">Akses Terkunci</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest leading-relaxed max-w-md mx-auto">
                            Pengajuan ini sudah masuk tahap <span class="text-rose-600">{{ getStatusLabel(request.status) }}</span> dan tidak dapat diubah lagi oleh sistem.
                        </p>
                        <Link :href="route('requests.index')" class="mt-8 inline-block px-10 py-4 bg-gray-900 text-pail-gold rounded-2xl font-black text-[10px] uppercase tracking-widest">
                            Kembali ke Daftar
                        </Link>
                    </div>

                    <div v-else class="p-10 md:p-16 relative z-10">
                        <form @submit.prevent="submit" class="space-y-10">
                            <!-- Type Selection -->
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
                                            class="w-full border-gray-100 dark:border-gray-700 rounded-[1.5rem] bg-gray-50/50 dark:bg-gray-900 text-xl font-mono font-black tracking-tighter focus:ring-2 focus:ring-pail-gold focus:border-pail-gold py-4 pl-14 pr-6 transition-all text-gray-900 dark:text-white" 
                                            required 
                                        />
                                    </div>
                                    <div v-if="form.errors.estimated_cost" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.estimated_cost }}</div>
                                </div>

                                <!-- File Upload -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Perbarui Foto (Opsional)</label>
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
                                                    {{ fileName || 'Ganti Foto Bukti...' }}
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
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="pt-8 border-t border-gray-50 dark:border-gray-700 flex flex-col md:flex-row items-center gap-6">
                                <button 
                                    type="submit" 
                                    class="w-full md:w-auto px-12 py-5 bg-gray-900 text-pail-gold rounded-[2rem] hover:bg-black font-black text-[12px] uppercase tracking-[0.3em] shadow-2xl shadow-black/20 transition-all hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50" 
                                    :disabled="form.processing"
                                >
                                    SIMPAN PERUBAHAN DATA
                                </button>
                                <div v-if="request.photo_evidence" class="flex items-center gap-3">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Foto Saat Ini:</span>
                                    <img :src="`/storage/${request.photo_evidence}`" class="w-12 h-12 rounded-xl object-cover border-2 border-gray-100 shadow-sm" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

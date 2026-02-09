<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import IconSelect from "@/Components/IconSelect.vue";
import ClockIcon from "@/Components/Icons/ClockIcon.vue";
import RefreshIcon from "@/Components/Icons/RefreshIcon.vue";
import CheckCircleIcon from "@/Components/Icons/CheckCircleIcon.vue";
import XIcon from "@/Components/Icons/XIcon.vue";
import ShieldCheckIcon from "@/Components/Icons/ShieldCheckIcon.vue";

const props = defineProps({
    request: Object,
    institutions: Array,
});

const form = useForm({
    _method: 'PUT',
    institution_id: props.request.institution_id || "",
    type: props.request.type || "utilitas",
    title: props.request.title || "",
    description: props.request.description || "",
    estimated_cost: props.request.estimated_cost || 0,
    status: props.request.status || "pending",
    admin_note: props.request.admin_note || "",
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
    form.post(route("admin.requests.update", props.request.id), {
        preserveScroll: true,
    });
};

const statusOptions = [
    { value: 'pending', label: 'Tinjauan', icon: ClockIcon, iconClass: 'text-yellow-500' },
    { value: 'processed', label: 'Diproses', icon: RefreshIcon, iconClass: 'text-blue-500' },
    { value: 'approved', label: 'Disetujui', icon: CheckCircleIcon, iconClass: 'text-green-500' },
    { value: 'rejected', label: 'Ditolak', icon: XIcon, iconClass: 'text-red-500' },
    { value: 'completed', label: 'Selesai', icon: ShieldCheckIcon, iconClass: 'text-purple-500' }
];

const types = [
    { value: 'utilitas', label: 'UTILITAS', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { value: 'barang_habis_pakai', label: 'B7 (BARANG HABIS PAKAI)', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { value: 'darurat', label: 'DARURAT', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.133-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
];

const getStatusLabel = (status) => {
    return statusOptions.find(o => o.value === status)?.label || status;
};
</script>

<template>
    <Head title="Proses Pengajuan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 sm:px-0 font-sans">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-10 bg-pail-gold rounded-full"></div>
                    <div>
                        <h2 class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter">
                            Proses <span class="text-pail-gold">Pengajuan</span>
                        </h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2">
                             #RE-{{ request.id.toString().padStart(4, '0') }} &bull; {{ getStatusLabel(request.status) }}
                        </p>
                    </div>
                </div>
                <Link :href="route('admin.requests.index')" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-2xl hover:bg-gray-200 font-black text-[10px] uppercase tracking-widest transition-all">
                    &larr; Kembali
                </Link>
            </div>
        </template>

        <div class="pt-8 pb-20">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-12">
                        
                        <!-- Left Panel: Current View -->
                        <div class="lg:col-span-5 bg-gray-50/50 dark:bg-gray-900/50 p-8 sm:p-12 border-b lg:border-b-0 lg:border-r border-gray-100 dark:border-gray-800">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                                <span class="w-8 h-px bg-gray-300"></span>
                                Informasi Pengajuan
                            </h3>

                            <div class="space-y-10">
                                <div>
                                    <span class="text-[10px] font-black text-pail-gold uppercase tracking-widest block mb-2">{{ request.institution?.name }}</span>
                                    <h4 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight">{{ request.title }}</h4>
                                </div>

                                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 leading-relaxed italic">"{{ request.description }}"</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Estimasi Biaya</span>
                                        <p class="text-xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(request.estimated_cost) }}</p>
                                    </div>
                                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Kategori</span>
                                        <p class="text-sm font-black text-pail-gold uppercase tracking-widest">{{ request.type }}</p>
                                    </div>
                                </div>

                                <div v-if="request.photo_evidence">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Bukti Lampiran</span>
                                    <div class="group relative aspect-video rounded-3xl overflow-hidden border-2 border-white dark:border-gray-700 shadow-xl">
                                        <img :src="`/storage/${request.photo_evidence}`" class="w-full h-full object-cover grayscale-[30%] group-hover:grayscale-0 transition-all duration-700" />
                                        <a :href="`/storage/${request.photo_evidence}`" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                             <span class="px-6 py-3 bg-white text-gray-900 rounded-xl font-black text-[10px] uppercase tracking-widest">Lihat Foto Penuh</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Processing Form -->
                        <div class="lg:col-span-7 p-8 sm:p-14">
                            <h3 class="text-[10px] font-black text-pail-gold uppercase tracking-[0.3em] mb-12 flex items-center gap-3">
                                <span class="w-8 h-px bg-pail-gold"></span>
                                Panel Kontrol Admin
                            </h3>

                            <form @submit.prevent="submit" class="space-y-10">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Institution -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Verifikasi Lembaga/Unit</label>
                                        <SearchableSelect
                                            v-model="form.institution_id"
                                            :options="institutions"
                                            placeholder="Pilih Lembaga..."
                                            :customLabel="(opt) => `${opt.code} - ${opt.name}`"
                                            class="!rounded-2xl shadow-inner bg-gray-50/50"
                                        />
                                        <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.institution_id }}</div>
                                    </div>

                                    <!-- Category Select (Small Cards) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Ubah Kategori (Jika perlu)</label>
                                        <div class="flex flex-wrap gap-3">
                                            <button 
                                                v-for="t in types" 
                                                :key="t.value"
                                                type="button"
                                                @click="form.type = t.value"
                                                class="px-5 py-3 rounded-xl border-2 transition-all flex items-center gap-3"
                                                :class="form.type === t.value ? 'border-pail-gold bg-pail-gold/5 text-pail-gold' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="t.icon"></path></svg>
                                                <span class="text-[9px] font-black uppercase tracking-widest">{{ t.label }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Title & Cost -->
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Judul Pengajuan</label>
                                        <input v-model="form.title" type="text" class="w-full border-gray-100 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-900 text-sm font-bold focus:ring-pail-gold focus:border-pail-gold p-4" required />
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Penyesuaian Anggaran</label>
                                        <input v-model="costDisplay" type="text" class="w-full border-gray-100 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-900 text-lg font-black font-mono focus:ring-pail-gold focus:border-pail-gold p-4 tracking-tighter" required />
                                    </div>

                                    <!-- Status Decision -->
                                    <div class="md:col-span-2 pt-6 border-t border-gray-50 dark:border-gray-800">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 text-center">KEPUTUSAN & TINDAK LANJUT</label>
                                        <IconSelect 
                                            v-model="form.status" 
                                            :options="statusOptions"
                                        />
                                        <div v-if="form.errors.status" class="text-red-500 text-[10px] font-bold uppercase mt-2 tracking-widest">{{ form.errors.status }}</div>
                                    </div>

                                    <!-- Admin Note -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Catatan Verifikasi (Tampil ke Lembaga)</label>
                                        <textarea v-model="form.admin_note" rows="3" class="w-full border-gray-100 dark:border-gray-700 rounded-[2rem] bg-gray-50 dark:bg-gray-900 text-sm font-bold focus:ring-pail-gold focus:border-pail-gold p-6 leading-relaxed" placeholder="Tuliskan alasan penolakan atau instruksi selanjutnya..."></textarea>
                                    </div>

                                    <!-- Photo Exchange -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Ganti Foto Lampiran (Opsional)</label>
                                        <div class="relative group">
                                            <input type="file" @change="onFileChange" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                            <div class="w-full border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-3xl bg-gray-50 dark:bg-gray-900 py-4 px-6 flex items-center gap-4 group-hover:border-pail-gold transition-all">
                                                <div class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 truncate">{{ fileName || 'Klik Untuk Unggah Foto Pengganti' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-10 border-t border-gray-50 dark:border-gray-800">
                                    <button 
                                        type="submit" 
                                        class="w-full py-5 bg-gray-900 text-white rounded-full hover:bg-black font-black text-xs uppercase tracking-[0.3em] shadow-2xl transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-6" 
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing" class="w-4 h-4 border-2 border-pail-gold border-t-transparent rounded-full animate-spin"></span>
                                        UPDATE STATUS & SIMPAN PERUBAHAN
                                    </button>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-6 text-center">
                                        Perubahan status akan segera memberitahu lembaga terkait melalui dashboard mereka.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

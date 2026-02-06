<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import IconSelect from "@/Components/IconSelect.vue";
import ClockIcon from "@/Components/Icons/ClockIcon.vue";
import CheckCircleIcon from "@/Components/Icons/CheckCircleIcon.vue";
import XIcon from "@/Components/Icons/XIcon.vue";

const props = defineProps({
    request: Object,
    institutions: Array,
});

const form = useForm({
    _method: 'PUT', // Method spoofing for file uploads
    institution_id: props.request.institution_id || "",
    type: props.request.type || "",
    title: props.request.title || "",
    description: props.request.description || "",
    estimated_cost: props.request.estimated_cost || 0,
    status: props.request.status || "pending",
    admin_note: props.request.admin_note || "",
    photo_evidence: null,
});

const costDisplay = computed({
    get: () => formatRupiah(form.estimated_cost),
    set: (val) => {
        form.estimated_cost = parseRupiah(val);
    }
});

const submit = () => {
    form.post(route("admin.requests.update", props.request.id), {
        preserveScroll: true,
    });
};

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "approved":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const statusOptions = [
    { value: 'pending', label: 'Pendampingan', icon: ClockIcon, iconClass: 'text-yellow-500' },
    { value: 'approved', label: 'Disetujui', icon: CheckCircleIcon, iconClass: 'text-green-500' },
    { value: 'rejected', label: 'Ditolak', icon: XIcon, iconClass: 'text-red-500' }
];
</script>

<template>
    <Head title="Proses Pengajuan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 font-sans">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter text-center sm:text-left">
                    Proses Pengajuan
                </h2>
                <Link :href="route('admin.requests.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all text-center sm:text-right">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 sm:p-12">
                        <header class="mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Detail Pengajuan</h3>
                                <p class="text-xs sm:text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                    Tinjau dokumen, verifikasi estimasi biaya, dan berikan keputusan akhir untuk pengajuan operasional ini.
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-xl sm:rounded-2xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest border shadow-sm"
                                    :class="request.status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 
                                            request.status === 'approved' ? 'bg-green-50 text-green-700 border-green-100' : 
                                            'bg-red-50 text-red-700 border-red-100'">
                                    Status: {{ request.status }}
                                </span>
                            </div>
                        </header>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12">
                            <!-- Left: Content Info -->
                            <div class="lg:col-span-5 space-y-6 sm:space-y-8">
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-800/50">
                                    <div class="mb-6">
                                        <span class="px-3 py-1.5 rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700 mb-4 inline-block">
                                            Lembaga: {{ request.user?.institution?.code || 'URT' }}
                                        </span>
                                        <h4 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-3 uppercase leading-none">{{ request.title }}</h4>
                                        <p class="text-[10px] sm:text-sm text-gray-500 font-medium leading-[1.8]">{{ request.description }}</p>
                                    </div>

                                    <div class="space-y-4 pt-6 border-t border-gray-200/50 dark:border-gray-700">
                                        <div class="flex justify-between items-center gap-2">
                                            <span class="text-[8px] sm:text-[9px] uppercase font-black text-gray-400 tracking-widest shrink-0">Jenis</span>
                                            <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white dark:bg-gray-800 rounded-xl text-[9px] sm:text-xs font-black text-pail-gold border border-gray-100 dark:border-gray-700 uppercase tracking-widest truncate">{{ request.type }}</span>
                                        </div>
                                        <div class="flex justify-between items-center gap-2">
                                            <span class="text-[8px] sm:text-[9px] uppercase font-black text-gray-400 tracking-widest shrink-0">Estimasi Biaya</span>
                                            <span class="text-lg sm:text-xl font-black font-mono text-gray-900 dark:text-white tracking-tighter">{{ formatRupiah(request.estimated_cost) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="request.photo_evidence" class="bg-gray-50 dark:bg-gray-900/50 p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-800/50">
                                    <h5 class="text-[8px] sm:text-[10px] uppercase font-black text-gray-400 tracking-widest mb-6">Bukti Dokumentasi</h5>
                                    <div class="group relative overflow-hidden rounded-[2rem] border border-gray-200 dark:border-gray-700">
                                        <img :src="`/storage/${request.photo_evidence}`" alt="Bukti Foto" class="w-full h-auto grayscale-[50%] group-hover:grayscale-0 transition-all duration-500 scale-105 group-hover:scale-100" />
                                        <a :href="`/storage/${request.photo_evidence}`" target="_blank" class="absolute inset-0 bg-pail-gold/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all font-black text-[10px] text-white uppercase tracking-widest">
                                            Klik untuk Memperbesar
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Decision Form -->
                            <div class="lg:col-span-7">
                                <form @submit.prevent="submit" class="space-y-8">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Lembaga/Unit</label>
                                            <SearchableSelect
                                                v-model="form.institution_id"
                                                :options="institutions"
                                                placeholder="- Pilih Lembaga/Unit -"
                                                :customLabel="(opt) => `${opt.code} - ${opt.name}`"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Tipe Pengajuan</label>
                                            <input v-model="form.type" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" required />
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Update Judul</label>
                                            <input v-model="form.title" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" required />
                                        </div>

                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Deskripsi Detail</label>
                                            <textarea v-model="form.description" rows="3" class="w-full border-gray-100 rounded-3xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-6 leading-relaxed" required></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Lembaga Biaya</label>
                                            <input v-model="costDisplay" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-xl font-black font-mono tracking-tighter px-6" required />
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Unggah Baru (Opsional)</label>
                                            <input type="file" @change="e => form.photo_evidence = e.target.files[0]" class="w-full text-xs text-gray-400 file:mr-4 file:py-4 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-gray-100 file:text-gray-600 hover:file:bg-gray-200 transition bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700" />
                                        </div>

                                        <div class="col-span-2 pt-6 border-t border-gray-50 dark:border-gray-800">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Status Keputusan</label>
                                                    <IconSelect 
                                                        v-model="form.status" 
                                                        :options="statusOptions"
                                                    />
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Catatan Verifikasi</label>
                                                    <textarea v-model="form.admin_note" rows="2" class="w-full border-gray-100 rounded-3xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-sm font-bold focus:ring-pail-gold focus:border-pail-gold p-6" placeholder="Misal: Biaya disesuaikan..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 mt-8 pt-8 border-t border-gray-50 dark:border-gray-800">
                                        <button 
                                            type="submit" 
                                            class="w-full py-4 sm:py-5 bg-pail-gold text-white rounded-full sm:rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-[10px] sm:text-xs" 
                                            :disabled="form.processing"
                                        >
                                            Simpan Keputusan Final
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

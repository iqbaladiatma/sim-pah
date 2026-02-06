<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";
import SearchableSelect from "@/Components/SearchableSelect.vue";

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

const onFileChange = (e) => {
    form.photo_evidence = e.target.files[0];
};

const submit = () => {
    form.post(route("admin.requests.store"));
};
</script>

<template>
    <Head title="Buat Pengajuan Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 font-sans">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter text-center sm:text-left">
                    Buat Pengajuan Baru
                </h2>
                <Link :href="route('admin.requests.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all text-center sm:text-right">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-pail-gold opacity-5 rounded-full blur-3xl"></div>
                    
                    <div class="p-6 sm:p-12 relative z-10">
                        <header class="mb-10 sm:mb-14">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-[2px] bg-pail-gold"></div>
                                <span class="text-[10px] font-black text-pail-gold uppercase tracking-[0.3em]">Operational Portal</span>
                            </div>
                            <h3 class="text-2xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-3">Form Pengajuan</h3>
                            <p class="text-xs sm:text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                Masukkan rincian kebutuhan operasional lembaga. Pastikan data estimasi biaya dan bukti pendukung akurat untuk proses audit.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Lembaga / Unit Pengaju</label>
                                    <SearchableSelect
                                        v-model="form.institution_id"
                                        :options="institutions"
                                        placeholder="- Pilih Lembaga Unit -"
                                        :customLabel="(opt) => `${opt.code} - ${opt.name}`"
                                        class="premium-select"
                                    />
                                    <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.institution_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Klasifikasi Tipe</label>
                                    <input v-model="form.type" type="text" list="type-suggestions" 
                                        class="w-full h-16 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-8 transition-all" 
                                        placeholder="E.g. Utilitas, B7" required />
                                    <datalist id="type-suggestions">
                                        <option value="Utilitas"></option>
                                        <option value="Barang Habis Pakai (B7)"></option>
                                        <option value="Darurat"></option>
                                        <option value="Pemeliharaan"></option>
                                    </datalist>
                                    <div v-if="form.errors.type" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.type }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Judul Pengajuan</label>
                                    <input v-model="form.title" type="text" 
                                        class="w-full h-16 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-8 transition-all" 
                                        placeholder="Ringkasan singkat..." required />
                                    <div v-if="form.errors.title" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.title }}</div>
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Deskripsi & Justifikasi</label>
                                    <textarea v-model="form.description" rows="5" 
                                        class="w-full border-gray-100 rounded-[2.5rem] bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-8 leading-relaxed transition-all" 
                                        placeholder="Berikan rincian lengkap mengenai kebutuhan ini..." required></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.description }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Estimasi Anggaran</label>
                                    <div class="relative group">
                                        <div class="absolute left-6 top-1/2 -translate-y-1/2 text-pail-gold font-black text-lg">Rp</div>
                                        <input v-model="costDisplay" type="text" 
                                            class="w-full h-20 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-3xl font-black font-mono tracking-tighter text-gray-900 dark:text-white pl-16 pr-8 focus:ring-pail-gold focus:border-pail-gold" 
                                            required />
                                    </div>
                                    <div v-if="form.errors.estimated_cost" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.estimated_cost }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Bukti Foto / Lampiran</label>
                                    <div class="relative group">
                                        <input type="file" @change="onFileChange" 
                                            class="w-full text-[10px] text-gray-400 file:mr-6 file:py-5 file:px-8 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:bg-gray-900 file:text-pail-gold hover:file:bg-pail-gold hover:file:text-white file:transition-all bg-gray-50/50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 h-20 flex items-center" />
                                    </div>
                                    <div v-if="form.errors.photo_evidence" class="text-red-500 text-[10px] font-black uppercase mt-3 ml-1 tracking-widest">{{ form.errors.photo_evidence }}</div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 mt-10 sm:mt-14 pt-8 sm:pt-12 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="w-full sm:flex-1 py-5 sm:py-6 bg-pail-gold text-white rounded-full sm:rounded-[2rem] hover:bg-yellow-600 font-black shadow-2xl shadow-pail-gold/30 transition-all uppercase tracking-[0.3em] text-[10px] sm:text-xs transform active:scale-[0.98]" 
                                    :disabled="form.processing"
                                >
                                    Konfirmasi & Kirim
                                </button>
                                <Link :href="route('admin.requests.index')" 
                                    class="w-full sm:auto px-10 py-5 sm:py-6 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-full sm:rounded-[2rem] hover:bg-gray-100 dark:hover:bg-gray-600 font-black transition-all text-[10px] sm:text-xs uppercase tracking-[0.2em] border border-gray-100 dark:border-gray-600 text-center">
                                    Batalkan
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

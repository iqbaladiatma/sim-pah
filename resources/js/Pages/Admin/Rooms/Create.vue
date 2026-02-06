<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import SearchableSelect from "@/Components/SearchableSelect.vue";

const props = defineProps({
    institutions: Array,
});

const form = useForm({
    institution_id: "",
    name: "",
    description: "",
});

const submit = () => {
    form.post(route("admin.rooms.store"));
};
</script>

<template>
    <Head title="Tambah Ruangan Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter text-center sm:text-left">
                    Tambah Ruangan
                </h2>
                <Link :href="route('admin.rooms.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all text-center sm:text-right">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 sm:p-12">
                        <header class="mb-8 sm:mb-12">
                            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Manajemen Lokasi</h3>
                            <p class="text-xs sm:text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                Daftarkan area, ruangan, atau lokasi fisik baru untuk pengorganisasian item inventaris yang lebih akurat.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-8">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Penempatan Lembaga</label>
                                        <SearchableSelect
                                            v-model="form.institution_id"
                                            :options="institutions"
                                            placeholder="- Pilih Lembaga -"
                                            :customLabel="(opt) => `${opt.code} - ${opt.name}`"
                                        />
                                        <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.institution_id }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Ruangan / Lokasi</label>
                                        <input v-model="form.name" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="Misal: Lab Komputer 1, Gudang..." required />
                                        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.name }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Keterangan Spesifik</label>
                                    <textarea v-model="form.description" rows="5" 
                                        class="w-full border-gray-100 rounded-[2rem] bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-6 leading-relaxed" 
                                        placeholder="Berikan detail operasional atau lokasi absolut ruangan ini..."></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-6 sm:pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="w-full py-4 sm:py-5 bg-pail-gold text-white rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-[10px] sm:text-xs" 
                                    :disabled="form.processing"
                                >
                                    Konfirmasi Penyimpanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

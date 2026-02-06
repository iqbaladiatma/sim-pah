<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    code: "",
    description: "",
});

const submit = () => {
    form.post(route("admin.institutions.store"));
};
</script>

<template>
    <Head title="Tambah Lembaga Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Tambah Lembaga
                </h2>
                <Link :href="route('admin.institutions.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-12">
                        <header class="mb-12">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Pendaftaran Unit</h3>
                            <p class="text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                Tambahkan lembaga atau unit operasional baru ke dalam sistem untuk manajemen inventaris mandiri.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-8">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Lembaga</label>
                                        <input v-model="form.name" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="Misal: SMA Abu Hurairah" required autofocus />
                                        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Kode Unit Unik</label>
                                        <input v-model="form.code" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="Contoh: SMA-AH" required />
                                        <div v-if="form.errors.code" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.code }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Deskripsi & Catatan</label>
                                    <textarea v-model="form.description" rows="5" 
                                        class="w-full border-gray-100 rounded-[2rem] bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-6 leading-relaxed" 
                                        placeholder="Berikan keterangan singkat mengenai unit ini..."></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="flex-1 py-5 bg-pail-gold text-white rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs" 
                                    :disabled="form.processing"
                                >
                                    Konfirmasi Pendaftaran
                                </button>
                                <Link :href="route('admin.institutions.index')" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-[2rem] hover:bg-gray-100 font-bold transition text-xs uppercase tracking-widest">
                                    Batal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

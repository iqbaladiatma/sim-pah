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
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Tambah Lembaga Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Form Lembaga</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Masukkan detail lembaga baru untuk sistem inventaris.
                                </p>
                            </div>
                            <Link :href="route('admin.institutions.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-xl space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lembaga</label>
                                <input v-model="form.name" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: SMA Abu Hurairah" required autofocus />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kode Unit (Singkatan Unik)</label>
                                <input v-model="form.code" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: SMA-AH" required />
                                <div v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi (Opsional)</label>
                                <textarea v-model="form.description" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold"></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="submit" class="px-8 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">Simpan Lembaga</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

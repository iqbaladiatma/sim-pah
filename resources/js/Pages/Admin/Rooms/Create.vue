<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

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
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Tambah Ruangan Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Form Ruangan</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Tambahkan data ruangan atau lokasi baru.
                                </p>
                            </div>
                            <Link :href="route('admin.rooms.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-xl">
                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Lembaga</label>
                                <select v-model="form.institution_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required>
                                    <option value="">- Pilih Lembaga -</option>
                                    <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.code }} - {{ inst.name }}</option>
                                </select>
                                <div v-if="form.errors.institution_id" class="text-red-500 text-xs mt-1">{{ form.errors.institution_id }}</div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Ruangan</label>
                                <input v-model="form.name" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: Lab Komputer 1, Gudang..." required />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan / Deskripsi</label>
                                <textarea v-model="form.description" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: Lantai 2, Sebelah Perpustakaan..."></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <button type="submit" class="px-8 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">Simpan Ruangan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { formatRupiah, parseRupiah } from "@/Utils/format";

const props = defineProps({
    requests: Object,
});

const form = useForm({
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

const isCreateModalOpen = ref(false);

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitCreate = () => {
    form.post(route("requests.store"), {
        onSuccess: () => closeCreateModal(),
    });
};

const onFileChange = (e) => {
    form.photo_evidence = e.target.files[0];
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
</script>

<template>
    <Head title="Pengajuan Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                Data Pengajuan Saya
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add Button -->
                <div class="mb-8 flex justify-end">
                    <button
                        @click="openCreateModal()"
                        class="px-6 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-pail-gold/20 font-bold"
                    >
                        + Buat Pengajuan Baru
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400">
                                    <th class="px-6 py-4 text-left">Tipe</th>
                                    <th class="px-6 py-4 text-left">Judul</th>
                                    <th class="px-6 py-4 text-left">Est. Biaya</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="req in requests.data" :key="req.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold uppercase text-gray-500">{{ req.type }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ req.title }}</div>
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ req.description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-gray-700 dark:text-gray-300">
                                        {{ formatRupiah(req.estimated_cost) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full uppercase" :class="getStatusColor(req.status)">
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="text-pail-gold hover:underline font-bold text-xs">Lihat Bukti</a>
                                        <div v-if="req.admin_note" class="text-[10px] text-gray-400 mt-1 italic">Note: {{ req.admin_note }}</div>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada pengajuan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="isCreateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-black mb-6 text-gray-900 dark:text-white">Form Pengajuan Unit</h3>

                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipe Pengajuan</label>
                        <input v-model="form.type" type="text" class="block w-full border-gray-200 rounded-xl shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" placeholder="Contoh: Utilitas, B7, Darurat" required />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Judul / Keperluan</label>
                        <input v-model="form.title" type="text" class="block w-full border-gray-200 rounded-xl shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required placeholder="Contoh: Perbaikan AC Masjid" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Deskripsi Lengkap</label>
                        <textarea v-model="form.description" rows="3" class="block w-full border-gray-200 rounded-xl shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required placeholder="Jelaskan secara detail..."></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimasi Biaya (Otomatis IDR)</label>
                        <input v-model="costDisplay" type="text" class="block w-full border-gray-200 rounded-xl shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono font-bold text-lg" required />
                    </div>


                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" @click="closeCreateModal" class="px-6 py-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 font-bold transition">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

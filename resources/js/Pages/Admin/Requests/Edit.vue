<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";

const props = defineProps({
    request: Object,
});

const form = useForm({
    status: props.request.status || "approved",
    admin_note: props.request.admin_note || "",
});

const submit = () => {
    form.put(route("admin.requests.update", props.request.id));
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
    <Head title="Proses Pengajuan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Proses Pengajuan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pengajuan</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Tinjau dan proses pengajuan dari user.
                                </p>
                            </div>
                            <Link :href="route('admin.requests.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <!-- Left Column: Request Details -->
                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-3xl border border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {{ request.user?.institution?.code || 'ADMIN' }}
                                        </span>
                                        <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider shadow-sm"
                                            :class="getStatusColor(request.status)">
                                            {{ request.status }}
                                        </span>
                                    </div>
                                    
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ request.title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">{{ request.description }}</p>
                                    
                                    <div class="flex justify-between items-center py-3 border-t border-gray-200 dark:border-gray-700">
                                        <span class="text-xs uppercase font-black text-gray-400">Tipe</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ request.type }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-t border-gray-200 dark:border-gray-700">
                                        <span class="text-xs uppercase font-black text-gray-400">Estimasi Biaya</span>
                                        <span class="font-mono font-bold text-lg text-gray-900 dark:text-white">{{ formatRupiah(request.estimated_cost) }}</span>
                                    </div>
                                </div>

                                <div v-if="request.photo_evidence" class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-3xl border border-gray-100 dark:border-gray-800">
                                    <h5 class="text-xs uppercase font-black text-gray-400 mb-4">Bukti Foto</h5>
                                    <img :src="`/storage/${request.photo_evidence}`" alt="Bukti Foto" class="w-full h-auto rounded-xl shadow-md border border-gray-200 dark:border-gray-700" />
                                    <a :href="`/storage/${request.photo_evidence}`" target="_blank" class="block mt-4 text-center text-blue-600 hover:text-blue-800 font-bold text-sm underline">
                                        Lihat Ukuran Penuh
                                    </a>
                                </div>
                            </div>

                            <!-- Right Column: Admin Action Form -->
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Form Keputusan</h4>
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keputusan</label>
                                        <select v-model="form.status" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold">
                                            <option value="approved">Setujui (Approved)</option>
                                            <option value="rejected">Tolak (Rejected)</option>
                                        </select>
                                        <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Admin</label>
                                        <textarea v-model="form.admin_note" rows="4" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Opsional..."></textarea>
                                        <div v-if="form.errors.admin_note" class="text-red-500 text-xs mt-1">{{ form.errors.admin_note }}</div>
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 font-black shadow-lg shadow-blue-600/20 transition uppercase tracking-widest" :disabled="form.processing">
                                            Simpan Keputusan
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

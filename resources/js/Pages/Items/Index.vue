<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    items: Object,
});

const form = useForm({
    stock: 0,
    reason: "",
});

const isModalOpen = ref(false);
const editingItem = ref(null);

const openModal = (item) => {
    editingItem.value = item;
    form.stock = item.stock;
    form.reason = "";
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.put(route("items.update", editingItem.value.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Head title="Inventaris Lembaga" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Inventaris Unit
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-[95%] sm:px-6 lg:px-8">
                <!-- Info Banner -->
                <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-2xl">
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-bold">
                        ℹ️ Berikut adalah daftar inventaris yang tercatat di unit Anda. Klik "Update Stok" jika ada perubahan jumlah fisik barang.
                    </p>
                </div>

                <!-- Table Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto p-2">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-widest bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-4 py-4 text-center">No.</th>
                                    <th class="px-4 py-4 text-left">Ruangan</th>
                                    <th class="px-4 py-4 text-left">Jenis Barang</th>
                                    <th class="px-4 py-4 text-left">Merk</th>
                                    <th class="px-4 py-4 text-center">Stok</th>
                                    <th class="px-4 py-4 text-center">Keadaan</th>
                                    <th class="px-4 py-4 text-left">Keterangan</th>
                                    <th class="px-4 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(item, index) in items.data" :key="item.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-xs">
                                    <td class="px-4 py-4 text-center text-gray-400 font-mono">{{ index + 1 }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-700 dark:text-gray-300 capitalize">{{ item.room?.name || '-' }}</td>
                                    <td class="px-4 py-4 font-black text-gray-900 dark:text-white uppercase">{{ item.name }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-600 dark:text-gray-400">{{ item.brand }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-1 rounded-lg font-black" :class="item.stock <= item.min_stock ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'">
                                            {{ item.stock }} <span class="text-[9px] uppercase opacity-60 ml-0.5">{{ item.unit }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full font-black uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-400 max-w-[150px] truncate" :title="item.note">{{ item.note || '-' }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <button @click="openModal(item)" class="px-4 py-2 bg-pail-gold text-white rounded-xl shadow-lg shadow-pail-gold/20 font-black uppercase text-[10px] hover:bg-yellow-600 transition">Update Stok</button>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td colspan="8" class="px-6 py-20 text-center text-gray-400 italic font-bold">Belum ada data inventaris unit.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-black mb-6 text-gray-900 dark:text-white uppercase tracking-tighter">
                    Request Update Stok
                </h3>
                <p class="mb-6 text-sm text-gray-500 font-bold border-b pb-4">
                    Barang: <span class="text-pail-gold uppercase">{{ editingItem?.name }}</span> ({{ editingItem?.brand }})
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah Fisik Baru</label>
                        <input v-model="form.stock" type="number" min="0" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-lg focus:ring-pail-gold focus:border-pail-gold font-black text-center" required />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alasan / Keterangan Perubahan</label>
                        <textarea v-model="form.reason" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required placeholder="Contoh: Barang dipakai operasional bulanan..."></textarea>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-black shadow-lg shadow-pail-gold/20 transition uppercase tracking-widest" :disabled="form.processing">Kirim Permintaan Update</button>
                        <button type="button" @click="closeModal" class="w-full py-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 font-bold transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

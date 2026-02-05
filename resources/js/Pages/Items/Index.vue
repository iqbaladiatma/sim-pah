<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

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
                <div class="mb-6 p-5 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl flex items-start gap-3">
                    <InfoIcon className="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium leading-relaxed">
                        Berikut adalah daftar inventaris yang tercatat di unit Anda. Klik "Update Stok" jika ada perubahan jumlah fisik barang.
                    </p>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
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
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="(item, index) in items.data" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors duration-150 text-xs">
                                    <td class="px-4 py-4 text-center text-gray-400 font-mono text-[11px]">{{ index + 1 }}</td>
                                    <td class="px-4 py-4 font-semibold text-gray-700 dark:text-gray-300 capitalize">{{ item.room?.name || '-' }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-900 dark:text-white uppercase">{{ item.name }}</td>
                                    <td class="px-4 py-4 font-medium text-gray-600 dark:text-gray-400">{{ item.brand }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg font-bold text-xs" :class="item.stock <= item.min_stock ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400'">
                                            {{ item.stock }} <span class="text-[9px] uppercase opacity-70 ml-0.5">{{ item.unit }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-500 dark:text-gray-400 max-w-[150px] truncate" :title="item.note">{{ item.note || '-' }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <button @click="openModal(item)" class="px-4 py-2 bg-pail-gold text-white rounded-xl shadow-md hover:shadow-lg hover:bg-yellow-600 transition-all duration-200 font-semibold uppercase text-[10px]">
                                            Update Stok
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td colspan="8" class="px-6 py-16 text-center text-gray-400 italic font-medium">Belum ada data inventaris unit.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="(item, index) in items.data" :key="item.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base uppercase mb-1">{{ item.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.brand }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400'">
                                    {{ item.condition }}
                                </span>
                            </div>

                            <!-- Info Grid -->
                            <div class="space-y-2.5 mb-4">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Ruangan</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ item.room?.name || '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Stok</span>
                                    <span class="px-2.5 py-1 rounded-lg font-bold text-xs" :class="item.stock <= item.min_stock ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400'">
                                        {{ item.stock }} {{ item.unit }}
                                    </span>
                                </div>
                                <div v-if="item.note" class="py-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">Keterangan</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ item.note }}</p>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <button @click="openModal(item)" class="w-full py-3 bg-pail-gold text-white rounded-xl shadow-md hover:shadow-lg hover:bg-yellow-600 transition-all duration-200 font-semibold text-sm">
                                Update Stok
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="items.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada data inventaris unit.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Minimalist Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" @click.self="closeModal">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="isModalOpen" class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Modal Header -->
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Request Update Stok
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Barang: <span class="text-pail-gold font-semibold">{{ editingItem?.name }}</span> ({{ editingItem?.brand }})
                            </p>
                        </div>

                        <!-- Modal Body -->
                        <form @submit.prevent="submit" class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah Fisik Baru
                                </label>
                                <input 
                                    v-model="form.stock" 
                                    type="number" 
                                    min="0" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 dark:text-white text-lg focus:ring-2 focus:ring-pail-gold focus:border-transparent font-semibold text-center transition" 
                                    required 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Alasan / Keterangan Perubahan
                                </label>
                                <textarea 
                                    v-model="form.reason" 
                                    rows="3" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-pail-gold focus:border-transparent font-medium transition resize-none" 
                                    required 
                                    placeholder="Contoh: Barang dipakai operasional bulanan..."
                                ></textarea>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                                <button 
                                    type="button" 
                                    @click="closeModal" 
                                    class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 font-semibold transition-all duration-200"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit" 
                                    class="w-full sm:flex-1 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 font-semibold shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed" 
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Mengirim...' : 'Kirim Permintaan Update' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

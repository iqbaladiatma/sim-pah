<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    items: Object,
    institutions: Array,
});

const form = useForm({
    institution_id: "",
    room_id: "",
    name: "",
    purchase_date: "",
    stock: 0,
    source: "",
    condition: "Baik",
    responsible_person: "",
    note: "",
});

const isModalOpen = ref(false);
const editingItem = ref(null);
const rooms = ref([]);
const isLoadingRooms = ref(false);

const fetchRooms = async (institutionId) => {
    if (!institutionId) {
        rooms.value = [];
        return;
    }
    isLoadingRooms.value = true;
    try {
        const response = await axios.get(route('admin.rooms.by_institution', institutionId));
        rooms.value = response.data;
    } catch (error) {
        console.error("Failed to fetch rooms", error);
    } finally {
        isLoadingRooms.value = false;
    }
};

watch(() => form.institution_id, (newVal) => {
    if (!editingItem.value) {
        form.room_id = "";
    }
    fetchRooms(newVal);
});

const openModal = async (item = null) => {
    if (item) {
        editingItem.value = item;
        form.institution_id = item.institution_id;
        await fetchRooms(item.institution_id);
        form.room_id = item.room_id;
        form.name = item.name;
        form.purchase_date = item.purchase_date;
        form.stock = item.stock;
        form.source = item.source;
        form.condition = item.condition;
        form.responsible_person = item.responsible_person;
        form.note = item.note;
    } else {
        editingItem.value = null;
        form.reset();
        form.institution_id = "";
        form.room_id = "";
        rooms.value = [];
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route("admin.items.update", editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.items.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (id) => {
    if (confirm("Yakin ingin menghapus barang ini?")) {
        form.delete(route("admin.items.destroy", id));
    }
};

const importForm = useForm({
    file: null,
});

const isImportModalOpen = ref(false);

const openImportModal = () => {
    isImportModalOpen.value = true;
};

const closeImportModal = () => {
    isImportModalOpen.value = false;
    importForm.reset();
};

const handleImport = () => {
    importForm.post(route("admin.items.import"), {
        onSuccess: () => closeImportModal(),
    });
};
</script>

<template>
    <Head title="Manajemen Barang (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Manajemen Seluruh Barang (Inventaris)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-[95%] sm:px-6 lg:px-8">
                <!-- Action Buttons -->
                <div class="mb-8 flex justify-end gap-3">
                    <button
                        @click="openImportModal"
                        class="px-6 py-3 bg-green-600 text-white rounded-2xl hover:bg-green-700 transition shadow-lg shadow-green-600/20 font-bold text-sm flex items-center gap-2"
                    >
                        <span>📂</span> Impor Excel
                    </button>
                    <button
                        @click="openModal()"
                        class="px-6 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 transition shadow-lg shadow-pail-gold/20 font-bold text-sm flex items-center gap-2"
                    >
                        <span>+</span> Tambah Barang
                    </button>
                </div>

                <!-- Table Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto p-2">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-widest bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-4 py-4 text-center">No.</th>
                                    <th class="px-4 py-4 text-left">Lembaga</th>
                                    <th class="px-4 py-4 text-left font-black text-rose-500">RUANGAN</th>
                                    <th class="px-4 py-4 text-left">JENIS BARANG</th>
                                    <th class="px-4 py-4 text-left">TANGGAL PEMBUKUAN</th>
                                    <th class="px-4 py-4 text-center">KUANTITAS</th>
                                    <th class="px-4 py-4 text-left">SUMBER PEROLEHAN</th>
                                    <th class="px-4 py-4 text-center">KEADAAN</th>
                                    <th class="px-4 py-4 text-left">PENANGGUNG JAWAB</th>
                                    <th class="px-4 py-4 text-left">KETERANGAN</th>
                                    <th class="px-4 py-4 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(item, index) in items.data" :key="item.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-[11px]">
                                    <td class="px-4 py-4 text-center text-gray-400 font-mono">{{ index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <span class="font-black text-pail-gold">{{ item.institution?.code || '-' }}</span>
                                    </td>
                                    <td class="px-4 py-4 font-bold text-gray-700 dark:text-gray-300 capitalize text-nowrap">{{ item.room?.name || '-' }}</td>
                                    <td class="px-4 py-4 font-black text-gray-900 dark:text-white uppercase">{{ item.name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ item.purchase_date || '-' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-1 rounded-lg font-black bg-gray-50 text-gray-900">
                                            {{ item.stock }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 italic">{{ item.source }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full font-black uppercase text-[9px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-500">{{ item.responsible_person || '-' }}</td>
                                    <td class="px-4 py-4 text-gray-400 max-w-[150px] truncate" :title="item.note">{{ item.note || '-' }}</td>
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        <button @click="openModal(item)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition mr-1 font-bold italic underline">Edit</button>
                                        <button @click="deleteItem(item.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition font-bold italic underline">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td colspan="11" class="px-6 py-20 text-center text-gray-400 italic font-bold">Belum ada data inventaris.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination -->
                <Pagination :links="items.links" />
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-10 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                        {{ editingItem ? "📝 Edit Data Barang" : "✨ Tambah Barang Baru" }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition text-2xl">&times;</button>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Row 1: Institution & Room -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lembaga Pemilik</label>
                        <select v-model="form.institution_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required>
                            <option value="">- Pilih Lembaga -</option>
                            <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.code }} - {{ inst.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">📍 Lokasi / Ruangan</label>
                        <select v-model="form.room_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required :disabled="!form.institution_id || isLoadingRooms">
                            <option value="">- Pilih Ruangan -</option>
                            <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                        </select>
                    </div>

                    <!-- Row 2: Jenis -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">📦 JENIS BARANG</label>
                        <input v-model="form.name" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: AC, Laptop, Meja" required />
                    </div>

                    <!-- Row 3: Tgl Pembukuan & Kuantitas -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">📅 TANGGAL PEMBUKUAN</label>
                        <input v-model="form.purchase_date" type="date" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-mono font-bold" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">🔢 KUANTITAS</label>
                        <input v-model="form.stock" type="number" min="0" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold text-center" required />
                    </div>

                    <!-- Row 4: Sumber & Keadaan -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">💎 SUMBER PEROLEHAN</label>
                        <input v-model="form.source" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: Hibah, APB, Yayasan" required />
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">✅ KEADAAN BARANG</label>
                        <select v-model="form.condition" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Row 5: PJ -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">👤 PENANGGUNG JAWAB</label>
                        <input v-model="form.responsible_person" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Nama petugas" />
                    </div>

                    <!-- Row 6: Keterangan -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">📝 KETERANGAN</label>
                        <textarea v-model="form.note" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Berikan catatan tambahan jika ada..."></textarea>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" @click="closeModal" class="px-8 py-3 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 font-bold transition">Batal</button>
                        <button type="submit" class="px-10 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-black mb-6 text-gray-900 dark:text-white uppercase tracking-tighter italic text-center">🚀 Import Inventory</h3>
                <form @submit.prevent="handleImport">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input
                            @input="importForm.file = $event.target.files[0]"
                            type="file"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition shadow-sm"
                            required
                        />
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl mb-8 text-[10px] text-gray-500 leading-relaxed border border-gray-100 dark:border-gray-800">
                        <p class="font-black text-gray-700 dark:text-gray-300 uppercase mb-3 text-center border-b pb-2">📋 Format Header Kolom:</p>
                        <div class="space-y-1 font-mono text-[9px] text-center">
                            <code>lembaga_kode, ruangan, jenis_barang, tanggal_pembukuan_pembelian, kuantitas, sumber_perolehan_barang, keadaan_barang, penanggung_jawab, keterangan</code>
                        </div>
                        <a :href="route('admin.items.template')" class="mt-4 block text-center py-3 px-4 bg-blue-50 text-blue-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition border border-blue-100 shadow-sm">
                             📥 Download Template Terbaru (.csv)
                        </a>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-2xl hover:bg-green-700 font-black shadow-lg shadow-green-600/20 transition uppercase tracking-widest" :disabled="importForm.processing">Mulai Impor Sekarang</button>
                        <button type="button" @click="closeImportModal" class="w-full py-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 font-bold transition">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

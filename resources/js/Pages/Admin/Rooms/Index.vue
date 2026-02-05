<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    rooms: Object,
    institutions: Array,
});

const form = useForm({
    institution_id: "",
    name: "",
    description: "",
});

const isModalOpen = ref(false);
const editingRoom = ref(null);

const openModal = (room = null) => {
    if (room) {
        editingRoom.value = room;
        form.institution_id = room.institution_id;
        form.name = room.name;
        form.description = room.description;
    } else {
        editingRoom.value = null;
        form.reset();
        form.institution_id = "";
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingRoom.value) {
        form.put(route("admin.rooms.update", editingRoom.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.rooms.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteRoom = (id) => {
    if (confirm("Yakin ingin menghapus ruangan ini?")) {
        form.delete(route("admin.rooms.destroy", id));
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
    importForm.post(route("admin.rooms.import"), {
        onSuccess: () => closeImportModal(),
    });
};
</script>

<template>
    <Head title="Manajemen Ruangan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Master Data Ruangan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                        <span>+</span> Tambah Ruangan
                    </button>
                </div>

                <!-- Table Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-widest bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-left">Lembaga</th>
                                    <th class="px-6 py-4 text-left">Nama Ruangan</th>
                                    <th class="px-6 py-4 text-left">Deskripsi / Lokasi</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="room in rooms.data" :key="room.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                    <td class="px-6 py-4 font-black text-pail-gold">{{ room.institution?.code }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ room.name }}</td>
                                    <td class="px-6 py-4 text-gray-500 italic">{{ room.description || '-' }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <button @click="openModal(room)" class="text-blue-600 hover:text-blue-900 font-bold mr-3 underline italic">Edit</button>
                                        <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-900 font-bold underline italic">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="rooms.data.length === 0">
                                    <td colspan="4" class="px-6 py-20 text-center text-gray-400 italic font-bold text-sm">Belum ada data ruangan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination -->
                <Pagination :links="rooms.links" />
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-2xl font-black mb-8 text-gray-900 dark:text-white uppercase tracking-tighter text-center">
                    {{ editingRoom ? "📝 Edit Ruangan" : "✨ Ruangan Baru" }}
                </h3>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Lembaga</label>
                        <select v-model="form.institution_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" required>
                            <option value="">- Pilih Lembaga -</option>
                            <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.code }} - {{ inst.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Ruangan</label>
                        <input v-model="form.name" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: Lab Komputer 1, Gudang..." required />
                        <div v-if="form.errors.name" class="text-red-500 text-[10px] mt-1 font-bold">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan / Deskripsi</label>
                        <textarea v-model="form.description" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold" placeholder="Contoh: Lantai 2, Sebelah Perpustakaan..."></textarea>
                    </div>

                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="closeModal" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 font-bold transition">Batal</button>
                        <button type="submit" class="flex-[2] py-4 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-black shadow-lg shadow-pail-gold/20 transition uppercase tracking-widest" :disabled="form.processing">Simpan Ruangan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-black mb-6 text-gray-900 dark:text-white uppercase tracking-tighter italic text-center text-green-600">📂 Import Master Ruangan</h3>
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
                        <p class="font-black text-gray-700 dark:text-gray-300 uppercase mb-3 text-center border-b pb-2">📋 Format Kolom:</p>
                        <div class="space-y-1 font-mono text-[9px] text-center">
                            <code>institution_code, name, description</code>
                        </div>
                        <a :href="route('admin.rooms.template')" class="mt-4 block text-center py-3 px-4 bg-blue-50 text-blue-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition border border-blue-100 shadow-sm">
                             📥 Download Template Ruangan (.csv)
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

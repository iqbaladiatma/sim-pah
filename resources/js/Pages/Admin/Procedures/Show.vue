<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Use local icons from the project
import DownloadIcon from '@/Components/Icons/DownloadIcon.vue';
import UploadIcon from '@/Components/Icons/InboxIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import PencilIcon from '@/Components/Icons/EditIcon.vue';
import PhotographIcon from '@/Components/Icons/SparklesIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';

const props = defineProps({
    type: String,
    procedure: Object,
    data: Object,
    rooms: Array,
    institutions: Array,
    items: Array,
    users: Array,
    vehicles: Array,
});

const showCreateModal = ref(false);
const form = useForm({
    title: '',
    description: '',
    location: '',
    cost: 0,
    scheduled_at: '',
    status: 'pending',
    photo_evidence: null,
    institution_id: '',
    room_id: '',
    vehicle_id: '',
});

const submit = () => {
    form.post(route('admin.procedures.store', props.type), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        }
    });
};

const deleteItem = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        form.delete(route('admin.procedures.destroy', { type: props.type, id }));
    }
};

const exportExcel = () => {
    window.location.href = route('admin.procedures.export', props.type);
};

const fileInput = ref(null);
const importExcel = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const importForm = useForm({ file });
    importForm.post(route('admin.procedures.import', props.type), {
        onSuccess: () => {
            if (fileInput.value) fileInput.value.value = '';
        }
    });
};
</script>

<template>
    <Head :title="procedure.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.procedures.index')" class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-pail-gold shadow-sm transition-all shrink-0">
                        &larr;
                    </Link>
                    <div>
                        <h2 class="text-lg md:text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight">{{ procedure.title }}</h2>
                        <p class="text-[9px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Management Console</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                    <button @click="exportExcel" class="flex-1 sm:flex-none px-4 md:px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[9px] md:text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                        <DownloadIcon class="w-4 h-4" /> <span class="hidden sm:inline">Export</span>
                    </button>
                    <label class="flex-1 sm:flex-none px-4 md:px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[9px] md:text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <UploadIcon class="w-4 h-4" /> <span class="hidden sm:inline">Import</span>
                        <input type="file" ref="fileInput" @change="importExcel" class="hidden" accept=".xlsx,.xls,.csv" />
                    </label>
                    <button @click="showCreateModal = true" class="w-full sm:w-auto px-6 md:px-8 py-3 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-white rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all flex items-center justify-center gap-2 shadow-2xl">
                        <PlusIcon class="w-4 h-4" /> Tambah Data
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Table Section (Card view on mobile) -->
                <div class="bg-white dark:bg-gray-800 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <!-- Dynamic Headers based on Type -->
                            <template v-if="type.includes('kendaraan')">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Armada / Plat</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Detail & Merk</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Pajak/Entry</th>
                            </template>
                            <template v-else-if="type.includes('aset')">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Informasi Aset</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori/Lembaga</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kondisi</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nilai Aset</th>
                            </template>
                            <template v-else>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data & Detail Aktivitas</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi/Lembaga</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Penyelesaian</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Biaya/Nilai</th>
                            </template>
                            
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <tr v-for="item in data.data" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors group">
                            <!-- Specialized Columns for VEHICLES -->
                            <template v-if="type.includes('kendaraan')">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ item.name }}</span>
                                        <span class="text-[10px] font-bold text-pail-gold mt-1">{{ item.plate_number }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.brand }} ({{ item.year }})</span>
                                        <span class="text-[9px] text-gray-400 mt-1 uppercase">{{ item.type }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                        :class="item.status === 'available' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'">
                                        {{ item.status }}
                                    </span>
                                </td>
                            </template>

                            <!-- Generic Content for Others -->
                            <template v-else>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase truncate max-w-xs">{{ item.title || item.name }}</span>
                                        <span class="text-[10px] text-gray-400 mt-1 truncate max-w-xs">{{ item.description || item.reason || 'Sesuai Prosedur URT' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.location || 'Area Terbuka' }}</span>
                                        <span class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1">{{ item.institution?.name || 'Pusat' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                        :class="item.status === 'completed' || item.status === 'approved' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-yellow-50 text-yellow-600 border-yellow-100'">
                                        {{ item.status || 'Active' }}
                                    </span>
                                </td>
                            </template>

                            <td class="px-8 py-6 text-right">
                                <span class="text-xs font-mono font-black text-gray-900 dark:text-white">
                                    {{ item.cost || item.tax_expiry || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 hover:text-pail-gold">
                                        <PencilIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteItem(item.id)" class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-300 hover:text-red-500">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                <div v-for="item in data.data" :key="item.id" class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight">{{ item.name || item.title }}</span>
                            <span class="text-[10px] font-bold text-pail-gold mt-1 uppercase tracking-widest" v-if="item.plate_number">{{ item.plate_number }}</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                            :class="item.status === 'available' || item.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'">
                            {{ item.status || 'Active' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Lokasi / Detail</p>
                            <p class="text-[10px] font-bold text-gray-600 dark:text-gray-300 uppercase leading-tight">
                                {{ item.location || item.brand || 'URT Center' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Nilai / Biaya</p>
                            <p class="text-[10px] font-mono font-black text-gray-900 dark:text-white leading-tight">
                                {{ item.cost || item.tax_expiry || 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                        <button class="flex-1 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-pail-gold">
                            <PencilIcon class="w-4 h-4 mr-2" /> <span class="text-[9px] font-black uppercase">Edit</span>
                        </button>
                        <button @click="deleteItem(item.id)" class="flex-1 py-3 rounded-xl bg-red-50 flex items-center justify-center text-red-300 hover:text-red-500">
                            <TrashIcon class="w-4 h-4 mr-2" /> <span class="text-[9px] font-black uppercase">Hapus</span>
                        </button>
                    </div>
                </div>
                <div v-if="data.data.length === 0" class="p-12 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada data.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center gap-2">
                <Link v-for="link in data.links" :key="link.label" :href="link.url || '#'" 
                    class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all"
                    :class="link.active ? 'bg-gray-900 text-pail-gold border-gray-900' : 'bg-white text-gray-400 border-gray-100 hover:border-pail-gold'"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreateModal" class="fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-black/60 backdrop-blur-md">
                <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-t-[2rem] sm:rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-white/10 max-h-[90vh] overflow-y-auto pb-28 sm:pb-0">
                    <div class="p-6 md:p-12">
                        <div class="flex items-start justify-between mb-8 md:mb-10">
                            <div>
                                <h3 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight">Entri Prosedur Baru</h3>
                                <p class="text-[9px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">SOP Entry System</p>
                            </div>
                            <button @click="showCreateModal = false" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-500 transition-colors flex items-center justify-center shrink-0">
                                <XIcon class="w-6 h-6" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Field Khusus KENDARAAN -->
                                <template v-if="type.includes('kendaraan') && !type.includes('pengajuan')">
                                    <div class="sm:col-span-2">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nama Kendaraan</label>
                                        <input v-model="form.name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Bus Sekolah Hino">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nomor Plat (TNKB)</label>
                                        <input v-model="form.plate_number" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="DR 1234 XY">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Merk / Tipe</label>
                                        <input v-model="form.brand" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Toyota / Mitsubishi">
                                    </div>
                                </template>

                                <!-- Field Standar / MAINTENANCE -->
                                <template v-else>
                                    <div class="sm:col-span-2">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Judul Aktivitas / Nama Data</label>
                                        <input v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Pembersihan AC Ruang Mudir">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Deskripsi / Detail Prosedur</label>
                                        <textarea v-model="form.description" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="3"></textarea>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Area / Lokasi Fisik</label>
                                        <input v-model="form.location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nilai Finansial (Biaya/Harga)</label>
                                        <input v-model="form.cost" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                    </div>
                                </template>

                                <!-- Common Selectors -->
                                <div v-if="!type.includes('registrasi-kendaraan')">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lembaga Terkait</label>
                                    <select v-model="form.institution_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                        <option value="">Pilih Lembaga</option>
                                        <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
                                    </select>
                                </div>
                                <div v-if="type.includes('kendaraan') && type.includes('pengajuan')">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pilih Kendaraan</label>
                                    <select v-model="form.vehicle_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                        <option value="">Pilih Armada</option>
                                        <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.name }} - {{ v.plate_number }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-8 md:mt-12 flex flex-col-reverse sm:flex-row justify-end gap-3 md:gap-4">
                                <button @click="showCreateModal = false" type="button" class="w-full sm:w-auto px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-gray-200 transition-all text-center">Batal</button>
                                <button :disabled="form.processing" type="submit" class="w-full sm:w-auto px-12 py-4 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl disabled:opacity-50">
                                    Simpan Dokumentasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>

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

const airBersihItems = [
    { title: 'Sumber air bersih', standard: 'Debit air yang cukup', method: 'Alat Ukur ( ltr / dtk )', frequency: 'Harian' },
    { title: 'Instalasi pipa dan Stop keran distribusi', standard: 'Tidak bocor dan tersumbat, Stopkeran tidak rusak', method: 'Visual', frequency: 'Harian' },
    { title: 'Tandon', standard: 'Tidak bocor, bersih dari endapan, bebas lumut', method: 'Visual', frequency: 'Harian' },
    { title: 'Kualitas air', standard: 'Jernih dan tidak berbau', method: 'Visual', frequency: 'Harian' },
    { title: 'Keran', standard: 'Keran tidak bocor / rusak', method: 'Visual', frequency: 'Harian' },
];

const pompaItems = [
    { title: 'Konektor', standard: 'Tersambung Baik', method: 'Visual', frequency: 'per 6 bulan' },
    { title: 'Relay', standard: 'Berfungsi Baik', method: 'Tes Fungsi', frequency: 'Bulanan' },
    { title: 'Kontaktor', standard: 'Berfungsi Baik', method: 'Tes Fungsi', frequency: 'Bulanan' },
    { title: 'MCB', standard: 'Tidak melebihi beban', method: 'Alat Ukur', frequency: 'Bulanan' },
    { title: 'Lampu Indikator', standard: 'Menyala', method: 'Visual', frequency: 'Bulanan' },
    { title: 'Kebersihan Panel', standard: 'Tidak Berdebu, Tidak Basah/Lembab', method: 'Visual', frequency: 'Bulanan' },
    { title: 'Kebocoran Body Pompa', standard: 'Terdapat arus bocor', method: 'Alat Ukur', frequency: 'Bulanan' },
    { title: 'Kapasitor', standard: 'Sesuai Spesifikasi', method: 'Alat Ukur', frequency: 'Bulanan' },
];

const airMinumItems = [
    { title: 'Sumber air minum', standard: 'Debit air yang cukup', method: 'Alat Ukur ( ltr / dtk )', frequency: 'Harian' },
    { title: 'Sistem filterisasi', standard: 'lampu UV dalam masa aktif, filter brfungsi dengan baik', method: 'Visual', frequency: 'Bulanan' },
    { title: 'Tangki air minum', standard: 'Bebas lumut dan kotoran', method: 'Visual', frequency: 'Bulanan' },
    { title: 'Kualitas Air Minum', standard: 'Sesuai Standar Mutu air minum Permenkes No.2 Tahun 2023', method: 'Uji lab', frequency: '6 Bulanan' },
];

const gensetItems = [
    { title: 'Push button, push lamp,tombol emergency', standard: 'Berfungsi baik, tidak pecah', method: 'Manual', frequency: '2 Minggu sekali' },
    { title: 'Panel MCB', standard: 'Berfungsi baik, Listrik ON', method: 'Pengukuran', frequency: '2 Minggu sekali' },
    { title: 'Lampu indikator M/C', standard: 'Berfungsi baik,lampu indikator menyala', method: 'Visual', frequency: '2 Minggu sekali' },
    { title: 'Dinamo stater', standard: 'Berfungsi baik dan tidak macet', method: 'Tes fungsi', frequency: '2 Minggu sekali' },
    { title: 'Oli Pelumas', standard: 'Level diantara batas max & min,cek filter oli', method: 'Visual', frequency: '2 Minggu sekali' },
    { title: 'BBM solar', standard: 'Indikator volume bbm', method: 'Visual', frequency: '2 Minggu sekali' },
    { title: 'Sistem pembuangan', standard: 'Kepekatan asap yang dihasilkan', method: 'Visual', frequency: '2 Minggu sekali' },
    { title: 'Filter udara', standard: 'Ketebalan debu tersaring', method: 'Manual', frequency: 'Bulanan' },
    { title: 'Filter solar', standard: 'Berfungsi baik and tidak berlumpur', method: 'Manual', frequency: 'Bulanan' },
    { title: 'Radiator', standard: 'level cairan pendingin mesin cukup', method: 'Visual', frequency: 'Bulanan' },
    { title: 'Charger aki', standard: 'Voltase pengisian aki normal', method: 'Alat ukur', frequency: 'Bulanan' },
    { title: 'Accu/batre', standard: 'Voltase aki normal', method: 'Alat ukur', frequency: 'Bulanan' },
];

const fiscalYears = [
    '2024 - 2025',
    '2025 - 2026',
    '2026 - 2027',
    '2027 - 2028',
];

const handlePeriodicItemChange = () => {
    let selected = null;
    if (props.type === 'pemeliharaan-air-bersih') {
        selected = airBersihItems.find(i => i.title === form.title);
    } else if (props.type === 'pemeliharaan-pompa') {
        selected = pompaItems.find(i => i.title === form.title);
    } else if (props.type === 'pemeliharaan-air-minum') {
        selected = airMinumItems.find(i => i.title === form.title);
    } else if (props.type === 'pemeliharaan-genset') {
        selected = gensetItems.find(i => i.title === form.title);
    }
    
    if (selected) {
        form.check_standard = selected.standard;
        form.check_method = selected.method;
        form.check_frequency = selected.frequency;
    }
};

const showCreateModal = ref(false);
const isEdit = ref(false);
const editId = ref(null);

const form = useForm({
    title: '',
    description: '',
    subcategory: '',
    location: '',
    cost: 0,
    scheduled_at: '',
    status: 'pending',
    photo_evidence: null,
    institution_id: '',
    room_id: '',
    vehicle_id: '',
    // ISO Standard Fields
    code: '',
    brand: '',
    specification: '',
    material: '',
    purchased_at: '',
    source: '',
    price: 0,
    condition: 'B',
    before_condition: '',
    after_condition: '',
    action_taken: '',
    // Additional Buku Induk Fields
    no_urut_satker: '',
    no_urut_pondok: '',
    depreciation_price: 0,
    responsible_person: '',
    stock: 1,
    unit: 'Unit',
    received_at: '',
    serial_number: '',
    size: '',
    // Specialized Maintenance Fields (Gedung, AC, Kamar Mandi)
    check_standard: '',
    check_method: '',
    check_frequency: '',
    year: new Date().getFullYear(),
    jul: '', aug: '', sep: '', oct: '', nov: '', dec: '',
    jan: '', feb: '', mar: '', apr: '', may: '', jun: '',
    kran_air: '', lampu: '', fiting_lampu: '', saklar_lampu: '',
    ember: '', gayung: '', closet: '', pintu: '', grendel_pintu: '',
    ac_indoor_pc: '', ac_indoor_sw: '', ac_outdoor_freon: '', ac_outdoor_amp: '',
    ac_kelistrikan_jaringan: '', ac_kelistrikan_tegangan: '',
    ac_divisi: '', ac_tgl_bln: '',
    // Kipas Fields
    fan_type: '',
    // Septik Fields
    st_baik: '', st_penuh: '', st_bocor: '', st_bau: '',
    // Pump Fields
    ...['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'].reduce((acc, m) => {
        ['putra', 'putri', 'lawata'].forEach(a => acc[`${m}_${a}`] = '');
        return acc;
    }, {}),
    performed_by: '',
    completed_at: new Date().toISOString().split('T')[0]
});

const openCreateModal = () => {
    isEdit.value = false;
    editId.value = null;
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (item) => {
    isEdit.value = true;
    editId.value = item.id;
    form.reset();
    
    // Fill form with item data
    Object.keys(form.data()).forEach(key => {
        if (Object.prototype.hasOwnProperty.call(item, key)) {
            form[key] = item[key];
        }
    });

    // Special handles
    if (item.institution_id) form.institution_id = item.institution_id;
    if (item.room_id) form.room_id = item.room_id;
    if (item.vehicle_id) form.vehicle_id = item.vehicle_id;

    showCreateModal.value = true;
};

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.procedures.update', { type: props.type, id: editId.value }), {
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.procedures.store', props.type), {
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            }
        });
    }
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
                    <div v-if="type === 'pemeliharaan-septik'" class="px-4 py-2 bg-pail-gold/10 rounded-xl border border-pail-gold/20 flex items-center gap-2 mr-2">
                        <span class="text-[9px] font-black text-pail-gold uppercase tracking-widest">TGL:</span>
                        <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}</span>
                    </div>
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
                        <!-- Row 1 for Specialized & Double Headers -->
                        <tr v-if="['pendataan-aset', 'kir-ruangan', 'pemeliharaan-gedung', 'pemeliharaan-ac', 'pemeliharaan-kamar-mandi', 'pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset', 'pemeliharaan-kipas', 'pemeliharaan-septik', 'pemeliharaan-sarpras', 'agenda-perbaikan'].includes(type)" class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <template v-if="['pendataan-aset', 'kir-ruangan', 'pemeliharaan-gedung', 'pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                <th rowspan="2" v-if="!['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center">No.</th>
                            </template>
                            
                            <template v-if="['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No.</th>
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item Pengecekan</th>
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Standard</th>
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Metode / FRK</th>
                                <th :colspan="['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? 24 : 36" class="px-4 py-4 text-[10px] font-black text-pail-gold uppercase tracking-widest text-center border-l dark:border-gray-700 bg-gray-100/30 dark:bg-gray-800/30">
                                    <div v-if="type === 'pemeliharaan-genset'" class="flex flex-col gap-1">
                                        <span>FORMULIR PEMELIHARAAN GENSET</span>
                                        <div class="flex justify-center gap-4 text-[8px] font-black text-gray-400">
                                            <span>KGT: {{ data.data[0]?.subcategory || '...' }}</span>
                                            <span>MSN: {{ data.data[0]?.serial_number || '...' }}</span>
                                            <span>LOK: {{ data.data[0]?.location || '...' }}</span>
                                            <span>THN: {{ data.data[0]?.year || '...' }}</span>
                                        </div>
                                    </div>
                                    <span v-else>
                                        {{ type === 'pemeliharaan-pompa' ? 'CEKLIST PEMELIHARAAN POMPA & PANEL' : (type === 'pemeliharaan-air-bersih' ? 'FORMULIR PEMELIHARAAN AIR BERSIH' : 'FORMULIR PEMELIHARAAN AIR MINUM') }} (Lokasi: {{ data.data[0]?.location || '...' }}) - Tahun : {{ data.data[0]?.year || '...' }}
                                    </span>
                                </th>
                                <th rowspan="3" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Manajemen</th>
                            </template>
                            
                            <template v-if="type === 'pendataan-aset'">
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Satuan Kerja</th>
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap">Jumlah Aset</th>
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Satuan</th>
                            </template>

                            <template v-if="type === 'pemeliharaan-gedung'">
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item Pengecekan</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Standard</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Metode</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono">FRK</th>
                            </template>
                            
                            <!-- specialized for Pemeliharaan AC (Row 1 of 3) -->
                            <template v-else-if="type === 'pemeliharaan-ac'">
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No.</th>
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Ruangan</th>
                                <th colspan="6" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 bg-gray-100/30 dark:bg-gray-800/30">
                                    <div class="flex flex-col">
                                        <span>JENIS PEMELIHARAAN AC</span>
                                        <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Divisi: {{ data.data[0]?.ac_divisi || '...' }} | Bln: {{ data.data[0]?.ac_tgl_bln || '...' }}</span>
                                    </div>
                                </th>
                                <th rowspan="3" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Petugas</th>
                            </template>

                            <!-- specialized for Pemeliharaan Kamar Mandi (Row 1 Meta) -->
                            <template v-else-if="type === 'pemeliharaan-kamar-mandi'">
                                <th colspan="15" class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap bg-gray-100/30 dark:bg-gray-800/30">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex gap-12">
                                            <span>AREA : {{ data.data[0]?.subcategory || '........................' }}</span>
                                            <span>TANGGAL KEGIATAN : {{ data.data[0]?.completed_at ? new Date(data.data[0]?.completed_at).toLocaleDateString() : '........................' }}</span>
                                        </div>
                                    </div>
                                </th>
                            </template>
                            
                            <!-- specialized for Pemeliharaan Septik Tank (Row 1) -->
                            <template v-else-if="type === 'pemeliharaan-septik'">
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No.</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Pemeriksaan</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi</th>
                                <th colspan="4" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Kondisi Septik Tank</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tindakan Pemeliharaan</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Hasil Tindakan</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono">Petugas</th>
                            </template>
                            
                            <!-- specialized for Pemeliharaan Sarpras (Row 1) -->
                            <template v-else-if="type === 'pemeliharaan-sarpras'">
                                <th colspan="9" class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center bg-gray-100/30 dark:bg-gray-800/30">
                                    <div class="flex items-center justify-center gap-4">
                                        <span>FORMULIR LAPORAN PEMELIHARAAN SARANA PRASARANA</span>
                                        <div class="px-4 py-1.5 bg-pail-gold/10 border border-pail-gold/20 rounded-lg text-pail-gold">
                                            Pemeliharaan : {{ data.data[0]?.subcategory || '....................' }}
                                        </div>
                                    </div>
                                </th>
                            </template>
                            
                            <!-- specialized for Agenda Perbaikan Sarpras (Row 1) -->
                            <template v-else-if="type === 'agenda-perbaikan'">
                                <th colspan="5" class="px-6 py-8 text-[10px] font-black uppercase tracking-widest text-center bg-gray-100/30 dark:bg-gray-800/30 border-b dark:border-gray-700">
                                    <div class="flex flex-col gap-4">
                                        <span class="text-lg md:text-xl text-pail-gold">JADWAL AGENDA PERBAIKAN SARPRAS</span>
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-black uppercase tracking-widest">Lokasi :</span>
                                                <span class="text-xs font-black text-gray-900 dark:text-white uppercase">{{ data.data[0]?.room?.name || data.data[0]?.location || '....................' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 border-t border-gray-100 dark:border-gray-700 pt-2 min-w-[200px] justify-center">
                                                <span class="text-[9px] font-black uppercase tracking-widest">Tgl :</span>
                                                <span class="text-xs font-black text-gray-900 dark:text-white uppercase">{{ data.data[0]?.scheduled_at || data.data[0]?.completed_at || '....................' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </template>
                            
                            <!-- Middle Section Metas -->
                            <th v-if="['pendataan-aset', 'kir-ruangan'].includes(type)" colspan="3" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap">Keadaan Barang</th>
                            
                            <!-- specialized for Pemeliharaan Gedung Meta info -->
                            <th v-if="type === 'pemeliharaan-gedung'" colspan="12" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap bg-gray-100/30 dark:bg-gray-800/30">
                                <div class="flex flex-col items-center">
                                    <div class="flex gap-8 mb-1">
                                        <span>Gedung : {{ data.data[0]?.subcategory || '....................' }}</span>
                                        <span>Lokasi : {{ data.data[0]?.location || '....................' }}</span>
                                    </div>
                                    <span class="text-pail-gold">Tahun : {{ data.data[0]?.year || '....................' }}</span>
                                </div>
                            </th>

                            <template v-if="type === 'pendataan-aset'">
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tanggal Pengecekan</th>
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Petugas Pemeriksa</th>
                                <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Nilai Aset</th>
                            </template>

                            <th v-if="!['pemeliharaan-gedung', 'pemeliharaan-ac', 'pemeliharaan-kamar-mandi', 'pemeliharaan-septik', 'pemeliharaan-sarpras'].includes(type)" rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Keterangan</th>
                            
                            <!-- Manajemen Alignment -->
                            <th v-if="type === 'pemeliharaan-ac'" rowspan="3" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Manajemen</th>
                            <th v-else-if="type === 'pemeliharaan-kamar-mandi'" rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Manajemen</th>
                            <th v-else-if="!['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Manajemen</th>
                        </tr>
                        <!-- Row 2 for Specialized Double Headers / Bathroom -->
                        <tr v-if="['pendataan-aset', 'kir-ruangan', 'pemeliharaan-gedung', 'pemeliharaan-ac', 'pemeliharaan-kamar-mandi', 'pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset', 'pemeliharaan-kipas', 'pemeliharaan-septik', 'pemeliharaan-sarpras', 'agenda-perbaikan'].includes(type)" class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                             <template v-if="type === 'pemeliharaan-septik'">
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700">Baik</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700">Penuh</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700">Bocor</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700">Bau</th>
                            </template>
                            <template v-if="['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                <th v-for="m in ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']" :key="m" :colspan="['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? 2 : 3" class="px-2 py-2 text-[8px] font-black text-gray-500 uppercase text-center border-l dark:border-gray-700">{{ m }}</th>
                            </template>
                            <template v-if="type === 'pemeliharaan-ac'">
                                <th colspan="2" class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Pencucian (Indoor)</th>
                                <th colspan="2" class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Outdoor</th>
                                <th colspan="2" class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Kelistrikan</th>
                            </template>
                            <template v-if="type === 'pendataan-aset' || type === 'kir-ruangan'">
                                <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Baik</th>
                                <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Kurang Baik</th>
                                <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Rusak</th>
                            </template>
                            <template v-if="type === 'pemeliharaan-gedung'">
                                <th v-for="month in ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']" :key="month" class="px-2 py-2 text-[8px] font-black text-gray-500 uppercase tracking-tighter text-center border-l dark:border-gray-700 min-w-[40px]">{{ month }}</th>
                            </template>
                            <template v-if="type === 'pemeliharaan-kamar-mandi'">
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No.</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tgl/Bln/Thn</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi / No KM</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Kran</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Lampu</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Fiting</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Saklar</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Ember</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Gayung</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Closet</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Pintu</th>
                                <th class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center">Grendel</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Petugas</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tindakan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ket</th>
                            </template>

                            <template v-else-if="type === 'agenda-perbaikan'">
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">No.</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Uraian Pekerjaan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Status</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">Petugas</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 border-r">Keterangan</th>
                            </template>

                            <template v-else-if="type === 'pemeliharaan-sarpras'">
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700">No.</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Sarana/Prasarana</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Jenis Pekerjaan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Lokasi/Ruangan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kondisi Sebelum</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tindakan Pemeliharaan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono">Kondisi Sesudah</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Petugas</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-r dark:border-gray-700">Keterangan</th>
                            </template>

                            <template v-else-if="type === 'pemeliharaan-kipas'">
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No.</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Pemeriksaan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi / Ruangan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipe Kipas</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Pemeliharaan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kondisi</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tindakan / Perbaikan</th>
                                <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Petugas</th>
                            </template>
                        </tr>
                        <!-- Row 3 for Specialized Triple Header -->
                        <tr v-if="['pemeliharaan-ac', 'pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <template v-if="type === 'pemeliharaan-ac'">
                                <th v-for="sub in ['PC', 'SW', 'Freon', 'Ampere', 'Jaringan', 'Tegangan']" :key="sub" class="px-2 py-2 text-[8px] font-black text-gray-400 uppercase text-center border-l dark:border-gray-700">{{ sub }}</th>
                            </template>
                            <template v-else-if="['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                <template v-for="i in 12" :key="i">
                                    <th v-for="area in (type === 'pemeliharaan-genset' ? ['Pkn1', 'Pkn2'] : (type === 'pemeliharaan-air-minum' ? ['Pa', 'Pi'] : (type === 'pemeliharaan-pompa' ? ['Pa', 'Pi', 'Lwt'] : ['Lwt', 'Pa', 'Pi'])))" :key="area" class="px-1 py-2 text-[7px] font-black text-gray-400 uppercase text-center border-l dark:border-gray-700">{{ area }}</th>
                                </template>
                            </template>
                        </tr>

                        <!-- Standard Row for Other Types -->
                        <tr v-if="!['pendataan-aset', 'kir-ruangan', 'pemeliharaan-gedung', 'pemeliharaan-kamar-mandi', 'pemeliharaan-ac', 'pemeliharaan-kipas', 'pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset', 'pemeliharaan-septik', 'pemeliharaan-sarpras'].includes(type)" class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <!-- Dynamic Headers based on Type -->
                            <template v-if="type.includes('kendaraan')">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Armada / Plat</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Detail & Merk</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </template>
                            <template v-else-if="type === 'buku-induk'">
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">No.</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Satuan Kerja</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Ruangan</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Jenis Barang</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Merk</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Merk (Spec)</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tgl Buku/Beli</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">No Urut Satker</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Kode Barang</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">No Urut Pondok</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap">Kuantitas</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Satuan</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Sumber</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tgl Serah</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap">Kondisi</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Harga @</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap">Harga Depr</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">P.JWB</th>
                                <th class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Ket</th>
                            </template>
                            <template v-else-if="procedure?.model?.includes('Item')">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode / Nama Aset</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi / Lembaga</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Merk / Stok</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kondisi</th>
                            </template>
                            <template v-else>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aktivitas / Detail</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi/Lembaga</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kondisi (Sblm/Ssdh)</th>
                            </template>
                            
                            <th v-if="type !== 'buku-induk' && type !== 'pendataan-aset' && type !== 'kir-ruangan'" class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nilai / Sumber</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <tr v-for="(item, index) in data.data" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors group">
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
                                <!-- Specialized Content for BUKU INDUK -->
                                <template v-if="type === 'buku-induk'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.institution?.name || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.room?.name || 'Gudang' }}</td>
                                    <td class="px-4 py-6 text-sm font-black text-gray-900 dark:text-white uppercase whitespace-nowrap">{{ item.name }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-800 dark:text-white uppercase">{{ item.brand || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.specification || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ item.purchased_at || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.no_urut_satker || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-bold text-gray-400 uppercase whitespace-nowrap">{{ item.code || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.no_urut_pondok || '-' }}</td>
                                    <td class="px-4 py-6 text-center text-[10px] font-black text-gray-700 dark:text-gray-200">{{ item.stock || 0 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400">{{ item.unit || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.source || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ item.received_at || '-' }}</td>
                                    <td class="px-4 py-6 text-center">
                                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                            :class="item.condition === 'B' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-6 text-right text-xs font-mono font-black text-gray-900 dark:text-white">Rp {{ Number(item.price || 0).toLocaleString() }}</td>
                                    <td class="px-4 py-6 text-right text-xs font-mono font-black text-pail-gold">Rp {{ Number(item.depreciation_price || 0).toLocaleString() }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.responsible_person || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] text-gray-400 italic truncate max-w-[100px]">{{ item.note || '-' }}</td>
                                </template>

                                <!-- Specialized Content for PEMELIHARAAN KAMAR MANDI -->
                                <template v-else-if="type === 'pemeliharaan-kamar-mandi'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.completed_at || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.location || '-' }}</td>
                                    
                                    <!-- 9 Bathroom Component Columns -->
                                    <td v-for="col in ['kran_air', 'lampu', 'fiting_lampu', 'saklar_lampu', 'ember', 'gayung', 'closet', 'pintu', 'grendel_pintu']" :key="col" class="px-1 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">
                                        {{ item[col] || '-' }}
                                    </td>
                                    
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.performer?.name || 'Staff URT' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.action_taken || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] text-gray-400 italic truncate max-w-[100px]">{{ item.description || '-' }}</td>
                                </template>

                                <!-- Specialized Content for PEMELIHARAAN AC -->
                                <template v-else-if="type === 'pemeliharaan-ac'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.room?.name || item.location || '-' }}</td>
                                    
                                    <!-- 6 AC Check Columns -->
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_indoor_pc || '-' }}</td>
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_indoor_sw || '-' }}</td>
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_outdoor_freon || '-' }}</td>
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_outdoor_amp || '-' }}</td>
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_kelistrikan_jaringan || '-' }}</td>
                                    <td class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono">{{ item.ac_kelistrikan_tegangan || '-' }}</td>
                                    
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.performer?.name || 'Staff URT' }}</td>
                                </template>

                                <!-- Specialized Content for PEMELIHARAAN KIPAS -->
                                <template v-else-if="type === 'pemeliharaan-kipas'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.completed_at || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.room?.name || item.location || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.fan_type || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.subcategory || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center">
                                        <div class="inline-block px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest" :class="item.before_condition === 'B' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                            {{ item.before_condition === 'B' ? 'OK' : 'NG' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.action_taken || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.performer?.name || 'Staff URT' }}</td>
                                </template>

                                 <!-- Specialized Content for FORMULIR PEMELIHARAAN SEPTIK TANK -->
                                 <template v-else-if="type === 'pemeliharaan-septik'">
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center whitespace-nowrap">{{ item.completed_at || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.room?.name || item.location || '-' }}</td>
                                     
                                     <td v-for="col in ['st_baik', 'st_penuh', 'st_bocor', 'st_bau']" :key="col" class="px-1 py-6 text-center border-l dark:border-gray-700">
                                         <span v-if="item[col] === 'V'" class="text-pail-gold font-black text-[12px]">V</span>
                                         <span v-else class="text-gray-200 dark:text-gray-700">-</span>
                                     </td>

                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.action_taken || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.after_condition || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center whitespace-nowrap">{{ item.performer?.name || 'Staff URT' }}</td>
                                 </template>

                                 <!-- Specialized Content for JADWAL AGENDA PERBAIKAN SARPRAS -->
                                 <template v-else-if="type === 'agenda-perbaikan'">
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700">{{ index + 1 }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.title || '-' }}</td>
                                     <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                         <span class="inline-block px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest" 
                                               :class="{
                                                   'bg-yellow-100 text-yellow-600': item.status === 'pending',
                                                   'bg-blue-100 text-blue-600': (item.status === 'proses' || item.status === 'processing'),
                                                   'bg-green-100 text-green-600': (item.status === 'selesai' || item.status === 'completed')
                                               }">
                                             {{ item.status || 'PENDING' }}
                                         </span>
                                     </td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-l dark:border-gray-700">{{ item.performer?.name || 'Staff URT' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-l border-r dark:border-gray-700">{{ item.description || '-' }}</td>
                                 </template>

                                 <!-- Specialized Content for FORMULIR LAPORAN PEMELIHARAAN SARANA PRASARANA -->
                                 <template v-else-if="type === 'pemeliharaan-sarpras'">
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700">{{ index + 1 }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.title || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center">{{ item.check_standard || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center">{{ item.room?.name || item.location || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center font-mono">{{ item.before_condition || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center">{{ item.action_taken || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center font-mono">{{ item.after_condition || '-' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center whitespace-nowrap">{{ item.performer?.name || 'Staff URT' }}</td>
                                     <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-r dark:border-gray-700">{{ item.description || '-' }}</td>
                                 </template>

                                <!-- Specialized Content for PEMELIHARAAN GEDUNG -->
                                <template v-else-if="type === 'pemeliharaan-gedung'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase leading-tight">{{ item.title || '-' }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase leading-tight">{{ item.description }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.check_standard || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.check_method || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center font-mono">{{ item.check_frequency || '-' }}</td>
                                    
                                    <!-- 12 Months Status Indicators -->
                                    <td v-for="month in ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun']" :key="month" class="px-1 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item[month] === 'V'" class="text-green-500 font-black text-[10px]">V</span>
                                        <span v-else-if="item[month] === 'X'" class="text-red-500 font-black text-[10px]">X</span>
                                        <span v-else class="text-gray-200 dark:text-gray-700">-</span>
                                    </td>
                                </template>

                                <!-- Specialized Content for PEMELIHARAAN POMPA, AIR BERSIH, AIR MINUM & GENSET -->
                                <template v-else-if="['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.title || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.check_standard || '-' }}</td>
                                    <td class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.check_method || '-' }} / {{ item.check_frequency || '-' }}</td>
                                    
                                    <template v-for="m in ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun']" :key="m">
                                        <td v-for="a in (['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? ['putra', 'putri'] : (type === 'pemeliharaan-pompa' ? ['putra', 'putri', 'lawata'] : ['lawata', 'putra', 'putri']))" :key="a" class="px-1 py-6 text-center border-l dark:border-gray-700">
                                            <span v-if="item[`${m}_${a}`] === 'V'" class="text-green-500 font-black text-[8px]">V</span>
                                            <span v-else-if="item[`${m}_${a}`] === 'X'" class="text-red-500 font-black text-[8px]">X</span>
                                            <span v-else class="text-gray-200 dark:text-gray-700">-</span>
                                        </td>
                                    </template>
                                </template>

                                <!-- Specialized Content for KIR RUANGAN -->
                                <template v-else-if="type === 'kir-ruangan'">
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.name || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.brand || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.serial_number || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.size || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.material || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center">{{ item.purchased_at || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.code || '-' }}</td>
                                    <td class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center">{{ item.stock || 0 }} {{ item.unit }}</td>
                                    
                                    <!-- 3 Columns for Condition -->
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'B'" class="text-green-500 font-black">V</span>
                                    </td>
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'KB'" class="text-yellow-500 font-black">V</span>
                                    </td>
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'RB'" class="text-red-500 font-black">V</span>
                                    </td>
                                    
                                    <td class="px-4 py-6 text-[9px] text-gray-400 italic truncate max-w-[100px]">{{ item.note || '-' }}</td>
                                </template>

                                <!-- Specialized Content for PENDATAAN ASET -->
                                <template v-else-if="type === 'pendataan-aset'">
                                    <td class="px-6 py-6 text-[10px] font-black text-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="px-6 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ item.institution?.name || '-' }}</td>
                                    <td class="px-6 py-6 text-center text-[10px] font-black text-gray-900 dark:text-white">{{ item.stock || 0 }}</td>
                                    <td class="px-6 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.unit || '-' }}</td>
                                    
                                    <!-- 3 Columns for Condition -->
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'B'" class="text-green-500 font-black">V</span>
                                    </td>
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'KB'" class="text-yellow-500 font-black">V</span>
                                    </td>
                                    <td class="px-4 py-6 text-center border-l dark:border-gray-700">
                                        <span v-if="item.condition === 'RB'" class="text-red-500 font-black">V</span>
                                    </td>

                                    <td class="px-6 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '-' }}</td>
                                    <td class="px-6 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ item.responsible_person || '-' }}</td>
                                    <td class="px-6 py-6 text-right text-xs font-mono font-black text-gray-900 dark:text-white">Rp {{ Number(item.price || 0).toLocaleString() }}</td>
                                    <td class="px-6 py-6 text-[9px] text-gray-400 italic truncate max-w-[150px]">{{ item.note || '-' }}</td>
                                </template>

                                <!-- Generic Item Display -->
                                <template v-else-if="procedure?.model?.includes('Item')">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 dark:text-white uppercase truncate max-w-[200px]">{{ item.name }}</span>
                                            <span class="text-[9px] text-gray-400 font-bold mt-1 uppercase tracking-widest">{{ item.code || 'NO CODE' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase truncate max-w-[150px]">{{ item.location || item.room?.name || 'Gudang' }}</span>
                                            <span class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1 truncate max-w-[150px]">{{ item.institution?.name || 'Pusat' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-800 dark:text-white uppercase">{{ item.brand || '-' }}</span>
                                            <span class="text-[9px] text-gray-400 font-bold mt-1">{{ item.stock || 0 }} {{ item.unit || 'Unit' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                            :class="item.condition === 'B' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                </template>

                                <!-- Maintenance / Other Display -->
                                <template v-else>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 dark:text-white uppercase truncate max-w-xs">{{ item.title || item.name }}</span>
                                            <span class="text-[10px] text-gray-400 mt-1 truncate max-w-xs">{{ item.action_taken || item.description || 'Sesuai Prosedur URT' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase">{{ item.location || 'Area Terbuka' }}</span>
                                            <span class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1">{{ item.institution?.name || 'Pusat' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-[8px] font-black text-gray-400 uppercase">SBLM: {{ item.before_condition || '-' }}</span>
                                            <span class="text-[8px] font-black text-green-600 uppercase">SSDH: {{ item.after_condition || '-' }}</span>
                                        </div>
                                    </td>
                                </template>
                            </template>
                            
                            <td v-if="type !== 'buku-induk' && type !== 'pendataan-aset' && type !== 'kir-ruangan' && type !== 'pemeliharaan-gedung' && type !== 'pemeliharaan-kipas' && type !== 'agenda-perbaikan'" class="px-8 py-6 text-right">
                                <span class="text-xs font-mono font-black text-gray-900 dark:text-white">
                                    Rp {{ Number(item.price || item.cost || item.amount || 0).toLocaleString() }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEditModal(item)" class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 hover:text-pail-gold">
                                        <PencilIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteItem(item.id)" class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-300 hover:text-red-500">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="data.data.length === 0">
                            <td :colspan="type === 'buku-induk' ? 20 : (type === 'kir-ruangan' ? 14 : (type === 'pendataan-aset' ? 12 : (type === 'pemeliharaan-gedung' ? 18 : (type === 'pemeliharaan-ac' ? 10 : (type === 'pemeliharaan-kamar-mandi' ? 16 : (['pemeliharaan-pompa', 'pemeliharaan-air-bersih'].includes(type) ? 42 : (['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? 30 : (type === 'pemeliharaan-kipas' ? 8 : (type === 'pemeliharaan-septik' ? 10 : 7)))))))))" class="px-8 py-24 text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada data di sistem.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="type === 'agenda-perbaikan' && data.data.length > 0" class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-start gap-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                            <span class="shrink-0 text-pail-gold">KET :</span>
                            <div class="flex flex-col gap-1">
                                <span>- PERMINTAAN DARURAT TETAP DIDAHULUKAN JIKA ADA</span>
                                <span>- PERUBAHAN PERSONIL MENYESUAIKAN KONDISI DILAPANGAN</span>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Lokasi / Lembaga</p>
                            <p class="text-[10px] font-bold text-gray-600 dark:text-gray-300 uppercase leading-tight">
                                {{ item.room?.name || item.location || 'Gudang' }}
                            </p>
                            <p class="text-[8px] font-bold text-pail-gold uppercase mt-1" v-if="type === 'pemeliharaan-gedung'">
                                Gedung: {{ item.subcategory || '-' }}
                            </p>
                            <p class="text-[8px] font-bold text-pail-gold uppercase mt-1" v-else>
                                {{ item.institution?.name || 'Pusat' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Nilai / Biaya</p>
                            <p class="text-[10px] font-mono font-black text-gray-900 dark:text-white leading-tight">
                                Rp {{ Number(item.price || item.cost || 0).toLocaleString() }}
                            </p>
                            <p class="text-[8px] font-bold text-gray-400 uppercase mt-1" v-if="type === 'buku-induk'">
                                Dep: Rp {{ Number(item.depreciation_price || 0).toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                        <button @click="openEditModal(item)" class="flex-1 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-pail-gold">
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
                                <h3 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight">{{ isEdit ? 'Update Prosedur' : 'Entri Prosedur Baru' }}</h3>
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
                                    <!-- ISO FIELDS FOR ITEMS (Buku Induk & Laporan Aset) -->
                                    <template v-if="procedure?.model?.includes('Item')">
                                        <!-- SATKER always at top for ISO Forms -->
                                        <div class="sm:col-span-2">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Satuan Kerja (Lembaga) <span class="text-red-500">*Wajib</span></label>
                                            <select v-model="form.institution_id" required class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                <option value="">Pilih Lembaga</option>
                                                <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
                                            </select>
                                        </div>

                                        <div :class="type === 'pendataan-aset' || type === 'kir-ruangan' ? 'sm:col-span-2' : ''">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">{{ type === 'kir-ruangan' ? 'Jenis Barang / Nama Barang' : 'Jenis Barang' }} <span class="text-red-500">*Wajib</span></label>
                                            <input v-model="form.name" type="text" required class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Sesuai ISO Standard">
                                        </div>

                                        <div v-if="type !== 'pendataan-aset' && type !== 'kir-ruangan'">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Ruangan / Lokasi</label>
                                            <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                <option value="">Pilih Ruangan</option>
                                                <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                            </select>
                                        </div>

                                        <template v-if="type === 'pendataan-aset' || type === 'kir-ruangan'">
                                            <div v-if="type === 'kir-ruangan'" class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Merk/Kode</label>
                                                    <input v-model="form.brand" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">No. Seri Pabrik</label>
                                                    <input v-model="form.serial_number" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Ukuran</label>
                                                    <input v-model="form.size" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Bahan</label>
                                                    <input v-model="form.material" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">{{ type === 'kir-ruangan' ? 'Jumlah Barang' : 'Jumlah Aset' }}</label>
                                                    <input v-model="form.stock" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nama Satuan</label>
                                                    <input v-model="form.unit" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Unit/Pcs/dll">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:col-span-2">
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">{{ type === 'kir-ruangan' ? 'Tahun Perolehan' : 'Tanggal Pengecekan' }}</label>
                                                    <input v-model="form.purchased_at" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="DD-MM-YYYY">
                                                </div>
                                                <div v-if="type === 'kir-ruangan'">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">No. Kode Barang</label>
                                                    <input v-model="form.code" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div v-else>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pemeriksa</label>
                                                    <input v-model="form.responsible_person" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div v-if="type === 'pendataan-aset'">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nilai Aset</label>
                                                    <input v-model="form.price" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Merk (Brand)</label>
                                                <input v-model="form.brand" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Merk (Specification)</label>
                                                <input v-model="form.specification" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tgl Pembukuan / Pembelian</label>
                                                <input v-model="form.purchased_at" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="DD-MM-YYYY">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tgl Penyerahan</label>
                                                <input v-model="form.received_at" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="DD-MM-YYYY">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">No Urut Satker / Pondok</label>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input v-model="form.no_urut_satker" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="No. Satker">
                                                    <input v-model="form.no_urut_pondok" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="No. Pondok">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kode Barang</label>
                                                <input v-model="form.code" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kuantitas & Satuan</label>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input v-model="form.stock" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <input v-model="form.unit" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Unit">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Sumber Perolehan</label>
                                                <input v-model="form.source" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>

                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tahun Perolehan/Pengecekan</label>
                                                <input v-model="form.purchased_at" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="DD-MM-YYYY">
                                            </div>

                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Penanggung Jawab</label>
                                                <input v-model="form.responsible_person" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Harga Perolehan</label>
                                                <input v-model="form.price" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>

                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Harga Setelah Penyusutan</label>
                                                <input v-model="form.depreciation_price" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                        </template>

                                        <div class="sm:col-span-2">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Saat Ini</label>
                                            <select v-model="form.condition" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                <option value="B">BAIK (B)</option>
                                                <option value="KB">KURANG BAIK (KB)</option>
                                                <option value="RB">RUSAK BERAT (RB)</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Keterangan</label>
                                            <textarea v-model="form.note" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                        </div>
                                    </template>

                                    <!-- ISO FIELDS FOR MAINTENANCE -->
                                    <template v-else>
                                        <!-- Specialized fields for Pemeliharaan Kamar Mandi -->
                                        <template v-if="type === 'pemeliharaan-kamar-mandi'">
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">AREA (Grup Lokasi)</label>
                                                <input v-model="form.subcategory" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: AREA MASJID / AREA ASRAMA">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi / No Kamar Mandi</label>
                                                <input v-model="form.location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div class="sm:col-span-2 grid grid-cols-3 gap-3 border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
                                                <div v-for="comp in [['Kran Air','kran_air'], ['Lampu','lampu'], ['Fiting Lampu','fiting_lampu'], ['Saklar Lampu','saklar_lampu'], ['Ember','ember'], ['Gayung','gayung'], ['Closet','closet'], ['Pintu','pintu'], ['Grendel Pintu','grendel_pintu']]" :key="comp[1]">
                                                    <label class="text-[8px] font-black text-gray-400 uppercase mb-1 block">{{ comp[0] }}</label>
                                                    <input v-model="form[comp[1]]" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 mt-4 grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pelaksana</label>
                                                    <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                        <option value="">Pilih Petugas</option>
                                                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tanggal Pengecekan</label>
                                                    <input v-model="form.completed_at" type="date" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 mt-4">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tindakan / Perbaikan</label>
                                                <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan AC -->
                                        <template v-else-if="type === 'pemeliharaan-ac'">
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Divisi (Lembaga)</label>
                                                <input v-model="form.ac_divisi" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pelaksana</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Ruangan / Lokasi AC</label>
                                                <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Ruangan</option>
                                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tgl / Bln Pengecekan</label>
                                                <input v-model="form.ac_tgl_bln" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: 05 Feb">
                                            </div>

                                            <div class="sm:col-span-2 grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Indoor: PC</label>
                                                    <input v-model="form.ac_indoor_pc" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Indoor: SW</label>
                                                    <input v-model="form.ac_indoor_sw" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Outdoor: Freon</label>
                                                    <input v-model="form.ac_outdoor_freon" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Outdoor: Ampere</label>
                                                    <input v-model="form.ac_outdoor_amp" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kelistrikan: Jaringan</label>
                                                    <input v-model="form.ac_kelistrikan_jaringan" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kelistrikan: Tegangan</label>
                                                    <input v-model="form.ac_kelistrikan_tegangan" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for FORMULIR PEMELIHARAAN SEPTIK TANK -->
                                        <template v-else-if="type === 'pemeliharaan-septik'">
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tanggal Pemeriksaan</label>
                                                <input v-model="form.completed_at" type="date" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi / Ruangan</label>
                                                <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Ruangan</option>
                                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="sm:col-span-2 py-4">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 block text-center">Kondisi Septik Tank</label>
                                                <div class="grid grid-cols-4 gap-4">
                                                    <div v-for="(label, key) in { st_baik: 'Baik', st_penuh: 'Penuh', st_bocor: 'Bocor', st_bau: 'Bau' }" :key="key" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-pail-gold/30 transition-all">
                                                        <label class="text-[10px] font-black text-gray-400 uppercase mb-3">{{ label }}</label>
                                                        <select v-model="form[key]" class="w-full border-0 bg-transparent text-center text-sm font-black focus:ring-0 appearance-none cursor-pointer">
                                                            <option value="">-</option>
                                                            <option value="V">V</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-2 space-y-4">
                                                <div class="sm:col-span-2">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block font-black">Tindakan Pemeliharaan</label>
                                                    <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2" placeholder="Detail tindakan yang dilakukan..."></textarea>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block font-black">Hasil Tindakan</label>
                                                    <textarea v-model="form.after_condition" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2" placeholder="Kondisi akhir setelah tindakan..."></textarea>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pemeriksa</label>
                                                    <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                        <option value="">Pilih Petugas</option>
                                                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Kipas Angin -->
                                        <template v-else-if="type === 'pemeliharaan-kipas'">
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tanggal Pemeriksaan</label>
                                                <input v-model="form.completed_at" type="date" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi / Ruangan</label>
                                                <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Ruangan</option>
                                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tipe Kipas</label>
                                                <select v-model="form.fan_type" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Tipe</option>
                                                    <option value="Dinding">Dinding</option>
                                                    <option value="Langit-langit">Langit-langit</option>
                                                    <option value="Berdiri">Berdiri</option>
                                                    <option value="Exhaust">Exhaust</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Jenis Pemeliharaan</label>
                                                <select v-model="form.subcategory" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Jenis</option>
                                                    <option value="Rutin">Rutin</option>
                                                    <option value="Berkala">Berkala</option>
                                                    <option value="Perbaikan">Perbaikan</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Saat Diperiksa</label>
                                                <select v-model="form.before_condition" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="B">B (Baik/OK)</option>
                                                    <option value="RB">R (Rusak/NG)</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tindakan / Perbaikan</label>
                                                <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pemeriksa</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Gedung (Checklist Style) -->
                                        <template v-else-if="type === 'pemeliharaan-gedung'">
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Gedung</label>
                                                <input v-model="form.subcategory" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Gedung A">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi Spesifik</label>
                                                <input v-model="form.location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Lantai 1 / Koridor">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Item Pengecekan</label>
                                                <input v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Plafon / Atap">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Standard Pengecekan</label>
                                                <input v-model="form.check_standard" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Metode Pengecekan</label>
                                                <input v-model="form.check_method" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Frekuensi</label>
                                                <input v-model="form.check_frequency" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Harian/Bulanan/dll">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tahun Monitoring</label>
                                                <input v-model="form.year" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="2024/2025">
                                            </div>

                                            <!-- Monthly Log Entry -->
                                            <div class="sm:col-span-2 py-4">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 block text-center">Rekap Hasil Pengecekan (Jul - Jun)</label>
                                                <div class="grid grid-cols-6 gap-3">
                                                    <div v-for="month in ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun']" :key="month" class="flex flex-col items-center">
                                                        <label class="text-[8px] font-black text-gray-400 uppercase mb-1">{{ month }}</label>
                                                        <select v-model="form[month]" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-2 py-2 text-[10px] focus:ring-2 focus:ring-pail-gold">
                                                            <option value="">-</option>
                                                            <option value="V">V (OK)</option>
                                                            <option value="X">X (No)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 mt-4">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Penanggung Jawab (URT)</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Pompa, Air Bersih, Air Minum & Genset -->
                                        <template v-if="['pemeliharaan-pompa', 'pemeliharaan-air-bersih', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                            <div class="sm:col-span-2 grid grid-cols-2 gap-4">
                                                <div v-if="type === 'pemeliharaan-genset'">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kegiatan</label>
                                                    <input v-model="form.subcategory" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div v-if="type === 'pemeliharaan-genset'">
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">No Mesin</label>
                                                    <input v-model="form.serial_number" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi Unit {{ type === 'pemeliharaan-pompa' ? 'Pompa' : (type === 'pemeliharaan-air-bersih' ? 'Air Bersih' : (type === 'pemeliharaan-air-minum' ? 'Air Minum' : 'Genset')) }}</label>
                                                    <input v-model="form.location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tahun Monitoring</label>
                                                    <select v-model="form.year" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                        <option value="">Pilih Tahun</option>
                                                        <option v-for="y in fiscalYears" :key="y" :value="y">{{ y }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Item Pengecekan</label>
                                                <template v-if="['pemeliharaan-air-bersih', 'pemeliharaan-pompa', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)">
                                                    <select v-model="form.title" @change="handlePeriodicItemChange" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                        <option value="">Pilih Item Pengecekan</option>
                                                        <option v-for="item in (type === 'pemeliharaan-air-bersih' ? airBersihItems : (type === 'pemeliharaan-pompa' ? pompaItems : (type === 'pemeliharaan-air-minum' ? airMinumItems : gensetItems)))" :key="item.title" :value="item.title">{{ item.title }}</option>
                                                    </select>
                                                </template>
                                                <input v-else v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Standard</label>
                                                <input v-model="form.check_standard" type="text" :disabled="['pemeliharaan-air-bersih', 'pemeliharaan-pompa', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Metode</label>
                                                <input v-model="form.check_method" type="text" :disabled="['pemeliharaan-air-bersih', 'pemeliharaan-pompa', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Frekuensi (FRK)</label>
                                                <input v-model="form.check_frequency" type="text" :disabled="['pemeliharaan-air-bersih', 'pemeliharaan-pompa', 'pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Laporan Jika Ada Kerusakan (Catatan)</label>
                                                <textarea v-model="form.description" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tindakan Pemeliharaan / Perbaikan</label>
                                                <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas URT</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>

                                            <!-- Matrix Input for 36 columns -->
                                            <div class="sm:col-span-2 mt-6 overflow-x-auto">
                                                <table class="w-full border-collapse">
                                                    <thead>
                                                        <tr>
                                                            <th class="p-2 border bg-gray-100 text-[8px] font-black">BULAN</th>
                                                            <th class="p-2 border bg-gray-100 text-[8px] font-black text-blue-600">{{ type === 'pemeliharaan-genset' ? 'PEKAN 1 (Pkn1)' : 'PUTRA' }}</th>
                                                            <th class="p-2 border bg-gray-100 text-[8px] font-black text-pink-600">{{ type === 'pemeliharaan-genset' ? 'PEKAN 2 (Pkn2)' : 'PUTRI' }}</th>
                                                            <th v-if="!['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type)" class="p-2 border bg-gray-100 text-[8px] font-black text-green-600">LAWATA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="m in ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun']" :key="m">
                                                            <td class="p-2 border text-[10px] font-black uppercase bg-gray-50">{{ m }}</td>
                                                            <td v-for="a in (['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? ['putra', 'putri'] : ['putra', 'putri', 'lawata'])" :key="a" class="p-1 border">
                                                                <select v-model="form[`${m}_${a}`]" class="w-full border-0 bg-transparent text-[10px] focus:ring-0">
                                                                    <option value="">-</option>
                                                                    <option value="V">V</option>
                                                                    <option value="X">X</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Sarana Prasarana -->
                                        <template v-else-if="type === 'pemeliharaan-sarpras'">
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block tracking-widest font-black">Pemeliharaan (Kategori/Tag)</label>
                                                <input v-model="form.subcategory" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold font-black uppercase text-pail-gold" placeholder="CONTOH: GEDUNG / MEP / ELEKTRONIK">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nama Sarana/Prasarana</label>
                                                <input v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Jenis Pekerjaan</label>
                                                <input v-model="form.check_standard" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi / Ruangan</label>
                                                <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Ruangan</option>
                                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Sebelum</label>
                                                <input v-model="form.before_condition" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="OK / Rusak Ringan / dll">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tindakan Pemeliharaan</label>
                                                <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Sesudah</label>
                                                <input v-model="form.after_condition" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="OK / Beres / dll">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pelaksana</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Keterangan</label>
                                                <textarea v-model="form.description" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Agenda Perbaikan Sarpras -->
                                        <template v-else-if="type === 'agenda-perbaikan'">
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Uraian Pekerjaan</label>
                                                <input v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lokasi / Ruangan</label>
                                                <select v-model="form.room_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Ruangan</option>
                                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tanggal Agenda</label>
                                                <input v-model="form.scheduled_at" type="date" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Status</label>
                                                <select v-model="form.status" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="pending">Pending</option>
                                                    <option value="proses">Proses</option>
                                                    <option value="selesai">Selesai</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pelaksana</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Keterangan</label>
                                                <textarea v-model="form.description" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2" placeholder="Detail tambahan..."></textarea>
                                            </div>
                                        </template>

                                        <!-- Standard Maintenance logic -->
                                        <template v-else>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Lembaga Terkait</label>
                                                <select v-model="form.institution_id" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Lembaga</option>
                                                    <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Judul Aktivitas</label>
                                                <input v-model="form.title" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" placeholder="Contoh: Pembersihan AC Ruang Mudir">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Sebelum</label>
                                                <input v-model="form.before_condition" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kondisi Sesudah</label>
                                                <input v-model="form.after_condition" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tindakan Pemeliharaan / Perbaikan</label>
                                                <textarea v-model="form.action_taken" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold" rows="2"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Area / Lokasi Fisik</label>
                                                <input v-model="form.location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Biaya / Nilai</label>
                                                <input v-model="form.cost" type="number" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Petugas Pelaksana</label>
                                                <select v-model="form.performed_by" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold">
                                                    <option value="">Pilih Petugas</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                                </select>
                                            </div>
                                        </template>
                                    </template>
                                </template>

                                <!-- Common Selectors for Vehicles Only -->
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

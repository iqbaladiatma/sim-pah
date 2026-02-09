<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextArea from '@/Components/TextArea.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue'; // Assuming this exists or generic select
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import DocumentIcon from '@/Components/Icons/DocumentIcon.vue';
import UploadIcon from '@/Components/Icons/UploadIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';

const props = defineProps({
    type: String,
    initialData: Object
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    title: '',
    fiscal_year: new Date().getFullYear().toString(),
    description: '',
    institution_id: '',
    items: [
        {
            description: '',
            volume: 1,
            unit: 'Unit',
            unit_price: 0,
            total_price: 0,
            remarks: '',
            attachments: [] // file objects
        }
    ]
});

const isEdit = computed(() => !!props.initialData && !!props.initialData.id);

onMounted(() => {
    if (props.initialData) {
        form.title = props.initialData.title || '';
        form.fiscal_year = props.initialData.fiscal_year || new Date().getFullYear().toString();
        form.description = props.initialData.description || '';
        form.institution_id = props.initialData.institution_id || '';
        
        if (props.initialData.items && props.initialData.items.length > 0) {
            form.items = props.initialData.items.map(item => ({
                id: item.id,
                description: item.description,
                volume: item.volume,
                unit: item.unit,
                unit_price: item.unit_price,
                total_price: item.total_price || (item.volume * item.unit_price),
                remarks: item.remarks || '',
                attachments: [] 
            }));
        }
    }
});

// If editing, populate form (logic to be added if needed, currently assumes CREATE)

const titleOptions = [
    'PAB TAHUN 2026',
    'PAB TAHUN 2027',
    'ANALISIS KEBUTUHAN FASILITAS TAMBAHAN',
    'ANGGARAN BIAYA OPERASIONAL'
];

const unitOptions = ['Unit', 'Pcs', 'Set', 'Box', 'Meter', 'Lot', 'Paket', 'Org/Hari', 'Bulan'];

const addItem = () => {
    form.items.push({
        description: '',
        volume: 1,
        unit: 'Unit',
        unit_price: 0,
        total_price: 0,
        remarks: '',
        attachments: []
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calculateTotal = (item) => {
    item.total_price = (item.volume || 0) * (item.unit_price || 0);
};

// Handle file input change
const handleFileChange = (event, index) => {
    const files = Array.from(event.target.files);
    // Append instead of replace selection
    form.items[index].attachments = [...form.items[index].attachments, ...files];
};

const removeFile = (itemIndex, fileIndex) => {
    form.items[itemIndex].attachments.splice(fileIndex, 1);
};

const getFilePreview = (file) => {
    if (file instanceof File && file.type.startsWith('image/')) {
        return URL.createObjectURL(file);
    }
    return null;
};

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.procedures.update', { type: props.type, id: props.initialData.id }), {
            onSuccess: () => {
                emit('success');
                emit('close');
            },
            onError: (err) => {
                console.error(err);
            }
        });
    } else {
        form.post(route('admin.procedures.store', props.type), {
            onSuccess: () => {
                emit('success');
                emit('close');
            },
            onError: (err) => {
                console.error(err);
            }
        });
    }
};

const grandTotal = computed(() => {
    return form.items.reduce((acc, item) => acc + (item.total_price || 0), 0);
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
};

const formatInputCurrency = (event, index, field) => {
    let value = event.target.value.replace(/[^0-9]/g, '');
    if (value) {
        form.items[index][field] = parseInt(value);
    } else {
        form.items[index][field] = 0;
    }
    calculateTotal(form.items[index]);
};

const displayCurrency = (val) => {
    if (!val) return '';
    return new Intl.NumberFormat('id-ID').format(val);
};
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl">
        <div class="flex items-center justify-between mb-8 border-b dark:border-gray-700/50 pb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight">
                    {{ isEdit ? 'Update Data Analisis / PAB' : 'Isi Data Analisis / PAB' }}
                </h2>
                <p class="text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Management Entry Console</p>
            </div>
            <button @click="$emit('close')" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-500 transition-colors flex items-center justify-center shrink-0">
                <XIcon class="w-6 h-6" />
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <InputLabel value="Judul Dokumen" />
                <input list="titles" v-model="form.title" class="w-full mt-1 px-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-2xl focus:border-pail-gold focus:ring-pail-gold">
                <datalist id="titles">
                    <option v-for="t in titleOptions" :value="t" />
                </datalist>
            </div>
            <div>
                <InputLabel value="Tahun Anggaran" />
                <TextInput v-model="form.fiscal_year" type="number" class="w-full mt-1" />
            </div>
            <div class="md:col-span-2">
                <InputLabel value="Keterangan / Deskripsi Umum" />
                <TextArea v-model="form.description" class="w-full mt-1" rows="2" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Detail Item & Kebutuhan</h3>
                <SecondaryButton @click="addItem" size="sm" class="flex items-center gap-1.5">
                    <PlusIcon class="w-3.5 h-3.5" /> Tambah Item
                </SecondaryButton>
            </div>

            <div v-for="(item, index) in form.items" :key="index" class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/50 relative">
                <button v-if="form.items.length > 1" @click="removeItem(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                    <TrashIcon class="w-5 h-5" />
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Description & Specs -->
                    <div class="md:col-span-3">
                        <InputLabel value="Uraian Barang" class="whitespace-nowrap" />
                        <TextInput v-model="item.description" class="w-full mt-1 px-4 py-3 rounded-2xl" placeholder="Nama item..." />
                        <TextArea v-model="item.remarks" class="w-full mt-2 text-[10px] dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-xl" rows="1" placeholder="Spek singkat..." />
                    </div>

                    <!-- Vol, Unit, Price -->
                    <div class="md:col-span-2">
                        <InputLabel value="Volume" />
                        <input 
                            type="number" 
                            step="0.01" 
                            v-model="item.volume" 
                            @input="calculateTotal(item)"
                            class="w-full mt-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 dark:text-gray-100 rounded-2xl focus:border-pail-gold focus:ring-pail-gold text-sm font-bold transition-all"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <InputLabel value="Satuan" />
                        <select v-model="item.unit" class="w-full mt-1 px-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-2xl focus:border-pail-gold focus:ring-pail-gold">
                            <option v-for="u in unitOptions" :value="u">{{ u }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <InputLabel value="Harga Satuan" class="whitespace-nowrap" />
                        <div class="relative mt-1">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">RP</span>
                            <input 
                                type="text"
                                :value="displayCurrency(item.unit_price)"
                                @input="(e) => formatInputCurrency(e, index, 'unit_price')"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 dark:text-gray-100 rounded-2xl focus:border-pail-gold focus:ring-pail-gold font-mono text-xs font-bold transition-all"
                                placeholder="0"
                            />
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <InputLabel value="Total" />
                        <div class="mt-1 p-3 bg-gray-100 dark:bg-gray-950/50 rounded-2xl border border-gray-200 dark:border-gray-700 font-mono text-xs font-black text-right text-pail-gold tracking-tighter h-[46px] flex items-center justify-end shadow-inner">
                            {{ formatCurrency(item.total_price) }}
                        </div>
                    </div>
                </div>

                <!-- File Upload Per Item -->
                <div class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-gray-700">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Bukti Pendukung (Foto / Nota per Baris)</label>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                        <!-- Existing Attachments / Previews -->
                        <div v-for="(file, fIdx) in item.attachments" :key="fIdx" class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 shadow-sm">
                            <img v-if="getFilePreview(file)" :src="getFilePreview(file)" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex flex-col items-center justify-center p-2 text-center">
                                <DocumentIcon class="w-6 h-6 text-gray-400 mb-1" />
                                <span class="text-[8px] font-bold text-gray-500 truncate w-full px-1">{{ file.name }}</span>
                            </div>
                            
                            <!-- Remove Backdrop -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" @click="removeFile(index, fIdx)" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Add File Button -->
                        <label class="aspect-square rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 flex flex-col items-center justify-center cursor-pointer hover:border-pail-gold hover:bg-pail-gold/5 transition-all group">
                            <input type="file" multiple @change="(e) => handleFileChange(e, index)" class="hidden" />
                            <UploadIcon class="w-6 h-6 text-gray-400 group-hover:text-pail-gold transition-colors" />
                            <span class="text-[8px] font-black text-gray-400 group-hover:text-pail-gold uppercase mt-1">Upload</span>
                        </label>
                    </div>
                    
                    <p v-if="item.attachments.length === 0" class="mt-2 text-[9px] text-gray-400 italic">Belum ada file bukti yang diupload (Maks. 5MB per file)</p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-xl font-black text-gray-800 dark:text-white">
                TOTAL: <span class="text-pail-gold">{{ formatCurrency(grandTotal) }}</span>
            </div>
            <div class="flex gap-4 w-full md:w-auto">
                <SecondaryButton @click="$emit('close')" class="w-full md:w-auto justify-center">Batal</SecondaryButton>
                <PrimaryButton @click="submit" :disabled="form.processing" class="w-full md:w-auto justify-center">
                    Simpan Data
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

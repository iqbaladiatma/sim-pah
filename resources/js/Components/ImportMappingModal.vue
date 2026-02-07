<script setup>
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import UploadIcon from '@/Components/Icons/InboxIcon.vue';

const props = defineProps({
    show: Boolean,
    processing: Boolean,
    sheets: {
        type: Array,
        default: () => []
    },
    fileHeaders: {
        type: Array,
        default: () => []
    },
    requiredFields: {
        type: Array,
        default: () => [] 
    },
    fileName: String
});

const emit = defineEmits(['close', 'analyze', 'submit']);

const fileInput = ref(null);
const selectedFile = ref(null);
const selectedSheet = ref('');
const mapping = ref({});

// Reset state when modal opens/closes
watch(() => props.show, (newVal) => {
    if (!newVal) {
        selectedFile.value = null;
        selectedSheet.value = '';
        mapping.value = {};
        if (fileInput.value) fileInput.value.value = '';
    }
});

// Auto-match columns when headers change
watch(() => props.fileHeaders, (newHeaders) => {
    mapping.value = {};
    if (newHeaders.length > 0 && props.requiredFields.length > 0) {
        props.requiredFields.forEach(field => {
            const match = newHeaders.find(h => 
                h.toLowerCase().replace(/[^a-z0-9]/g, '') === field.label.toLowerCase().replace(/[^a-z0-9]/g, '') ||
                h.toLowerCase() === field.key.toLowerCase()
            );
            if (match) {
                mapping.value[field.key] = match;
            } else {
                mapping.value[field.key] = '';
            }
        });
    }
}, { deep: true });

// Initial sheet selection
watch(() => props.sheets, (sheets) => {
    if (sheets.length > 0 && !selectedSheet.value) {
        selectedSheet.value = sheets[0];
    }
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        selectedSheet.value = '';
        emit('analyze', { file });
    }
};

const handleSheetChange = () => {
    if (selectedFile.value && selectedSheet.value) {
        emit('analyze', { file: selectedFile.value, sheet: selectedSheet.value });
    }
};

const isMappingValid = computed(() => {
    return props.requiredFields
        .filter(f => f.required)
        .every(f => mapping.value[f.key]);
});

const submit = () => {
    emit('submit', {
        file: selectedFile.value,
        mapping: mapping.value,
        sheet: selectedSheet.value
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase mb-4 flex items-center gap-2">
                <UploadIcon class="w-5 h-5 text-pail-gold" />
                Import Data
            </h2>

            <!-- Step 1: File Selection -->
            <div v-if="!fileHeaders.length" class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer relative">
                    <input 
                        type="file" 
                        ref="fileInput" 
                        @change="handleFileChange" 
                        accept=".xlsx,.xls,.csv"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        :disabled="processing"
                    />
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 bg-pail-gold/10 rounded-full flex items-center justify-center text-pail-gold">
                            <UploadIcon class="w-6 h-6" />
                        </div>
                        <div v-if="processing" class="text-sm font-bold text-gray-500 animate-pulse">
                            Menganalisis file...
                        </div>
                        <div v-else>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                Klik atau seret file ke sini
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Format: .xlsx, .xls, .csv
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 & 3: Sheet & Mapping -->
            <div v-else class="space-y-6">
                <!-- File Info & Change File -->
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">File Terpilih</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ selectedFile?.name }}</span>
                    </div>
                    <button @click="selectedFile = null; fileHeaders = []" class="text-[10px] font-black text-red-400 hover:text-red-500 uppercase tracking-widest">
                        Ganti File
                    </button>
                </div>

                <!-- Sheet Selector -->
                <div v-if="sheets.length > 1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Pilih Sheet</label>
                    <select v-model="selectedSheet" @change="handleSheetChange" :disabled="processing"
                        class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-xs py-2 px-3 focus:ring-2 focus:ring-pail-gold focus:border-pail-gold">
                        <option v-for="sheet in sheets" :key="sheet" :value="sheet">{{ sheet }}</option>
                    </select>
                </div>

                <div v-if="processing" class="py-8 text-center text-gray-500 animate-pulse">
                    Memuat kolom...
                </div>

                <!-- Mapping Form -->
                <div v-else class="">
                    <h3 class="text-[10px] font-black text-pail-gold uppercase tracking-widest mb-3">Mapping Kolom</h3>
                    <div class="space-y-3 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div v-for="field in requiredFields" :key="field.key" class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 items-center p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest block"
                                    :class="field.required ? 'text-gray-900 dark:text-white' : 'text-gray-500'">
                                    {{ field.label }}
                                    <span v-if="field.required" class="text-red-500">*</span>
                                </label>
                                <span class="text-[9px] text-gray-400 font-mono">{{ field.key }}</span>
                            </div>
                            
                            <div>
                                <select v-model="mapping[field.key]" 
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-600 rounded-lg text-xs py-2 px-3 focus:ring-2 focus:ring-pail-gold focus:border-pail-gold">
                                    <option value="">-- Abaikan --</option>
                                    <option v-for="header in fileHeaders" :key="header" :value="header">
                                        {{ header }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="$emit('close')" class="px-5 py-2.5 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    Batal
                </button>
                <button 
                    v-if="fileHeaders.length"
                    @click="submit" 
                    :disabled="!isMappingValid || processing"
                    class="px-6 py-2.5 bg-pail-gold text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-pail-gold/90 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-pail-gold/20 flex items-center gap-2">
                    <span v-if="processing">Memproses...</span>
                    <span v-else>Import Data</span>
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

import SearchIcon from "@/Components/Icons/SearchIcon.vue";

const props = defineProps({
    institutions: {
        type: Array,
        required: true
    },
    modelValue: {
        type: [Number, String],
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const filteredInstitutions = ref([]);
const selectedInstitution = ref(null);

onMounted(() => {
    if (props.modelValue && props.institutions.length) {
        selectedInstitution.value = props.institutions.find(i => i.id === props.modelValue);
    }
});

watch(() => props.institutions, (newVal) => {
    filteredInstitutions.value = newVal;
    if (props.modelValue) {
        selectedInstitution.value = newVal.find(i => i.id === props.modelValue);
    }
}, { immediate: true });

watch(searchQuery, (newQuery) => {
    if (!newQuery) {
        filteredInstitutions.value = props.institutions;
    } else {
        filteredInstitutions.value = props.institutions.filter(i => 
            i.name.toLowerCase().includes(newQuery.toLowerCase()) || 
            i.code.toLowerCase().includes(newQuery.toLowerCase())
        );
    }
});

const select = (institution) => {
    selectedInstitution.value = institution;
    emit('update:modelValue', institution.id);
    isOpen.value = false;
    searchQuery.value = '';
};

const toggle = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="relative w-full font-sans">
        <div 
            @click="toggle"
            class="w-full bg-white dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-full block p-4 cursor-pointer flex justify-between items-center transition-all hover:bg-gray-50 dark:hover:bg-gray-800 shadow-sm"
        >
            <span class="font-bold truncate pr-4">
                {{ selectedInstitution ? `${selectedInstitution.name} (${selectedInstitution.code})` : 'Pilih Unit Kerja / Lembaga' }}
            </span>
            <svg class="w-5 h-5 text-pail-gold shrink-0 transition-transform duration-300" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <div v-if="isOpen" class="absolute z-[100] w-full mt-3 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-gray-100 dark:border-gray-700 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.2)] overflow-hidden transition-all animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/30">
                <div class="relative">
                    <SearchIcon className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Cari lembaga atau kode..."
                        class="w-full pl-10 pr-4 py-3 text-xs font-black uppercase tracking-widest bg-white dark:bg-gray-800 dark:text-white border-gray-100 dark:border-gray-700 rounded-full focus:ring-pail-gold focus:border-pail-gold shadow-sm"
                        autofocus
                    />
                </div>
            </div>
            <ul class="max-h-64 overflow-y-auto scrollbar-hide py-2">
                <li 
                    v-for="institution in filteredInstitutions" 
                    :key="institution.id"
                    @click="select(institution)"
                    class="mx-2 px-4 py-3.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-pail-gold hover:text-white rounded-xl cursor-pointer transition-all flex items-center justify-between group"
                    :class="selectedInstitution?.id === institution.id ? 'bg-pail-gold/10 text-pail-gold' : ''"
                >
                    <span class="truncate">{{ institution.name }}</span>
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-40 group-hover:text-white/80 shrink-0 ml-4">{{ institution.code }}</span>
                </li>
                <li v-if="filteredInstitutions.length === 0" class="p-8 text-center">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest leading-relaxed">
                        Lembaga tidak ditemukan<br/>
                        <span class="opacity-50">Cek kembali kata kunci Anda</span>
                    </p>
                </li>
            </ul>
        </div>
    </div>
</template>

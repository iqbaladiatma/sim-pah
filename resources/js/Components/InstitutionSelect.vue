<script setup>
import { ref, watch, onMounted } from 'vue';

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
    <div class="relative w-full">
        <div 
            @click="toggle"
            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-pail-gold focus:border-pail-gold block p-2.5 cursor-pointer flex justify-between items-center"
        >
            <span>{{ selectedInstitution ? `${selectedInstitution.name} (${selectedInstitution.code})` : 'Pilih Lembaga' }}</span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <div v-if="isOpen" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl overflow-hidden">
            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari lembaga..."
                    class="w-full p-2 text-sm bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded focus:ring-pail-gold"
                    autofocus
                />
            </div>
            <ul class="max-h-60 overflow-y-auto">
                <li 
                    v-for="institution in filteredInstitutions" 
                    :key="institution.id"
                    @click="select(institution)"
                    class="p-2.5 text-sm text-gray-900 dark:text-gray-200 hover:bg-pail-gold hover:text-white cursor-pointer transition flex items-center justify-between"
                >
                    <span>{{ institution.name }}</span>
                    <span class="text-xs opacity-60">{{ institution.code }}</span>
                </li>
                <li v-if="filteredInstitutions.length === 0" class="p-2.5 text-sm text-gray-500 text-center">
                    Lembaga tidak ditemukan
                </li>
            </ul>
        </div>
    </div>
</template>

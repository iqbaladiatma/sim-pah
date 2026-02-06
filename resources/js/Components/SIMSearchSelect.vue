<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import SearchIcon from "@/Components/Icons/SearchIcon.vue";

/**
 * SIMSearchSelect - A premium searchable dropdown component
 * Optimized for SIM PAH Mataram Design System
 */

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    options: {
        type: Array,
        required: true,
        default: () => []
    },
    placeholder: {
        type: String,
        default: '- Pilih Opsi -'
    },
    labelKey: {
        type: String,
        default: 'name'
    },
    valueKey: {
        type: String,
        default: 'id'
    },
    customLabel: {
        type: Function,
        default: null
    },
    error: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    icon: {
        type: [Object, Function],
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const searchQuery = ref('');
const containerRef = ref(null);
const searchInput = ref(null);

// Get display label for an option
const getOptionLabel = (option) => {
    if (!option) return '';
    if (props.customLabel) return props.customLabel(option);
    return option[props.labelKey];
};

// Selected option object
const selectedOption = computed(() => {
    return props.options.find(opt => String(opt[props.valueKey]) === String(props.modelValue));
});

// Filtered options based on search query
const filteredOptions = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.options;
    
    return props.options.filter(opt => {
        const label = getOptionLabel(opt).toLowerCase();
        return label.includes(query);
    });
});

// Select an option
const selectOption = (option) => {
    const value = option[props.valueKey];
    emit('update:modelValue', value);
    emit('change', option);
    closeDropdown();
};

// Toggle dropdown state
const toggleDropdown = async () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    
    if (isOpen.value) {
        await nextTick();
        searchInput.value?.focus();
    }
};

const closeDropdown = () => {
    isOpen.value = false;
    searchQuery.value = '';
};

// Handle clicks outside container
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div class="relative w-full" ref="containerRef">
        <!-- Label Section -->
        <label v-if="label" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3 ml-1 flex items-center gap-2">
            <component v-if="icon" :is="icon" class="w-3 h-3 text-pail-gold" />
            {{ label }}
        </label>

        <!-- Trigger Button -->
        <div 
            @click="toggleDropdown"
            :class="[
                'group w-full h-14 rounded-2xl border-2 transition-all duration-300 flex items-center justify-between px-6 cursor-pointer relative overflow-hidden',
                isOpen 
                    ? 'border-pail-gold bg-white dark:bg-gray-800 shadow-lg shadow-pail-gold/10' 
                    : 'border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/50 hover:border-gray-200 dark:hover:border-gray-600',
                disabled ? 'opacity-50 cursor-not-allowed grayscale' : '',
                error ? 'border-red-500/50 bg-red-50/10' : ''
            ]"
        >
            <div class="flex flex-col truncate pr-4">
                <span 
                    v-if="selectedOption" 
                    class="text-sm font-black text-gray-900 dark:text-white uppercase truncate"
                >
                    {{ getOptionLabel(selectedOption) }}
                </span>
                <span 
                    v-else 
                    class="text-sm font-bold text-gray-400 dark:text-gray-600 uppercase tracking-tight"
                >
                    {{ placeholder }}
                </span>
            </div>

            <!-- Arrow Icon -->
            <div 
                class="shrink-0 transition-transform duration-500"
                :class="isOpen ? 'rotate-180 text-pail-gold' : 'text-gray-300'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            <!-- Error Indicator Pulse -->
            <div v-if="error" class="absolute right-0 top-0 w-1 h-full bg-red-500 animate-pulse"></div>
        </div>

        <!-- Dropdown Panel -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
        >
            <div 
                v-if="isOpen"
                class="absolute z-[100] w-full mt-3 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-[2rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.15)] overflow-hidden"
            >
                <!-- Search Box -->
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                    <div class="relative group">
                        <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-pail-gold transition-colors" />
                        <input 
                            ref="searchInput"
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari data..."
                            class="w-full h-12 pl-12 pr-4 bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl text-xs font-black uppercase tracking-widest focus:ring-pail-gold focus:border-pail-gold transition-all"
                        />
                    </div>
                </div>

                <!-- Options List -->
                <ul class="max-h-72 overflow-y-auto py-2 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700">
                    <li 
                        v-for="option in filteredOptions" 
                        :key="option[valueKey]"
                        @click="selectOption(option)"
                        class="mx-3 px-5 py-4 mb-1 rounded-2xl flex items-center justify-between group cursor-pointer transition-all hover:bg-pail-gold/10"
                        :class="String(option[valueKey]) === String(modelValue) ? 'bg-pail-gold/5' : ''"
                    >
                        <div class="flex flex-col">
                            <span 
                                class="text-xs font-black uppercase tracking-tight transition-colors"
                                :class="String(option[valueKey]) === String(modelValue) ? 'text-pail-gold' : 'text-gray-700 dark:text-gray-300'"
                            >
                                {{ getOptionLabel(option) }}
                            </span>
                        </div>

                        <!-- Checkmark -->
                        <div 
                            v-if="String(option[valueKey]) === String(modelValue)"
                            class="w-6 h-6 rounded-lg bg-pail-gold flex items-center justify-center text-white shadow-lg shadow-pail-gold/20 scale-100 animate-in zoom-in-50 duration-300"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </li>

                    <!-- Empty State -->
                    <li v-if="filteredOptions.length === 0" class="p-10 text-center">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 italic">Data tidak ditemukan</div>
                        <div class="text-[8px] font-bold text-gray-300 uppercase">Coba kata kunci lain</div>
                    </li>
                </ul>
            </div>
        </Transition>

        <!-- Error Text -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div v-if="error" class="mt-2 text-[10px] font-black text-red-500 uppercase tracking-widest ml-1">
                {{ error }}
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    border-radius: 10px;
}
</style>

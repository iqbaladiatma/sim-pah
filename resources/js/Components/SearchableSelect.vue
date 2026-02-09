<script setup>
import { ref, watch, onMounted, computed, nextTick, onUnmounted } from 'vue';

const props = defineProps({
    options: {
        type: Array,
        required: true,
        default: () => []
    },
    modelValue: {
        type: [Number, String],
        default: null
    },
    labelKey: {
        type: String,
        default: 'name'
    },
    valueKey: {
        type: String,
        default: 'id'
    },
    placeholder: {
        type: String,
        default: 'Pilih opsi'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    allowCreate: {
        type: Boolean,
        default: false
    },
    customLabel: {
        type: Function,
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'change', 'create']);

const isOpen = ref(false);
const searchQuery = ref('');
const containerRef = ref(null);
const searchInput = ref(null);

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
        searchQuery.value = '';
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Since we can't clean up easily in <script setup> without onUnmounted
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option => {
        const label = getLabel(option).toLowerCase();
        return label.includes(query);
    });
});

const selectedOption = computed(() => {
    // If options are objects
    const found = props.options.find(o => {
        if (typeof o === 'object') return o[props.valueKey] === props.modelValue;
        return o === props.modelValue;
    });
    
    if (found) return found;

    // If not found and allowCreate is true
    if (props.allowCreate && props.modelValue) {
        // If options are primitives, return the value itself
        if (props.options.length > 0 && typeof props.options[0] !== 'object') {
             return props.modelValue;
        }
        return { [props.labelKey]: props.modelValue, [props.valueKey]: props.modelValue, _isNew: true };
    }
    
    return null;
});

const getLabel = (option) => {
    if (!option) return '';
    if (typeof option !== 'object') return String(option); // Handle primitive
    if (props.customLabel) {
        return props.customLabel(option);
    }
    return option[props.labelKey];
};

const select = (option) => {
    const val = (typeof option === 'object') ? option[props.valueKey] : option;
    emit('update:modelValue', val);
    emit('change', option);
    isOpen.value = false;
    searchQuery.value = '';
};

const createOption = () => {
    const newVal = searchQuery.value.trim();
    if (!newVal) return;
    
    emit('create', newVal);
    emit('update:modelValue', newVal); // Assuming for simple strings, the value is the string itself
    isOpen.value = false;
    searchQuery.value = '';
};

const toggle = async () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        await nextTick();
        searchInput.value?.focus();
    } else {
        searchQuery.value = '';
    }
};
</script>

<template>
    <div class="relative w-full" ref="containerRef">
        <!-- Trigger Button -->
        <div 
            @click="toggle"
            :class="[
                'w-full border rounded-2xl bg-gray-50/50 dark:bg-gray-900 text-sm font-bold transition-all p-2.5 cursor-pointer flex justify-between items-center',
                disabled 
                    ? 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700' 
                    : 'border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100 focus:ring-pail-gold focus:border-pail-gold hover:border-gray-300'
            ]"
        >
            <span class="truncate block">
                {{ selectedOption ? getLabel(selectedOption) : placeholder }}
            </span>
            <svg 
                class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                :class="{ 'rotate-180': isOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <!-- Dropdown Menu -->
        <div 
            v-if="isOpen" 
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-100"
        >
            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <input 
                    ref="searchInput"
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari..."
                    class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-pail-gold/50 focus:border-pail-gold outline-none transition-all"
                    @keydown.enter.prevent="createOption"
                />
            </div>
            <ul class="max-h-60 overflow-y-auto py-1">
                <li 
                    v-for="option in filteredOptions" 
                    :key="option[valueKey]"
                    @click="select(option)"
                    class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-pail-gold/10 hover:text-pail-gold cursor-pointer transition-colors flex items-center justify-between group"
                    :class="{ 'bg-pail-gold/5 text-pail-gold font-bold': option[valueKey] === modelValue }"
                >
                    <span class="block truncate">{{ getLabel(option) }}</span>
                    <span v-if="option[valueKey] === modelValue" class="text-pail-gold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </li>
                
                <!-- Create Option -->
                <li 
                    v-if="allowCreate && searchQuery && filteredOptions.length === 0" 
                    @click="createOption"
                    class="px-4 py-3 text-sm text-pail-gold hover:bg-pail-gold/10 cursor-pointer italic border-t border-gray-100 dark:border-gray-700 font-bold"
                >
                    + Tambah "{{ searchQuery }}"
                </li>

                <li v-else-if="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center italic">
                    Tidak ditemukan.
                </li>
            </ul>
        </div>
    </div>
</template>

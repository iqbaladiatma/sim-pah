<template>
    <div class="relative icon-select-container">
        <button
            type="button"
            @click.stop="isOpen = !isOpen"
            class="w-full h-16 border-gray-100 rounded-3xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold focus:border-pail-gold px-6 flex items-center justify-between border"
        >
            <div class="flex items-center gap-3">
                <component :is="selectedOption?.icon" v-if="selectedOption?.icon" class="w-5 h-5" :class="selectedOption?.iconClass" />
                <span>{{ selectedOption?.label }}</span>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown opens UPWARD to prevent being cut off -->
        <div
            v-if="isOpen"
            class="absolute z-[100] w-full bottom-full mb-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
        >
            <div class="max-h-64 overflow-y-auto">
                <button
                    v-for="option in options"
                    :key="option.value"
                    type="button"
                    @click="selectOption(option)"
                    class="w-full px-6 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center gap-3 font-black text-sm uppercase tracking-widest"
                    :class="{ 'bg-pail-gold/10 text-pail-gold': modelValue === option.value }"
                >
                    <component :is="option.icon" v-if="option.icon" class="w-5 h-5" :class="option.iconClass" />
                    <span>{{ option.label }}</span>
                    <svg v-if="modelValue === option.value" class="w-4 h-4 ml-auto text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: String,
    options: Array, // [{ value: 'pending', label: 'Pendampingan', icon: ClockIcon, iconClass: 'text-yellow-500' }]
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);

const selectedOption = computed(() => {
    return props.options.find(opt => opt.value === props.modelValue);
});

const selectOption = (option) => {
    emit('update:modelValue', option.value);
    isOpen.value = false;
};

const closeOnClickOutside = (event) => {
    if (!event.target.closest('.icon-select-container')) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeOnClickOutside);
});
</script>

<template>
    <div class="relative">
        <button
            type="button"
            @click="isOpen = !isOpen"
            class="w-full h-16 border-gray-100 rounded-3xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold focus:border-pail-gold px-6 flex items-center justify-between border"
        >
            <div class="flex items-center gap-3">
                <component :is="selectedOption?.icon" v-if="selectedOption?.icon" class="w-5 h-5" :class="selectedOption?.iconClass" />
                <span>{{ selectedOption?.label }}</span>
            </div>
            <svg class="w-5 h-5 text-gray-400" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div
            v-if="isOpen"
            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
        >
            <button
                v-for="option in options"
                :key="option.value"
                type="button"
                @click="selectOption(option)"
                class="w-full px-6 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center gap-3 font-black text-sm uppercase tracking-widest"
                :class="{ 'bg-pail-gold/10': modelValue === option.value }"
            >
                <component :is="option.icon" v-if="option.icon" class="w-5 h-5" :class="option.iconClass" />
                <span>{{ option.label }}</span>
            </button>
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
    if (!event.target.closest('.relative')) {
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

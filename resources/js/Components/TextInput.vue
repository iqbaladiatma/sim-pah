<script setup>
import { onMounted, ref, computed } from 'vue';

defineOptions({
    inheritAttrs: false
});

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
});

const input = ref(null);
const showPassword = ref(false);

const inputType = computed(() => {
    if (props.type === 'password' && showPassword.value) {
        return 'text';
    }
    return props.type;
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative w-full">
        <input
            :type="inputType"
            class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-pail-gold dark:focus:ring-pail-gold transition-all duration-300 placeholder:text-gray-300 dark:placeholder:text-gray-600"
            v-bind="$attrs"
            v-model="model"
            ref="input"
        />
        
        <button 
            v-if="type === 'password'" 
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-pail-gold transition-colors focus:outline-none z-10"
        >
            <!-- When password HIDDEN (!showPassword), show SLASHED EYE (indicating it is currently hidden) -->
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
            <!-- When password VISIBLE (showPassword), show OPEN EYE (indicating it is currently visible) -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>
</template>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="closeOnBackdrop && close()"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4">
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
          >
            <div
              v-if="show"
              class="relative w-full max-w-md transform overflow-hidden rounded-t-[2rem] sm:rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all"
              @click.stop
            >
              <!-- Icon -->
              <div class="flex items-center justify-center pt-8 pb-4">
                <div
                  :class="[
                    'flex h-16 w-16 items-center justify-center rounded-full',
                    variantClasses.bg
                  ]"
                >
                  <svg
                    :class="['h-8 w-8', variantClasses.icon]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      :d="iconPath"
                    />
                  </svg>
                </div>
              </div>

              <!-- Content -->
              <div class="px-6 pb-6 text-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                  {{ title }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ message }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex gap-3 px-6 pb-6">
                <button
                  type="button"
                  @click="close()"
                  class="flex-1 rounded-full px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                >
                  {{ cancelText }}
                </button>
                <button
                  type="button"
                  @click="confirm()"
                  :class="[
                    'flex-1 rounded-full px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200',
                    variantClasses.button
                  ]"
                >
                  {{ confirmText }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Konfirmasi'
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Konfirmasi'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  variant: {
    type: String,
    default: 'danger', // danger, warning, success, info
    validator: (value) => ['danger', 'warning', 'success', 'info'].includes(value)
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close', 'confirm']);

const iconPath = computed(() => {
  const icons = {
    danger: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  };
  return icons[props.variant];
});

const variantClasses = computed(() => {
  const variants = {
    danger: {
      bg: 'bg-red-100 dark:bg-red-900/20',
      icon: 'text-red-600 dark:text-red-400',
      button: 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600'
    },
    warning: {
      bg: 'bg-yellow-100 dark:bg-yellow-900/20',
      icon: 'text-yellow-600 dark:text-yellow-400',
      button: 'bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600'
    },
    success: {
      bg: 'bg-green-100 dark:bg-green-900/20',
      icon: 'text-green-600 dark:text-green-400',
      button: 'bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600'
    },
    info: {
      bg: 'bg-blue-100 dark:bg-blue-900/20',
      icon: 'text-blue-600 dark:text-blue-400',
      button: 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600'
    }
  };
  return variants[props.variant];
});

const close = () => {
  emit('close');
};

const confirm = () => {
  emit('confirm');
  emit('close');
};
</script>

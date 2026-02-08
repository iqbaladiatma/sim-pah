<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({
    message: String,
});
</script>

<template>
    <Head title="Maintenance Mode" />

    <div
        class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center p-6 relative overflow-hidden"
    >
        <!-- Decoration -->
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[1000px] bg-pail-gold opacity-[0.03] rounded-full blur-[120px]"
        ></div>

        <div
            class="max-w-md w-full bg-white dark:bg-gray-800 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-700 shadow-2xl relative z-10 text-center"
        >
            <div
                class="w-20 h-20 rounded-3xl bg-pail-gold/10 flex items-center justify-center text-pail-gold mx-auto mb-8 border border-pail-gold/20 animate-bounce"
            >
                <svg
                    class="w-10 h-10"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    ></path>
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    ></path>
                </svg>
            </div>

            <h1
                class="text-3xl font-black text-gray-900 dark:text-white leading-tight uppercase tracking-tighter mb-4"
            >
                Under Maintenance
            </h1>

            <p
                class="text-sm text-gray-400 font-bold uppercase tracking-widest mb-6"
            >
                SIM PAH - Edisi Mataram
            </p>

            <div
                class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-8"
            >
                {{ message }}
            </div>

            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <span
                        class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                    ></span>
                    <span
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest"
                        >IT Team is on Duty</span
                    >
                </div>

                <a
                    v-if="$page.props.system_settings?.emergency_contact"
                    :href="
                        'https://wa.me/' +
                        (
                            $page.props.system_settings?.emergency_contact || ''
                        ).replace(/[^0-9]/g, '')
                    "
                    target="_blank"
                    class="mt-4 px-6 py-3 bg-pail-gold/10 text-pail-gold rounded-2xl border border-pail-gold/20 text-[10px] font-black uppercase tracking-widest hover:bg-pail-gold hover:text-white transition-all flex items-center gap-3 shadow-lg shadow-pail-gold/5"
                >
                    <svg
                        class="w-4 h-4"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.29-4.143c1.589.943 3.133 1.415 4.75 1.416 5.482.002 9.944-4.461 9.947-9.945.001-2.657-1.034-5.155-2.913-7.034-1.879-1.878-4.377-2.913-7.033-2.913-5.483 0-9.944 4.461-9.947 9.945 0 1.791.47 3.536 1.359 5.062l-1.063 3.886 3.987-1.047zm11.452-6.559c-.303-.151-1.788-.882-2.065-.982-.277-.1-.478-.151-.68.151-.202.302-.782 1.007-.959 1.208-.177.201-.353.226-.656.075-.302-.151-1.277-.47-2.433-1.498-.899-.803-1.506-1.795-1.683-2.097-.177-.301-.019-.465.132-.615.136-.134.302-.353.454-.529.151-.176.201-.302.302-.503.101-.201.05-.378-.026-.529-.075-.151-.68-1.637-.933-2.249-.246-.596-.499-.515-.68-.525l-.58-.01c-.201 0-.53.075-.806.378-.277.302-1.058 1.033-1.058 2.52 0 1.488 1.084 2.922 1.235 3.123s2.132 3.256 5.166 4.569c.721.312 1.284.499 1.721.639.723.23 1.381.197 1.902.12.58-.087 1.788-.731 2.041-1.439.252-.707.252-1.314.177-1.438s-.278-.225-.58-.376z"
                        />
                    </svg>
                    Hubungi Admin URT
                </a>
            </div>
        </div>

        <p
            class="mt-12 text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-[0.3em]"
        >
            Abu Hurairah Engineering
        </p>
    </div>
</template>

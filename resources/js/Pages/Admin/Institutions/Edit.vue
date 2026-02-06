<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";


const props = defineProps({
    institution: Object,
});

const form = useForm({
    name: props.institution.name,
    code: props.institution.code,
    description: props.institution.description || "",
});

const submit = () => {
    form.put(route("admin.institutions.update", props.institution.id));
};
</script>

<template>
    <Head title="Edit Lembaga" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Edit Lembaga
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Lembaga</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Perbarui informasi lembaga.
                                </p>
                            </div>
                            <Link :href="route('admin.institutions.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lembaga</label>
                                <input
                                    id="name"
                                    type="text"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kode Unit</label>
                                <input
                                    id="code"
                                    type="text"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    v-model="form.code"
                                    required
                                />
                                <div v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi (Opsional)</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    rows="3"
                                ></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <button type="submit" class="px-10 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">
                                    Simpan Perubahan
                                </button>
                                <Link :href="route('admin.institutions.index')" class="px-8 py-3 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 font-bold transition text-center">
                                    Batal
                                </Link>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400 font-bold">✓ Tersimpan.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

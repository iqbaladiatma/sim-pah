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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Profil Lembaga
                </h2>
                <Link :href="route('admin.institutions.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-12">
                        <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Update Lembaga</h3>
                                <p class="text-sm text-gray-400 font-medium leading-relaxed max-w-sm">
                                    Modifikasi identitas dan rincian operasional unit lembaga.
                                </p>
                            </div>
                            <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ID Unit</div>
                                <div class="text-xl font-black text-pail-gold tracking-tighter">#{{ institution.id }}</div>
                            </div>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-8">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Lembaga</label>
                                        <input v-model="form.name" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            required autofocus />
                                        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Kode Unit</label>
                                        <input v-model="form.code" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            required />
                                        <div v-if="form.errors.code" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.code }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Deskripsi & Catatan</label>
                                    <textarea v-model="form.description" rows="5" 
                                        class="w-full border-gray-100 rounded-[2rem] bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-6 leading-relaxed"></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 mt-8 pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="flex-1 py-5 bg-pail-gold text-white rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs" 
                                    :disabled="form.processing"
                                >
                                    Perbarui Data Lembaga
                                </button>
                                <Link :href="route('admin.institutions.index')" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-[2rem] hover:bg-gray-100 font-bold transition text-xs uppercase tracking-widest">
                                    Batal
                                </Link>
                                <Transition
                                    enter-active-class="transition ease-in-out duration-500"
                                    enter-from-class="opacity-0 translate-x-4"
                                    leave-active-class="transition ease-in-out duration-500"
                                    leave-to-class="opacity-0 -translate-x-4"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-[10px] font-black text-green-500 uppercase tracking-widest">✓ Tersimpan</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

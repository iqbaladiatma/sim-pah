<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    institutions: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    role: "lembaga",
    institution_id: "",
    phone: "",
});

// Format institutions untuk SearchableSelect
const institutionOptions = computed(() => {
    return props.institutions.map(inst => ({
        id: inst.id,
        name: inst.name,
        code: inst.code,
    }));
});

const submit = () => {
    form.post(route("admin.users.store"));
};
</script>

<template>
    <Head title="Tambah User Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Tambah User Baru
                </h2>
                <Link :href="route('admin.users.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-12">
                        <header class="mb-12">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Data Pengguna</h3>
                            <p class="text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                Daftarkan akun baru untuk akses sistem. Pastikan alamat email unik dan berika peran yang sesuai dengan tanggung jawabnya.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <!-- Basic Information Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Lengkap</label>
                                        <input v-model="form.name" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-full bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="Masukkan nama lengkap..." required autofocus />
                                        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Alamat Email</label>
                                        <input v-model="form.email" type="email" 
                                            class="w-full h-14 border-gray-100 rounded-full bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="user@example.com" required />
                                        <div v-if="form.errors.email" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.email }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Kata Sandi</label>
                                        <input v-model="form.password" type="password" 
                                            class="w-full h-14 border-gray-100 rounded-full bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="Minimal 8 karakter..." required />
                                        <div v-if="form.errors.password" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.password }}</div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Akses Role</label>
                                        <select v-model="form.role" class="w-full h-14 border-gray-100 rounded-full bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold px-6">
                                            <option value="super admin">Super Admin (IT)</option>
                                            <option value="admin">Admin (URT)</option>
                                            <option value="lembaga">Lembaga</option>
                                        </select>
                                        <div v-if="form.errors.role" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.role }}</div>
                                    </div>
                                    
                                    <div v-if="form.role === 'lembaga'">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Penempatan Lembaga</label>
                                        <SearchableSelect
                                            v-model="form.institution_id"
                                            :options="institutionOptions"
                                            placeholder="- Pilih Lembaga -"
                                            value-key="id"
                                            label-key="name"
                                            :custom-label="(option) => `${option.code} - ${option.name}`"
                                        />
                                        <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.institution_id }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">WhatsApp / No. HP</label>
                                        <input v-model="form.phone" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-full bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="6281..." />
                                        <div v-if="form.errors.phone" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.phone }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Section -->
                            <div class="flex items-center gap-4 mt-8 pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="flex-1 py-5 bg-pail-gold text-white rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs" 
                                    :disabled="form.processing"
                                >
                                    Konfirmasi & Registrasi User
                                </button>
                                <Link :href="route('admin.users.index')" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-[2rem] hover:bg-gray-100 font-bold transition text-xs uppercase tracking-widest">
                                    Batal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

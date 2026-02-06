<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    user: Object,
    institutions: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "", // Empty for edit
    role: props.user.role,
    institution_id: props.user.institution_id || "",
    phone: props.user.phone || "",
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
    form.put(route("admin.users.update", props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Profil Pengguna
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
                        <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Update Akun</h3>
                                <p class="text-sm text-gray-400 font-medium leading-relaxed max-w-sm">
                                    Modifikasi informasi autentikasi dan otorisasi pengguna.
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center font-black text-2xl text-pail-gold border border-gray-100 dark:border-gray-800">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Terdaftar Sejak</div>
                                    <div class="text-sm font-black text-gray-900 dark:text-white">{{ new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                                </div>
                            </div>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <!-- Detail Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Lengkap</label>
                                        <input v-model="form.name" type="text" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            required autofocus />
                                        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Email Address</label>
                                        <input v-model="form.email" type="email" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            required />
                                        <div v-if="form.errors.email" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.email }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Password (Opsional)</label>
                                        <input v-model="form.password" type="password" 
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" 
                                            placeholder="••••••••" />
                                        <p class="text-[9px] text-gray-400 font-bold italic mt-2 ml-1">* Kosongkan jika tidak ingin mengubah password.</p>
                                        <div v-if="form.errors.password" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.password }}</div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Akses Role</label>
                                        <select v-model="form.role" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold px-6">
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
                                            class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6" />
                                        <div v-if="form.errors.phone" class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">{{ form.errors.phone }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-6 mt-8 pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="flex-1 py-5 bg-pail-gold text-white rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-xs" 
                                    :disabled="form.processing"
                                >
                                    Perbarui Data User
                                </button>
                                <Link :href="route('admin.users.index')" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-[2rem] hover:bg-gray-100 font-bold transition text-xs uppercase tracking-widest">
                                    Batal
                                </Link>
                                <Transition
                                    enter-active-class="transition ease-in-out duration-500"
                                    enter-from-class="opacity-0 translate-x-4"
                                    leave-active-class="transition ease-in-out duration-500"
                                    leave-to-class="opacity-0 -translate-x-4"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-[10px] font-black text-green-500 uppercase tracking-widest">✓ Berhasil Disimpan</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

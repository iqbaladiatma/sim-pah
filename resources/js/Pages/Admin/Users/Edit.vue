<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";


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

const submit = () => {
    form.put(route("admin.users.update", props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Update Informasi User</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Ubah detail pengguna. Kosongkan password jika tidak ingin mengubahnya.
                                </p>
                            </div>
                            <Link :href="route('admin.users.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
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
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    v-model="form.email"
                                    required
                                />
                                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Password (Opsional)</label>
                                <input
                                    id="password"
                                    type="password"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    v-model="form.password"
                                    placeholder="Biarkan kosong jika tidak ingin mengubah"
                                />
                                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role / Peran</label>
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    >
                                        <option value="super admin">Super Admin</option>
                                        <option value="admin">Admin</option>
                                        <option value="lembaga">Lembaga</option>
                                    </select>
                                    <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lembaga (Opsional)</label>
                                    <select
                                        id="institution_id"
                                        v-model="form.institution_id"
                                        class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    >
                                        <option value="">- Pilih Lembaga -</option>
                                        <option :value="null">Bukan Lembaga</option>
                                        <option
                                            v-for="inst in institutions"
                                            :key="inst.id"
                                            :value="inst.id"
                                        >
                                            {{ inst.code }} - {{ inst.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.institution_id" class="text-red-500 text-xs mt-1">{{ form.errors.institution_id }}</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">No. Telepon (Opsional)</label>
                                <input
                                    id="phone"
                                    type="text"
                                    class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all"
                                    v-model="form.phone"
                                />
                                <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <button type="submit" class="px-10 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">
                                    Simpan Perubahan
                                </button>
                                <Link :href="route('admin.users.index')" class="px-8 py-3 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 font-bold transition text-center">
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

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InstitutionSelect from "@/Components/InstitutionSelect.vue";


const props = defineProps({
    items: Object,
    institutions: Array, // Only popluated for Admin
    rooms: Array,
    filters: Object,
});

const user = usePage().props.auth.user;
const isAdmin = user.role === 'admin' || user.is_super_admin;
const search = ref(props.filters.search || '');

// --- Search Logic ---
watch(search, (value) => {
    router.get(
        route('items.index'),
        { search: value },
        { preserveState: true, replace: true, preserveScroll: true }
    );
});

// --- Modal States ---
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedItem = ref(null);

// --- Forms ---
const form = useForm({
    institution_id: user.institution_id || '',
    room_id: '',
    name: '',
    brand: '',
    stock: 0,
    unit: 'Pcs',
    condition: 'Baik',
    note: '',
    min_stock: 0,
});

const deleteForm = useForm({
    reason: '',
});

// --- Handlers ---

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    // Default values
    if (!isAdmin) {
        form.institution_id = user.institution_id;
    }
    isCreateModalOpen.value = true;
};

const openEditModal = (item) => {
    selectedItem.value = item;
    form.reset();
    form.clearErrors();
    form.institution_id = item.institution_id;
    form.room_id = item.room_id;
    form.name = item.name;
    form.brand = item.brand;
    form.stock = item.stock;
    form.unit = item.unit;
    form.condition = item.condition;
    form.note = item.note;
    form.min_stock = item.min_stock;
    
    isEditModalOpen.value = true;
};

const openDeleteModal = (item) => {
    selectedItem.value = item;
    deleteForm.reset();
    deleteForm.clearErrors();
    isDeleteModalOpen.value = true;
};

const submitCreate = () => {
    form.post(route('items.store'), {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
        },
    });
};

const submitEdit = () => {
    form.put(route('items.update', selectedItem.value.id), {
        onSuccess: () => {
            isEditModalOpen.value = false;
            form.reset();
        },
    });
};

const submitDelete = () => {
    deleteForm.delete(route('items.destroy', selectedItem.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            deleteForm.reset();
        },
    });
};

// Filtered rooms based on selected institution (for Admin)
const filteredRooms = computed(() => {
    if (!isAdmin) return props.rooms;
    if (!form.institution_id) return [];
    return props.rooms.filter(r => r.institution_id == form.institution_id);
});

</script>

<template>
    <Head title="Manajemen Item" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Manajemen Aset & Inventaris
                </h2>
                <PrimaryButton @click="openCreateModal" class="bg-[#C9A658] hover:bg-[#b08d45] border-none shadow-lg text-white">
                    <span class="mr-2">+</span> Tambah Barang
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-[95%] sm:px-6 lg:px-8">
                
                <!-- Search & Filters -->
                <div class="mb-6 flex gap-4">
                    <TextInput
                        v-model="search"
                        placeholder="Cari item..."
                        class="w-full md:w-1/3"
                    />
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th v-if="isAdmin" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lembaga</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Kondisi</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="item in items.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ item.institution?.name || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ item.name }}</span>
                                            <span class="text-xs text-gray-500">{{ item.brand }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ item.room?.name || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full"
                                            :class="item.stock <= item.min_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                                            {{ item.stock }} {{ item.unit }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-600 font-semibold border border-blue-100">
                                            {{ item.condition }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="openEditModal(item)" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 px-3 py-1 rounded-full transition hover:bg-indigo-100">
                                            Edit
                                        </button>
                                        <button @click="openDeleteModal(item)" class="text-red-600 hover:text-red-900 font-bold bg-red-50 px-3 py-1 rounded-full transition hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="items.data.length === 0">
                                    <td :colspan="isAdmin ? 6 : 5" class="px-6 py-10 text-center text-gray-500 italic">
                                        Tidak ada data yang ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    <div v-for="item in items.data" :key="item.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ item.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.brand }}</p>
                                    <span v-if="isAdmin" class="text-xs text-blue-500 font-semibold mt-1 block">{{ item.institution?.name }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full font-semibold text-[10px]" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'">
                                    {{ item.condition }}
                                </span>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Ruangan:</span>
                                    <span class="font-medium dark:text-gray-200">{{ item.room?.name || '-' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Stok:</span>
                                    <span class="font-bold" :class="item.stock <= item.min_stock ? 'text-red-600' : 'text-green-600'">
                                        {{ item.stock }} {{ item.unit }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <button @click="openEditModal(item)" class="py-2.5 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm hover:bg-indigo-100">
                                    Edit
                                </button>
                                <button @click="openDeleteModal(item)" class="py-2.5 bg-red-50 text-red-700 rounded-xl font-bold text-sm hover:bg-red-100">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                     <div v-if="items.data.length === 0" class="text-center py-10 text-gray-500 italic">
                        Tidak ada data yang ditemukan.
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="isCreateModalOpen || isEditModalOpen" @close="isCreateModalOpen = false; isEditModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 font-bold uppercase border-b pb-2">
                    {{ isCreateModalOpen ? 'Tambah Barang Baru' : 'Edit Barang' }}
                </h2>

                <form @submit.prevent="isCreateModalOpen ? submitCreate() : submitEdit()">
                    <div class="space-y-4">
                        <!-- Institution (Admin Only) -->
                        <div v-if="isAdmin">
                            <InputLabel value="Lembaga" />
                            <InstitutionSelect 
                                v-model="form.institution_id" 
                                :institutions="institutions" 
                                class="mt-1" 
                            />
                            <div v-if="form.errors.institution_id" class="text-red-500 text-xs mt-1">{{ form.errors.institution_id }}</div>
                        </div>

                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Nama Barang" />
                                <TextInput v-model="form.name" class="w-full mt-1" required placeholder="Contoh: Laptop Asus" />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <InputLabel value="Merk / Brand" />
                                <TextInput v-model="form.brand" class="w-full mt-1" placeholder="Contoh: Asus, Logitech" />
                            </div>
                        </div>

                        <!-- Stock & Unit -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel value="Stok" />
                                <TextInput v-model="form.stock" type="number" class="w-full mt-1" required />
                                <div v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</div>
                            </div>
                             <div>
                                <InputLabel value="Satuan" />
                                <TextInput v-model="form.unit" class="w-full mt-1" required placeholder="Pcs, Unit, Set" />
                                <div v-if="form.errors.unit" class="text-red-500 text-xs mt-1">{{ form.errors.unit }}</div>
                            </div>
                            <div>
                                <InputLabel value="Min. Stok (Alert)" />
                                <TextInput v-model="form.min_stock" type="number" class="w-full mt-1" />
                                <div v-if="form.errors.min_stock" class="text-red-500 text-xs mt-1">{{ form.errors.min_stock }}</div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Ruangan" />
                                <select v-model="form.room_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                    <option value="">-- Pilih Ruangan --</option>
                                    <option v-for="room in filteredRooms" :key="room.id" :value="room.id">
                                        {{ room.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.room_id" class="text-red-500 text-xs mt-1">{{ form.errors.room_id }}</div>
                            </div>
                            <div>
                                <InputLabel value="Kondisi" />
                                <select v-model="form.condition" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                                <div v-if="form.errors.condition" class="text-red-500 text-xs mt-1">{{ form.errors.condition }}</div>
                            </div>
                        </div>

                        <div>
                            <InputLabel value="Keterangan / Catatan" />
                            <textarea v-model="form.note" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" rows="3"></textarea>
                            <div v-if="form.errors.note" class="text-red-500 text-xs mt-1">{{ form.errors.note }}</div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="isCreateModalOpen = false; isEditModalOpen = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing" class="bg-pail-gold hover:bg-yellow-600 border-none">
                            {{ isCreateModalOpen ? 'Simpan Data' : 'Perbarui Data' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal (Validation Request) -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false">
            <div class="p-6">
                 <h2 class="text-lg font-bold text-red-600 mb-4 uppercase">
                    {{ isAdmin ? 'Hapus Barang' : 'Request Hapus Barang' }}
                </h2>
                
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    Apakah Anda yakin ingin menghapus barang <strong>{{ selectedItem?.name }}</strong>?
                    <br>
                    <span v-if="!isAdmin">Permintaan ini akan dikirim ke Admin untuk disetujui.</span>
                    <span v-else>Tindakan ini akan menghapus data secara permanen (atau sesuai kebijakan sistem).</span>
                </p>

                <form @submit.prevent="submitDelete">
                    <div class="mb-4">
                        <InputLabel value="Alasan Penghapusan (Wajib)" class="text-red-700" />
                        <textarea 
                            v-model="deleteForm.reason" 
                            required 
                            class="w-full mt-1 border-red-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
                            rows="3"
                            placeholder="Contoh: Barang sudah rusak total dan dibuang..."
                        ></textarea>
                         <div v-if="deleteForm.errors.reason" class="text-red-500 text-xs mt-1">{{ deleteForm.errors.reason }}</div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="isDeleteModalOpen = false">Batal</SecondaryButton>
                        <DangerButton :disabled="deleteForm.processing">
                            {{ isAdmin ? 'Hapus Sekarang' : 'Kirim Request Hapus' }}
                        </DangerButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

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
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                        Manajemen Aset & Inventaris
                    </h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Sistem Katalog Barang Unit</p>
                </div>
                <button @click="openCreateModal" class="px-6 py-4 bg-gray-900 text-pail-gold rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center gap-3 group">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Barang
                </button>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Premium Search Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="relative flex-1">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <TextInput
                                v-model="search"
                                placeholder="Cari nama barang, merk, atau ruangan..."
                                class="w-full pl-12 pr-4 py-3 rounded-full border-2 border-gray-100 dark:border-gray-700 focus:border-pail-gold focus:ring-pail-gold/20 font-bold text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Items Grid -->
                <div class="hidden md:block space-y-4">
                    <div v-for="item in items.data" :key="item.id" class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-start gap-6">
                                    <!-- Icon/Badge -->
                                    <div class="w-16 h-16 rounded-[1.5rem] bg-pail-gold/10 border-2 border-pail-gold/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                        <svg class="w-8 h-8 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight group-hover:text-pail-gold transition-colors">{{ item.name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mt-1">{{ item.brand || 'No Brand' }}</p>
                                                <span v-if="isAdmin" class="inline-block mt-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-black uppercase tracking-wider rounded-full">
                                                    {{ item.institution?.name || '-' }}
                                                </span>
                                            </div>
                                            <span class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm" :class="item.condition === 'Baik' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-orange-50 text-orange-600 border border-orange-100'">
                                                {{ item.condition }}
                                            </span>
                                        </div>
                                        
                                        <!-- Details Grid -->
                                        <div class="grid grid-cols-3 gap-6 py-4 border-t border-gray-100 dark:border-gray-700">
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ruangan</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ item.room?.name || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Stok Tersedia</p>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-2xl font-black tracking-tighter" :class="item.stock <= item.min_stock ? 'text-red-600' : 'text-green-600'">{{ item.stock }}</span>
                                                    <span class="text-xs font-bold text-gray-400 uppercase">{{ item.unit }}</span>
                                                </div>
                                                <p v-if="item.stock <= item.min_stock" class="text-[9px] font-black text-red-500 uppercase tracking-wider mt-1 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                    Stok Rendah
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Min. Stok</p>
                                                <p class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ item.min_stock }} {{ item.unit }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Note if exists -->
                                        <div v-if="item.note" class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 italic">{{ item.note }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-3 ml-6">
                                <button @click="openEditModal(item)" class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all flex items-center justify-center group/btn">
                                    <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button @click="openDeleteModal(item)" class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all flex items-center justify-center group/btn">
                                    <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-if="items.data.length === 0" class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 p-20 text-center">
                        <div class="flex flex-col items-center text-gray-400">
                            <svg class="w-16 h-16 mb-6 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p class="font-black uppercase tracking-[0.3em] text-sm">Belum Ada Barang</p>
                            <p class="text-xs text-gray-300 font-bold mt-2 uppercase">Mulai dengan menambahkan barang baru.</p>
                        </div>
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

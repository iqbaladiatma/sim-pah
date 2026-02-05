<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";
import LocationIcon from "@/Components/Icons/LocationIcon.vue";
import PackageIcon from "@/Components/Icons/PackageIcon.vue";
import CalendarIcon from "@/Components/Icons/CalendarIcon.vue";
import NumberIcon from "@/Components/Icons/NumberIcon.vue";
import DiamondIcon from "@/Components/Icons/DiamondIcon.vue";
import CheckIcon from "@/Components/Icons/CheckIcon.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import DocumentIcon from "@/Components/Icons/DocumentIcon.vue";

const props = defineProps({
    institutions: Array,
});

const form = useForm({
    institution_id: "",
    room_id: "",
    name: "",
    purchase_date: "",
    stock: 0,
    source: "",
    condition: "Baik",
    responsible_person: "",
    note: "",
});

const rooms = ref([]);
const isLoadingRooms = ref(false);

const fetchRooms = async (institutionId) => {
    if (!institutionId) {
        rooms.value = [];
        return;
    }
    isLoadingRooms.value = true;
    try {
        const response = await axios.get(route('admin.rooms.by_institution', institutionId));
        rooms.value = response.data;
    } catch (error) {
        console.error("Failed to fetch rooms", error);
    } finally {
        isLoadingRooms.value = false;
    }
};

watch(() => form.institution_id, (newVal) => {
    form.room_id = "";
    fetchRooms(newVal);
});

const submit = () => {
    form.post(route("admin.items.store"));
};
</script>

<template>
    <Head title="Tambah Barang Baru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Barang Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <header class="mb-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Form Inventaris</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Lengkapi data di bawah ini untuk menambahkan aset baru.
                                </p>
                            </div>
                            <Link :href="route('admin.items.index')" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-bold text-sm transition-all">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Row 1: Institution & Room -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lembaga Pemilik</label>
                                <select v-model="form.institution_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" required>
                                    <option value="">- Pilih Lembaga -</option>
                                    <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.code }} - {{ inst.name }}</option>
                                </select>
                                <div v-if="form.errors.institution_id" class="text-red-500 text-xs mt-1">{{ form.errors.institution_id }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <LocationIcon className="w-3.5 h-3.5" /> Lokasi / Ruangan
                                </label>
                                <select v-model="form.room_id" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" required :disabled="!form.institution_id || isLoadingRooms">
                                    <option value="">- Pilih Ruangan -</option>
                                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                </select>
                                <div v-if="form.errors.room_id" class="text-red-500 text-xs mt-1">{{ form.errors.room_id }}</div>
                            </div>

                            <!-- Row 2: Jenis -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <PackageIcon className="w-3.5 h-3.5" /> Jenis Barang
                                </label>
                                <input v-model="form.name" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" placeholder="Contoh: AC, Laptop, Meja" required />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <!-- Row 3: Tgl Pembukuan & Kuantitas -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <CalendarIcon className="w-3.5 h-3.5" /> Tanggal Pembukuan
                                </label>
                                <input v-model="form.purchase_date" type="date" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-mono font-bold transition-all" />
                                <div v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_date }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <NumberIcon className="w-3.5 h-3.5" /> Kuantitas
                                </label>
                                <input v-model="form.stock" type="number" min="0" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold text-center transition-all" required />
                                <div v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</div>
                            </div>

                            <!-- Row 4: Sumber & Keadaan -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <DiamondIcon className="w-3.5 h-3.5" /> Sumber Perolehan
                                </label>
                                <input v-model="form.source" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" placeholder="Contoh: Hibah, APB, Yayasan" required />
                                <div v-if="form.errors.source" class="text-red-500 text-xs mt-1">{{ form.errors.source }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <CheckIcon className="w-3.5 h-3.5" /> Keadaan Barang
                                </label>
                                <select v-model="form.condition" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" required>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                                <div v-if="form.errors.condition" class="text-red-500 text-xs mt-1">{{ form.errors.condition }}</div>
                            </div>

                            <!-- Row 5: PJ -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <UserIcon className="w-3.5 h-3.5" /> Penanggung Jawab
                                </label>
                                <input v-model="form.responsible_person" type="text" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" placeholder="Nama petugas" />
                                <div v-if="form.errors.responsible_person" class="text-red-500 text-xs mt-1">{{ form.errors.responsible_person }}</div>
                            </div>

                            <!-- Row 6: Keterangan -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                    <DocumentIcon className="w-3.5 h-3.5" /> Keterangan
                                </label>
                                <textarea v-model="form.note" rows="3" class="w-full border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold transition-all" placeholder="Berikan catatan tambahan jika ada..."></textarea>
                                <div v-if="form.errors.note" class="text-red-500 text-xs mt-1">{{ form.errors.note }}</div>
                            </div>

                            <div class="md:col-span-2 flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <Link :href="route('admin.items.index')" class="px-8 py-3 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 font-bold transition text-center">Batal</Link>
                                <button type="submit" class="px-10 py-3 bg-pail-gold text-white rounded-2xl hover:bg-yellow-600 font-bold shadow-lg shadow-pail-gold/20 transition" :disabled="form.processing">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

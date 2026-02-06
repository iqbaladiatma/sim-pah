<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
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
    item: Object,
    institutions: Array,
});

const form = useForm({
    institution_id: props.item.institution_id,
    room_id: props.item.room_id,
    name: props.item.name,
    purchase_date: props.item.purchase_date,
    stock: props.item.stock,
    source: props.item.source,
    condition: props.item.condition,
    responsible_person: props.item.responsible_person || "",
    note: props.item.note || "",
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

watch(() => form.institution_id, (newVal, oldVal) => {
    // Only reset room_id if checking a different institution (not initial load if handled elsewhere, but here watch is lazy by default)
    // Actually, on mounting, oldVal is undefined? No, lazy watch doesn't run on mount.
    // So if user changes institution, reset room.
    form.room_id = "";
    fetchRooms(newVal);
});

onMounted(() => {
    if (form.institution_id) {
        // Manually fetch rooms for the existing institution, without resetting room_id
        fetchRooms(form.institution_id);
    }
});

const submit = () => {
    form.put(route("admin.items.update", props.item.id));
};
</script>

<template>
    <Head title="Edit Data Barang" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 font-sans">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter text-center sm:text-left">
                    Edit Data Barang
                </h2>
                <Link :href="route('admin.items.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all text-center sm:text-right">
                    &larr; Batalkan
                </Link>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] sm:rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 sm:p-12">
                        <header class="mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Pembaruan Aset</h3>
                                <p class="text-xs sm:text-sm text-gray-400 font-medium leading-relaxed max-w-xl">
                                    Modifikasi detail inventaris, status kondisi, atau lokasi penempatan aset secara akurat.
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-xl sm:rounded-2xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest border shadow-sm bg-blue-50 text-blue-700 border-blue-100">
                                    ID: {{ item.id }}
                                </span>
                            </div>
                        </header>

                        <form @submit.prevent="submit" class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                                <!-- Owners & Location -->
                                <div class="space-y-8 col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-10">
                                    <div>
                                        <label class="block text-[10px] font-black text-pail-gold uppercase tracking-widest mb-3 ml-1">Lembaga Pemilik Aset</label>
                                        <select v-model="form.institution_id" class="w-full h-14 border-gray-100 rounded-3xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold focus:border-pail-gold px-6">
                                            <option value="">- Pilih Lembaga -</option>
                                            <option v-for="inst in institutions" :key="inst.id" :value="inst.id">{{ inst.code }} - {{ inst.name }}</option>
                                        </select>
                                        <div v-if="form.errors.institution_id" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.institution_id }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                            <LocationIcon className="w-3 h-3" /> Lokasi / Ruangan Penempatan
                                        </label>
                                        <select v-model="form.room_id" class="w-full h-14 border-gray-100 rounded-3xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm font-black uppercase tracking-widest focus:ring-pail-gold focus:border-pail-gold px-6" :disabled="!form.institution_id || isLoadingRooms">
                                            <option value="">- Pilih Ruangan -</option>
                                            <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                                        </select>
                                        <div v-if="form.errors.room_id" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.room_id }}</div>
                                    </div>
                                </div>

                                <!-- Main Details -->
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <PackageIcon className="w-3 h-3" /> Nama Barang & Spesifikasi Ringkas
                                    </label>
                                    <input v-model="form.name" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold px-6 uppercase tracking-tight" placeholder="Misal: AC Samsung 1/2 PK" required />
                                    <div v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.name }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <CalendarIcon className="w-3 h-3" /> Tanggal Perolehan
                                    </label>
                                    <input v-model="form.purchase_date" type="date" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-mono font-black px-6" />
                                    <div v-if="form.errors.purchase_date" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.purchase_date }}</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <NumberIcon className="w-3 h-3" /> Jumlah Kuantitas Unit
                                    </label>
                                    <input v-model="form.stock" type="number" min="0" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-lg focus:ring-pail-gold focus:border-pail-gold font-black px-6 text-center" required />
                                    <div v-if="form.errors.stock" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.stock }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <DiamondIcon className="w-3 h-3" /> Sumber Dana / Perolehan
                                    </label>
                                    <input v-model="form.source" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-black px-6" placeholder="APB, Hibah, Yayasan" required />
                                    <div v-if="form.errors.source" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.source }}</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <CheckIcon className="w-3 h-3" /> Kondisi Aset Saat Ini
                                    </label>
                                    <select v-model="form.condition" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-black px-6 uppercase tracking-widest">
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                    <div v-if="form.errors.condition" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.condition }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <UserIcon className="w-3 h-3" /> Nama Penanggung Jawab
                                    </label>
                                    <input v-model="form.responsible_person" type="text" class="w-full h-14 border-gray-100 rounded-2xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-black px-6" placeholder="Petugas URT / Lembaga" />
                                    <div v-if="form.errors.responsible_person" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.responsible_person }}</div>
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 flex items-center gap-2">
                                        <DocumentIcon className="w-3 h-3" /> Catatan Tambahan
                                    </label>
                                    <textarea v-model="form.note" rows="3" class="w-full border-gray-100 rounded-3xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-pail-gold focus:border-pail-gold font-bold p-6 leading-relaxed" placeholder="Spesifikasi teknis, nomor seri, dll..."></textarea>
                                    <div v-if="form.errors.note" class="text-red-500 text-[10px] font-black uppercase mt-2 ml-1">{{ form.errors.note }}</div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4 pt-8 sm:pt-10 border-t border-gray-50 dark:border-gray-800">
                                <button 
                                    type="submit" 
                                    class="w-full sm:w-auto px-12 py-4 sm:py-5 bg-pail-gold text-white rounded-full sm:rounded-[2rem] hover:bg-yellow-600 font-black shadow-xl shadow-pail-gold/20 transition-all uppercase tracking-[0.2em] text-[10px] sm:text-xs text-center" 
                                    :disabled="form.processing"
                                >
                                    Push Perubahan
                                </button>
                                <Link :href="route('admin.items.index')" class="w-full sm:w-auto px-8 py-4 sm:py-5 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-full sm:rounded-[2rem] hover:bg-gray-100 dark:hover:bg-gray-600 font-bold transition text-[10px] sm:text-xs uppercase tracking-widest text-center">
                                    Batalkan
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

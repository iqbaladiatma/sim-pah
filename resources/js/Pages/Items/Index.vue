<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    items: Object,
    institutions: Array, // Only populates for Admin
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value.role === "admin");

const form = useForm({
    name: "",
    institution_id: "",
    stock: 0,
    unit: "",
    min_stock: 0,
    reason: "", // For Karyawan requests
});

const isModalOpen = ref(false);
const editingItem = ref(null);

const openModal = (item = null) => {
    if (item) {
        editingItem.value = item;
        form.name = item.name;
        form.institution_id = item.institution_id;
        form.stock = item.stock;
        form.unit = item.unit;
        form.min_stock = item.min_stock;
        form.reason = ""; // Reset reason
    } else {
        // Create Mode (Admin Only)
        editingItem.value = null;
        form.reset();
        form.institution_id = ""; // Default empty
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route("items.update", editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("items.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (id) => {
    if (confirm("Yakin ingin menghapus barang ini?")) {
        form.delete(route("items.destroy", id));
    }
};
</script>

<template>
    <Head title="Manajemen Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen Barang (Inventaris)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add Button (Admin Only) -->
                <div v-if="isAdmin" class="mb-6 flex justify-end">
                    <button
                        @click="openModal()"
                        class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600 transition"
                    >
                        + Tambah Barang Baru
                    </button>
                </div>

                <!-- Table -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead>
                                <tr>
                                    <th
                                        v-if="isAdmin"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Min. Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr v-for="item in items.data" :key="item.id">
                                    <td
                                        v-if="isAdmin"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            item.institution
                                                ? item.institution.code
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-bold"
                                    >
                                        {{ item.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-lg font-bold"
                                            :class="{
                                                'text-red-500':
                                                    item.stock <=
                                                    item.min_stock,
                                                'text-green-600':
                                                    item.stock > item.min_stock,
                                            }"
                                        >
                                            {{ item.stock }}
                                        </span>
                                        <span
                                            class="text-xs text-gray-500 ml-1"
                                            >{{ item.unit }}</span
                                        >
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ item.min_stock }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            @click="openModal(item)"
                                            class="text-blue-600 hover:text-blue-900 mr-2"
                                        >
                                            {{
                                                isAdmin ? "Edit" : "Update Stok"
                                            }}
                                        </button>
                                        <button
                                            v-if="isAdmin"
                                            @click="deleteItem(item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div
                            class="mt-4"
                            v-if="items.links && items.links.length > 3"
                        >
                            <!-- Implement Pagination here if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6"
            >
                <h3
                    class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100"
                >
                    {{
                        editingItem
                            ? isAdmin
                                ? "Edit Barang"
                                : "Request Update Stok"
                            : "Tambah Barang Baru"
                    }}
                </h3>

                <form @submit.prevent="submit">
                    <!-- Admin Fields -->
                    <div v-if="isAdmin">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Lembaga</label
                            >
                            <select
                                v-model="form.institution_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            >
                                <option value="">- Pilih Lembaga -</option>
                                <option
                                    v-for="inst in institutions"
                                    :key="inst.id"
                                    :value="inst.id"
                                >
                                    {{ inst.code }} - {{ inst.name }}
                                </option>
                            </select>
                            <div
                                v-if="form.errors.institution_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.institution_id }}
                            </div>
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Nama Barang</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            />
                            <div
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>
                    </div>

                    <!-- Shared Fields (Readonly for Karyawan except Stock) -->
                    <div v-if="!isAdmin && editingItem" class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Nama Barang</label
                        >
                        <input
                            :value="form.name"
                            disabled
                            type="text"
                            class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm dark:bg-gray-600 dark:text-gray-300 cursor-not-allowed"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Stok {{ isAdmin ? "" : "Baru" }}</label
                            >
                            <input
                                v-model="form.stock"
                                type="number"
                                min="0"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            />
                            <div
                                v-if="form.errors.stock"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.stock }}
                            </div>
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Satuan</label
                            >
                            <input
                                v-model="form.unit"
                                :disabled="!isAdmin"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{
                                    'bg-gray-100 dark:bg-gray-600': !isAdmin,
                                }"
                                required
                            />
                            <div
                                v-if="form.errors.unit"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.unit }}
                            </div>
                        </div>
                    </div>

                    <div v-if="isAdmin" class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Min. Stok Warning</label
                        >
                        <input
                            v-model="form.min_stock"
                            type="number"
                            min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        />
                        <div
                            v-if="form.errors.min_stock"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.min_stock }}
                        </div>
                    </div>

                    <!-- Karyawan Reason Field -->
                    <div v-if="!isAdmin && editingItem" class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Alasan Perubahan
                            <span class="text-red-500">*</span></label
                        >
                        <textarea
                            v-model="form.reason"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                            placeholder="Contoh: Barang rusak, pemakaian bulanan..."
                        ></textarea>
                        <div
                            v-if="form.errors.reason"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.reason }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600"
                            :disabled="form.processing"
                        >
                            {{ isAdmin ? "Simpan" : "Kirim Request" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

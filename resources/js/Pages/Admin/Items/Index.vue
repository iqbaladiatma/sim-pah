<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    items: Object,
    institutions: Array,
});

const form = useForm({
    name: "",
    institution_id: "",
    stock: 0,
    unit: "",
    min_stock: 0,
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
    } else {
        editingItem.value = null;
        form.reset();
        form.institution_id = "";
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
        form.put(route("admin.items.update", editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.items.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (id) => {
    if (confirm("Yakin ingin menghapus barang ini?")) {
        form.delete(route("admin.items.destroy", id));
    }
};
</script>

<template>
    <Head title="Manajemen Barang (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen Seluruh Barang (Inventaris)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add Button -->
                <div class="mb-6 flex justify-end">
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
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteItem(item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    {{ editingItem ? "Edit Barang" : "Tambah Barang Baru" }}
                </h3>

                <form @submit.prevent="submit">
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

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Stok</label
                            >
                            <input
                                v-model="form.stock"
                                type="number"
                                min="0"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Satuan</label
                            >
                            <input
                                v-model="form.unit"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            />
                        </div>
                    </div>

                    <div class="mb-4">
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

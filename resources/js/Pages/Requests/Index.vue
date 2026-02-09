<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";
import { ref, watch } from "vue";
import debounce from "lodash/debounce";
import Swal from "sweetalert2";

const props = defineProps({
    requests: Object,
    filters: Object,
});

const search = ref(props.filters.search);
const currentStatus = ref(props.filters.status || "all");

const updateFilters = debounce(() => {
    router.get(
        route("requests.index"),
        {
            search: search.value,
            status: currentStatus.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

watch([search, currentStatus], () => {
    updateFilters();
});

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "bg-amber-50 text-amber-600 border-amber-100";
        case "approved":
            return "bg-emerald-50 text-emerald-600 border-emerald-100";
        case "rejected":
            return "bg-rose-50 text-rose-600 border-rose-100";
        case "processed":
            return "bg-indigo-50 text-indigo-600 border-indigo-100";
        case "completed":
            return "bg-sky-50 text-sky-600 border-sky-100";
        default:
            return "bg-gray-50 text-gray-600 border-gray-100";
    }
};

const getStatusLabel = (status) => {
    const labels = {
        pending: "Tinjauan",
        processed: "Diproses",
        approved: "Disetujui",
        rejected: "Ditolak",
        completed: "Selesai",
    };
    return labels[status] || status;
};

const deleteRequest = (id) => {
    Swal.fire({
        title: "Batalkan Pengajuan?",
        text: "Silakan tuliskan alasan pembatalan pengajuan ini:",
        input: "textarea",
        inputPlaceholder: "Tulis alasan di sini...",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#C9A658",
        cancelButtonColor: "#f3f4f6",
        confirmButtonText: "Ya, Batalkan!",
        cancelButtonText: "Batal",
        inputValidator: (value) => {
            if (!value) {
                return "Alasan pembatalan wajib diisi!";
            }
        },
        customClass: {
            confirmButton:
                "text-white font-bold px-6 py-3 rounded-xl uppercase tracking-widest text-xs",
            cancelButton:
                "text-gray-500 font-bold px-6 py-3 rounded-xl uppercase tracking-widest text-xs border border-gray-200",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("requests.destroy", id), {
                data: { deletion_reason: result.value },
                onSuccess: () => {
                    Swal.fire({
                        title: "Dibatalkan!",
                        text: "Pengajuan Anda telah berhasil dibatalkan.",
                        icon: "success",
                        confirmButtonColor: "#C9A658",
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Pengajuan Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter decoration-pail-gold decoration-4"
                    >
                        Data <span class="text-pail-gold">Pengajuan</span>
                    </h2>
                    <p
                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2"
                    >
                        <span class="w-8 h-[1px] bg-pail-gold"></span>
                        Utilitas, Biaya & Darurat
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        v-if="
                            $page.props.system_settings?.allow_new_requests !==
                            '0'
                        "
                    >
                        <Link
                            :href="route('requests.create')"
                            class="px-8 py-4 bg-gray-900 text-pail-gold rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center gap-3 group border border-pail-gold/30 hover:scale-105 active:scale-95"
                        >
                            <div
                                class="w-6 h-6 rounded-full bg-pail-gold/10 flex items-center justify-center group-hover:bg-pail-gold/20 transition-colors"
                            >
                                <svg
                                    class="w-4 h-4 group-hover:rotate-90 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2.5"
                                        d="M12 4v16m8-8H4"
                                    ></path>
                                </svg>
                            </div>
                            Buat Pengajuan
                        </Link>
                    </div>
                    <div v-else class="flex flex-col items-end">
                        <div
                            class="px-8 py-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] flex items-center gap-3 cursor-not-allowed border border-gray-200 dark:border-gray-700 opacity-60"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                ></path>
                            </svg>
                            Fitur Ditutup
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="pt-8 pb-20">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                <!-- Advanced Filters -->
                <div
                    class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-xl rounded-[2.5rem] border border-white dark:border-gray-700 shadow-xl shadow-black/5 p-4"
                >
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <!-- Search -->
                        <div class="relative w-full lg:w-96 group">
                            <div
                                class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="h-4 w-4 text-gray-400 group-focus-within:text-pail-gold transition-colors"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="CARI PENGAJUAN..."
                                class="block w-full pl-14 pr-6 py-4 bg-white dark:bg-gray-900 border-none rounded-[1.8rem] text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-pail-gold shadow-inner"
                            />
                        </div>

                        <!-- Status Tabs -->
                        <div
                            class="flex-1 flex items-center bg-gray-100 dark:bg-gray-900 p-1.5 rounded-[2rem] w-full overflow-x-auto no-scrollbar"
                        >
                            <button
                                v-for="status in [
                                    'all',
                                    'pending',
                                    'processed',
                                    'approved',
                                    'rejected',
                                    'completed',
                                ]"
                                :key="status"
                                @click="currentStatus = status"
                                class="flex-1 min-w-[100px] py-3 px-4 rounded-[1.5rem] text-[9px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap"
                                :class="
                                    currentStatus === status
                                        ? 'bg-white dark:bg-gray-800 text-pail-gold shadow-lg shadow-black/5 scale-[1.02]'
                                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'
                                "
                            >
                                {{
                                    status === "all"
                                        ? "Semua"
                                        : getStatusLabel(status)
                                }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Requests Content -->
                <div class="space-y-6">
                    <!-- Statistics Bar (Subtle) -->
                    <div class="flex items-center justify-between px-8">
                        <span
                            class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em]"
                        >
                            Menampilkan {{ requests.data.length }} dari
                            {{ requests.total }} Pengajuan
                        </span>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1">
                                <span
                                    class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"
                                ></span>
                                <span
                                    class="text-[9px] font-black text-gray-500 uppercase tracking-widest"
                                    >REAL-TIME STATUS INDUCED</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Table View (Desktop) -->
                    <div
                        class="hidden md:block bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-2xl overflow-hidden transition-all duration-500"
                    >
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-50 dark:border-gray-700"
                                    >
                                        <th
                                            class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Identitas
                                        </th>
                                        <th
                                            class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Informasi Pengajuan
                                        </th>
                                        <th
                                            class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Estimasi Biaya
                                        </th>
                                        <th
                                            class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Status Progress
                                        </th>
                                        <th
                                            class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right"
                                        >
                                            Manajemen
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-50 dark:divide-gray-700/50"
                                >
                                    <tr
                                        v-for="req in requests.data"
                                        :key="req.id"
                                        class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/20 transition-all"
                                    >
                                        <!-- Identitas Column -->
                                        <td class="px-10 py-6">
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-gray-900 dark:bg-black p-[1px]"
                                                >
                                                    <div
                                                        class="w-full h-full rounded-2xl bg-gray-900 flex flex-col items-center justify-center border border-white/5"
                                                    >
                                                        <span
                                                            class="text-[8px] font-black text-pail-gold uppercase leading-none mb-0.5 opacity-70"
                                                            >{{
                                                                new Date(
                                                                    req.created_at,
                                                                ).toLocaleDateString(
                                                                    "id-ID",
                                                                    {
                                                                        month: "short",
                                                                    },
                                                                )
                                                            }}</span
                                                        >
                                                        <span
                                                            class="text-lg font-black text-white leading-none tracking-tighter"
                                                            >{{
                                                                new Date(
                                                                    req.created_at,
                                                                ).getDate()
                                                            }}</span
                                                        >
                                                    </div>
                                                </div>
                                                <div>
                                                    <div
                                                        class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest"
                                                    >
                                                        #{{
                                                            req.id
                                                                .toString()
                                                                .padStart(
                                                                    4,
                                                                    "0",
                                                                )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-[9px] font-bold text-pail-gold uppercase tracking-[0.2em] mt-0.5"
                                                    >
                                                        {{ req.type }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Informasi Column -->
                                        <td class="px-10 py-6">
                                            <div class="max-w-md">
                                                <p
                                                    class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight group-hover:text-pail-gold transition-colors leading-relaxed"
                                                >
                                                    {{ req.title }}
                                                </p>
                                                <p
                                                    class="text-[10px] text-gray-400 mt-1 line-clamp-1 italic font-medium leading-relaxed"
                                                >
                                                    "{{ req.description }}"
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Biaya Column -->
                                        <td class="px-10 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1"
                                                    >Total Pagu</span
                                                >
                                                <p
                                                    class="text-xs font-black text-gray-900 dark:text-white font-mono tracking-tighter"
                                                >
                                                    {{
                                                        formatRupiah(
                                                            req.estimated_cost,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Status Column -->
                                        <td class="px-10 py-6 text-center">
                                            <span
                                                class="px-4 py-1.5 text-[9px] font-black rounded-full border shadow-sm inline-flex items-center gap-2 uppercase tracking-widest"
                                                :class="
                                                    getStatusColor(req.status)
                                                "
                                            >
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-current opacity-80 animate-pulse"
                                                ></span>
                                                {{ getStatusLabel(req.status) }}
                                            </span>
                                        </td>

                                        <!-- Aksi Column -->
                                        <td class="px-10 py-6">
                                            <div
                                                class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0"
                                            >
                                                <!-- Detail -->
                                                <Link
                                                    :href="
                                                        route(
                                                            'requests.show',
                                                            req.id,
                                                        )
                                                    "
                                                    class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-400 hover:text-blue-500 flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-lg"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                        ></path>
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                        ></path>
                                                    </svg>
                                                </Link>

                                                <!-- View Photo -->
                                                <a
                                                    v-if="req.photo_evidence"
                                                    :href="`/storage/${req.photo_evidence}`"
                                                    target="_blank"
                                                    class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/20 text-sky-500 flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-lg shadow-sky-500/10"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        ></path>
                                                    </svg>
                                                </a>

                                                <!-- Delete -->
                                                <button
                                                    v-if="
                                                        req.status === 'pending'
                                                    "
                                                    @click="
                                                        deleteRequest(req.id)
                                                    "
                                                    class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-400 hover:text-rose-600 flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-lg shadow-rose-500/10"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="requests.data.length === 0">
                                        <td
                                            colspan="5"
                                            class="px-10 py-40 text-center"
                                        >
                                            <div
                                                class="flex flex-col items-center max-w-xs mx-auto"
                                            >
                                                <div
                                                    class="w-24 h-24 rounded-[2.5rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center mb-6"
                                                >
                                                    <svg
                                                        class="w-10 h-10 text-gray-200"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                                        ></path>
                                                    </svg>
                                                </div>
                                                <h4
                                                    class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-[0.2em] mb-2"
                                                >
                                                    Workspace Kosong
                                                </h4>
                                                <p
                                                    class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-relaxed"
                                                >
                                                    Tidak ditemukan data
                                                    pengajuan untuk kriteria
                                                    ini.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Cards (Enhanced) -->
                    <div class="md:hidden space-y-6">
                        <div
                            v-for="req in requests.data"
                            :key="req.id"
                            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden group"
                        >
                            <div class="p-8">
                                <div
                                    class="flex items-start justify-between mb-6"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center border border-pail-gold/20"
                                        >
                                            <span
                                                class="text-base font-black text-white leading-none tracking-tighter"
                                                >{{
                                                    new Date(
                                                        req.created_at,
                                                    ).getDate()
                                                }}</span
                                            >
                                        </div>
                                        <div>
                                            <span
                                                class="text-[8px] font-black uppercase text-gray-400 tracking-widest block"
                                                >{{ req.type }}</span
                                            >
                                            <span
                                                class="text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                                >#{{
                                                    req.id
                                                        .toString()
                                                        .padStart(4, "0")
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <span
                                        class="px-3 py-1 text-[8px] font-black rounded-full uppercase tracking-widest border shadow-sm"
                                        :class="getStatusColor(req.status)"
                                    >
                                        {{ getStatusLabel(req.status) }}
                                    </span>
                                </div>

                                <h3
                                    class="font-black text-gray-900 dark:text-white text-base uppercase tracking-tight mb-3"
                                >
                                    {{ req.title }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-6 leading-relaxed bg-gray-50 dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-50 dark:border-gray-700 italic"
                                >
                                    "{{ req.description }}"
                                </p>

                                <div
                                    class="flex items-center justify-between p-4 bg-pail-gold rounded-2xl mb-6 shadow-lg shadow-pail-gold/20"
                                >
                                    <span
                                        class="text-[8px] font-black text-white uppercase tracking-widest"
                                        >Estimasi Biaya</span
                                    >
                                    <span
                                        class="font-mono font-black text-white text-sm"
                                        >{{
                                            formatRupiah(req.estimated_cost)
                                        }}</span
                                    >
                                </div>

                                <div
                                    v-if="req.admin_note"
                                    class="mb-6 p-4 bg-amber-50 rounded-2xl border border-amber-100 border-l-4 border-l-amber-400"
                                >
                                    <span
                                        class="text-[8px] font-black text-amber-600 uppercase tracking-widest block mb-1"
                                        >Catatan Admin</span
                                    >
                                    <p
                                        class="text-[10px] text-amber-900 font-bold italic leading-relaxed"
                                    >
                                        "{{ req.admin_note }}"
                                    </p>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <Link
                                        :href="route('requests.show', req.id)"
                                        class="flex-1 py-4 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center transition-all active:scale-95"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            ></path>
                                        </svg>
                                    </Link>
                                    <a
                                        v-if="req.photo_evidence"
                                        :href="`/storage/${req.photo_evidence}`"
                                        target="_blank"
                                        class="flex-1 py-4 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center transition-all active:scale-95"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </a>

                                    <button
                                        v-if="req.status === 'pending'"
                                        @click="deleteRequest(req.id)"
                                        class="flex-1 py-4 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/10 transition-all active:scale-95"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination (Custom Styled) -->
                    <div
                        v-if="requests.links.length > 3"
                        class="flex justify-center mt-12 pb-10"
                    >
                        <div
                            class="flex items-center gap-2 bg-white dark:bg-gray-800 p-2 rounded-[2rem] shadow-xl border border-gray-50 dark:border-gray-700"
                        >
                            <template
                                v-for="(link, k) in requests.links"
                                :key="k"
                            >
                                <div
                                    v-if="link.url === null"
                                    class="px-4 py-2 text-[10px] font-black text-gray-300 uppercase tracking-widest"
                                    v-html="link.label"
                                ></div>
                                <Link
                                    v-else
                                    :href="link.url"
                                    class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                                    :class="
                                        link.active
                                            ? 'bg-pail-gold text-white shadow-lg shadow-pail-gold/30'
                                            : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                                    "
                                    v-html="link.label"
                                ></Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

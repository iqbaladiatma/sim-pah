<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";
import Swal from "sweetalert2";

const props = defineProps({
    request: Object,
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

const deleteRequest = () => {
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
        cancelButtonText: "Kembali",
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
            router.delete(route("requests.destroy", props.request.id), {
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
    <Head title="Detail Pengajuan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter"
                    >
                        Detail <span class="text-pail-gold">Pengajuan</span>
                    </h2>
                    <p
                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2"
                    >
                        <span class="w-8 h-[1px] bg-pail-gold"></span>
                        ID: #{{ request.id.toString().padStart(4, "0") }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('requests.index')"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-2xl hover:bg-gray-200 font-black text-[10px] uppercase tracking-widest transition-all"
                    >
                        &larr; Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-8 pb-20">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-2 space-y-8">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-[2.5rem] md:rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-2xl overflow-hidden"
                        >
                            <div class="p-6 md:p-14">
                                <!-- Status Badge -->
                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 sm:mb-10"
                                >
                                    <span
                                        class="px-5 py-2 text-[9px] font-black rounded-full border shadow-sm inline-flex items-center gap-2 uppercase tracking-[0.2em]"
                                        :class="getStatusColor(request.status)"
                                    >
                                        <span
                                            class="w-2 h-2 rounded-full bg-current opacity-80 animate-pulse"
                                        ></span>
                                        {{ getStatusLabel(request.status) }}
                                    </span>
                                    <span
                                        class="text-[9px] font-black text-gray-300 uppercase tracking-widest pl-1"
                                    >
                                        {{
                                            new Date(
                                                request.created_at,
                                            ).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "long",
                                                year: "numeric",
                                            })
                                        }}
                                    </span>
                                </div>

                                <h1
                                    class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-6 leading-tight"
                                >
                                    {{ request.title }}
                                </h1>

                                <div
                                    class="flex items-center gap-6 mb-10 pb-10 border-b border-gray-50 dark:border-gray-700"
                                >
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1"
                                            >Kategori</span
                                        >
                                        <span
                                            class="text-xs font-black text-pail-gold uppercase tracking-widest"
                                            >{{
                                                request.type.replace(/_/g, " ")
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="w-[1px] h-8 bg-gray-100 dark:bg-gray-700"
                                    ></div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1"
                                            >Estimasi Biaya</span
                                        >
                                        <span
                                            class="text-xl font-black text-gray-900 dark:text-white tracking-tighter"
                                            >{{
                                                formatRupiah(
                                                    request.estimated_cost,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <span
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-4"
                                            >Deskripsi Pengajuan</span
                                        >
                                        <div
                                            class="bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] p-8 border border-gray-50 dark:border-gray-700"
                                        >
                                            <p
                                                class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed font-medium"
                                            >
                                                {{ request.description }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="request.admin_note">
                                        <span
                                            class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] block mb-4"
                                            >Catatan Admin URT</span
                                        >
                                        <div
                                            class="bg-amber-50 rounded-[2rem] p-8 border border-amber-100 border-l-8 border-l-amber-400"
                                        >
                                            <p
                                                class="text-sm text-amber-900 leading-relaxed font-black italic"
                                            >
                                                "{{ request.admin_note }}"
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Evidence & Actions -->
                    <div class="space-y-8">
                        <!-- Evidence Card -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-2xl overflow-hidden p-8"
                        >
                            <span
                                class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-6 px-4"
                                >Foto Bukti</span
                            >

                            <div
                                v-if="request.photo_evidence"
                                class="relative group"
                            >
                                <img
                                    :src="`/storage/${request.photo_evidence}`"
                                    class="w-full aspect-square object-cover rounded-[2rem] shadow-xl transition-all group-hover:scale-[1.02]"
                                />
                                <a
                                    :href="`/storage/${request.photo_evidence}`"
                                    target="_blank"
                                    class="absolute inset-0 bg-black/40 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center"
                                >
                                    <span
                                        class="px-6 py-3 bg-white text-black text-[10px] font-black uppercase tracking-widest rounded-xl"
                                        >Lihat Fullscreen</span
                                    >
                                </a>
                            </div>
                            <div
                                v-else
                                class="w-full aspect-square bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex flex-col items-center justify-center text-gray-300 border-2 border-dashed border-gray-100 dark:border-gray-700"
                            >
                                <svg
                                    class="w-12 h-12 mb-4 opacity-20"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <span
                                    class="text-[8px] font-black uppercase tracking-[0.2em]"
                                    >Tidak Ada Foto</span
                                >
                            </div>
                        </div>

                        <!-- User Info -->
                        <div
                            class="bg-gray-900 dark:bg-black rounded-[3rem] shadow-2xl p-8 border border-white/5"
                        >
                            <span
                                class="text-[8px] font-black text-gray-500 uppercase tracking-[0.3em] block mb-6"
                                >Pengaju Sistem</span
                            >
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-pail-gold flex items-center justify-center text-white font-black"
                                >
                                    {{ request.user?.name.charAt(0) }}
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-black text-white uppercase tracking-wider"
                                    >
                                        {{ request.user?.name }}
                                    </p>
                                    <p
                                        class="text-[9px] font-bold text-pail-gold uppercase tracking-widest mt-0.5"
                                    >
                                        {{ request.institution?.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            v-if="request.status === 'pending'"
                            class="grid grid-cols-1 gap-4 pt-4"
                        >
                            <button
                                @click="deleteRequest"
                                class="w-full py-5 bg-rose-50 text-rose-600 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] text-center hover:bg-rose-100 transition-all border border-rose-100"
                            >
                                Batalkan Pengajuan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

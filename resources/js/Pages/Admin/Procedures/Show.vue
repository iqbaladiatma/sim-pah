<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, watch, computed, onMounted } from "vue";

// Use local icons from the project
import DownloadIcon from "@/Components/Icons/DownloadIcon.vue";
import UploadIcon from "@/Components/Icons/InboxIcon.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import TrashIcon from "@/Components/Icons/TrashIcon.vue";
import PencilIcon from "@/Components/Icons/EditIcon.vue";
import PhotographIcon from "@/Components/Icons/SparklesIcon.vue";
import XIcon from "@/Components/Icons/XIcon.vue";
import FolderIcon from "@/Components/Icons/FolderIcon.vue";
import LocationIcon from "@/Components/Icons/LocationIcon.vue";
import UserIcon from "@/Components/Icons/UserIcon.vue";
import DocumentIcon from "@/Components/Icons/DocumentIcon.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";
import BudgetPlanForm from "./Partials/BudgetPlanForm.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import Modal from "@/Components/Modal.vue";
import ImportMappingModal from "@/Components/ImportMappingModal.vue";
import axios from "axios";

// DEBUG & EXPORT STATE
const debugMode = ref(false); // Default disabled for production
const debugLogs = ref([]);
const showExportProgressModal = ref(false);
const exportProgress = ref(0);

const addLog = (msg) => {
    if (!debugMode.value) return;
    const time = new Date().toLocaleTimeString();
    debugLogs.value.unshift(`[${time}] ${msg}`);
};

const props = defineProps({
    type: String,
    procedure: Object,
    data: Object,
    rooms: Array,
    institutions: Array,
    items: Array,
    users: Array,
    vehicles: Array,
    filters: Object,
});

const grandTotal = computed(() => {
    return props.data.data.reduce(
        (sum, item) => sum + (parseFloat(item.total_price) || 0),
        0,
    );
});

const tableColspan = computed(() => {
    const t = props.type;
    if (t === "buku-induk") return 20;
    if (t === "berita-acara-pemeriksaan") return 20;
    if (t === "kir-ruangan") return 14;
    if (t === "pendataan-aset") return 13;
    if (t === "pemeliharaan-gedung") return 18;
    if (t === "pemeliharaan-ac") return 10;
    if (t === "pemeliharaan-kamar-mandi") return 16;
    if (["pemeliharaan-pompa", "pemeliharaan-air-bersih"].includes(t))
        return 42;
    if (["pemeliharaan-air-minum", "pemeliharaan-genset"].includes(t))
        return 30;
    if (t === "pemeliharaan-kipas") return 9;
    if (t === "pemeliharaan-septik") return 11;
    if (t === "pemeliharaan-sarpras") return 10;
    if (t === "agenda-perbaikan") return 8;
    if (t === "pengajuan-rab") return 9;
    if (t === "analisis-kebutuhan") return 7;
    if (["pengadaan-sarpras", "penerimaan-barang"].includes(t)) return 10;
    if (t === "penyerahan-barang") return 8;
    if (t === "jadwal-token") return 9;
    if (t === "pemeliharaan-kebersihan") return 17;
    if (t === "detailed-monitoring") return 26;
    if (t === "weekly-activity") return 10;
    if (t === "vehicle-log") return 10;
    if (t === "pemeliharaan-listrik" || t === "electrical-maintenance")
        return 18;
    if (t === "kelengkapan-alat") return 9;
    if (t === "monitoring-kebersihan") return 17;
    if (t === "monitoring-aset") return 11;
    if (t === "pemilihan-evaluasi") return 6;
    if (t === "pelelangan-aset") return 9;
    if (t === "peminjaman-barang") return 11;
    if (t === "laporan-proyek") return 11;
    if (t === "rekapan-pengajuan") return 10;
    if (t === "penggunaan-kendaraan") return 8;
    if (t === "parkir-area") return 8;
    if (t === "ceklist-iso") return 7;
    if (["pab-tahunan", "visi-2030"].includes(t)) return 6;
    if (t.includes("kendaraan")) return 5;
    return 7; // default
});

const formatDate = (dateString, withDay = false) => {
    if (!dateString) return "-";

    // If it's a simple YYYY-MM-DD, it might be parsed as UTC and shift the day.
    // We append a time or use a different separator to force local time parsing.
    let normalized = dateString;
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        normalized = dateString.replace(/-/g, "/");
    }

    try {
        const date = new Date(normalized);
        if (isNaN(date.getTime())) return dateString;

        const options = {
            day: "numeric",
            month: "short",
            year: "numeric",
        };

        if (withDay) {
            options.weekday = "long";
        }

        // Use Indonesian format (e.g., Sabtu, 7 Feb 2026)
        return date.toLocaleDateString("id-ID", options);
    } catch (e) {
        return dateString;
    }
};

/**
 * Money / Currency Formatter
 */
const formatRupiah = (val) => {
    if (val === null || val === undefined || val === "") return "";
    const number =
        typeof val === "string" ? parseFloat(val.replace(/[^0-9.-]/g, "")) : val;
    if (isNaN(number)) return "";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};

const handleRupiahInput = (event, field) => {
    // Get raw digits
    const rawValue = event.target.value.replace(/[^0-9]/g, "");
    const numericValue = rawValue ? parseInt(rawValue) : 0;

    // Update the Inertia form state (as raw number)
    form[field] = numericValue;

    // Force update the input display value immediately to formatted version
    event.target.value = formatRupiah(numericValue);
};

const airBersihItems = [
    {
        title: "Sumber air bersih",
        standard: "Debit air yang cukup",
        method: "Alat Ukur ( ltr / dtk )",
        frequency: "Harian",
    },
    {
        title: "Instalasi pipa dan Stop keran distribusi",
        standard: "Tidak bocor dan tersumbat, Stopkeran tidak rusak",
        method: "Visual",
        frequency: "Harian",
    },
    {
        title: "Tandon",
        standard: "Tidak bocor, bersih dari endapan, bebas lumut",
        method: "Visual",
        frequency: "Harian",
    },
    {
        title: "Kualitas air",
        standard: "Jernih dan tidak berbau",
        method: "Visual",
        frequency: "Harian",
    },
    {
        title: "Keran",
        standard: "Keran tidak bocor / rusak",
        method: "Visual",
        frequency: "Harian",
    },
];

const pompaItems = [
    {
        title: "Konektor",
        standard: "Tersambung Baik",
        method: "Visual",
        frequency: "per 6 bulan",
    },
    {
        title: "Relay",
        standard: "Berfungsi Baik",
        method: "Tes Fungsi",
        frequency: "Bulanan",
    },
    {
        title: "Kontaktor",
        standard: "Berfungsi Baik",
        method: "Tes Fungsi",
        frequency: "Bulanan",
    },
    {
        title: "MCB",
        standard: "Tidak melebihi beban",
        method: "Alat Ukur",
        frequency: "Bulanan",
    },
    {
        title: "Lampu Indikator",
        standard: "Menyala",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Kebersihan Panel",
        standard: "Tidak Berdebu, Tidak Basah/Lembab",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Kebocoran Body Pompa",
        standard: "Terdapat arus bocor",
        method: "Alat Ukur",
        frequency: "Bulanan",
    },
    {
        title: "Kapasitor",
        standard: "Sesuai Spesifikasi",
        method: "Alat Ukur",
        frequency: "Bulanan",
    },
];

const airMinumItems = [
    {
        title: "Sumber air minum",
        standard: "Debit air yang cukup",
        method: "Alat Ukur ( ltr / dtk )",
        frequency: "Harian",
    },
    {
        title: "Sistem filterisasi",
        standard: "lampu UV dalam masa aktif, filter brfungsi dengan baik",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Tangki air minum",
        standard: "Bebas lumut dan kotoran",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Kualitas Air Minum",
        standard: "Sesuai Standar Mutu air minum Permenkes No.2 Tahun 2023",
        method: "Uji lab",
        frequency: "6 Bulanan",
    },
];

const gensetItems = [
    {
        title: "Push button, push lamp,tombol emergency",
        standard: "Berfungsi baik, tidak pecah",
        method: "Manual",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Panel MCB",
        standard: "Berfungsi baik, Listrik ON",
        method: "Pengukuran",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Lampu indikator M/C",
        standard: "Berfungsi baik,lampu indikator menyala",
        method: "Visual",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Dinamo stater",
        standard: "Berfungsi baik dan tidak macet",
        method: "Tes fungsi",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Oli Pelumas",
        standard: "Level diantara batas max & min,cek filter oli",
        method: "Visual",
        frequency: "2 Minggu sekali",
    },
    {
        title: "BBM solar",
        standard: "Indikator volume bbm",
        method: "Visual",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Sistem pembuangan",
        standard: "Kepekatan asap yang dihasilkan",
        method: "Visual",
        frequency: "2 Minggu sekali",
    },
    {
        title: "Filter udara",
        standard: "Ketebalan debu tersaring",
        method: "Manual",
        frequency: "Bulanan",
    },
    {
        title: "Filter solar",
        standard: "Berfungsi baik and tidak berlumpur",
        method: "Manual",
        frequency: "Bulanan",
    },
    {
        title: "Radiator",
        standard: "level cairan pendingin mesin cukup",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Charger aki",
        standard: "Voltase pengisian aki normal",
        method: "Alat ukur",
        frequency: "Bulanan",
    },
    {
        title: "Accu/batre",
        standard: "Voltase aki normal",
        method: "Alat ukur",
        frequency: "Bulanan",
    },
];

const listrikItems = [
    {
        title: "Pemeriksaan instalasi",
        standard: "Kabel tidak terkelupas, konek bagus",
        method: "Visual",
        frequency: "3 Bulan",
    },
    {
        title: "Pemeriksaan panel listrik",
        standard: "Tidak ada komponen terbakar",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Pengecekan arus listrik",
        standard: "Stabil di 220 Volt",
        method: "Alat ukur",
        frequency: "Bulanan",
    },
    {
        title: "Pengecekan ampere meter",
        standard: "Berfungsi normal",
        method: "Visual",
        frequency: "Bulanan",
    },
    {
        title: "Pengecekan volt meter",
        standard: "Berfungsi normal",
        method: "Visual",
        frequency: "Bulanan",
    },
];

const fiscalYears = [
    "2024 - 2025",
    "2025 - 2026",
    "2026 - 2027",
    "2027 - 2028",
];

// Unit Options for Satuan Dropdown
const unitOptions = ref([
    "Buah",
    "Unit",
    "Pcs",
    "Set",
    "Zak",
    "Lonjor",
    "Meter",
    "Kg",
    "Liter",
    "Dus",
    "Box",
    "Karton",
]);

// Petugas Options - Default list with option to add custom
const petugasOptions = ref([
    "Admin",
    "Admin URT",
    "Admin Sarpras",
    "Teknisi Listrik",
    "Teknisi AC",
    "Petugas Kebersihan",
    "Security",
    "Koordinator Gedung",
]);

const handlePeriodicItemChange = () => {
    let selected = null;
    if (props.type === "pemeliharaan-air-bersih") {
        selected = airBersihItems.find((i) => i.title === form.title);
    } else if (props.type === "pemeliharaan-pompa") {
        selected = pompaItems.find((i) => i.title === form.title);
    } else if (props.type === "pemeliharaan-air-minum") {
        selected = airMinumItems.find((i) => i.title === form.title);
    } else if (props.type === "pemeliharaan-genset") {
        selected = gensetItems.find((i) => i.title === form.title);
    } else if (props.type === "pemeliharaan-listrik") {
        selected = listrikItems.find((i) => i.title === form.title);
    }

    if (selected) {
        if (props.type === "pemeliharaan-listrik") {
            form.specification = selected.standard;
            form.action_taken = selected.method;
            form.frequency = selected.frequency;
        } else {
            form.check_standard = selected.standard;
            form.check_method = selected.method;
            form.check_frequency = selected.frequency;
        }
    }
};

// Simplified handlers for createable selects
const handleUnitCreate = (val) => {
    // Add to options locally so it appears selected immediately (optional, SearchableSelect handles display)
    if (!unitOptions.includes(val)) {
        unitOptions.push(val);
    }
    form.unit = val;
};

const handlePetugasCreate = (val) => {
    if (!petugasOptions.value.includes(val)) {
        petugasOptions.value.push(val);
    }
    form.performed_by = val;
};

// Condition Options and Handlers
const beforeConditionOptions = ref([
    "Baik",
    "Rusak Ringan",
    "Rusak Berat",
    "Perlu Pemeliharaan",
    "Kotor",
    "Bersih",
    "Kurang Terawat",
]);

const afterConditionOptions = ref([
    "Baik",
    "Berfungsi Normal",
    "Bersih",
    "Selesai Diperbaiki",
    "Dalam Pengawasan",
    "Perlu Tindak Lanjut",
]);

const handleBeforeConditionCreate = (val) => {
    if (!beforeConditionOptions.value.includes(val)) {
        beforeConditionOptions.value.push(val);
    }
    form.before_condition = val;
};

const handleAfterConditionCreate = (val) => {
    if (!afterConditionOptions.value.includes(val)) {
        afterConditionOptions.value.push(val);
    }
    form.after_condition = val;
};

const showCreateModal = ref(false);
const isEdit = ref(false);
const editId = ref(null);

const form = useForm({
    title: "",
    other_title: "",
    description: "",
    subcategory: "",
    location: "",
    type: "",
    name: "", // Added missing field
    note: "", // Added missing field
    cost: 0,
    scheduled_at: "",
    status: "pending",
    photo_evidence: null,
    institution_id: "",
    room_id: "",
    vehicle_id: "",
    // ISO Standard Fields
    code: "",
    brand: "",
    specification: "",
    material: "",
    purchased_at: "",
    source: "",
    price: 0,
    condition: "B",
    condition_notes: "",
    before_condition: "",
    after_condition: "",
    action_taken: "",
    // Additional Buku Induk Fields
    no_urut_satker: "",
    no_urut_pondok: "",
    depreciation_price: 0,
    responsible_person: "",
    stock: 1,
    unit: "Buah",
    received_at: "",
    serial_number: "",
    // Electrical Maintenance Fields
    monthly_data: {},
    period_year: new Date().getFullYear().toString(),
    frequency: "Bulanan",
    size: "",
    // Specialized Maintenance Fields (Gedung, AC, Kamar Mandi)
    check_standard: "",
    check_method: "",
    check_frequency: "",
    year: new Date().getFullYear(),
    jul: "",
    aug: "",
    sep: "",
    oct: "",
    nov: "",
    dec: "",
    jan: "",
    feb: "",
    mar: "",
    apr: "",
    may: "",
    jun: "",
    // Procurement & Supplier Fields
    procurement_type: "",
    supplier_address: "",
    supplier_contact: "",
    supplier_product: "",
    sc_price: 0,
    sc_quality: 0,
    sc_delivery: 0,
    sc_service: 0,
    sc_legal: 0,
    sc_total: 0,
    // Bathroom Fields
    kran_air: "",
    lampu: "",
    fiting_lampu: "",
    saklar_lampu: "",
    ember: "",
    gayung: "",
    closet: "",
    pintu: "",
    grendel_pintu: "",
    ac_indoor_pc: "",
    ac_indoor_sw: "",
    ac_outdoor_freon: "",
    ac_outdoor_amp: "",
    ac_kelistrikan_jaringan: "",
    ac_kelistrikan_tegangan: "",
    ac_divisi: "",
    ac_tgl_bln: "",
    // Kipas Fields
    fan_type: "",
    // Septik Fields
    st_baik: "",
    st_penuh: "",
    st_bocor: "",
    st_bau: "",
    // Pump Fields
    ...[
        "jul",
        "aug",
        "sep",
        "oct",
        "nov",
        "dec",
        "jan",
        "feb",
        "mar",
        "apr",
        "may",
        "jun",
    ].reduce((acc, m) => {
        ["putra", "putri", "lawata"].forEach((a) => (acc[`${m}_${a}`] = ""));
        return acc;
    }, {}),
    check_standard: "",
    check_method: "",
    check_frequency: "",
    year: new Date().getFullYear(),
    jul_status: "",
    aug_status: "",
    sep_status: "",
    oct_status: "",
    nov_status: "",
    dec_status: "",
    jan_status: "",
    feb_status: "",
    mar_status: "",
    apr_status: "",
    may_status: "",
    jun_status: "",
    performed_by: "Admin URT",
    completed_at: new Date(
        new Date().getTime() - new Date().getTimezoneOffset() * 60000,
    )
        .toISOString()
        .split("T")[0],
    request_date: new Date(
        new Date().getTime() - new Date().getTimezoneOffset() * 60000,
    )
        .toISOString()
        .split("T")[0],
    damage_type: "",
    follow_up_date: "",
    remarks: "",
    volume: 0,
    unit: "",
    unit_price: 0,
    total_price: 0,
    budget_amount: 0,
    actual_amount: 0,
    attainment_percentage: 0,
    team_members: "",
    responsible_person: "",
    item_id: "",
    user_id: "",
    institution_id: "",
    quantity: 1,
    borrow_date: new Date(
        new Date().getTime() - new Date().getTimezoneOffset() * 60000,
    )
        .toISOString()
        .split("T")[0],
    borrow_condition: "",
    borrower_paraf: "",
    actual_return_date: "",
    return_condition: "",
    returner_paraf: "",
    value: 0,
    reason: "",
    action_date: new Date(
        new Date().getTime() - new Date().getTimezoneOffset() * 60000,
    )
        .toISOString()
        .split("T")[0],
    // Parking Log Fields
    vehicle_type: "Motor",
    plate_number: "",
    owner_name: "",
    entry_time: "",
    exit_time: "",
    gate_name: "",
    // ISO Checklist Fields
    category: "",
    frequency: "daily",
    items: [],
    is_active: true,
    // Vehicle Request Fields
    destination: "",
    purpose: "",
    start_time: "",
    end_time: "",
    start_mileage: null,
    end_mileage: null,
    admin_note: "",
    institution_name: "",
    time_range: "",
    fuel_level_before: "",
    condition_before: "",
});

watch([() => form.volume, () => form.unit_price], ([vol, price]) => {
    form.total_price = parseFloat((vol * price).toFixed(2));
});

watch(
    [
        () => form.sc_price,
        () => form.sc_quality,
        () => form.sc_delivery,
        () => form.sc_service,
        () => form.sc_legal,
    ],
    ([p, q, d, s, l]) => {
        const total =
            Number(p) * 0.25 +
            Number(q) * 0.3 +
            Number(d) * 0.2 +
            Number(s) * 0.15 +
            Number(l) * 0.1;
        form.sc_total = parseFloat(total.toFixed(2));
    },
);

watch(
    [() => form.budget_amount, () => form.actual_amount],
    ([budget, actual]) => {
        if (budget > 0) {
            form.attainment_percentage = parseFloat(
                ((actual / budget) * 100).toFixed(2),
            );
        } else {
            form.attainment_percentage = 0;
        }
    },
);

const newItem = ref("");
const addItem = () => {
    if (newItem.value.trim()) {
        form.items.push(newItem.value.trim());
        newItem.value = "";
    }
};
const removeItem = (index) => {
    form.items.splice(index, 1);
};

const openCreateModal = () => {
    isEdit.value = false;
    editId.value = null;
    form.reset();

    // ISO Checklist Helpers
    newItem.value = "";

    // Auto-fill time for Parking Log
    if (props.type === "parkir-area") {
        form.entry_time = new Date(
            new Date().getTime() - new Date().getTimezoneOffset() * 60000,
        )
            .toISOString()
            .slice(0, 16);
    }

    if (props.type === "penggunaan-kendaraan") {
        form.start_time = new Date(
            new Date().getTime() - new Date().getTimezoneOffset() * 60000,
        )
            .toISOString()
            .slice(0, 16);
    }

    if (props.type === "registrasi-kendaraan") {
        form.status = "available";
        form.type = "Mobil";
    }

    showCreateModal.value = true;
};

const openEditModal = (item) => {
    isEdit.value = true;
    editId.value = item.id;
    form.reset();

    // Fill form with item data
    Object.keys(form.data()).forEach((key) => {
        if (Object.prototype.hasOwnProperty.call(item, key)) {
            form[key] = item[key];
        }
    });

    // Special handles
    if (item.institution_id) form.institution_id = item.institution_id;
    if (item.room_id) form.room_id = item.room_id;
    if (item.vehicle_id) form.vehicle_id = item.vehicle_id;

    // Ensure custom Unit and Petugas are in options
    if (item.unit && !unitOptions.value.includes(item.unit)) {
        unitOptions.value.push(item.unit);
    }
    if (
        item.performed_by &&
        !petugasOptions.value.includes(item.performed_by)
    ) {
        petugasOptions.value.push(item.performed_by);
    }

    // Ensure custom Condition values are in options
    if (
        item.before_condition &&
        !beforeConditionOptions.value.includes(item.before_condition)
    ) {
        beforeConditionOptions.value.push(item.before_condition);
    }
    if (
        item.after_condition &&
        !afterConditionOptions.value.includes(item.after_condition)
    ) {
        afterConditionOptions.value.push(item.after_condition);
    }

    if (props.type === "pemeliharaan-listrik") {
        form.monthly_data = item.monthly_data || {};
    }

    // Format dates for parkir-area (datetime-local needs YYYY-MM-DDTHH:mm)
    if (props.type === "parkir-area") {
        if (item.entry_time)
            form.entry_time = new Date(
                new Date(item.entry_time).getTime() -
                    new Date().getTimezoneOffset() * 60000,
            )
                .toISOString()
                .slice(0, 16);
        if (item.exit_time)
            form.exit_time = new Date(
                new Date(item.exit_time).getTime() -
                    new Date().getTimezoneOffset() * 60000,
            )
                .toISOString()
                .slice(0, 16);
    }

    // Format dates for penggunaan-kendaraan
    if (props.type === "penggunaan-kendaraan") {
        if (item.start_time)
            form.start_time = new Date(
                new Date(item.start_time).getTime() -
                    new Date().getTimezoneOffset() * 60000,
            )
                .toISOString()
                .slice(0, 16);
        if (item.end_time)
            form.end_time = new Date(
                new Date(item.end_time).getTime() -
                    new Date().getTimezoneOffset() * 60000,
            )
                .toISOString()
                .slice(0, 16);
    }

    showCreateModal.value = true;
};

const submit = () => {
    if (props.type === "pemeliharaan-listrik" && form.title === "Lainnya") {
        form.title = form.other_title;
    }

    if (isEdit.value) {
        form.put(
            route("admin.procedures.update", {
                type: props.type,
                id: editId.value,
            }),
            {
                onSuccess: () => {
                    showCreateModal.value = false;
                    form.reset();
                },
            },
        );
    } else {
        form.post(route("admin.procedures.store", props.type), {
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            },
        });
    }
};

const showConfirmModal = ref(false);
const itemToDelete = ref(null);

const deleteItem = (id) => {
    itemToDelete.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = () => {
    if (itemToDelete.value) {
        form.delete(
            route("admin.procedures.destroy", {
                type: props.type,
                id: itemToDelete.value,
            }),
            {
                onSuccess: () => {
                    showConfirmModal.value = false;
                    itemToDelete.value = null;
                },
            },
        );
    }
};

const exportExcel = async () => {
    addLog("Memulai proses export...");
    showExportProgressModal.value = true;
    exportProgress.value = 0;

    try {
        addLog(`Requesting export for type: ${props.type}`);
        const response = await axios.get(route("admin.procedures.export", props.type), {
            responseType: 'blob',
            onDownloadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    exportProgress.value = percentCompleted;
                    addLog(`Download progress: ${percentCompleted}%`);
                } else {
                    addLog(`Download bytes: ${progressEvent.loaded}`);
                }
            }
        });

        addLog("Data diterima, memproses file...");
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        
        // Try to get filename from headers
        const contentDisposition = response.headers['content-disposition'];
        let fileName = `${props.procedure.title}_${new Date().toISOString().slice(0,10)}.xlsx`;
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
            if (fileNameMatch && fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
        }
        
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
        
        addLog("File berhasil didownload.");
        setTimeout(() => {
            showExportProgressModal.value = false;
        }, 800);

    } catch (error) {
        console.error(error);
        addLog("EXPORT ERROR: " + (error.response?.data?.message || error.message));
        alert("Gagal melakukan export. Cek debug log.");
        showExportProgressModal.value = false;
    }
};

const showImportModal = ref(false);
const importHeaders = ref([]);
const importFields = ref([]);
const importSheets = ref([]);
const isAnalyzing = ref(false);

const openImportModal = () => {
    addLog("Membuka modal import...");
    // Reset state for fresh start
    importHeaders.value = [];
    importFields.value = [];
    importSheets.value = [];
    showImportModal.value = true;
};

const onAnalyze = ({ file, sheet }) => {
    addLog(`Menganalisa file: ${file.name} (Sheet: ${sheet || 'Default'})`);
    isAnalyzing.value = true;

    const formData = new FormData();
    formData.append("file", file);
    formData.append("type", props.type);
    if (sheet) formData.append("sheet", sheet);

    axios
        .post(route("admin.procedures.check_import_headers"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then((response) => {
            addLog("Analisa berhasil. Headers diterima.");
            importHeaders.value = response.data.headers;
            importFields.value = response.data.fields;
            importSheets.value = response.data.sheets || [];
            addLog(`Deteksi ${importHeaders.value.length} kolom dan ${importSheets.value.length} sheet.`);
        })
        .catch((error) => {
            console.error(error);
            const errMsg = error.response?.data?.error || error.message;
            addLog("ANALYSIS ERROR: " + errMsg);
            alert("Gagal membaca file: " + errMsg);
        })
        .finally(() => {
            isAnalyzing.value = false;
        });
};

const handleImportSubmit = ({ file, mapping, sheet }) => {
    addLog("Mengirim data import...");
    const importForm = useForm({
        file: file,
        mapping: mapping,
        sheet: sheet,
    });

    importForm.post(route("admin.procedures.import", props.type), {
        onSuccess: () => {
            addLog("Import berhasil diselesaikan.");
            showImportModal.value = false;
            // Success notification is handled by flash messages usually
        },
        onError: (errors) => {
            addLog("IMPORT ERROR: " + JSON.stringify(errors));
            console.error(errors);
        },
    });
};
</script>

<template>
    <Head :title="procedure.title" />

    <ConfirmModal
        :show="showConfirmModal"
        title="Hapus Data"
        message="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan."
        confirm-text="Ya, Hapus"
        cancel-text="Batal"
        variant="danger"
        @close="showConfirmModal = false"
        @confirm="confirmDelete"
    />

    <ImportMappingModal
        :show="showImportModal"
        :processing="isAnalyzing"
        :file-headers="importHeaders"
        :sheets="importSheets"
        :required-fields="importFields"
        :template-url="route('admin.procedures.template', props.type)"
        @close="showImportModal = false"
        @analyze="onAnalyze"
        @submit="handleImportSubmit"
    />

    <!-- EXPORT PROGRESS MODAL -->
    <Modal :show="showExportProgressModal" :closeable="false" maxWidth="sm">
        <div class="p-6 text-center">
            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase mb-4">
                Exporting Data...
            </h3>
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4 dark:bg-gray-700 overflow-hidden">
                <div 
                    class="bg-pail-gold h-4 rounded-full transition-all duration-300 ease-out"
                    :style="{ width: exportProgress + '%' }"
                ></div>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                {{ exportProgress }}% Completed
            </p>
            <p class="text-[10px] text-gray-400 mt-2">
                Mohon tunggu, sedang memproses file excel...
            </p>
        </div>
    </Modal>

    <!-- DEBUG PANEL (TEMPORARY) -->
    <div v-if="debugMode" class="fixed bottom-0 left-0 right-0 bg-gray-900 text-green-400 p-4 z-[999] border-t border-gray-700 shadow-2xl font-mono text-xs max-h-64 overflow-y-auto opacity-95">
        <div class="flex justify-between items-center mb-2 border-b border-gray-700 pb-1 sticky top-0 bg-gray-900">
            <span class="font-bold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                SYSTEM DEBUG LOGGER
            </span>
            <div class="flex gap-3">
                <button @click="debugMode = false" class="text-xs hover:text-white hover:underline">[HIDE]</button>
                <button @click="debugLogs = []" class="text-xs hover:text-white hover:underline">[CLEAR]</button>
            </div>
        </div>
        <div class="flex flex-col-reverse">
            <div v-for="(log, i) in debugLogs" :key="i" class="mb-0.5 break-words whitespace-pre-wrap border-b border-gray-800/50 pb-0.5">
                {{ log }}
            </div>
        </div>
        <div v-if="debugLogs.length === 0" class="text-gray-600 italic py-2 text-center">Waiting for system events...</div>
    </div>

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-4"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.procedures.index')"
                        class="w-9 h-9 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-pail-gold shadow-sm transition-all shrink-0 border border-gray-100 dark:border-gray-700"
                    >
                        &larr;
                    </Link>
                    <div>
                        <h2
                            class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight"
                        >
                            {{ procedure.title }}
                        </h2>
                        <p
                            class="text-[9px] font-black text-pail-gold uppercase tracking-widest mt-0.5"
                        >
                            Management Console
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div
                        v-if="type === 'pemeliharaan-listrik'"
                        class="flex flex-wrap items-center gap-2 mr-2"
                    >
                        <div
                            class="px-3 py-1.5 bg-pail-gold/10 rounded-lg border border-pail-gold/20 flex items-center gap-2"
                        >
                            <span
                                class="text-[8px] font-black text-pail-gold uppercase tracking-widest"
                                >TAHUN:</span
                            >
                            <select
                                :value="
                                    filters.year || new Date().getFullYear()
                                "
                                @change="
                                    (e) =>
                                        $inertia.get(
                                            route(
                                                'admin.procedures.show',
                                                type,
                                            ),
                                            {
                                                ...filters,
                                                year: e.target.value,
                                            },
                                            {
                                                preserveState: true,
                                                replace: true,
                                            },
                                        )
                                "
                                class="bg-transparent border-0 p-0 text-[10px] font-black text-gray-900 dark:text-white uppercase focus:ring-0 cursor-pointer"
                            >
                                <option
                                    v-for="y in [2024, 2025, 2026, 2027, 2028]"
                                    :key="y"
                                    :value="y"
                                >
                                    {{ y }}
                                </option>
                            </select>
                        </div>
                        <div
                            class="px-3 py-1.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 flex items-center gap-2"
                        >
                            <span
                                class="text-[8px] font-black text-gray-400 uppercase tracking-widest"
                                >LOKASI:</span
                            >
                            <input
                                type="text"
                                :value="filters.location"
                                @input="
                                    (e) =>
                                        $inertia.get(
                                            route(
                                                'admin.procedures.show',
                                                type,
                                            ),
                                            {
                                                ...filters,
                                                location: e.target.value,
                                            },
                                            {
                                                preserveState: true,
                                                replace: true,
                                                preserveScroll: true,
                                            },
                                        )
                                "
                                placeholder="Cari Lokasi..."
                                class="bg-transparent border-0 p-0 text-[10px] font-black text-gray-900 dark:text-white placeholder:text-gray-300 focus:ring-0 w-24 sm:w-32"
                            />
                        </div>
                    </div>
                    <div
                        v-if="type === 'pemeliharaan-septik'"
                        class="px-3 py-1.5 bg-pail-gold/10 rounded-lg border border-pail-gold/20 flex items-center gap-2 mr-1"
                    >
                        <span
                            class="text-[8px] font-black text-pail-gold uppercase tracking-widest"
                            >TGL:</span
                        >
                        <span
                            class="text-[9px] font-black text-gray-900 dark:text-white uppercase"
                            >{{ formatDate(new Date()) }}</span
                        >
                    </div>
                    <button
                        @click="exportExcel"
                        class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-black text-gray-600 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm"
                    >
                        <DownloadIcon class="w-3.5 h-3.5" />
                        <span>Export</span>
                    </button>
                    <button
                        @click="openImportModal"
                        class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-black text-gray-600 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm cursor-pointer"
                    >
                        <UploadIcon class="w-3.5 h-3.5" />
                        <span>Import</span>
                    </button>
                    <a
                        :href="route('admin.procedures.template', type)"
                        download
                        class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-black text-pail-gold uppercase tracking-widest hover:bg-pail-gold/5 transition-all flex items-center gap-2 shadow-sm"
                    >
                        <DocumentIcon class="w-3.5 h-3.5 text-pail-gold" />
                        <span>Template</span>
                    </a>
                    <button
                        @click="showCreateModal = true"
                        class="px-4 py-2 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all flex items-center gap-2 shadow-lg shadow-gray-200 dark:shadow-none hidden md:flex"
                    >
                        <PlusIcon class="w-3.5 h-3.5" /> Tambah Data
                    </button>
                    <button
                        @click="showCreateModal = true"
                        class="fixed bottom-10 right-8 z-50 px-6 py-3 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-110 transition-all flex items-center gap-2 shadow-2xl shadow-black/30 border border-pail-gold/20 md:hidden"
                    >
                        <PlusIcon class="w-4 h-4" /> Tambah
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="max-w-7xl mx-auto">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden"
                >
                    <!-- Mobile Card View -->
                    <div class="md:hidden grid grid-cols-1 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50">
                        <div v-for="item in data.data" :key="item.id" class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all">
                            
                            <!-- Status Indicator Strip -->
                            <div class="absolute left-0 top-0 bottom-0 w-1.5" :class="{
                                'bg-emerald-500': item.status === 'Completed' || item.status === 'Selesai' || item.status === 'approved' || item.condition === 'B',
                                'bg-amber-500': item.status === 'Pending' || item.status === 'pending' || item.condition === 'KB',
                                'bg-rose-500': item.status === 'Rejected' || item.status === 'rejected' || item.condition === 'RB' || item.condition === 'R',
                                'bg-blue-500': !item.status && !item.condition
                            }"></div>

                            <div class="pl-3">
                                <!-- Top Row: Date & Status -->
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 px-2 py-1 rounded-lg">
                                        {{ formatDate(item.created_at || item.date) }}
                                    </span>
                                    <span 
                                        v-if="item.status"
                                        class="px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest"
                                        :class="{
                                            'bg-emerald-100 text-emerald-600': ['Completed','Selesai','approved'].includes(item.status),
                                            'bg-amber-100 text-amber-600': ['Pending','pending','process'].includes(item.status),
                                            'bg-rose-100 text-rose-600': ['Rejected','rejected'].includes(item.status)
                                        }"
                                    >
                                        {{ item.status }}
                                    </span>
                                </div>

                                <!-- Main Title -->
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-snug mb-2 line-clamp-2">
                                    {{ item.title || item.name || item.description || 'No Title' }}
                                </h3>

                                <!-- Sub-details -->
                                <div class="space-y-2 mb-4">
                                    <div v-if="item.institution" class="flex items-center gap-2">
                                        <FolderIcon class="w-3.5 h-3.5 text-gray-400" />
                                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-300 truncate">
                                            {{ item.institution.name || item.institution }}
                                        </span>
                                    </div>
                                    <div v-if="item.location || item.room" class="flex items-center gap-2">
                                        <LocationIcon class="w-3.5 h-3.5 text-gray-400" />
                                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-300 truncate">
                                            {{ item.location || (item.room ? item.room.name : '-') }}
                                        </span>
                                    </div>
                                    <div v-if="item.performed_by || item.pic" class="flex items-center gap-2">
                                        <UserIcon class="w-3.5 h-3.5 text-gray-400" />
                                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-300 truncate">
                                            {{ item.performed_by || item.pic || '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Extra Details Grid -->
                                <div class="grid grid-cols-2 gap-2 mt-3 mb-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl" v-if="item.brand || item.year || item.period_year || item.quantity || item.stock || item.condition || item.total_price || item.value || item.cost">
                                    <div v-if="item.brand" class="flex flex-col">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Merk</span>
                                        <span class="text-[10px] font-medium text-gray-700 dark:text-gray-200 truncate">{{ item.brand }}</span>
                                    </div>
                                    <div v-if="item.year || item.period_year" class="flex flex-col">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Tahun</span>
                                        <span class="text-[10px] font-medium text-gray-700 dark:text-gray-200">{{ item.year || item.period_year }}</span>
                                    </div>
                                    <div v-if="item.quantity || item.stock" class="flex flex-col">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Jumlah</span>
                                        <span class="text-[10px] font-medium text-gray-700 dark:text-gray-200">{{ item.quantity || item.stock }} {{ item.unit }}</span>
                                    </div>
                                    <div v-if="item.condition" class="flex flex-col">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Kondisi</span>
                                        <span class="text-[10px] font-medium" :class="{
                                            'text-emerald-500': item.condition === 'B' || item.condition === 'Baik',
                                            'text-amber-500': item.condition === 'KB' || item.condition === 'Kurang Baik',
                                            'text-rose-500': item.condition === 'RB' || item.condition === 'Rusak Berat' || item.condition === 'R'
                                        }">{{ item.condition }}</span>
                                    </div>
                                    <div v-if="item.total_price || item.value || item.cost" class="flex flex-col col-span-2">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Nilai/Biaya</span>
                                        <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Rp {{ Number(item.total_price || item.value || item.cost).toLocaleString('id-ID') }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <button 
                                        @click="openEditModal(item)"
                                        class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-pail-gold hover:text-white transition-all flex items-center gap-1.5"
                                    >
                                        <PencilIcon class="w-3 h-3" /> Edit
                                    </button>
                                    <button 
                                        v-if="type !== 'buku-induk' && type !== 'pemeliharaan-listrik'"
                                        @click="confirmDelete(item)"
                                        class="px-3 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all flex items-center gap-1.5"
                                    >
                                        <TrashIcon class="w-3 h-3" /> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Table View with Horizontal Scroll -->
                    <div
                        class="hidden md:block overflow-x-auto border-b border-gray-100 dark:border-gray-700"
                    >
                        <table
                            class="w-full text-left border-collapse min-w-[800px]"
                        >
                            <thead>
                                <!-- Row 1 for Specialized & Double Headers -->
                                <tr
                                    v-if="
                                        ['pab-tahunan', 'visi-2030'].includes(
                                            type,
                                        )
                                    "
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left sticky left-0 bg-white dark:bg-gray-800 z-20 shadow-[10px_0_15px_-10px_rgba(0,0,0,0.1)] border-r dark:border-gray-700"
                                    >
                                        Aksi
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left"
                                    >
                                        Judul Dokumen
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left"
                                    >
                                        Tahun
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left"
                                    >
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left"
                                    >
                                        Total Anggaran
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Status
                                    </th>
                                </tr>
                                <tr
                                    v-if="['monitoring-aset', 'pendataan-aset', 'kir-ruangan'].includes(type)"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <template
                                        v-if="
                                            [
                                                'pendataan-aset',
                                                'kir-ruangan',
                                                'pemeliharaan-gedung',
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                            ].includes(type)
                                        "
                                    >
                                        <th
                                            rowspan="2"
                                            v-if="
                                                ![
                                                    'pemeliharaan-pompa',
                                                    'pemeliharaan-air-bersih',
                                                    'pemeliharaan-air-minum',
                                                    'pemeliharaan-genset',
                                                ].includes(type)
                                            "
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            No.
                                        </th>
                                    </template>

                                    <template
                                        v-if="
                                            [
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                            ].includes(type)
                                        "
                                    >
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Item Pengecekan
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Standard
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Metode / FRK
                                        </th>
                                        <th
                                            :colspan="
                                                [
                                                    'pemeliharaan-air-minum',
                                                    'pemeliharaan-genset',
                                                ].includes(type)
                                                    ? 24
                                                    : 36
                                            "
                                            class="px-4 py-4 text-[10px] font-black text-pail-gold uppercase tracking-widest text-center border-l dark:border-gray-700 bg-gray-100/30 dark:bg-gray-800/30"
                                        >
                                            <div
                                                v-if="
                                                    type ===
                                                    'pemeliharaan-genset'
                                                "
                                                class="flex flex-col gap-1"
                                            >
                                                <span
                                                    >FORMULIR PEMELIHARAAN
                                                    GENSET</span
                                                >
                                                <div
                                                    class="flex justify-center gap-4 text-[8px] font-black text-gray-400"
                                                >
                                                    <span
                                                        >KGT:
                                                        {{
                                                            data.data[0]
                                                                ?.subcategory ||
                                                            "..."
                                                        }}</span
                                                    >
                                                    <span
                                                        >MSN:
                                                        {{
                                                            data.data[0]
                                                                ?.serial_number ||
                                                            "..."
                                                        }}</span
                                                    >
                                                    <span
                                                        >LOK:
                                                        {{
                                                            data.data[0]
                                                                ?.location ||
                                                            "..."
                                                        }}</span
                                                    >
                                                    <span
                                                        >THN:
                                                        {{
                                                            data.data[0]
                                                                ?.year || "..."
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <span v-else>
                                                {{
                                                    type ===
                                                    "pemeliharaan-pompa"
                                                        ? "CEKLIST PEMELIHARAAN POMPA & PANEL"
                                                        : type ===
                                                            "pemeliharaan-air-bersih"
                                                          ? "FORMULIR PEMELIHARAAN AIR BERSIH"
                                                          : "FORMULIR PEMELIHARAAN AIR MINUM"
                                                }}
                                                (Lokasi:
                                                {{
                                                    data.data[0]?.location ||
                                                    "..."
                                                }}) - Tahun :
                                                {{
                                                    data.data[0]?.year || "..."
                                                }}
                                            </span>
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Keterangan
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                        >
                                            Manajemen
                                        </th>
                                    </template>

                                    <template v-if="type === 'kir-ruangan'">
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Nama Barang
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Merk
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center text-[8px]"
                                        >
                                            No. Seri
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Ukuran
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Bahan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Tahun
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Kode
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Jumlah
                                        </th>
                                        <th
                                            colspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Kondisi Saat Ini
                                        </th>
                                    </template>
                                    <template v-if="type === 'pendataan-aset'">
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Jenis Barang / Nama Aset
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Satuan Kerja
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Jumlah Aset
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Satuan
                                        </th>
                                        <th
                                            colspan="3"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 whitespace-nowrap"
                                        >
                                            Kondisi Saat Ini
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Tanggal Pengecekan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                        >
                                            Petugas Pemeriksa
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                        >
                                            Nilai Aset
                                        </th>
                                    </template>

                                    <template
                                        v-if="type === 'pemeliharaan-gedung'"
                                    >
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Item Pengecekan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Standard
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Metode
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono"
                                        >
                                            FRK
                                        </th>
                                    </template>

                                    <!-- specialized for Pemeliharaan AC (Row 1 of 3) -->
                                    <template
                                        v-else-if="type === 'pemeliharaan-ac'"
                                    >
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Ruangan
                                        </th>
                                        <th
                                            colspan="6"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 bg-gray-100/30 dark:bg-gray-800/30"
                                        >
                                            <div class="flex flex-col">
                                                <span
                                                    >JENIS PEMELIHARAAN AC</span
                                                >
                                                <span
                                                    class="text-[8px] font-bold text-gray-400 mt-1 uppercase tracking-tighter"
                                                    >Divisi:
                                                    {{
                                                        data.data[0]
                                                            ?.ac_divisi || "..."
                                                    }}
                                                    | Bln:
                                                    {{
                                                        data.data[0]
                                                            ?.ac_tgl_bln ||
                                                        "..."
                                                    }}</span
                                                >
                                            </div>
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Petugas
                                        </th>
                                    </template>

                                    <!-- specialized for Pemeliharaan Kamar Mandi (Row 1 Meta) -->
                                    <template
                                        v-else-if="
                                            type === 'pemeliharaan-kamar-mandi'
                                        "
                                    >
                                        <th
                                            colspan="15"
                                            class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap bg-gray-100/30 dark:bg-gray-800/30"
                                        >
                                            <div
                                                class="flex flex-col items-center gap-2"
                                            >
                                                <div class="flex gap-12">
                                                    <span
                                                        >AREA :
                                                        {{
                                                            data.data[0]
                                                                ?.subcategory ||
                                                            "........................"
                                                        }}</span
                                                    >
                                                    <span
                                                        >TANGGAL KEGIATAN :
                                                        {{
                                                            formatDate(
                                                                data.data[0]
                                                                    ?.completed_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </th>
                                    </template>

                                    <!-- specialized for Pemeliharaan Septik Tank (Row 1) -->
                                    <template
                                        v-else-if="
                                            type === 'pemeliharaan-septik'
                                        "
                                    >
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Tanggal Pemeriksaan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Lokasi
                                        </th>
                                        <th
                                            colspan="4"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 whitespace-nowrap"
                                        >
                                            Kondisi Septik Tank
                                        </th>
                                        <th
                                            colspan="4"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 whitespace-nowrap"
                                        >
                                            Kondisi Septik Tank
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Tindakan Pemeliharaan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Hasil Tindakan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono"
                                        >
                                            Petugas
                                        </th>
                                    </template>

                                    <!-- specialized for Pemeliharaan Sarpras (Row 1) -->
                                    <template
                                        v-else-if="
                                            type === 'pemeliharaan-sarpras'
                                        "
                                    >
                                        <th
                                            colspan="9"
                                            class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center bg-gray-100/30 dark:bg-gray-800/30"
                                        >
                                            <div
                                                class="flex items-center justify-center gap-4"
                                            >
                                                <span
                                                    >FORMULIR LAPORAN
                                                    PEMELIHARAAN SARANA
                                                    PRASARANA</span
                                                >
                                                <div
                                                    class="px-4 py-1.5 bg-pail-gold/10 border border-pail-gold/20 rounded-lg text-pail-gold"
                                                >
                                                    Pemeliharaan :
                                                    {{
                                                        data.data[0]
                                                            ?.subcategory ||
                                                        "...................."
                                                    }}
                                                </div>
                                            </div>
                                        </th>
                                    </template>

                                    <!-- specialized for Agenda Perbaikan Sarpras (Row 1) -->
                                    <template
                                        v-else-if="type === 'agenda-perbaikan'"
                                    >
                                        <th
                                            colspan="7"
                                            class="px-6 py-8 text-[10px] font-black uppercase tracking-widest text-center bg-gray-100/30 dark:bg-gray-800/30 border-b dark:border-gray-700"
                                        >
                                            <div class="flex flex-col gap-4">
                                                <span
                                                    class="text-lg md:text-xl text-pail-gold"
                                                    >JADWAL AGENDA PERBAIKAN
                                                    SARPRAS</span
                                                >
                                                <div
                                                    class="flex flex-col items-center gap-2 text-gray-400"
                                                >
                                                    <span
                                                        class="text-[9px] font-black uppercase tracking-widest opacity-60"
                                                        >Jadwal Perbaikan &
                                                        Maintenance Rutin</span
                                                    >
                                                </div>
                                            </div>
                                        </th>
                                    </template>

                                    <!-- Middle Section Metas -->
                                    <th
                                        v-if="
                                            [
                                                'monitoring-aset',
                                            ].includes(type)
                                        "
                                        colspan="3"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Keadaan Barang
                                    </th>

                                    <!-- specialized for Pemeliharaan Gedung Meta info -->
                                    <th
                                        v-if="type === 'pemeliharaan-gedung'"
                                        colspan="12"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap bg-gray-100/30 dark:bg-gray-800/30"
                                    >
                                        <div class="flex flex-col items-center">
                                            <div class="flex gap-8 mb-1">
                                                <span
                                                    >Gedung :
                                                    {{
                                                        data.data[0]
                                                            ?.subcategory ||
                                                        "...................."
                                                    }}</span
                                                >
                                                <span
                                                    >Lokasi :
                                                    {{
                                                        data.data[0]
                                                            ?.location ||
                                                        "...................."
                                                    }}</span
                                                >
                                            </div>
                                            <span class="text-pail-gold"
                                                >Tahun :
                                                {{
                                                    data.data[0]?.year ||
                                                    "...................."
                                                }}</span
                                            >
                                        </div>
                                    </th>

                                    <template v-if="type === 'monitoring-aset'">
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Satuan Kerja
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            JENIS BARANG / MERK
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            LOKASI / RUANGAN
                                        </th>
                                        <th
                                            colspan="3"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 whitespace-nowrap"
                                        >
                                            KONDISI
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Tgl Periksa
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Petugas
                                        </th>
                                    </template>




                                    <th
                                        v-if="
                                            ![
                                                'pemeliharaan-gedung',
                                                'pemeliharaan-ac',
                                                'pemeliharaan-kamar-mandi',
                                                'pemeliharaan-septik',
                                                'pemeliharaan-sarpras',
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                            ].includes(type)
                                        "
                                        rowspan="2"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>

                                    <!-- Manajemen Alignment -->
                                    <th
                                        v-if="type === 'pemeliharaan-ac'"
                                        rowspan="3"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                    <th
                                        v-else-if="
                                            type === 'pemeliharaan-kamar-mandi'
                                        "
                                        rowspan="2"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                    <th
                                        v-else-if="
                                            ![
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                                'electrical-maintenance',
                                            ].includes(type)
                                        "
                                        rowspan="2"
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                                <!-- Row 2 for Specialized Double Headers / Bathroom -->
                                <tr
                                    v-if="['monitoring-aset', 'pendataan-aset', 'kir-ruangan'].includes(type)"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <template v-if="type === 'monitoring-aset'">
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            B
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            KB
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            R
                                        </th>
                                    </template>
                                    <template
                                        v-if="type === 'pemeliharaan-septik'"
                                    >
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            Baik
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            Penuh
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            Bocor
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center border-l dark:border-gray-700"
                                        >
                                            Bau
                                        </th>
                                    </template>
                                    <template
                                        v-if="
                                            [
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                            ].includes(type)
                                        "
                                    >
                                        <th
                                            v-for="m in [
                                                'Jul',
                                                'Agu',
                                                'Sep',
                                                'Okt',
                                                'Nov',
                                                'Des',
                                                'Jan',
                                                'Feb',
                                                'Mar',
                                                'Apr',
                                                'Mei',
                                                'Jun',
                                            ]"
                                            :key="m"
                                            :colspan="
                                                [
                                                    'pemeliharaan-air-minum',
                                                    'pemeliharaan-genset',
                                                ].includes(type)
                                                    ? 2
                                                    : 3
                                            "
                                            class="px-2 py-2 text-[8px] font-black text-gray-500 uppercase text-center border-l dark:border-gray-700"
                                        >
                                            {{ m }}
                                        </th>
                                    </template>
                                    <template v-if="type === 'pemeliharaan-ac'">
                                        <th
                                            colspan="2"
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Pencucian (Indoor)
                                        </th>
                                        <th
                                            colspan="2"
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Outdoor
                                        </th>
                                        <th
                                            colspan="2"
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Kelistrikan
                                        </th>
                                    </template>
                                    <template
                                        v-if="
                                            type === 'pendataan-aset' ||
                                            type === 'kir-ruangan'
                                        "
                                    >
                                        <th
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Baik
                                        </th>
                                        <th
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Kurang Baik
                                        </th>
                                        <th
                                            class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Rusak
                                        </th>
                                    </template>
                                    <template
                                        v-if="type === 'pemeliharaan-gedung'"
                                    >
                                        <th
                                            v-for="month in [
                                                'Jul',
                                                'Agu',
                                                'Sep',
                                                'Okt',
                                                'Nov',
                                                'Des',
                                                'Jan',
                                                'Feb',
                                                'Mar',
                                                'Apr',
                                                'Mei',
                                                'Jun',
                                            ]"
                                            :key="month"
                                            class="px-2 py-2 text-[8px] font-black text-gray-500 uppercase tracking-tighter text-center border-l dark:border-gray-700 min-w-[40px]"
                                        >
                                            {{ month }}
                                        </th>
                                    </template>
                                    <template
                                        v-if="
                                            type === 'pemeliharaan-kamar-mandi'
                                        "
                                    >
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tgl/Bln/Thn
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Lokasi / No KM
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Kran
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Lampu
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Fiting
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Saklar
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Ember
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Gayung
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Closet
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Pintu
                                        </th>
                                        <th
                                            class="px-2 py-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-center"
                                        >
                                            Grendel
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Petugas
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Tindakan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Ket
                                        </th>
                                    </template>

                                    <template
                                        v-else-if="type === 'agenda-perbaikan'"
                                    >
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Lokasi / Ruangan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Uraian Pekerjaan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            Petugas
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700 border-r"
                                        >
                                            Keterangan
                                        </th>
                                    </template>

                                    <template
                                        v-else-if="
                                            type === 'pemeliharaan-sarpras'
                                        "
                                    >
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Nama Sarana/Prasarana
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Jenis Pekerjaan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Lokasi/Ruangan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Kondisi Sebelum
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Tindakan Pemeliharaan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center font-mono whitespace-nowrap"
                                        >
                                            Kondisi Sesudah
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Petugas
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-r dark:border-gray-700"
                                        >
                                            Keterangan
                                        </th>
                                    </template>

                                    <template
                                        v-else-if="
                                            type === 'pemeliharaan-kipas'
                                        "
                                    >
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tanggal Pemeriksaan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Lokasi / Ruangan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tipe Kipas
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Jenis Pemeliharaan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Kondisi
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tindakan / Perbaikan
                                        </th>
                                        <th
                                            class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Petugas
                                        </th>
                                    </template>
                                </tr>
                                <!-- Row 3 for Specialized Triple Header -->
                                <tr
                                    v-if="false"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <template v-if="type === 'pemeliharaan-ac'">
                                        <th
                                            v-for="sub in [
                                                'PC',
                                                'SW',
                                                'Freon',
                                                'Ampere',
                                                'Jaringan',
                                                'Tegangan',
                                            ]"
                                            :key="sub"
                                            class="px-2 py-2 text-[8px] font-black text-gray-400 uppercase text-center border-l dark:border-gray-700"
                                        >
                                            {{ sub }}
                                        </th>
                                    </template>
                                    <template
                                        v-else-if="
                                            [
                                                'pemeliharaan-pompa',
                                                'pemeliharaan-air-bersih',
                                                'pemeliharaan-air-minum',
                                                'pemeliharaan-genset',
                                            ].includes(type)
                                        "
                                    >
                                        <template v-for="i in 12" :key="i">
                                            <th
                                                v-for="area in type ===
                                                'pemeliharaan-genset'
                                                    ? ['Pkn1', 'Pkn2']
                                                    : type ===
                                                        'pemeliharaan-air-minum'
                                                      ? ['Pa', 'Pi']
                                                      : type ===
                                                          'pemeliharaan-pompa'
                                                        ? ['Pa', 'Pi', 'Lwt']
                                                        : ['Lwt', 'Pa', 'Pi']"
                                                :key="area"
                                                class="px-1 py-2 text-[7px] font-black text-gray-400 uppercase text-center border-l dark:border-gray-700"
                                            >
                                                {{ area }}
                                            </th>
                                        </template>
                                    </template>
                                </tr>

                                <!-- specialized for Rekapan Pengajuan Perbaikan (Single Row Header) -->
                                <tr
                                    v-if="type === 'rekapan-pengajuan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tgl Pengajuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Divisi Pemohon
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Permintaan Perbaikan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Jenis Kerusakan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tgl di TL
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pengajuan RAB -->
                                <tr
                                    v-if="type === 'pengajuan-rab'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tgl Pengajuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lembaga Pemohon
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Uraian Proyek
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Volume
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Satuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right"
                                    >
                                        Harga
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right"
                                    >
                                        Jumlah
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Laporan Proyek Kegiatan -->
                                <tr
                                    v-if="type === 'laporan-proyek'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Proyek Kegiatan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tgl Pelaksanaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi Kegiatan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        PJ Jawab
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tim Pelaksana
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Anggaran
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Realisasi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        % Capaian
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Peminjaman Barang -->
                                <template v-if="type === 'peminjaman-barang'">
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                    >
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            No
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Nama Peminjam/Divisi
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Nama Barang
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Kode Barang
                                        </th>
                                        <th
                                            colspan="3"
                                            class="px-4 py-3 text-[8px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Saat Peminjaman
                                        </th>
                                        <th
                                            colspan="3"
                                            class="px-4 py-3 text-[8px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Saat Kembali
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                        >
                                            Manajemen
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                    >
                                        <th
                                            class="px-4 py-3 border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Kondisi
                                        </th>
                                        <th class="px-4 py-3">Hari/Tgl</th>
                                        <th class="px-4 py-3">Paraf</th>
                                        <th
                                            class="px-4 py-3 border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Kondisi
                                        </th>
                                        <th class="px-4 py-3">Hari/Tgl</th>
                                        <th class="px-4 py-3">Paraf</th>
                                    </tr>
                                </template>

                                <!-- specialized for Pelelangan Aset -->
                                <tr
                                    v-if="type === 'pelelangan-aset'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kode Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jumlah (Unit)
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tahun Perolehan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Kondisi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Nilai Perkiraan (Rp)
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Berita Acara Pemeriksaan Aset -->
                                <tr
                                    v-if="type === 'berita-acara-pemeriksaan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Aset
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kode Aset
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Merk / Type
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tahun Perolehan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jumlah
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Kondisi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pengadaan Sarpras -->
                                <template v-if="type === 'pengadaan-sarpras'">
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                    >
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            No
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Uraian
                                        </th>
                                        <th
                                            colspan="2"
                                            class="px-4 py-3 text-[8px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Spesifikasi
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Jumlah
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Satuan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Keterangan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Status
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                        >
                                            Manajemen
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                    >
                                        <th
                                            class="px-4 py-3 border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Merk
                                        </th>
                                        <th class="px-4 py-3">Ukuran</th>
                                    </tr>
                                </template>

                                <!-- specialized for Kelengkapan Alat -->
                                <tr
                                    v-if="type === 'kelengkapan-alat'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal Pemeriksaan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Alat dan Bahan
                                    </th>
                                    <th
                                        colspan="4"
                                        class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                    >
                                        Kondisi Alat dan Bahan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                                <tr
                                    v-if="type === 'kelengkapan-alat'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                >
                                    <th
                                        class="px-4 py-3 border-l border-gray-100 dark:border-gray-700"
                                    >
                                        Kebutuhan/bln
                                    </th>
                                    <th class="px-4 py-3">Stok</th>
                                    <th class="px-4 py-3">Pengadaan</th>
                                    <th class="px-4 py-3">Satuan</th>
                                </tr>

                                <!-- specialized for Detailed Monitoring (Jadwal Kebersihan new) -->
                                <tr
                                    v-if="type === 'detailed-monitoring'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Nama Petugas
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Area Kerja
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Uraian Pekerjaan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Standar Pengecekan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Metode Pengecekan
                                    </th>
                                    <th
                                        colspan="18"
                                        class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                    >
                                        Penilaian
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        rowspan="2"
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                                <tr
                                    v-if="type === 'detailed-monitoring'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                >
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Senin
                                    </th>
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Selasa
                                    </th>
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Rabu
                                    </th>
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Kamis
                                    </th>
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Jumat
                                    </th>
                                    <th
                                        colspan="3"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Sabtu
                                    </th>
                                </tr>

                                <!-- specialized for Weekly Activity -->
                                <tr
                                    v-if="type === 'weekly-activity'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Pekanan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Nama Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Area Kegiatan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Uraian Pekerjaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Waktu
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Ceklist
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-right"
                                    >
                                        Paraf PJ
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-right"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Vehicle Log -->
                                <tr
                                    v-if="type === 'vehicle-log'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Bln/Tgl/Thn
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Jenis Kendaraan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Waktu Jam
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Unit Kerja
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tujuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Kilometer
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ampere BBM
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kondisi Kendaraan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-right"
                                    >
                                        Nama PJ
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Electrical Maintenance (Row 1 Header) -->
                                <tr
                                    v-if="type === 'pemeliharaan-listrik'"
                                    class="bg-gray-100/30 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        colspan="18"
                                        class="px-6 py-6 text-center"
                                    >
                                        <div class="flex flex-col gap-2">
                                            <span
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter"
                                                >FORM PEMELIHARAAN JARINGAN
                                                LISTRIK</span
                                            >
                                            <div
                                                class="flex items-center justify-center gap-8 text-[10px] font-black text-pail-gold uppercase tracking-widest"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <span>LOKASI :</span>
                                                    <span
                                                        class="text-gray-900 dark:text-white border-b border-dotted border-pail-gold pb-0.5 min-w-[150px] inline-block"
                                                        >{{
                                                            filters.location ||
                                                            "...................."
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <span>TAHUN :</span>
                                                    <span
                                                        class="text-gray-900 dark:text-white border-b border-dotted border-pail-gold pb-0.5 min-w-[80px] inline-block"
                                                        >{{
                                                            filters.year ||
                                                            new Date().getFullYear()
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>

                                <!-- specialized for Electrical Maintenance (Row 2 Header) -->
                                <tr
                                    v-if="type === 'pemeliharaan-listrik'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 font-mono"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Item Pengecekan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Standard Pengecekan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Metode
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Frekuensi
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jul
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Agust
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Sept
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Okt
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Nov
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Des
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jan
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Feb
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Mar
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Apr
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Mei
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Juni
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Aksi
                                    </th>
                                </tr>

                                <!-- specialized for Monitoring Kebersihan -->
                                <tr
                                    v-if="type === 'monitoring-kebersihan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        rowspan="3"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Nama Petugas
                                    </th>
                                    <th
                                        rowspan="3"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Area Kerja
                                    </th>
                                    <th
                                        rowspan="3"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Uraian Pekerjaan
                                    </th>
                                    <th
                                        colspan="12"
                                        class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                    >
                                        Penilaian
                                    </th>
                                    <th
                                        rowspan="3"
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        rowspan="3"
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                                <tr
                                    v-if="type === 'monitoring-kebersihan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                >
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        Senin
                                    </th>
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 text-center"
                                    >
                                        Selasa
                                    </th>
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 text-center"
                                    >
                                        Rabu
                                    </th>
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 text-center"
                                    >
                                        Kamis
                                    </th>
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 text-center"
                                    >
                                        Jum'at
                                    </th>
                                    <th
                                        colspan="2"
                                        class="px-2 py-2 text-center"
                                    >
                                        Sabtu
                                    </th>
                                </tr>
                                <tr
                                    v-if="type === 'monitoring-kebersihan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                >
                                    <th
                                        class="px-2 py-2 border-l border-gray-100 dark:border-gray-700 text-center"
                                    >
                                        S
                                    </th>
                                    <th class="px-2 py-2 text-center">B</th>
                                    <th class="px-2 py-2 text-center">S</th>
                                    <th class="px-2 py-2 text-center">B</th>
                                    <th class="px-2 py-2 text-center">S</th>
                                    <th class="px-2 py-2 text-center">B</th>
                                    <th class="px-2 py-2 text-center">S</th>
                                    <th class="px-2 py-2 text-center">B</th>
                                    <th class="px-2 py-2 text-center">S</th>
                                    <th class="px-2 py-2 text-center">B</th>
                                    <th class="px-2 py-2 text-center">S</th>
                                    <th class="px-2 py-2 text-center">B</th>
                                </tr>

                                <!-- specialized for Jadwal Kebersihan -->
                                <tr
                                    v-if="type === 'jadwal-kebersihan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Hari
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Nama Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Area Kerja
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Waktu
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Uraian Pekerjaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Analisis Kebutuhan Sarpras -->
                                <tr
                                    v-if="type === 'analisis-kebutuhan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Uraian
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jumlah
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Satuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Pengadaan / Penggantian
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Kebersihan (Timeline) -->
                                <template
                                    v-if="type === 'pemeliharaan-kebersihan'"
                                >
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                    >
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            No
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-left"
                                        >
                                            URAIAN PEMELIHARAAN
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                        >
                                            OUTPUT PEMBIAYAAN KEGIATAN
                                        </th>
                                        <th
                                            colspan="12"
                                            class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700"
                                        >
                                            TIMELINE KEGIATAN
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Keterangan
                                        </th>
                                        <th
                                            rowspan="2"
                                            class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                        >
                                            Manajemen
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-400 font-black uppercase tracking-wider"
                                    >
                                        <th
                                            class="px-2 py-3 border-l border-gray-100 dark:border-gray-700"
                                        >
                                            Jul-25
                                        </th>
                                        <th class="px-2 py-3">Agu-25</th>
                                        <th class="px-2 py-3">Sep-25</th>
                                        <th class="px-2 py-3">Okt-25</th>
                                        <th class="px-2 py-3">Nov-25</th>
                                        <th class="px-2 py-3">Des-25</th>
                                        <th class="px-2 py-3">Jan-26</th>
                                        <th class="px-2 py-3">Feb-26</th>
                                        <th class="px-2 py-3">Mar-26</th>
                                        <th class="px-2 py-3">Apr-26</th>
                                        <th class="px-2 py-3">Mei-26</th>
                                        <th class="px-2 py-3">Jun-26</th>
                                    </tr>
                                </template>

                                <!-- specialized for Jadwal Token -->
                                <tr
                                    v-if="type === 'jadwal-token'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal Pengisian
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Meter / ID Pelanggan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Nominal Token (Rp)
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Nomor Token
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanda Tangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Penyerahan Barang -->
                                <tr
                                    v-if="type === 'penyerahan-barang'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Spesifikasi Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jumlah
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Satuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Kondisi barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Penerimaan Barang -->
                                <tr
                                    v-if="type === 'penerimaan-barang'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal Penerimaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Spesifikasi Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jml
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Satuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Asal Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Penerima
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemilihan & Evaluasi Supplier -->
                                <tr
                                    v-if="type === 'pemilihan-evaluasi'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kategori Supplier
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Supplier
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Skor Akhir
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Parkir Area -->
                                <tr
                                    v-if="type === 'parkir-area'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tipe Kendaraan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        No Plast / TNKB
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Pemilik
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jam Masuk
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Jam Keluar
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center"
                                    >
                                        Gerbang/Gate
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Penggunaan Kendaraan -->
                                <tr
                                    v-if="type === 'penggunaan-kendaraan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Armada
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        User/Lembaga
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tujuan & Keperluan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Berangkat
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Pulang
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for ISO Checklist -->
                                <tr
                                    v-if="type === 'ceklist-iso'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Judul Ceklist
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kategori
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Frekuensi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Item
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Kamar Mandi -->
                                <tr
                                    v-if="type === 'pemeliharaan-kamar-mandi'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Kran
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Lampu
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Fiting
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Saklar
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Ember
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Gayung
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Closet
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Pintu
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Grendel
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tindakan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan AC -->
                                <tr
                                    v-if="type === 'pemeliharaan-ac'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi / Ruangan
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Indoor PC
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Indoor SW
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Outdoor Freon
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Outdoor Ampere
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Listrik Jaringan
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Listrik Tegangan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Electrical Maintenance -->
                                <tr
                                    v-if="
                                        type === 'electrical-maintenance' ||
                                        type === 'pemeliharaan-listrik'
                                    "
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Item Pengecekan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Standard
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Metode
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Freq
                                    </th>
                                    <th
                                        v-for="m in [
                                            'Jul',
                                            'Agu',
                                            'Sep',
                                            'Okt',
                                            'Nov',
                                            'Des',
                                            'Jan',
                                            'Feb',
                                            'Mar',
                                            'Apr',
                                            'Mei',
                                            'Jun',
                                        ]"
                                        :key="m"
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-l dark:border-gray-700"
                                    >
                                        {{ m }}
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Septik Tank -->
                                <tr
                                    v-if="type === 'pemeliharaan-septik'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Baik
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Penuh
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Bocor
                                    </th>
                                    <th
                                        class="px-2 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Bau
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tindakan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Hasil
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Kipas Angin -->
                                <tr
                                    v-if="type === 'pemeliharaan-kipas'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Jenis
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Kondisi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tindakan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Gedung -->
                                <tr
                                    v-if="type === 'pemeliharaan-gedung'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Item Pengecekan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Std
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Metode
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Freq
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jul
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Agu
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Sep
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Okt
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Nov
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Des
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jan
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Feb
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Mar
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Apr
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Mei
                                    </th>
                                    <th
                                        class="px-1 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jun
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Pemeliharaan Pompa, Air, Genset (Matrix) -->
                                <template
                                    v-if="
                                        [
                                            'pemeliharaan-pompa',
                                            'pemeliharaan-air-bersih',
                                            'pemeliharaan-air-minum',
                                            'pemeliharaan-genset',
                                        ].includes(type)
                                    "
                                >
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                    >
                                        <th
                                            rowspan="3"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            No
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Item Pengecekan
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Std
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Mtd/Freq
                                        </th>
                                        <th
                                            :colspan="['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? 24 : 36"
                                            class="px-4 py-3 text-[9px] font-black text-pail-gold uppercase tracking-widest text-center border-l border-gray-100 dark:border-gray-700 bg-gray-100/10 dark:bg-gray-800/10"
                                        >
                                            Rekap Monitoring (PA = Putra, PI = Putri, LT = Lawata / P1 = Pekan 1, P2 = Pekan 2)
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Keterangan
                                        </th>
                                        <th
                                            rowspan="3"
                                            class="px-8 py-6 text-[9px] font-black text-gray-100 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-pail-gold dark:bg-pail-gold z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)] border-l dark:border-gray-700/50"
                                        >
                                            Manajemen
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[8px] text-gray-500 font-black uppercase tracking-wider"
                                    >
                                        <th
                                            v-for="m in ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']"
                                            :key="m"
                                            :colspan="['pemeliharaan-air-minum', 'pemeliharaan-genset'].includes(type) ? 2 : 3"
                                            class="px-1 py-2 text-center border-l dark:border-gray-700 bg-gray-100/5 dark:bg-gray-800/5"
                                        >
                                            {{ m }}
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 text-[7px] text-gray-400 font-black uppercase tracking-tighter"
                                    >
                                        <template v-for="i in 12" :key="i">
                                            <th
                                                v-for="area in type === 'pemeliharaan-genset'
                                                    ? ['P1', 'P2']
                                                    : type === 'pemeliharaan-air-minum'
                                                      ? ['Pa', 'Pi']
                                                      : type === 'pemeliharaan-pompa'
                                                        ? ['Pa', 'Pi', 'Lt']
                                                        : ['Lt', 'Pa', 'Pi']"
                                                :key="area"
                                                class="px-1 py-1 text-center border-l dark:border-gray-700"
                                            >
                                                {{ area }}
                                            </th>
                                        </template>
                                    </tr>
                                </template>

                                <!-- specialized for Pemeliharaan Sarpras -->
                                <tr
                                    v-if="type === 'pemeliharaan-sarpras'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Prasarana
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jenis
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Kondisi Sblm
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Tindakan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Kondisi Ssdh
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for Agenda Perbaikan -->
                                <tr
                                    v-if="type === 'agenda-perbaikan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Waktu
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Uraian Pekerjaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>

                                <!-- specialized for KIR Ruangan -->
                                <tr
                                    v-if="type === 'kir-ruangan'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Merk/Unit
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        No Seri
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Ukuran
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Bahan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Thn
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Kode
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Jml
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        B
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        KB
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        RB
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>



                                <!-- specialized for Pengadaan Sarpras (Missing Header) -->
                                <tr
                                    v-if="type === 'pengadaan-sarpras'"
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Judul Pengadaan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Merk
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Ukuran
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Satuan
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                    >
                                        Deskripsi
                                    </th>
                                    <th
                                        class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                                <!-- Standard Row for Other Types -->
                                <tr
                                    v-if="
                                        ![
                                            'rab-tahunan',
                                            'visi-2030',
                                            'pendataan-aset',
                                            'kir-ruangan',
                                            'pemeliharaan-gedung',
                                            'pemeliharaan-kamar-mandi',
                                            'pemeliharaan-ac',
                                            'pemeliharaan-kipas',
                                            'pemeliharaan-pompa',
                                            'pemeliharaan-air-bersih',
                                            'pemeliharaan-air-minum',
                                            'pemeliharaan-genset',
                                            'pemeliharaan-septik',
                                            'pemeliharaan-sarpras',
                                            'rekapan-pengajuan',
                                            'laporan-proyek',
                                            'peminjaman-barang',
                                            'pelelangan-aset',
                                            'berita-acara-pemeriksaan',
                                            'pengadaan-sarpras',
                                            'analisis-kebutuhan',
                                            'pengajuan-rab',
                                            'penerimaan-barang',
                                            'penyerahan-barang',
                                            'jadwal-token',
                                            'pemeliharaan-kebersihan',
                                            'jadwal-kebersihan',
                                            'kelengkapan-alat',
                                            'monitoring-kebersihan',
                                            'detailed-monitoring',
                                            'weekly-activity',
                                            'vehicle-log',
                                            'electrical-maintenance',
                                            'monitoring-aset',
                                            'parkir-area',
                                            'penggunaan-kendaraan',
                                            'ceklist-iso',
                                            'agenda-perbaikan',
                                            'pemeliharaan-listrik',
                                        ].includes(type)
                                    "
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700"
                                >
                                    <!-- Dynamic Headers based on Type -->
                                    <template v-if="type.includes('kendaraan')">
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Armada / Plat
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Detail & Merk
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                        >
                                            Manajemen
                                        </th>
                                    </template>

                                    <template
                                        v-else-if="
                                            type === 'buku-induk' ||
                                            type === 'berita-acara-pemeriksaan'
                                        "
                                    >
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Satuan Kerja
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Ruangan
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Jenis Barang
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Merk
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Merk (Spec)
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tgl Buku/Beli
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            No Urut Satker
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Kode Barang
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            No Urut Pondok
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Kuantitas
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Satuan
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Sumber
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Tgl Serah
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center whitespace-nowrap"
                                        >
                                            Kondisi
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                        >
                                            Harga @
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                        >
                                            Harga Depr
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            P.JWB
                                        </th>
                                        <th
                                            class="px-4 py-6 text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap"
                                        >
                                            Ket
                                        </th>
                                    </template>
                                    <template
                                        v-else-if="
                                            procedure?.model?.includes('Item')
                                        "
                                    >
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Kode / Nama Aset
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Lokasi / Lembaga
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Merk / Stok
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Kondisi
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Aktivitas / Detail
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Lokasi/Lembaga
                                        </th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                        >
                                            Kondisi (Sblm/Ssdh)
                                        </th>
                                    </template>

                                    <th
                                        v-if="
                                            type !== 'buku-induk' &&
                                            type !== 'pendataan-aset' &&
                                            type !== 'kir-ruangan'
                                        "
                                        class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap"
                                    >
                                        Nilai / Sumber
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right whitespace-nowrap sticky right-0 bg-gray-50 dark:bg-gray-900 z-20 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)]"
                                    >
                                        Manajemen
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-50 dark:divide-gray-700/50"
                            >
                                <tr
                                    v-for="(item, index) in data.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors group"
                                >
                                    <!-- Specialized Columns for REGISTRASI KENDARAAN -->
                                    <template
                                        v-if="type === 'registrasi-kendaraan'"
                                    >
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-black text-gray-900 dark:text-white uppercase"
                                                    >{{ item.name }}</span
                                                >
                                                <span
                                                    class="text-[10px] font-bold text-pail-gold mt-1"
                                                    >{{
                                                        item.plate_number
                                                    }}</span
                                                >
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                                    >{{ item.brand }} ({{
                                                        item.year
                                                    }})</span
                                                >
                                                <span
                                                    class="text-[9px] text-gray-400 mt-1 uppercase"
                                                    >{{ item.type }}</span
                                                >
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                :class="
                                                    item.status === 'available'
                                                        ? 'bg-green-50 text-green-600 border-green-100'
                                                        : 'bg-red-50 text-red-600 border-red-100'
                                                "
                                            >
                                                {{ item.status }}
                                            </span>
                                        </td>
                                    </template>

                                    <!-- Specialized Columns for PENGGUNAAN KENDARAAN -->
                                    <template
                                        v-else-if="
                                            type === 'penggunaan-kendaraan'
                                        "
                                    >
                                        <td
                                            class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                        >
                                            {{ item.request_date || "-" }}
                                        </td>
                                        <td
                                            class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                        >
                                            {{ item.vehicle?.name || "-" }}
                                            <div
                                                class="text-[9px] text-gray-400 font-bold tracking-wider"
                                            >
                                                {{
                                                    item.vehicle
                                                        ?.plate_number || ""
                                                }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                        >
                                            {{
                                                item.institution_name ||
                                                item.user?.name ||
                                                "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-4 py-6 border-l border-gray-100 dark:border-gray-800"
                                        >
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                                    >{{
                                                        item.destination
                                                    }}</span
                                                >
                                                <span
                                                    class="text-[9px] text-gray-400 mt-1 italic"
                                                    >{{ item.purpose }}</span
                                                >
                                            </div>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-[10px] font-black text-gray-900 dark:text-white"
                                                    >{{
                                                        item.start_time
                                                            ? new Date(
                                                                  item.start_time,
                                                              ).toLocaleTimeString(
                                                                  [],
                                                                  {
                                                                      hour: "2-digit",
                                                                      minute: "2-digit",
                                                                  },
                                                              )
                                                            : "-"
                                                    }}</span
                                                >
                                                <span
                                                    class="text-[8px] text-gray-400 uppercase tracking-widest mt-0.5 whitespace-nowrap"
                                                    >Berangkat</span
                                                >
                                            </div>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-[10px] font-black text-gray-900 dark:text-white"
                                                    >{{
                                                        item.end_time
                                                            ? new Date(
                                                                  item.end_time,
                                                              ).toLocaleTimeString(
                                                                  [],
                                                                  {
                                                                      hour: "2-digit",
                                                                      minute: "2-digit",
                                                                  },
                                                              )
                                                            : "-"
                                                    }}</span
                                                >
                                                <span
                                                    class="text-[8px] text-gray-400 uppercase tracking-widest mt-0.5 whitespace-nowrap"
                                                    >Pulang</span
                                                >
                                            </div>
                                        </td>
                                    </template>

                                    <!-- Specialized Columns for ISO CHECKLIST -->
                                    <template
                                        v-else-if="type === 'ceklist-iso'"
                                    >
                                        <td
                                            class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-4 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                                    >{{ item.title }}</span
                                                >
                                                <span
                                                    class="text-[9px] text-gray-400 mt-0.5 uppercase tracking-widest font-bold font-mono"
                                                    >{{ item.frequency }}</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                        >
                                            {{ item.category }}
                                        </td>
                                        <td
                                            class="px-4 py-6 text-center text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                        >
                                            {{
                                                item.frequency === "daily"
                                                    ? "Harian"
                                                    : item.frequency ===
                                                        "weekly"
                                                      ? "Pekanan"
                                                      : item.frequency ===
                                                          "monthly"
                                                        ? "Bulanan"
                                                        : "Tahunan"
                                            }}
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <div
                                                class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg"
                                            >
                                                <span
                                                    class="text-[10px] font-black text-gray-700 dark:text-gray-300 uppercase"
                                                    >{{
                                                        Array.isArray(
                                                            item.items,
                                                        )
                                                            ? item.items.length
                                                            : 0
                                                    }}
                                                    Item</span
                                                >
                                            </div>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                :class="
                                                    item.is_active
                                                        ? 'bg-green-50 text-green-600 border-green-100'
                                                        : 'bg-red-50 text-red-600 border-red-100'
                                                "
                                            >
                                                {{
                                                    item.is_active
                                                        ? "Aktif"
                                                        : "Non-Aktif"
                                                }}
                                            </span>
                                        </td>
                                    </template>

                                    <!-- Specialized Content for Analisis / RAB -->
                                    <template
                                        v-else-if="
                                            [
                                                'rab-tahunan',
                                                'visi-2030',
                                            ].includes(type)
                                        "
                                    >
                                        <td
                                            class="px-6 py-4 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                        >
                                            {{ item.title }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-[10px] font-black text-gray-400 font-mono"
                                        >
                                            {{ item.fiscal_year }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                        >
                                            {{ item.institution?.name || "-" }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-[10px] font-black text-pail-gold font-mono"
                                        >
                                            Rp
                                            {{
                                                Number(
                                                    item.total_budget || 0,
                                                ).toLocaleString("id-ID")
                                            }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                :class="
                                                    item.status === 'approved'
                                                        ? 'bg-green-50 text-green-600 border-green-100'
                                                        : 'bg-yellow-50 text-yellow-600 border-yellow-100'
                                                "
                                            >
                                                {{ item.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button
                                                @click="openEditModal(item)"
                                                class="text-gray-400 hover:text-pail-gold transition-colors mr-2"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="confirmDelete(item.id)"
                                                class="text-gray-400 hover:text-red-500 transition-colors"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </td>
                                    </template>

                                    <!-- Generic Content for Others -->
                                    <template v-else>
                                        <!-- Specialized Content for REKAPAN PENGAJUAN -->
                                        <template
                                            v-if="type === 'rekapan-pengajuan'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.request_date,
                                                        true,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.institution?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{ item.damage_type || "-" }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="inline-block px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest"
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-600':
                                                            item.status ===
                                                            'pending',
                                                        'bg-blue-100 text-blue-600':
                                                            item.status ===
                                                                'proses' ||
                                                            item.status ===
                                                                'processing',
                                                        'bg-green-100 text-green-600':
                                                            item.status ===
                                                                'selesai' ||
                                                            item.status ===
                                                                'completed',
                                                    }"
                                                >
                                                    {{
                                                        item.status || "PENDING"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{
                                                    formatDate(
                                                        item.follow_up_date,
                                                        true,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] text-gray-400 italic text-right"
                                            >
                                                {{ item.remarks || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pengajuan RAB -->
                                        <template
                                            v-else-if="type === 'pengajuan-rab'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.request_date,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.institution?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center"
                                            >
                                                {{ item.volume || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-right"
                                            >
                                                Rp
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.unit_price,
                                                        ) || 0
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold text-right"
                                            >
                                                Rp
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.total_price,
                                                        ) || 0
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Laporan Proyek Kegiatan -->
                                        <template
                                            v-else-if="
                                                type === 'laporan-proyek'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.responsible_person ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase leading-tight"
                                            >
                                                {{ item.team_members || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-right"
                                            >
                                                Rp
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.budget_amount,
                                                        ) || 0
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold text-right whitespace-nowrap"
                                            >
                                                Rp
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.actual_amount,
                                                        ) || 0
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-right whitespace-nowrap"
                                            >
                                                Rp
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.budget_amount ||
                                                                0,
                                                        ) -
                                                        parseFloat(
                                                            item.actual_amount ||
                                                                0,
                                                        )
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <div
                                                    class="flex flex-col items-center"
                                                >
                                                    <span
                                                        class="text-[10px] font-black text-pail-gold"
                                                        >{{
                                                            item.attainment_percentage ||
                                                            0
                                                        }}%</span
                                                    >
                                                    <div
                                                        class="w-12 h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-1 overflow-hidden"
                                                    >
                                                        <div
                                                            class="h-full bg-pail-gold transition-all"
                                                            :style="{
                                                                width:
                                                                    Math.min(
                                                                        item.attainment_percentage ||
                                                                            0,
                                                                        100,
                                                                    ) + '%',
                                                            }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] text-gray-400 italic"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Peminjaman Barang -->
                                        <template
                                            v-else-if="
                                                type === 'peminjaman-barang'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-4 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                                        >{{
                                                            item.user?.name ||
                                                            "-"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1"
                                                        >{{
                                                            item.institution
                                                                ?.name || "-"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.item?.name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest"
                                            >
                                                {{ item.item?.code || "-" }}
                                            </td>

                                            <!-- Saat Peminjaman -->
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center border-l border-gray-100 dark:border-gray-700 uppercase"
                                            >
                                                {{
                                                    item.borrow_condition || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.borrow_date,
                                                        true,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold text-center uppercase"
                                            >
                                                {{ item.borrower_paraf || "-" }}
                                            </td>

                                            <!-- Saat Kembali -->
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white text-center border-l border-gray-100 dark:border-gray-700 uppercase"
                                            >
                                                {{
                                                    item.return_condition || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.actual_return_date,
                                                        true,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold text-center uppercase"
                                            >
                                                {{ item.returner_paraf || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pelelangan Aset -->
                                        <template
                                            v-else-if="
                                                type === 'pelelangan-aset'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.item?.name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest"
                                            >
                                                {{ item.item?.code || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }} Unit
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{
                                                    item.item?.purchased_at ||
                                                    "-"
                                                }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="
                                                        item.item?.condition ===
                                                        'B'
                                                            ? 'bg-green-50 text-green-600 border-green-100'
                                                            : item.item
                                                                    ?.condition ===
                                                                'KB'
                                                              ? 'bg-yellow-50 text-yellow-600 border-yellow-100'
                                                              : 'bg-red-50 text-red-600 border-red-100'
                                                    "
                                                >
                                                    {{
                                                        item.item?.condition ||
                                                        "-"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-right text-xs font-mono font-black text-gray-900 dark:text-white"
                                            >
                                                Rp
                                                {{
                                                    Number(
                                                        item.value || 0,
                                                    ).toLocaleString()
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.reason || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Monitoring Kebersihan -->
                                        <template
                                            v-else-if="
                                                type === 'monitoring-kebersihan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center border-l border-gray-50 dark:border-gray-800 font-bold text-green-600"
                                            >
                                                {{ item.mon_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.mon_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-green-600"
                                            >
                                                {{ item.tue_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.tue_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-green-600"
                                            >
                                                {{ item.wed_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.wed_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-green-600"
                                            >
                                                {{ item.thu_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.thu_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-green-600"
                                            >
                                                {{ item.fri_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.fri_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-green-600"
                                            >
                                                {{ item.sat_status ? "√" : "" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center font-bold text-red-400"
                                            >
                                                {{
                                                    !item.sat_status ? "√" : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Detailed Monitoring -->
                                        <template
                                            v-else-if="
                                                type === 'detailed-monitoring'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase"
                                            >
                                                {{ item.standard_check || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase"
                                            >
                                                {{ item.method_check || "-" }}
                                            </td>

                                            <!-- Dynamic Ratings -->
                                            <template
                                                v-for="day in [
                                                    'mon',
                                                    'tue',
                                                    'wed',
                                                    'thu',
                                                    'fri',
                                                    'sat',
                                                ]"
                                                :key="day"
                                            >
                                                <td
                                                    class="px-1 py-6 text-[8px] text-center border-l border-gray-50 dark:border-gray-800"
                                                    :class="{
                                                        'bg-green-50':
                                                            item[
                                                                day + '_rating'
                                                            ] ===
                                                            'sangat_bersih',
                                                    }"
                                                >
                                                    {{
                                                        item[
                                                            day + "_rating"
                                                        ] === "sangat_bersih"
                                                            ? "✓"
                                                            : ""
                                                    }}
                                                </td>
                                                <td
                                                    class="px-1 py-6 text-[8px] text-center"
                                                    :class="{
                                                        'bg-blue-50':
                                                            item[
                                                                day + '_rating'
                                                            ] === 'bersih',
                                                    }"
                                                >
                                                    {{
                                                        item[
                                                            day + "_rating"
                                                        ] === "bersih"
                                                            ? "✓"
                                                            : ""
                                                    }}
                                                </td>
                                                <td
                                                    class="px-1 py-6 text-[8px] text-center"
                                                    :class="{
                                                        'bg-red-50':
                                                            item[
                                                                day + '_rating'
                                                            ] ===
                                                            'tidak_bersih',
                                                    }"
                                                >
                                                    {{
                                                        item[
                                                            day + "_rating"
                                                        ] === "tidak_bersih"
                                                            ? "✓"
                                                            : ""
                                                    }}
                                                </td>
                                            </template>

                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pemeliharaan Listrik -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-listrik'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase text-center"
                                            >
                                                {{ item.specification || "-" }}
                                            </td>
                                            <!-- Standard -->
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase text-center"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <!-- Metode -->
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold uppercase text-center"
                                            >
                                                {{ item.frequency || "-" }}
                                            </td>

                                            <!-- Months Loop -->
                                            <td
                                                v-for="(monthKey, mIdx) in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="mIdx"
                                                class="px-2 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <div
                                                    v-if="
                                                        item.monthly_data &&
                                                        item.monthly_data[
                                                            monthKey
                                                        ]
                                                    "
                                                    class="flex items-center justify-center"
                                                >
                                                    <span
                                                        v-if="
                                                            item.monthly_data[
                                                                monthKey
                                                            ] === 'V'
                                                        "
                                                        class="font-black text-green-600"
                                                        >V</span
                                                    >
                                                    <span
                                                        v-else-if="
                                                            item.monthly_data[
                                                                monthKey
                                                            ] === 'X'
                                                        "
                                                        class="font-black text-red-600"
                                                        >X</span
                                                    >
                                                    <span
                                                        v-else
                                                        class="text-[9px] text-gray-400"
                                                        >{{
                                                            item.monthly_data[
                                                                monthKey
                                                            ]
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Weekly Activity -->
                                        <template
                                            v-else-if="
                                                type === 'weekly-activity'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.week_name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.scheduled_at || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-center"
                                            >
                                                <span
                                                    v-if="item.is_checked"
                                                    class="text-green-600 font-bold text-lg"
                                                    >✓</span
                                                >
                                                <span
                                                    v-else
                                                    class="text-gray-300"
                                                    >-</span
                                                >
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <td
                                                class="px-8 py-6 text-right whitespace-nowrap text-gray-900 dark:text-white font-bold text-[10px] uppercase"
                                            >
                                                {{
                                                    item.responsible_person ||
                                                    "-"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Vehicle Log -->
                                        <template
                                            v-else-if="type === 'vehicle-log'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.request_date,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.vehicle?.name || "-"
                                                }}
                                                ({{
                                                    item.vehicle
                                                        ?.plate_number || "-"
                                                }})
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.time_range || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.institution_name ||
                                                    item.user?.institution
                                                        ?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.destination || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.start_mileage || 0 }} KM
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.fuel_level_before ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.condition_before || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-8 py-6 text-right whitespace-nowrap text-[10px] font-bold uppercase"
                                            >
                                                {{
                                                    item.responsible_person ||
                                                    "-"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Electrical Maintenance -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'electrical-maintenance'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase"
                                            >
                                                {{ item.standard_check || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase"
                                            >
                                                {{ item.method_check || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-center text-gray-500 uppercase"
                                            >
                                                {{ item.frequency || "-" }}
                                            </td>

                                            <!-- Monthly Status -->
                                            <template
                                                v-for="month in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="month"
                                            >
                                                <td
                                                    class="px-2 py-6 text-[10px] font-bold text-center"
                                                    :class="{
                                                        'text-green-600':
                                                            item[
                                                                month +
                                                                    '_status'
                                                            ] === 'V',
                                                        'text-red-600':
                                                            item[
                                                                month +
                                                                    '_status'
                                                            ] === 'X',
                                                    }"
                                                >
                                                    {{
                                                        item[
                                                            month + "_status"
                                                        ] || "-"
                                                    }}
                                                </td>
                                            </template>
                                        </template>

                                        <!-- Specialized Content for Berita Acara Pemeriksaan Aset -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'berita-acara-pemeriksaan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.item?.name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest"
                                            >
                                                {{ item.item?.code || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.item?.brand || "-" }} /
                                                {{
                                                    item.item?.specification ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{
                                                    item.item?.purchased_at ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }} Unit
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="{
                                                        'bg-green-50 text-green-600 border-green-100':
                                                            item.before_condition ===
                                                            'Baik',
                                                        'bg-yellow-50 text-yellow-600 border-yellow-100':
                                                            item.before_condition ===
                                                            'Rusak Ringan',
                                                        'bg-red-50 text-red-600 border-red-100':
                                                            item.before_condition ===
                                                            'Rusak Berat',
                                                        'bg-gray-50 text-gray-600 border-gray-100':
                                                            item.before_condition ===
                                                            'Hilang',
                                                    }"
                                                >
                                                    {{
                                                        item.before_condition ||
                                                        "-"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pemeliharaan Kebersihan (Timeline) -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'pemeliharaan-kebersihan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-right"
                                            >
                                                {{
                                                    item.cost
                                                        ? "Rp " +
                                                          Number(
                                                              item.cost,
                                                          ).toLocaleString()
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                v-for="month in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="month"
                                                class="px-2 py-6 text-[11px] font-black text-pail-gold text-center border-l border-gray-50 dark:border-gray-800"
                                            >
                                                {{ item[month] || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed border-l border-gray-50 dark:border-gray-800"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Jadwal Kebersihan -->
                                        <template
                                            v-else-if="
                                                type === 'jadwal-kebersihan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.subcategory || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.scheduled_at || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pengajuan RAB -->
                                        <template
                                            v-else-if="type === 'pengajuan-rab'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.volume || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td class="px-4 py-6 text-right">
                                                <span
                                                    class="text-xs font-mono font-black text-gray-900 dark:text-white"
                                                >
                                                    Rp
                                                    {{
                                                        Number(
                                                            item.unit_price ||
                                                                0,
                                                        ).toLocaleString()
                                                    }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-6 text-right">
                                                <span
                                                    class="text-xs font-mono font-black text-gray-900 dark:text-white"
                                                >
                                                    Rp
                                                    {{
                                                        Number(
                                                            item.total_price ||
                                                                0,
                                                        ).toLocaleString()
                                                    }}
                                                </span>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Jadwal Token -->
                                        <template
                                            v-else-if="type === 'jadwal-token'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}<br />
                                                <span
                                                    class="text-[9px] text-gray-400"
                                                    >{{
                                                        item.serial_number ||
                                                        "-"
                                                    }}</span
                                                >
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                Rp
                                                {{
                                                    Number(
                                                        item.total_price || 0,
                                                    ).toLocaleString()
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-pail-gold text-center tracking-[0.2em]"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center uppercase italic"
                                            >
                                                TERLAMPIR
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Penyerahan Barang -->
                                        <template
                                            v-else-if="
                                                type === 'penyerahan-barang'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.brand || "-" }}
                                                {{
                                                    item.size
                                                        ? "/ " + item.size
                                                        : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                <span
                                                    class="px-2 py-1 rounded text-[10px]"
                                                    :class="
                                                        item.after_condition ===
                                                            'B' ||
                                                        item.after_condition ===
                                                            'Baik'
                                                            ? 'bg-green-50 text-green-600'
                                                            : 'bg-red-50 text-red-600'
                                                    "
                                                >
                                                    {{
                                                        item.after_condition ||
                                                        "-"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Penerimaan Barang -->
                                        <template
                                            v-else-if="
                                                type === 'penerimaan-barang'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.brand || "-" }}
                                                {{
                                                    item.size
                                                        ? "/ " + item.size
                                                        : ""
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.source || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-pail-gold uppercase"
                                            >
                                                {{
                                                    item.performer?.name || "-"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pengadaan Sarpras -->
                                        <template
                                            v-else-if="
                                                type === 'pengadaan-sarpras'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.brand || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.size || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-medium text-gray-500 uppercase leading-relaxed"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="{
                                                        'bg-yellow-50 text-yellow-600 border-yellow-100':
                                                            item.status ===
                                                            'pending',
                                                        'bg-blue-50 text-blue-600 border-blue-100':
                                                            item.status ===
                                                            'ongoing',
                                                        'bg-green-50 text-green-600 border-green-100':
                                                            item.status ===
                                                            'completed',
                                                        'bg-red-50 text-red-600 border-red-100':
                                                            item.status ===
                                                            'cancelled',
                                                    }"
                                                >
                                                    {{
                                                        item.status || "pending"
                                                    }}
                                                </span>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Analisis Kebutuhan Sarpras -->
                                        <template
                                            v-else-if="
                                                type === 'analisis-kebutuhan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{
                                                    item.institution?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.quantity || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center uppercase"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="{
                                                        'bg-indigo-50 text-indigo-600 border-indigo-100':
                                                            item.procurement_type ===
                                                            'Pengadaan',
                                                        'bg-purple-50 text-purple-600 border-purple-100':
                                                            item.procurement_type ===
                                                            'Penggantian',
                                                    }"
                                                >
                                                    {{
                                                        item.procurement_type ||
                                                        "-"
                                                    }}
                                                </span>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Pemilihan & Evaluasi Supplier -->
                                        <template
                                            v-else-if="
                                                type === 'pemilihan-evaluasi'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.subcategory || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white text-center"
                                            >
                                                <div
                                                    class="flex flex-col items-center"
                                                >
                                                    <span
                                                        class="text-sm font-black"
                                                        >{{
                                                            item.sc_total
                                                        }}%</span
                                                    >
                                                    <span
                                                        class="text-[8px] uppercase tracking-widest text-gray-400"
                                                        >Total Skor</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="{
                                                        'bg-green-50 text-green-600 border-green-100':
                                                            item.status ===
                                                            'Terpilih',
                                                        'bg-yellow-50 text-yellow-600 border-yellow-100':
                                                            item.status ===
                                                            'Cadangan',
                                                        'bg-red-50 text-red-600 border-red-100':
                                                            item.status ===
                                                            'Tidak Terpilih',
                                                    }"
                                                >
                                                    {{ item.status || "-" }}
                                                </span>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for Parkir Area -->
                                        <template
                                            v-else-if="type === 'parkir-area'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.vehicle_type || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.plate_number || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.owner_name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-700 dark:text-gray-300 text-center"
                                            >
                                                {{
                                                    item.entry_time
                                                        ? new Date(
                                                              item.entry_time,
                                                          ).toLocaleTimeString(
                                                              [],
                                                              {
                                                                  hour: "2-digit",
                                                                  minute: "2-digit",
                                                              },
                                                          )
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-700 dark:text-gray-300 text-center"
                                            >
                                                {{
                                                    item.exit_time
                                                        ? new Date(
                                                              item.exit_time,
                                                          ).toLocaleTimeString(
                                                              [],
                                                              {
                                                                  hour: "2-digit",
                                                                  minute: "2-digit",
                                                              },
                                                          )
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight text-center"
                                            >
                                                {{ item.gate_name || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for BUKU INDUK -->
                                        <template
                                            v-else-if="
                                                type === 'buku-induk' ||
                                                type ===
                                                    'berita-acara-pemeriksaan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.institution?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                            >
                                                {{
                                                    item.room?.name || "Gudang"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-sm font-black text-gray-900 dark:text-white uppercase whitespace-nowrap"
                                            >
                                                {{ item.name }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-800 dark:text-white uppercase"
                                            >
                                                {{ item.brand || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{ item.specification || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 whitespace-nowrap"
                                            >
                                                {{ item.purchased_at || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.no_urut_satker || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-bold text-gray-400 uppercase whitespace-nowrap"
                                            >
                                                {{ item.code || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.no_urut_pondok || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-center text-[10px] font-black text-gray-700 dark:text-gray-200"
                                            >
                                                {{ item.stock || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400"
                                            >
                                                {{ item.unit || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                            >
                                                {{ item.source || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 whitespace-nowrap"
                                            >
                                                {{ item.received_at || "-" }}
                                            </td>
                                            <td class="px-4 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="
                                                        item.condition === 'B'
                                                            ? 'bg-green-50 text-green-600 border-green-100'
                                                            : 'bg-red-50 text-red-600 border-red-100'
                                                    "
                                                >
                                                    {{ item.condition }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-right text-xs font-mono font-black text-gray-900 dark:text-white"
                                            >
                                                Rp
                                                {{
                                                    Number(
                                                        item.price || 0,
                                                    ).toLocaleString()
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-right text-xs font-mono font-black text-pail-gold"
                                            >
                                                Rp
                                                {{
                                                    Number(
                                                        item.depreciation_price ||
                                                            0,
                                                    ).toLocaleString()
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                            >
                                                {{
                                                    item.responsible_person ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] text-gray-400 italic truncate max-w-[100px]"
                                            >
                                                {{ item.note || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for PEMELIHARAAN KAMAR MANDI -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'pemeliharaan-kamar-mandi'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.location || "-" }}
                                            </td>

                                            <!-- 9 Bathroom Component Columns -->
                                            <td
                                                v-for="col in [
                                                    'kran_air',
                                                    'lampu',
                                                    'fiting_lampu',
                                                    'saklar_lampu',
                                                    'ember',
                                                    'gayung',
                                                    'closet',
                                                    'pintu',
                                                    'grendel_pintu',
                                                ]"
                                                :key="col"
                                                class="px-1 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{ item[col] || "-" }}
                                            </td>

                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] text-gray-400 italic whitespace-normal min-w-[150px]"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <template
                                            v-else-if="
                                                type === 'monitoring-aset'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.institution?.name || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                            >
                                                {{ item.title || "-" }}
                                                <span
                                                    class="block text-[9px] text-gray-400 mt-1"
                                                    >{{
                                                        item.brand || "-"
                                                    }}</span
                                                >
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[11px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.location || "-" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center border-l border-gray-100 dark:border-gray-700 font-bold"
                                                :class="
                                                    item.condition === 'B'
                                                        ? 'text-green-600'
                                                        : 'text-gray-200'
                                                "
                                            >
                                                {{
                                                    item.condition === "B"
                                                        ? "✓"
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center border-l border-gray-100 dark:border-gray-700 font-bold"
                                                :class="
                                                    item.condition === 'KB'
                                                        ? 'text-yellow-600'
                                                        : 'text-gray-200'
                                                "
                                            >
                                                {{
                                                    item.condition === "KB"
                                                        ? "✓"
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-[10px] text-center border-l border-gray-100 dark:border-gray-700 font-bold"
                                                :class="
                                                    item.condition === 'R'
                                                        ? 'text-red-600'
                                                        : 'text-gray-200'
                                                "
                                            >
                                                {{
                                                    item.condition === "R"
                                                        ? "✓"
                                                        : "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center"
                                            >
                                                {{ formatDate(item.completed_at) }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center uppercase"
                                            >
                                                {{ item.performed_by || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400"
                                            >
                                                {{ item.condition_notes || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for ELECTRICAL MAINTENANCE -->
                                        <template
                                            v-else-if="
                                                type ===
                                                    'electrical-maintenance' ||
                                                type === 'pemeliharaan-listrik'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase whitespace-nowrap"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center whitespace-nowrap"
                                            >
                                                {{ item.check_standard || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center whitespace-nowrap"
                                            >
                                                {{ item.check_method || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center whitespace-nowrap"
                                            >
                                                {{
                                                    item.check_frequency || "-"
                                                }}
                                            </td>

                                            <td
                                                v-for="month in [
                                                    'jul_status',
                                                    'aug_status',
                                                    'sep_status',
                                                    'oct_status',
                                                    'nov_status',
                                                    'dec_status',
                                                    'jan_status',
                                                    'feb_status',
                                                    'mar_status',
                                                    'apr_status',
                                                    'may_status',
                                                    'jun_status',
                                                ]"
                                                :key="month"
                                                class="px-1 py-6 text-center border-l dark:border-gray-700 whitespace-nowrap"
                                            >
                                                <span
                                                    v-if="item[month] === 'V'"
                                                    class="text-green-500 font-black text-[10px]"
                                                    >V</span
                                                >
                                                <span
                                                    v-else-if="
                                                        item[month] === 'X'
                                                    "
                                                    class="text-red-500 font-black text-[10px]"
                                                    >X</span
                                                >
                                                <span
                                                    v-else
                                                    class="text-gray-200 dark:text-gray-700"
                                                    >-</span
                                                >
                                            </td>
                                        </template>

                                        <!-- Specialized Content for PEMELIHARAAN AC -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-ac'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>

                                            <!-- 6 AC Check Columns -->
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{ item.ac_indoor_pc || "-" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{ item.ac_indoor_sw || "-" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{
                                                    item.ac_outdoor_freon || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{ item.ac_outdoor_amp || "-" }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{
                                                    item.ac_kelistrikan_jaringan ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-2 py-6 text-center border-l dark:border-gray-700 text-[10px] font-black text-gray-600 dark:text-gray-400 font-mono"
                                            >
                                                {{
                                                    item.ac_kelistrikan_tegangan ||
                                                    "-"
                                                }}
                                            </td>

                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for PEMELIHARAAN KIPAS -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-kipas'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.fan_type || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{ item.subcategory || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                <div
                                                    class="inline-block px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest"
                                                    :class="
                                                        item.before_condition ===
                                                        'B'
                                                            ? 'bg-green-100 text-green-600'
                                                            : 'bg-red-100 text-red-600'
                                                    "
                                                >
                                                    {{
                                                        item.before_condition ===
                                                        "B"
                                                            ? "OK"
                                                            : "NG"
                                                    }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for FORMULIR PEMELIHARAAN SEPTIK TANK -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-septik'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center whitespace-nowrap"
                                            >
                                                {{
                                                    formatDate(
                                                        item.completed_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>

                                            <td
                                                v-for="col in [
                                                    'st_baik',
                                                    'st_penuh',
                                                    'st_bocor',
                                                    'st_bau',
                                                ]"
                                                :key="col"
                                                class="px-1 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    v-if="item[col] === 'V'"
                                                    class="text-pail-gold font-black text-[12px]"
                                                    >V</span
                                                >
                                                <span
                                                    v-else
                                                    class="text-gray-200 dark:text-gray-700"
                                                    >-</span
                                                >
                                            </td>

                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{
                                                    item.after_condition || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center whitespace-nowrap"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for JADWAL AGENDA PERBAIKAN SARPRAS -->
                                        <template
                                            v-else-if="
                                                type === 'agenda-perbaikan'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{
                                                    formatDate(
                                                        item.scheduled_at ||
                                                            item.created_at,
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-600 p-2 rounded-full font-black':
                                                            item.status ===
                                                            'pending',
                                                        'bg-blue-100 text-blue-600 p-2 rounded-full font-black':
                                                            item.status ===
                                                                'proses' ||
                                                            item.status ===
                                                                'processing',
                                                        'bg-green-100 text-green-600 p-2 rounded-full font-black':
                                                            item.status ===
                                                                'selesai' ||
                                                            item.status ===
                                                                'completed',
                                                    }"
                                                >
                                                    {{
                                                        item.status || "PENDING"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-l border-r dark:border-gray-700"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                        </template>

                                        <!-- Specialized Content for KIR RUANGAN -->
                                        <template
                                            v-else-if="type === 'kir-ruangan'"
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.name ||
                                                    item.title ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase"
                                            >
                                                {{ item.brand || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase font-mono"
                                            >
                                                {{ item.serial_number || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase text-center"
                                            >
                                                {{ item.size || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 uppercase text-center"
                                            >
                                                {{ item.material || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{
                                                    item.year ||
                                                    (item.purchased_at
                                                        ? new Date(
                                                              item.purchased_at,
                                                          ).getFullYear()
                                                        : "-")
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center font-mono"
                                            >
                                                {{ item.code || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{ item.stock || 0 }}
                                                {{ item.unit || "Unit" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-green-600 uppercase text-center font-bold"
                                            >
                                                {{
                                                    item.condition === "B"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-yellow-600 uppercase text-center font-bold"
                                            >
                                                {{
                                                    item.condition === "KB"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-red-600 uppercase text-center font-bold"
                                            >
                                                {{
                                                    item.condition === "RB"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] text-gray-400 italic whitespace-normal min-w-[150px]"
                                            >
                                                {{
                                                    item.note ||
                                                    item.description ||
                                                    "-"
                                                }}
                                            </td>
                                            <!-- Action buttons moved to universal column -->
                                        </template>

                                        <!-- Specialized Content for PENDATAAN ASET -->
                                        <template
                                            v-else-if="
                                                type === 'pendataan-aset'
                                            "
                                        >
                                            <td
                                                class="px-6 py-4 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.name || "-" }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{
                                                    item.institution?.name ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ item.stock || 0 }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ item.unit || "Unit" }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-green-600 text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    item.condition === "B"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-yellow-600 text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    item.condition === "KB"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-red-600 text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    item.condition === "RB"
                                                        ? item.stock || 0
                                                        : 0
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    formatDate(item.updated_at)
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-gray-600 dark:text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{
                                                    item.performed_by ||
                                                    item.responsible_person ||
                                                    item.person_in_charge ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[10px] font-black text-gray-900 dark:text-white text-right border-l dark:border-gray-700 whitespace-nowrap"
                                            >
                                                {{ formatRupiah(item.price) }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-[9px] text-gray-400 italic text-center border-l dark:border-gray-700 whitespace-normal min-w-[150px]"
                                            >
                                                {{
                                                    item.note ||
                                                    item.description ||
                                                    "-"
                                                }}
                                            </td>
                                            <!-- Action buttons moved to universal column -->
                                        </template>

                                        <!-- Specialized Content for FORMULIR LAPORAN PEMELIHARAAN SARANA PRASARANA -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-sarpras'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center border-l dark:border-gray-700"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{ item.check_standard || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center"
                                            >
                                                {{
                                                    item.room?.name ||
                                                    item.location ||
                                                    "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center font-mono"
                                            >
                                                {{
                                                    item.before_condition || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center"
                                            >
                                                {{ item.action_taken || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase text-center font-mono"
                                            >
                                                {{
                                                    item.after_condition || "-"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center whitespace-nowrap"
                                            >
                                                {{
                                                    item.performer?.name ||
                                                    "Staff URT"
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase text-center border-r dark:border-gray-700"
                                            >
                                                {{ item.description || "-" }}
                                            </td>
                                            <!-- Action buttons moved to universal column -->
                                        </template>

                                        <!-- Specialized Content for PEMELIHARAAN GEDUNG -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-gedung'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-4 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-gray-900 dark:text-white uppercase leading-tight"
                                                        >{{
                                                            item.title || "-"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[8px] font-bold text-gray-400 mt-1 uppercase leading-tight"
                                                        >{{
                                                            item.description
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center"
                                            >
                                                {{ item.check_standard || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center"
                                            >
                                                {{ item.check_method || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center font-mono"
                                            >
                                                {{
                                                    item.check_frequency || "-"
                                                }}
                                            </td>

                                            <!-- 12 Months Status Indicators -->
                                            <td
                                                v-for="month in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="month"
                                                class="px-1 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    v-if="item[month] === 'V'"
                                                    class="text-green-500 font-black text-[10px]"
                                                    >V</span
                                                >
                                                <span
                                                    v-else-if="
                                                        item[month] === 'X'
                                                    "
                                                    class="text-red-500 font-black text-[10px]"
                                                    >X</span
                                                >
                                                <span
                                                    v-else
                                                    class="text-gray-200 dark:text-gray-700"
                                                    >-</span
                                                >
                                            </td>
                                        </template>

                                        <!-- Specialized Content for PEMELIHARAAN POMPA, AIR BERSIH, AIR MINUM & GENSET -->
                                        <template
                                            v-else-if="
                                                [
                                                    'pemeliharaan-pompa',
                                                    'pemeliharaan-air-bersih',
                                                    'pemeliharaan-air-minum',
                                                    'pemeliharaan-genset',
                                                ].includes(type)
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-900 dark:text-white uppercase"
                                            >
                                                {{ item.title || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center"
                                            >
                                                {{ item.check_standard || "-" }}
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[9px] font-black text-gray-600 dark:text-gray-400 text-center"
                                            >
                                                {{ item.check_method || "-" }} /
                                                {{
                                                    item.check_frequency || "-"
                                                }}
                                            </td>

                                            <template
                                                v-for="m in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="m"
                                            >
                                                <td
                                                    v-for="a in [
                                                        'pemeliharaan-air-minum',
                                                        'pemeliharaan-genset',
                                                    ].includes(type)
                                                        ? ['putra', 'putri']
                                                        : type ===
                                                            'pemeliharaan-pompa'
                                                          ? [
                                                                'putra',
                                                                'putri',
                                                                'lawata',
                                                            ]
                                                          : [
                                                                'lawata',
                                                                'putra',
                                                                'putri',
                                                            ]"
                                                    :key="a"
                                                    class="px-1 py-6 text-center border-l dark:border-gray-700"
                                                >
                                                    <span
                                                        v-if="
                                                            item[
                                                                `${m}_${a}`
                                                            ] === 'V'
                                                        "
                                                        class="text-green-500 font-black text-[8px]"
                                                        >V</span
                                                    >
                                                    <span
                                                        v-else-if="
                                                            item[
                                                                `${m}_${a}`
                                                            ] === 'X'
                                                        "
                                                        class="text-red-500 font-black text-[8px]"
                                                        >X</span
                                                    >
                                                    <span
                                                        v-else
                                                        class="text-gray-200 dark:text-gray-700"
                                                        >-</span
                                                    >
                                                </td>
                                            </template>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-500 dark:text-gray-400 text-center border-l border-gray-100 dark:border-gray-700 max-w-[200px]"
                                            >
                                                <div class="truncate">
                                                    {{
                                                        item.description || "-"
                                                    }}
                                                </div>
                                                <div
                                                    v-if="item.action_taken"
                                                    class="text-[8px] text-pail-gold truncate mt-1"
                                                >
                                                    {{ item.action_taken }}
                                                </div>
                                            </td>
                                            <!-- Action buttons moved to universal column -->
                                        </template>

                                        <!-- Generic Item Display -->
                                        <template
                                            v-else-if="
                                                procedure?.model?.includes(
                                                    'Item',
                                                )
                                            "
                                        >
                                            <td
                                                class="px-8 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <span
                                                        class="text-xs font-black text-gray-900 dark:text-white uppercase"
                                                        >{{ item.name }}</span
                                                    >
                                                    <span
                                                        class="text-[8px] text-gray-400 font-bold uppercase tracking-widest border px-2 py-0.5 rounded-md"
                                                        >{{
                                                            item.code ||
                                                            "NO CODE"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase truncate max-w-[150px]"
                                                        >{{
                                                            item.location ||
                                                            item.room?.name ||
                                                            "Gudang"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1 truncate max-w-[150px]"
                                                        >{{
                                                            item.institution
                                                                ?.name ||
                                                            "Pusat"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-gray-800 dark:text-white uppercase"
                                                        >{{
                                                            item.brand || "-"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-gray-400 font-bold mt-1"
                                                        >{{ item.stock || 0 }}
                                                        {{
                                                            item.unit || "Unit"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                    :class="
                                                        item.condition === 'B'
                                                            ? 'bg-green-50 text-green-600 border-green-100'
                                                            : 'bg-red-50 text-red-600 border-red-100'
                                                    "
                                                >
                                                    {{ item.condition }}
                                                </span>
                                            </td>
                                        </template>

                                        <!-- Specialized Content for MONITORING ASET -->
                                        <template
                                            v-else-if="
                                                type === 'monitoring-aset'
                                            "
                                        >
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-400 text-center"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-4 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-xs font-black text-gray-900 dark:text-white uppercase"
                                                        >{{
                                                            item.name ||
                                                            item.title ||
                                                            "-"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-gray-400 uppercase mt-0.5"
                                                        >{{
                                                            item.brand || "-"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-6 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase"
                                            >
                                                {{
                                                    item.location ||
                                                    item.room?.name ||
                                                    "-"
                                                }}
                                            </td>

                                            <!-- 3 Columns for Condition Checks -->
                                            <td
                                                class="px-4 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    v-if="
                                                        item.condition === 'B'
                                                    "
                                                    class="text-green-500 font-black"
                                                    >V</span
                                                >
                                            </td>
                                            <td
                                                class="px-4 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    v-if="
                                                        item.condition === 'KB'
                                                    "
                                                    class="text-yellow-500 font-black"
                                                    >V</span
                                                >
                                            </td>
                                            <td
                                                class="px-4 py-6 text-center border-l dark:border-gray-700"
                                            >
                                                <span
                                                    v-if="
                                                        item.condition ===
                                                            'R' ||
                                                        item.condition === 'RB'
                                                    "
                                                    class="text-red-500 font-black"
                                                    >V</span
                                                >
                                            </td>

                                            <td
                                                class="px-8 py-6 text-[9px] text-gray-400 italic whitespace-normal min-w-[150px]"
                                            >
                                                {{
                                                    item.note ||
                                                    item.description ||
                                                    "-"
                                                }}
                                            </td>
                                            <!-- Action buttons moved to universal column -->
                                        </template>

                                        <!-- Specialized Content for BERITA ACARA PEMERIKSAAN -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'berita-acara-pemeriksaan'
                                            "
                                        >
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-gray-900 dark:text-white uppercase truncate max-w-xs"
                                                        >{{
                                                            item.title ||
                                                            item.name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[10px] text-gray-400 mt-1 truncate max-w-xs"
                                                        >{{
                                                            item.description ||
                                                            item.condition_notes ||
                                                            "Pemeriksaan Aset"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                                        >{{
                                                            item.location ||
                                                            item.room?.name ||
                                                            "Lokasi Tidak Spesifik"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1"
                                                        >{{
                                                            item.institution
                                                                ?.name ||
                                                            "Pusat"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div
                                                    class="flex flex-col items-center justify-center"
                                                >
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border"
                                                        :class="{
                                                            'bg-green-50 text-green-600 border-green-100':
                                                                item.condition ===
                                                                'B',
                                                            'bg-yellow-50 text-yellow-600 border-yellow-100':
                                                                item.condition ===
                                                                'KB',
                                                            'bg-red-50 text-red-600 border-red-100':
                                                                item.condition ===
                                                                    'R' ||
                                                                item.condition ===
                                                                    'RB',
                                                        }"
                                                    >
                                                        {{
                                                            item.condition ===
                                                            "B"
                                                                ? "BAIK"
                                                                : item.condition ===
                                                                    "KB"
                                                                  ? "KURANG BAIK"
                                                                  : item.condition ===
                                                                          "R" ||
                                                                      item.condition ===
                                                                          "RB"
                                                                    ? "RUSAK"
                                                                    : item.condition ||
                                                                      "-"
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-[8px] font-black text-gray-400 uppercase mt-1"
                                                        >{{
                                                            item.completed_at ||
                                                            "-"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                        </template>

                                        <!-- Maintenance / Other Display -->
                                        <template v-else>
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-gray-900 dark:text-white uppercase whitespace-normal min-w-[200px]"
                                                        >{{
                                                            item.title ||
                                                            item.name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[10px] text-gray-400 mt-1 whitespace-normal min-w-[200px]"
                                                        >{{
                                                            item.action_taken ||
                                                            item.description ||
                                                            "Sesuai Prosedur URT"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-gray-600 dark:text-gray-300 uppercase"
                                                        >{{
                                                            item.location ||
                                                            "Area Terbuka"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-pail-gold font-bold uppercase tracking-widest mt-1"
                                                        >{{
                                                            item.institution
                                                                ?.name ||
                                                            "Pusat"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div
                                                    class="flex flex-col items-center gap-1"
                                                >
                                                    <span
                                                        class="text-[8px] font-black text-gray-400 uppercase"
                                                        >SBLM:
                                                        {{
                                                            item.before_condition ||
                                                            "-"
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[8px] font-black text-green-600 uppercase"
                                                        >SSDH:
                                                        {{
                                                            item.after_condition ||
                                                            "-"
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                        </template>
                                    </template>

                                    <!-- Universal Action Column (Sticky Right) -->
                                    <td
                                        class="px-8 py-6 text-right sticky right-0 bg-white dark:bg-gray-800 shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.05)] dark:shadow-[-15px_0_20px_-10px_rgba(0,0,0,0.5)] z-10 border-l border-gray-50 dark:border-gray-700"
                                    >
                                        <div
                                            class="flex justify-end gap-3 transition-opacity"
                                        >
                                            <button
                                                @click="openEditModal(item)"
                                                class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-pail-gold hover:bg-pail-gold/10 transition-all"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="deleteItem(item.id)"
                                                class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="data.data.length === 0">
                                    <td
                                        :colspan="tableColspan"
                                        class="px-8 py-24 text-center"
                                    >
                                        <p
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        >
                                            Belum ada data di sistem.
                                        </p>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        type === 'pengajuan-rab' &&
                                        data.data.length > 0
                                    "
                                    class="bg-gray-900 text-white font-black"
                                >
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-[12px] uppercase tracking-widest text-right"
                                    >
                                        TOTAL
                                    </td>
                                    <td
                                        class="px-4 py-8 text-[12px] text-pail-gold text-right"
                                    >
                                        Rp
                                        {{ grandTotal.toLocaleString("id-ID") }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Removed Card View as per request for horizontal scroll only -->
                    <div
                        v-if="false"
                        class="md:hidden divide-y divide-gray-50 dark:divide-gray-700/50"
                    >
                        <div
                            v-for="(item, index) in data.data"
                            :key="item.id"
                            class="p-5 space-y-4"
                        >
                            <!-- Card Header -->
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center text-pail-gold text-[10px] font-black shrink-0 shadow-lg"
                                    >
                                        {{ index + 1 }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3
                                            class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight truncate"
                                        >
                                            {{
                                                item.title ||
                                                item.name ||
                                                item.subcategory ||
                                                item.item?.name ||
                                                "Data Prosedur"
                                            }}
                                        </h3>
                                        <p
                                            class="text-[8px] font-bold text-pail-gold uppercase tracking-widest mt-0.5 truncate"
                                        >
                                            {{
                                                item.code ||
                                                item.serial_number ||
                                                item.brand ||
                                                item.item?.code ||
                                                "#" + item.id
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button
                                        @click="openEditModal(item)"
                                        class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center text-gray-400 hover:text-pail-gold transition-colors"
                                    >
                                        <PencilIcon class="w-3.5 h-3.5" />
                                    </button>
                                    <button
                                        @click="deleteItem(item.id)"
                                        class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/10 flex items-center justify-center text-red-400 hover:text-red-600 transition-colors"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Card Info Grid -->
                            <div class="grid grid-cols-2 gap-y-4 gap-x-2">
                                <!-- Column 1: Core Meta -->
                                <div
                                    v-if="
                                        item.room?.name ||
                                        item.location ||
                                        item.institution?.name ||
                                        item.institution_name
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Lokasi / Lembaga
                                    </p>
                                    <p
                                        class="text-[9px] font-black text-gray-800 dark:text-gray-200 uppercase leading-tight truncate"
                                    >
                                        {{
                                            item.room?.name ||
                                            item.location ||
                                            item.institution?.name ||
                                            item.institution_name
                                        }}
                                    </p>
                                </div>

                                <!-- Column 2: Status Indicators -->
                                <div
                                    v-if="
                                        item.status ||
                                        item.condition ||
                                        item.after_condition ||
                                        item.before_condition
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Status / Kondisi
                                    </p>
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-full text-[7px] font-black uppercase tracking-tighter border shadow-sm"
                                        :class="{
                                            'bg-green-50 text-green-600 border-green-100':
                                                [
                                                    'selesai',
                                                    'completed',
                                                    'available',
                                                    'Terpilih',
                                                    'B',
                                                    'Baik',
                                                    'v',
                                                    'V',
                                                    'OK',
                                                ].includes(
                                                    (
                                                        item.status ||
                                                        item.condition ||
                                                        item.after_condition ||
                                                        item.before_condition ||
                                                        ''
                                                    )
                                                        .toString()
                                                        .toUpperCase() === 'V'
                                                        ? 'V'
                                                        : item.status ||
                                                              item.condition ||
                                                              item.after_condition ||
                                                              item.before_condition ||
                                                              '',
                                                ),
                                            'bg-yellow-50 text-yellow-600 border-yellow-100':
                                                [
                                                    'pending',
                                                    'proses',
                                                    'Cadangan',
                                                    'KB',
                                                    'PROCESSING',
                                                ].includes(
                                                    (
                                                        item.status || ''
                                                    ).toUpperCase(),
                                                ),
                                            'bg-red-50 text-red-600 border-red-100':
                                                [
                                                    'RUSAK',
                                                    'R',
                                                    'NG',
                                                    'TIDAK TERPILIH',
                                                    'X',
                                                ].includes(
                                                    (
                                                        item.status ||
                                                        item.condition ||
                                                        item.after_condition ||
                                                        ''
                                                    )
                                                        .toString()
                                                        .toUpperCase(),
                                                ),
                                        }"
                                    >
                                        {{
                                            item.status ||
                                            item.condition ||
                                            item.after_condition ||
                                            item.before_condition ||
                                            "Aktif"
                                        }}
                                    </span>
                                </div>

                                <!-- Column 3: Temporal Data -->
                                <div
                                    v-if="
                                        item.completed_at ||
                                        item.request_date ||
                                        item.borrow_date ||
                                        item.scheduled_at ||
                                        item.purchased_at
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Tanggal
                                    </p>
                                    <p
                                        class="text-[9px] font-black text-gray-700 dark:text-gray-300"
                                    >
                                        {{
                                            formatDate(
                                                item.completed_at ||
                                                    item.request_date ||
                                                    item.borrow_date ||
                                                    item.scheduled_at ||
                                                    item.purchased_at,
                                            )
                                        }}
                                    </p>
                                </div>

                                <!-- Column 4: Financial Meta -->
                                <div
                                    v-if="
                                        item.total_price ||
                                        item.price ||
                                        item.cost ||
                                        item.budget_amount
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Estimasi Nilai
                                    </p>
                                    <p
                                        class="text-[10px] font-black text-pail-gold font-mono leading-none"
                                    >
                                        Rp
                                        {{
                                            Number(
                                                item.total_price ||
                                                    item.price ||
                                                    item.cost ||
                                                    item.budget_amount ||
                                                    0,
                                            ).toLocaleString()
                                        }}
                                    </p>
                                </div>

                                <!-- Column 5: Resource (Secondary) -->
                                <div
                                    v-if="
                                        item.quantity ||
                                        item.stock ||
                                        item.volume
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Jumlah / Volume
                                    </p>
                                    <p
                                        class="text-[9px] font-black text-gray-700 dark:text-gray-300 uppercase"
                                    >
                                        {{
                                            item.quantity ||
                                            item.stock ||
                                            item.volume
                                        }}
                                        {{ item.unit || "UNIT" }}
                                    </p>
                                </div>

                                <!-- Column 6: Performer / Responsible -->
                                <div
                                    v-if="
                                        item.performer?.name ||
                                        item.responsible_person ||
                                        item.performed_by
                                    "
                                    class="space-y-1"
                                >
                                    <p
                                        class="text-[7px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Petugas / P.Jawab
                                    </p>
                                    <p
                                        class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase truncate"
                                    >
                                        {{
                                            item.performer?.name ||
                                            item.responsible_person ||
                                            item.performed_by
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer Detail: Keterangan / Action -->
                            <div
                                v-if="
                                    item.description ||
                                    item.remarks ||
                                    item.note ||
                                    item.action_taken
                                "
                                class="pt-3 border-t border-gray-50 dark:border-gray-700/50"
                            >
                                <p
                                    class="text-[7px] font-black text-gray-400 uppercase tracking-widest mb-1.5"
                                >
                                    Keterangan / Tindakan
                                </p>
                                <div
                                    class="px-3 py-2 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-[9px] text-gray-500 italic leading-relaxed"
                                >
                                    {{
                                        item.description ||
                                        item.remarks ||
                                        item.note ||
                                        item.action_taken ||
                                        "-"
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="!data.data.length"
                            class="py-20 flex flex-col items-center justify-center opacity-30"
                        >
                            <svg
                                class="w-12 h-12 mb-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                ></path>
                            </svg>
                            <p
                                class="text-[10px] font-black uppercase tracking-widest"
                            >
                                Data Tidak Ditemukan
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="
                            type === 'agenda-perbaikan' && data.data.length > 0
                        "
                        class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex flex-col gap-2">
                            <div
                                class="flex items-start gap-4 text-[10px] font-black uppercase text-gray-400 tracking-widest"
                            >
                                <span class="shrink-0 text-pail-gold"
                                    >KET :</span
                                >
                                <div class="flex flex-col gap-1">
                                    <span
                                        >- PERMINTAAN DARURAT TETAP DIDAHULUKAN
                                        JIKA ADA</span
                                    >
                                    <span
                                        >- PERUBAHAN PERSONIL MENYESUAIKAN
                                        KONDISI DILAPANGAN</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center gap-2">
                <Link
                    v-for="link in data.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all"
                    :class="
                        link.active
                            ? 'bg-gray-900 text-pail-gold border-gray-900'
                            : 'bg-white text-gray-400 border-gray-100 hover:border-pail-gold'
                    "
                    v-html="link.label"
                />
            </div>
        </div>
        <!-- Create Modal -->
        <Teleport to="body">
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-black/60 backdrop-blur-md"
            >
                <div
                    class="bg-white dark:bg-gray-800 w-full rounded-t-[2rem] sm:rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-white/10 max-h-[90vh] overflow-y-auto pb-28 sm:pb-0"
                    :class="
                        ['rab-tahunan', 'visi-2030'].includes(type)
                            ? 'max-w-5xl'
                            : 'max-w-2xl'
                    "
                >
                    <BudgetPlanForm
                        v-if="['rab-tahunan', 'visi-2030'].includes(type)"
                        :type="type"
                        :initialData="isEdit ? form : null"
                        @close="showCreateModal = false"
                        @success="showCreateModal = false"
                    />
                    <div v-else class="p-6 md:p-12">
                        <div
                            class="flex items-start justify-between mb-8 md:mb-10"
                        >
                            <div>
                                <h3
                                    class="text-xl md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight"
                                >
                                    {{
                                        isEdit
                                            ? "Update Prosedur"
                                            : "Entri Prosedur Baru"
                                    }}
                                </h3>
                                <p
                                    class="text-[9px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1"
                                >
                                    SOP Entry System
                                </p>
                            </div>
                            <button
                                @click="showCreateModal = false"
                                class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-500 transition-colors flex items-center justify-center shrink-0"
                            >
                                <XIcon class="w-6 h-6" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Field Khusus REGISTRASI KENDARAAN -->
                                <template
                                    v-if="type === 'registrasi-kendaraan'"
                                >
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Nama Kendaraan</label
                                        >
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="Contoh: Bus Sekolah Hino"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Nomor Plat (TNKB)</label
                                        >
                                        <input
                                            v-model="form.plate_number"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="DR 1234 XY"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Merk / Tipe</label
                                        >
                                        <input
                                            v-model="form.brand"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="Toyota / Mitsubishi"
                                        />
                                    </div>
                                    <div
                                        class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Tipe Armada</label
                                            >
                                            <select
                                                v-model="form.type"
                                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            >
                                                <option value="Mobil">
                                                    Mobil
                                                </option>
                                                <option value="Motor">
                                                    Motor
                                                </option>
                                                <option value="Bus">Bus</option>
                                                <option value="Pick Up">
                                                    Pick Up
                                                </option>
                                                <option value="Lainnya">
                                                    Lainnya
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Status Unit</label
                                            >
                                            <select
                                                v-model="form.status"
                                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            >
                                                <option value="available">
                                                    Tersedia (Ready)
                                                </option>
                                                <option value="in_use">
                                                    Sedang Digunakan
                                                </option>
                                                <option value="maintenance">
                                                    Dalam Perbaikan
                                                </option>
                                                <option value="retired">
                                                    Rusak / Tidak Layak
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </template>

                                <!-- Field Khusus PENGGUNAAN KENDARAAN -->
                                <template
                                    v-else-if="type === 'penggunaan-kendaraan'"
                                >
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Pilih Armada (Kendaraan)</label
                                        >
                                        <SearchableSelect
                                            v-model="form.vehicle_id"
                                            :options="vehicles"
                                            placeholder="Pilih Armada"
                                            :customLabel="
                                                (opt) =>
                                                    `${opt.name} - ${opt.plate_number}`
                                            "
                                        />
                                        <div
                                            v-if="form.errors.vehicle_id"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.vehicle_id }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Lembaga / Unit Pemakai</label
                                        >
                                        <input
                                            v-model="form.institution_name"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="Contoh: KMI Putra / URT"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Tujuan Perjalanan</label
                                        >
                                        <input
                                            v-model="form.destination"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                        <div
                                            v-if="form.errors.destination"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.destination }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Keperluan / Acara</label
                                        >
                                        <textarea
                                            v-model="form.purpose"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            rows="2"
                                        ></textarea>
                                        <div
                                            v-if="form.errors.purpose"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.purpose }}
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Jam Berangkat (Estimasi)</label
                                        >
                                        <input
                                            v-model="form.start_time"
                                            type="datetime-local"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Jam Pulang (Estimasi)</label
                                        >
                                        <input
                                            v-model="form.end_time"
                                            type="datetime-local"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Penanggung Jawab / Sopir</label
                                        >
                                        <input
                                            v-model="form.responsible_person"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                </template>

                                <!-- Field Khusus ISO CHECKLIST -->
                                <template v-else-if="type === 'ceklist-iso'">
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Judul Ceklist (ISO Name)</label
                                        >
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="Contoh: Ceklist Kebersihan Gedung"
                                        />
                                        <div
                                            v-if="form.errors.title"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.title }}
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Kategori</label
                                        >
                                        <select
                                            v-model="form.category"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        >
                                            <option value="">
                                                Pilih Kategori
                                            </option>
                                            <option value="Kebersihan">
                                                Kebersihan
                                            </option>
                                            <option value="Keamanan">
                                                Keamanan
                                            </option>
                                            <option value="Sarpras">
                                                Sarpras
                                            </option>
                                            <option value="Kendaraan">
                                                Kendaraan
                                            </option>
                                            <option value="Umum">Umum</option>
                                        </select>
                                        <div
                                            v-if="form.errors.category"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.category }}
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Frekuensi Cek</label
                                        >
                                        <select
                                            v-model="form.frequency"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        >
                                            <option value="daily">
                                                Harian
                                            </option>
                                            <option value="weekly">
                                                Pekanan
                                            </option>
                                            <option value="monthly">
                                                Bulanan
                                            </option>
                                            <option value="yearly">
                                                Tahunan
                                            </option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2 space-y-4">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest block"
                                            >Daftar Item / Kegiatan</label
                                        >
                                        <div class="flex gap-2">
                                            <input
                                                v-model="newItem"
                                                @keyup.enter="addItem"
                                                type="text"
                                                class="flex-1 bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                placeholder="Ketik item checklist lalu tekan Enter"
                                            />
                                            <button
                                                type="button"
                                                @click="addItem"
                                                class="px-6 rounded-2xl bg-pail-gold text-white font-bold text-xs hover:bg-pail-gold/80 transition-colors"
                                            >
                                                Tambah
                                            </button>
                                        </div>
                                        <div
                                            class="space-y-2 max-h-60 overflow-y-auto pr-2"
                                        >
                                            <div
                                                v-for="(
                                                    item, idx
                                                ) in form.items"
                                                :key="idx"
                                                class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700"
                                            >
                                                <span
                                                    class="text-xs font-bold text-gray-700 dark:text-gray-300"
                                                    >{{ idx + 1 }}.
                                                    {{ item }}</span
                                                >
                                                <button
                                                    type="button"
                                                    @click="removeItem(idx)"
                                                    class="text-red-500 hover:text-red-700 p-1"
                                                >
                                                    <TrashIcon
                                                        class="w-4 h-4"
                                                    />
                                                </button>
                                            </div>
                                            <div
                                                v-if="form.items.length === 0"
                                                class="text-center py-8 text-gray-400 text-[10px] uppercase font-bold tracking-widest border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-2xl"
                                            >
                                                Belum ada item ditambahkan
                                            </div>
                                        </div>
                                        <div
                                            v-if="form.errors.items"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.items }}
                                        </div>
                                    </div>
                                </template>

                                <!-- Field Khusus PEMELIHARAAN LISTRIK -->
                                <template
                                    v-else-if="type === 'pemeliharaan-listrik'"
                                >
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Item Pengecekan</label
                                        >
                                        <select
                                            v-model="form.title"
                                            @change="handlePeriodicItemChange"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        >
                                            <option value="">Pilih Item</option>
                                            <option
                                                v-for="item in listrikItems"
                                                :key="item.title"
                                                :value="item.title"
                                            >
                                                {{ item.title }}
                                            </option>
                                            <option value="Lainnya">
                                                Lainnya (Ketik Manual)
                                            </option>
                                        </select>
                                        <input
                                            v-if="form.title === 'Lainnya'"
                                            v-model="form.other_title"
                                            type="text"
                                            placeholder="Masukkan nama item..."
                                            class="mt-2 w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                        <div
                                            v-if="form.errors.title"
                                            class="text-red-500 text-[10px] font-bold mt-1"
                                        >
                                            {{ form.errors.title }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Lokasi / Area Jaringan</label
                                        >
                                        <input
                                            v-model="form.location"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            placeholder="Contoh: Gedung A, Panel Utama"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Standard</label
                                        >
                                        <input
                                            v-model="form.specification"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Metode</label
                                        >
                                        <input
                                            v-model="form.action_taken"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Frekuensi</label
                                        >
                                        <input
                                            v-model="form.frequency"
                                            type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                            >Tahun Periode</label
                                        >
                                        <select
                                            v-model="form.period_year"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                        >
                                            <option
                                                v-for="y in [
                                                    2024, 2025, 2026, 2027,
                                                    2028,
                                                ]"
                                                :key="y"
                                                :value="y.toString()"
                                            >
                                                {{ y }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2 mt-4">
                                        <div
                                            class="flex items-center justify-between mb-2"
                                        >
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest block"
                                                >Checklist Bulanan</label
                                            >
                                            <div class="flex gap-2">
                                                <span
                                                    class="text-[7px] font-black text-green-600 uppercase"
                                                    >V: OK</span
                                                >
                                                <span
                                                    class="text-[7px] font-black text-red-600 uppercase"
                                                    >X: NG</span
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="grid grid-cols-4 sm:grid-cols-6 gap-2"
                                        >
                                            <div
                                                v-for="(monthKey, idx) in [
                                                    'jul',
                                                    'aug',
                                                    'sep',
                                                    'oct',
                                                    'nov',
                                                    'dec',
                                                    'jan',
                                                    'feb',
                                                    'mar',
                                                    'apr',
                                                    'may',
                                                    'jun',
                                                ]"
                                                :key="monthKey"
                                                class="p-2 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700 flex flex-col items-center gap-1"
                                            >
                                                <span
                                                    class="text-[9px] font-black text-gray-400 uppercase whitespace-nowrap"
                                                    >{{
                                                        [
                                                            "JUL",
                                                            "AGT",
                                                            "SEP",
                                                            "OKT",
                                                            "NOV",
                                                            "DES",
                                                            "JAN",
                                                            "FEB",
                                                            "MAR",
                                                            "APR",
                                                            "MEI",
                                                            "JUN",
                                                        ][idx]
                                                    }}</span
                                                >
                                                <select
                                                    v-model="
                                                        form.monthly_data[
                                                            monthKey
                                                        ]
                                                    "
                                                    class="w-full text-[10px] font-bold bg-white dark:bg-gray-800 border-0 rounded-lg py-1 px-0 focus:ring-1 focus:ring-pail-gold text-center"
                                                >
                                                    <option :value="undefined">
                                                        -
                                                    </option>
                                                    <option value="V">V</option>
                                                    <option value="X">X</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Field Standar / MAINTENANCE -->
                                <template v-else>
                                    <!-- ISO FIELDS FOR ITEMS (Buku Induk & Laporan Aset) -->
                                    <template
                                        v-if="
                                            procedure?.model?.includes('Item')
                                        "
                                    >
                                        <!-- SATKER always at top for ISO Forms -->
                                        <div class="sm:col-span-2">
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Satuan Kerja (Lembaga)
                                                <span class="text-red-500"
                                                    >*Wajib</span
                                                ></label
                                            >
                                            <SearchableSelect
                                                v-model="form.institution_id"
                                                :options="institutions"
                                                placeholder="Pilih Lembaga"
                                            />
                                            <div
                                                v-if="
                                                    form.errors.institution_id
                                                "
                                                class="text-red-500 text-[10px] font-bold mt-1"
                                            >
                                                {{ form.errors.institution_id }}
                                            </div>
                                        </div>

                                        <!-- INSTITUTION SELECTION -->
                                        <div
                                            v-if="type === 'pendataan-aset' || type === 'kir-ruangan'"
                                            class="sm:col-span-2"
                                        >
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Satuan Kerja (Lembaga)</label
                                            >
                                            <SearchableSelect
                                                v-model="form.institution_id"
                                                :options="institutions"
                                                placeholder="Pilih Satuan Kerja..."
                                            />
                                        </div>

                                        <div
                                            :class="
                                                type === 'pendataan-aset' ||
                                                type === 'kir-ruangan'
                                                    ? 'sm:col-span-2'
                                                    : ''
                                            "
                                        >
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >{{
                                                    type === "kir-ruangan"
                                                        ? "Jenis Barang / Nama Barang"
                                                        : "Jenis Barang"
                                                }}
                                                <span class="text-red-500"
                                                    >*Wajib</span
                                                ></label
                                            >
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                required
                                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                placeholder="Sesuai ISO Standard"
                                            />
                                        </div>

                                        <div
                                            v-if="
                                                type !== 'pendataan-aset' &&
                                                type !== 'kir-ruangan'
                                            "
                                        >
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Ruangan / Lokasi</label
                                            >
                                            <SearchableSelect
                                                v-model="form.room_id"
                                                :options="rooms"
                                                placeholder="Pilih Ruangan"
                                            />
                                        </div>

                                        <template
                                            v-if="
                                                type === 'pendataan-aset' ||
                                                type === 'kir-ruangan'
                                            "
                                        >
                                            <div
                                                v-if="type === 'kir-ruangan'"
                                                class="grid grid-cols-2 gap-4"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Merk/Kode</label
                                                    >
                                                    <input
                                                        v-model="form.brand"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >No. Seri Pabrik</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.serial_number
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Ukuran</label
                                                    >
                                                    <input
                                                        v-model="form.size"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Bahan</label
                                                    >
                                                    <input
                                                        v-model="form.material"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >{{
                                                            type ===
                                                            "kir-ruangan"
                                                                ? "Jumlah Barang"
                                                                : "Jumlah Aset"
                                                        }}</label
                                                    >
                                                    <input
                                                        v-model="form.stock"
                                                        type="number"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Nama Satuan</label
                                                    >
                                                    <select
                                                        v-model="form.unit"
                                                        @change="
                                                            handleUnitChange
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option
                                                            v-for="unit in unitOptions"
                                                            :key="unit"
                                                            :value="unit"
                                                        >
                                                            {{ unit }}
                                                        </option>
                                                    </select>
                                                    <!-- Custom Unit Input -->
                                                    <div
                                                        v-if="showCustomUnit"
                                                        class="mt-2 flex gap-2"
                                                    >
                                                        <input
                                                            v-model="
                                                                customUnitValue
                                                            "
                                                            type="text"
                                                            placeholder="Masukkan satuan custom..."
                                                            class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        />
                                                        <button
                                                            @click="
                                                                applyCustomUnit
                                                            "
                                                            type="button"
                                                            class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                        >
                                                            OK
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:col-span-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >{{
                                                            type ===
                                                            "kir-ruangan"
                                                                ? "Tahun Perolehan"
                                                                : "Tanggal Pengecekan"
                                                        }}</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.purchased_at
                                                        "
                                                        type="date"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div
                                                    v-if="
                                                        type === 'kir-ruangan'
                                                    "
                                                >
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >No. Kode Barang</label
                                                    >
                                                    <input
                                                        v-model="form.code"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div v-else>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Petugas Pemeriksa</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="form.performed_by"
                                                        :options="petugasOptions"
                                                        placeholder="Pilih Petugas..."
                                                        allow-create
                                                        @create="handlePetugasCreate"
                                                    />
                                                </div>
                                                <div
                                                    v-if="
                                                        type ===
                                                        'pendataan-aset'
                                                    "
                                                >
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Nilai Aset</label
                                                    >
                                                    <input
                                                        :value="formatRupiah(form.price)"
                                                        @input="(e) => handleRupiahInput(e, 'price')"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk (Brand)</label
                                                >
                                                <input
                                                    v-model="form.brand"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk (Specification)</label
                                                >
                                                <input
                                                    v-model="form.specification"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl Pembukuan /
                                                    Pembelian</label
                                                >
                                                <input
                                                    v-model="form.purchased_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl Penyerahan</label
                                                >
                                                <input
                                                    v-model="form.received_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >No Urut Satker /
                                                    Pondok</label
                                                >
                                                <div
                                                    class="grid grid-cols-2 gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            form.no_urut_satker
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="No. Satker"
                                                    />
                                                    <input
                                                        v-model="
                                                            form.no_urut_pondok
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="No. Pondok"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kode Barang</label
                                                >
                                                <input
                                                    v-model="form.code"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kuantitas & Satuan</label
                                                >
                                                <div
                                                    class="grid grid-cols-2 gap-2"
                                                >
                                                    <input
                                                        v-model="form.stock"
                                                        type="number"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <select
                                                        v-model="form.unit"
                                                        @change="
                                                            handleUnitChange
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option
                                                            v-for="unit in unitOptions"
                                                            :key="unit"
                                                            :value="unit"
                                                        >
                                                            {{ unit }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <!-- Custom Unit Input -->
                                                <div
                                                    v-if="showCustomUnit"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customUnitValue
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan satuan custom..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="applyCustomUnit"
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Sumber Perolehan</label
                                                >
                                                <input
                                                    v-model="form.source"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tahun
                                                    Perolehan/Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.purchased_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Penanggung Jawab</label
                                                >
                                                <input
                                                    v-model="
                                                        form.responsible_person
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Harga Perolehan</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.price)"
                                                    @input="(e) => handleRupiahInput(e, 'price')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Harga Setelah
                                                    Penyusutan</label
                                                >
                                                <input
                                                    v-model="
                                                        form.depreciation_price
                                                    "
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                        </template>

                                        <div class="sm:col-span-2">
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Kondisi Saat Ini</label
                                            >
                                            <select
                                                v-model="form.condition"
                                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                            >
                                                <option value="B">
                                                    BAIK
                                                </option>
                                                <option value="KB">
                                                    KURANG BAIK
                                                </option>
                                                <option value="RB">
                                                    RUSAK
                                                </option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                >Keterangan</label
                                            >
                                            <textarea
                                                v-model="form.note"
                                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                rows="2"
                                            ></textarea>
                                        </div>
                                    </template>

                                    <!-- ISO FIELDS FOR MAINTENANCE -->
                                    <template v-else>
                                        <!-- Specialized fields for Pemeliharaan Kamar Mandi -->
                                        <template
                                            v-if="
                                                type ===
                                                'pemeliharaan-kamar-mandi'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >AREA (Grup Lokasi)</label
                                                >
                                                <input
                                                    v-model="form.subcategory"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: AREA MASJID / AREA ASRAMA"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / No Kamar
                                                    Mandi</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div
                                                class="sm:col-span-2 grid grid-cols-3 gap-3 border-t border-gray-100 dark:border-gray-700 pt-6 mt-2"
                                            >
                                                <div
                                                    v-for="comp in [
                                                        [
                                                            'Kran Air',
                                                            'kran_air',
                                                        ],
                                                        ['Lampu', 'lampu'],
                                                        [
                                                            'Fiting Lampu',
                                                            'fiting_lampu',
                                                        ],
                                                        [
                                                            'Saklar Lampu',
                                                            'saklar_lampu',
                                                        ],
                                                        ['Ember', 'ember'],
                                                        ['Gayung', 'gayung'],
                                                        ['Closet', 'closet'],
                                                        ['Pintu', 'pintu'],
                                                        [
                                                            'Grendel Pintu',
                                                            'grendel_pintu',
                                                        ],
                                                    ]"
                                                    :key="comp[1]"
                                                >
                                                    <label
                                                        class="text-[8px] font-black text-gray-400 uppercase mb-1 block"
                                                        >{{ comp[0] }}</label
                                                    >
                                                    <input
                                                        v-model="form[comp[1]]"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="sm:col-span-2 mt-4 grid grid-cols-2 gap-4"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Petugas
                                                        Pelaksana</label
                                                    >
                                                    <select
                                                        v-model="
                                                            form.performed_by
                                                        "
                                                        @change="
                                                            handlePetugasChange(
                                                                form.performed_by,
                                                            )
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option
                                                            v-for="petugas in petugasOptions"
                                                            :key="petugas"
                                                            :value="petugas"
                                                        >
                                                            {{ petugas }}
                                                        </option>
                                                        <option
                                                            value="_custom_"
                                                        >
                                                            + Tambah Petugas
                                                            Lain...
                                                        </option>
                                                    </select>
                                                    <div
                                                        v-if="showCustomPetugas"
                                                        class="mt-2 flex gap-2"
                                                    >
                                                        <input
                                                            v-model="
                                                                customPetugasValue
                                                            "
                                                            type="text"
                                                            placeholder="Nama petugas..."
                                                            class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        />
                                                        <button
                                                            @click="
                                                                applyCustomPetugas
                                                            "
                                                            type="button"
                                                            class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                        >
                                                            OK
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Tanggal
                                                        Pengecekan</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.completed_at
                                                        "
                                                        type="date"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 mt-4">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindakan / Perbaikan</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan AC -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-ac'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Divisi
                                                    (Unit/Lembaga)</label
                                                >
                                                <input
                                                    v-model="form.ac_divisi"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pelaksana</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Ruangan / Lokasi AC</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl / Bln Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.ac_tgl_bln"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: 05 Feb"
                                                />
                                            </div>

                                            <div
                                                class="sm:col-span-2 grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-6 mt-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Indoor: PC</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_indoor_pc
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Indoor: SW</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_indoor_sw
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Outdoor: Freon</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_outdoor_freon
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Outdoor: Ampere</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_outdoor_amp
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Kelistrikan:
                                                        Jaringan</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_kelistrikan_jaringan
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Kelistrikan:
                                                        Tegangan</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.ac_kelistrikan_tegangan
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for FORMULIR PEMELIHARAAN SEPTIK TANK -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-septik'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Pemeriksaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>

                                            <div class="sm:col-span-2 py-4">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 block text-center"
                                                    >Kondisi Septik Tank</label
                                                >
                                                <div
                                                    class="grid grid-cols-4 gap-4"
                                                >
                                                    <div
                                                        v-for="(label, key) in {
                                                            st_baik: 'Baik',
                                                            st_penuh: 'Penuh',
                                                            st_bocor: 'Bocor',
                                                            st_bau: 'Bau',
                                                        }"
                                                        :key="key"
                                                        class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-pail-gold/30 transition-all"
                                                    >
                                                        <label
                                                            class="text-[10px] font-black text-gray-400 uppercase mb-3"
                                                            >{{ label }}</label
                                                        >
                                                        <select
                                                            v-model="form[key]"
                                                            class="w-full border-0 bg-transparent text-center text-sm font-black focus:ring-0 appearance-none cursor-pointer"
                                                        >
                                                            <option value="">
                                                                -
                                                            </option>
                                                            <option value="V">
                                                                V
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="sm:col-span-2 space-y-4"
                                            >
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block font-black"
                                                        >Tindakan
                                                        Pemeliharaan</label
                                                    >
                                                    <textarea
                                                        v-model="
                                                            form.action_taken
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        rows="2"
                                                        placeholder="Detail tindakan yang dilakukan..."
                                                    ></textarea>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block font-black"
                                                        >Hasil Tindakan</label
                                                    >
                                                    <textarea
                                                        v-model="
                                                            form.after_condition
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        rows="2"
                                                        placeholder="Kondisi akhir setelah tindakan..."
                                                    ></textarea>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Petugas
                                                        Pemeriksa</label
                                                    >
                                                    <select
                                                        v-model="
                                                            form.performed_by
                                                        "
                                                        @change="
                                                            handlePetugasChange(
                                                                form.performed_by,
                                                            )
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option
                                                            v-for="petugas in petugasOptions"
                                                            :key="petugas"
                                                            :value="petugas"
                                                        >
                                                            {{ petugas }}
                                                        </option>
                                                        <option
                                                            value="_custom_"
                                                        >
                                                            + Tambah Petugas
                                                            Lain...
                                                        </option>
                                                    </select>
                                                    <div
                                                        v-if="showCustomPetugas"
                                                        class="mt-2 flex gap-2"
                                                    >
                                                        <input
                                                            v-model="
                                                                customPetugasValue
                                                            "
                                                            type="text"
                                                            placeholder="Nama petugas..."
                                                            class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        />
                                                        <button
                                                            @click="
                                                                applyCustomPetugas
                                                            "
                                                            type="button"
                                                            class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                        >
                                                            OK
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Kipas Angin -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-kipas'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Pemeriksaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tipe Kipas</label
                                                >
                                                <select
                                                    v-model="form.fan_type"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Tipe
                                                    </option>
                                                    <option value="Dinding">
                                                        Dinding
                                                    </option>
                                                    <option
                                                        value="Langit-langit"
                                                    >
                                                        Langit-langit
                                                    </option>
                                                    <option value="Berdiri">
                                                        Berdiri
                                                    </option>
                                                    <option value="Exhaust">
                                                        Exhaust
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jenis Pemeliharaan</label
                                                >
                                                <select
                                                    v-model="form.subcategory"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Jenis
                                                    </option>
                                                    <option value="Rutin">
                                                        Rutin
                                                    </option>
                                                    <option value="Berkala">
                                                        Berkala
                                                    </option>
                                                    <option value="Perbaikan">
                                                        Perbaikan
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Saat
                                                    Diperiksa</label
                                                >
                                                <select
                                                    v-model="
                                                        form.before_condition
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="B">
                                                        B (Baik/OK)
                                                    </option>
                                                    <option value="RB">
                                                        R (Rusak/NG)
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindakan / Perbaikan</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pemeriksa</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Gedung (Checklist Style) -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-gedung'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Gedung</label
                                                >
                                                <input
                                                    v-model="form.subcategory"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Gedung A"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi Spesifik</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Lantai 1 / Koridor"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Item Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Plafon / Atap"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Standard Pengecekan</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_standard
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Metode Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.check_method"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Frekuensi</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_frequency
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Harian/Bulanan/dll"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tahun Monitoring</label
                                                >
                                                <input
                                                    v-model="form.year"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="2024/2025"
                                                />
                                            </div>

                                            <!-- Monthly Log Entry -->
                                            <div class="sm:col-span-2 py-4">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 block text-center"
                                                    >Rekap Hasil Pengecekan (Jul
                                                    - Jun)</label
                                                >
                                                <div
                                                    class="grid grid-cols-6 gap-3"
                                                >
                                                    <div
                                                        v-for="month in [
                                                            'jul',
                                                            'aug',
                                                            'sep',
                                                            'oct',
                                                            'nov',
                                                            'dec',
                                                            'jan',
                                                            'feb',
                                                            'mar',
                                                            'apr',
                                                            'may',
                                                            'jun',
                                                        ]"
                                                        :key="month"
                                                        class="flex flex-col items-center"
                                                    >
                                                        <label
                                                            class="text-[8px] font-black text-gray-400 uppercase mb-1"
                                                            >{{ month }}</label
                                                        >
                                                        <select
                                                            v-model="
                                                                form[month]
                                                            "
                                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-2 py-2 text-[10px] focus:ring-2 focus:ring-pail-gold"
                                                        >
                                                            <option value="">
                                                                -
                                                            </option>
                                                            <option value="V">
                                                                V (OK)
                                                            </option>
                                                            <option value="X">
                                                                X (No)
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 mt-4">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Penanggung Jawab
                                                    (URT)</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Pompa, Air Bersih, Air Minum & Genset -->
                                        <template
                                            v-if="
                                                [
                                                    'pemeliharaan-pompa',
                                                    'pemeliharaan-air-bersih',
                                                    'pemeliharaan-air-minum',
                                                    'pemeliharaan-genset',
                                                ].includes(type)
                                            "
                                        >
                                            <div
                                                class="sm:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4"
                                            >
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Satuan Kerja
                                                        (Lembaga)</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="
                                                            form.institution_id
                                                        "
                                                        :options="institutions"
                                                        placeholder="Pilih Satuan Kerja..."
                                                    />
                                                </div>
                                                <div
                                                    v-if="
                                                        type ===
                                                        'pemeliharaan-genset'
                                                    "
                                                >
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Kegiatan</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.subcategory
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div
                                                    v-if="
                                                        type ===
                                                        'pemeliharaan-genset'
                                                    "
                                                >
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >No Mesin</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.serial_number
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Lokasi Unit
                                                        {{
                                                            type ===
                                                            "pemeliharaan-pompa"
                                                                ? "Pompa"
                                                                : type ===
                                                                    "pemeliharaan-air-bersih"
                                                                  ? "Air Bersih"
                                                                  : type ===
                                                                      "pemeliharaan-air-minum"
                                                                    ? "Air Minum"
                                                                    : "Genset"
                                                        }}</label
                                                    >
                                                    <input
                                                        v-model="form.location"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Tahun Monitoring</label
                                                    >
                                                    <select
                                                        v-model="form.year"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option value="">
                                                            Pilih Tahun
                                                        </option>
                                                        <option
                                                            v-for="y in fiscalYears"
                                                            :key="y"
                                                            :value="y"
                                                        >
                                                            {{ y }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Item Pengecekan</label
                                                >
                                                <template
                                                    v-if="
                                                        [
                                                            'pemeliharaan-air-bersih',
                                                            'pemeliharaan-pompa',
                                                            'pemeliharaan-air-minum',
                                                            'pemeliharaan-genset',
                                                        ].includes(type)
                                                    "
                                                >
                                                    <select
                                                        v-model="form.title"
                                                        @change="
                                                            handlePeriodicItemChange
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option value="">
                                                            Pilih Item
                                                            Pengecekan
                                                        </option>
                                                        <option
                                                            v-for="item in type ===
                                                            'pemeliharaan-air-bersih'
                                                                ? airBersihItems
                                                                : type ===
                                                                    'pemeliharaan-pompa'
                                                                  ? pompaItems
                                                                  : type ===
                                                                      'pemeliharaan-air-minum'
                                                                    ? airMinumItems
                                                                    : gensetItems"
                                                            :key="item.title"
                                                            :value="item.title"
                                                        >
                                                            {{ item.title }}
                                                        </option>
                                                    </select>
                                                </template>
                                                <input
                                                    v-else
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Standard</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_standard
                                                    "
                                                    type="text"
                                                    :disabled="
                                                        [
                                                            'pemeliharaan-air-bersih',
                                                            'pemeliharaan-pompa',
                                                            'pemeliharaan-air-minum',
                                                            'pemeliharaan-genset',
                                                        ].includes(type)
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Metode</label
                                                >
                                                <input
                                                    v-model="form.check_method"
                                                    type="text"
                                                    :disabled="
                                                        [
                                                            'pemeliharaan-air-bersih',
                                                            'pemeliharaan-pompa',
                                                            'pemeliharaan-air-minum',
                                                            'pemeliharaan-genset',
                                                        ].includes(type)
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Frekuensi (FRK)</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_frequency
                                                    "
                                                    type="text"
                                                    :disabled="
                                                        [
                                                            'pemeliharaan-air-bersih',
                                                            'pemeliharaan-pompa',
                                                            'pemeliharaan-air-minum',
                                                            'pemeliharaan-genset',
                                                        ].includes(type)
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold disabled:opacity-75"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Laporan Jika Ada Kerusakan
                                                    (Catatan)</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindakan Pemeliharaan /
                                                    Perbaikan</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas URT</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Matrix Input for 36 columns -->
                                            <div
                                                class="sm:col-span-2 mt-6 overflow-x-auto"
                                            >
                                                <table
                                                    class="w-full border-collapse"
                                                >
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                class="p-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-[8px] font-black"
                                                            >
                                                                BULAN
                                                            </th>
                                                            <th
                                                                class="p-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-[8px] font-black text-blue-600 dark:text-blue-400"
                                                            >
                                                                {{
                                                                    type ===
                                                                    "pemeliharaan-genset"
                                                                        ? "PEKAN 1 (Pkn1)"
                                                                        : "PUTRA"
                                                                }}
                                                            </th>
                                                            <th
                                                                class="p-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-[8px] font-black text-pink-600 dark:text-pink-400"
                                                            >
                                                                {{
                                                                    type ===
                                                                    "pemeliharaan-genset"
                                                                        ? "PEKAN 2 (Pkn2)"
                                                                        : "PUTRI"
                                                                }}
                                                            </th>
                                                            <th
                                                                v-if="
                                                                    ![
                                                                        'pemeliharaan-air-minum',
                                                                        'pemeliharaan-genset',
                                                                    ].includes(
                                                                        type,
                                                                    )
                                                                "
                                                                class="p-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-[8px] font-black text-green-600 dark:text-green-400"
                                                            >
                                                                LAWATA
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="m in [
                                                                'jul',
                                                                'aug',
                                                                'sep',
                                                                'oct',
                                                                'nov',
                                                                'dec',
                                                                'jan',
                                                                'feb',
                                                                'mar',
                                                                'apr',
                                                                'may',
                                                                'jun',
                                                            ]"
                                                            :key="m"
                                                        >
                                                            <td
                                                                class="p-2 border border-gray-300 dark:border-gray-600 text-[10px] font-black uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-200"
                                                            >
                                                                {{ m }}
                                                            </td>
                                                            <td
                                                                v-for="a in [
                                                                    'pemeliharaan-air-minum',
                                                                    'pemeliharaan-genset',
                                                                ].includes(type)
                                                                    ? [
                                                                          'putra',
                                                                          'putri',
                                                                      ]
                                                                    : [
                                                                          'putra',
                                                                          'putri',
                                                                          'lawata',
                                                                      ]"
                                                                :key="a"
                                                                class="p-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                                                            >
                                                                <select
                                                                    v-model="
                                                                        form[
                                                                            `${m}_${a}`
                                                                        ]
                                                                    "
                                                                    class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded px-2 py-1 text-[10px] font-bold focus:ring-2 focus:ring-pail-gold"
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        -
                                                                    </option>
                                                                    <option
                                                                        value="V"
                                                                    >
                                                                        ✓ (V)
                                                                    </option>
                                                                    <option
                                                                        value="X"
                                                                    >
                                                                        ✗ (X)
                                                                    </option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Sarana Prasarana -->
                                        <template
                                            v-else-if="
                                                type === 'pemeliharaan-sarpras'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block tracking-widest font-black"
                                                    >Pemeliharaan
                                                    (Kategori/Tag)</label
                                                >
                                                <input
                                                    v-model="form.subcategory"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold font-black uppercase text-pail-gold"
                                                    placeholder="CONTOH: GEDUNG / MEP / ELEKTRONIK"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama
                                                    Sarana/Prasarana</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jenis Pekerjaan</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_standard
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Sebelum</label
                                                >
                                                <input
                                                    v-model="
                                                        form.before_condition
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="OK / Rusak Ringan / dll"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindakan
                                                    Pemeliharaan</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Sesudah</label
                                                >
                                                <input
                                                    v-model="
                                                        form.after_condition
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="OK / Beres / dll"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pelaksana</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Rekapan Pengajuan Perbaikan -->
                                        <template
                                            v-else-if="
                                                type === 'rekapan-pengajuan'
                                            "
                                        >
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl Pengajuan</label
                                                >
                                                <input
                                                    v-model="form.request_date"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Divisi Pemohon</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Divisi"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Judul/Subjek
                                                    Pengajuan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Perbaikan Genteng Bocor"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Permintaan Perbaikan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail permintaan perbaikan..."
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Ruangan (Optional)</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi Spesifik /
                                                    Manual</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Depan Kantor"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jenis Kerusakan</label
                                                >
                                                <select
                                                    v-model="form.damage_type"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Jenis
                                                    </option>
                                                    <option value="Ringan">
                                                        Ringan
                                                    </option>
                                                    <option value="Sedang">
                                                        Sedang
                                                    </option>
                                                    <option value="Berat">
                                                        Berat
                                                    </option>
                                                    <option value="Darurat">
                                                        Darurat
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Status Progres</label
                                                >
                                                <select
                                                    v-model="form.status"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="pending">
                                                        Pending
                                                    </option>
                                                    <option value="ongoing">
                                                        Proses
                                                    </option>
                                                    <option value="completed">
                                                        Selesai
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl di TL (Tindak
                                                    Lanjut)</label
                                                >
                                                <input
                                                    v-model="
                                                        form.follow_up_date
                                                    "
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Perbaikan</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <!-- Custom Petugas Input -->
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan (Ket)</label
                                                >
                                                <textarea
                                                    v-model="form.remarks"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Catatan akhir..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pengajuan RAB -->
                                        <template
                                            v-else-if="type === 'pengajuan-rab'"
                                        >
                                            <div class="sm:col-span-1">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl Pengajuan</label
                                                >
                                                <input
                                                    v-model="form.request_date"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div class="sm:col-span-1">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lembaga Pemohon</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Lembaga..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Proyek</label
                                                >
                                                <textarea
                                                    v-model="form.title"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail uraian proyek / pekerjaan..."
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Volume</label
                                                >
                                                <input
                                                    v-model="form.volume"
                                                    type="number"
                                                    step="any"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <select
                                                    v-model="form.unit"
                                                    @change="handleUnitChange"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="unit in unitOptions"
                                                        :key="unit"
                                                        :value="unit"
                                                    >
                                                        {{ unit }}
                                                    </option>
                                                </select>
                                                <!-- Custom Unit Input -->
                                                <div
                                                    v-if="showCustomUnit"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customUnitValue
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan satuan custom..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="applyCustomUnit"
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Harga Satuan (Rp)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.unit_price)"
                                                    @input="(e) => handleRupiahInput(e, 'unit_price')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah Total (Auto)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.total_price)"
                                                    type="text"
                                                    disabled
                                                    class="w-full bg-gray-100 dark:bg-gray-700/50 border-0 rounded-2xl px-6 py-4 text-sm text-pail-gold font-black"
                                                />
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Monitoring Aset -->
                                        <template
                                            v-else-if="
                                                type === 'monitoring-aset'
                                            "
                                        >
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tgl Pemeriksaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.performed_by"
                                                    :options="petugasOptions"
                                                    placeholder="Pilih Petugas..."
                                                    allow-create
                                                    @create="handlePetugasCreate"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jenis Barang</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Kursi / Meja"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk (Optional)</label
                                                >
                                                <input
                                                    v-model="form.brand"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Ikea"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Kantor Utama"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi (Kantor)</label
                                                >
                                                <select
                                                    v-model="form.condition"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Kondisi
                                                    </option>
                                                    <option value="B">
                                                        B (Baik)
                                                    </option>
                                                    <option value="KB">
                                                        KB (Kurang Baik)
                                                    </option>
                                                    <option value="R">
                                                        R (Rusak)
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Catatan / Keterangan</label
                                                >
                                                <textarea
                                                    v-model="
                                                        form.condition_notes
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Berita Acara Pemeriksaan -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'berita-acara-pemeriksaan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Judul Berita Acara</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Pemeriksaan Aset Tahunan 2024"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Pemeriksaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pemeriksa</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.performed_by"
                                                    :options="petugasOptions"
                                                    placeholder="Pilih Petugas..."
                                                    allow-create
                                                    @create="
                                                        handlePetugasCreate
                                                    "
                                                />
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan..."
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Umum</label
                                                >
                                                <select
                                                    v-model="form.condition"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Kondisi
                                                    </option>
                                                    <option value="B">
                                                        Baik
                                                    </option>
                                                    <option value="KB">
                                                        Kurang Baik
                                                    </option>
                                                    <option value="RB">
                                                        Rusak Berat
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Hasil Pemeriksaan
                                                    (Detail)</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="4"
                                                    placeholder="Uraian hasil pemeriksaan..."
                                                ></textarea>
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindak Lanjut /
                                                    Rekomendasi</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Rekomendasi tindakan..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Electrical Maintenance -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'electrical-maintenance'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:col-span-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Tahun</label
                                                    >
                                                    <input
                                                        v-model="form.year"
                                                        type="number"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="2025"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Lokasi</label
                                                    >
                                                    <input
                                                        v-model="form.location"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="Contoh: Panel Utama"
                                                    />
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Item Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Pemeriksaan Instalasi"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Standard Pengecekan</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_standard
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Metode Pengecekan</label
                                                >
                                                <input
                                                    v-model="form.check_method"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Frekuensi</label
                                                >
                                                <input
                                                    v-model="
                                                        form.check_frequency
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>

                                            <div
                                                class="sm:col-span-2 border-t border-gray-100 dark:border-gray-800 pt-6 mt-2"
                                            >
                                                <label
                                                    class="text-[9px] font-black text-pail-gold uppercase tracking-widest mb-4 block text-center"
                                                    >Status Bulanan (V = OK, X =
                                                    NG)</label
                                                >
                                                <div
                                                    class="grid grid-cols-4 md:grid-cols-6 gap-4"
                                                >
                                                    <div
                                                        v-for="month in [
                                                            'jul',
                                                            'aug',
                                                            'sep',
                                                            'oct',
                                                            'nov',
                                                            'dec',
                                                            'jan',
                                                            'feb',
                                                            'mar',
                                                            'apr',
                                                            'may',
                                                            'jun',
                                                        ]"
                                                        :key="month"
                                                    >
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block text-center uppercase"
                                                            >{{ month }}</label
                                                        >
                                                        <select
                                                            v-model="
                                                                form[
                                                                    `${month}_status`
                                                                ]
                                                            "
                                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-2 py-3 text-[10px] text-center focus:ring-2 focus:ring-pail-gold"
                                                        >
                                                            <option value="">
                                                                -
                                                            </option>
                                                            <option value="V">
                                                                V
                                                            </option>
                                                            <option value="X">
                                                                X
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Laporan Proyek Kegiatan -->
                                        <template
                                            v-else-if="
                                                type === 'laporan-proyek'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Proyek Kegiatan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Nama proyek/kegiatan..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Pelaksanaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi Kegiatan</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Gedung SAR"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Penanggung Jawab</label
                                                >
                                                <input
                                                    v-model="
                                                        form.responsible_person
                                                    "
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Nama PJ..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tim Pelaksana (Satu per
                                                    baris)</label
                                                >
                                                <textarea
                                                    v-model="form.team_members"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Nama tim..."
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Anggaran (Rp)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.budget_amount)"
                                                    @input="(e) => handleRupiahInput(e, 'budget_amount')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Realisasi (Rp)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.actual_amount)"
                                                    @input="(e) => handleRupiahInput(e, 'actual_amount')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >% Capaian (Auto)</label
                                                >
                                                <input
                                                    v-model="
                                                        form.attainment_percentage
                                                    "
                                                    type="number"
                                                    disabled
                                                    class="w-full bg-gray-100 dark:bg-gray-700/50 border-0 rounded-2xl px-6 py-4 text-sm text-pail-gold font-black"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Catatan tambahan..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Peminjaman Barang -->
                                        <template
                                            v-else-if="
                                                type === 'peminjaman-barang'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Barang (Asset)</label
                                                >
                                                <select
                                                    v-model="form.item_id"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="">
                                                        Pilih Barang...
                                                    </option>
                                                    <option
                                                        v-for="item in items"
                                                        :key="item.id"
                                                        :value="item.id"
                                                    >
                                                        {{ item.name }} ({{
                                                            item.code
                                                        }})
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Peminjam</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.user_id"
                                                    :options="users"
                                                    placeholder="Pilih User..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Divisi/Satker</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Divisi..."
                                                />
                                            </div>

                                            <!-- Saat Peminjaman -->
                                            <div
                                                class="sm:col-span-2 mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800"
                                            >
                                                <h4
                                                    class="text-[10px] font-black text-pail-gold uppercase tracking-widest mb-4"
                                                >
                                                    Detail Peminjaman
                                                </h4>
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Kondisi
                                                            Barang</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.borrow_condition
                                                            "
                                                            type="text"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            placeholder="Contoh: Baik"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Hari/Tanggal</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.borrow_date
                                                            "
                                                            type="date"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Paraf (Nama)</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.borrower_paraf
                                                            "
                                                            type="text"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            placeholder="Nama PJ"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Saat Kembali -->
                                            <div
                                                class="sm:col-span-2 mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800"
                                            >
                                                <h4
                                                    class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-4"
                                                >
                                                    Detail Pengembalian
                                                </h4>
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Kondisi
                                                            Kembali</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.return_condition
                                                            "
                                                            type="text"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500"
                                                            placeholder="Contoh: Baik"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Hari/Tanggal</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.actual_return_date
                                                            "
                                                            type="date"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Paraf (Nama)</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.returner_paraf
                                                            "
                                                            type="text"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500"
                                                            placeholder="Nama Penerima"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pelelangan Aset -->
                                        <template
                                            v-else-if="
                                                type === 'pelelangan-aset'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Barang (Asset)</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.item_id"
                                                    :options="items"
                                                    placeholder="Pilih Barang..."
                                                    :customLabel="
                                                        (opt) =>
                                                            `${opt.name} (${opt.code})`
                                                    "
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah (Unit)</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nilai Perkiraan (Rp)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.value)"
                                                    @input="(e) => handleRupiahInput(e, 'value')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.reason"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail keterangan lelang..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pengadaan Sarpras -->
                                        <template
                                            v-else-if="
                                                type === 'pengadaan-sarpras'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lembaga</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Lembaga..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tahun Ajaran</label
                                                >
                                                <input
                                                    v-model="form.year"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: 2025/2026"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Status</label
                                                >
                                                <select
                                                    v-model="form.status"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="pending">
                                                        Pending
                                                    </option>
                                                    <option value="ongoing">
                                                        Ongoing
                                                    </option>
                                                    <option value="completed">
                                                        Completed
                                                    </option>
                                                    <option value="cancelled">
                                                        Cancelled
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Barang</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Nama barang yang diajukan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk</label
                                                >
                                                <input
                                                    v-model="form.brand"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Ukuran</label
                                                >
                                                <input
                                                    v-model="form.size"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.unit"
                                                    :options="unitOptions"
                                                    placeholder="Pilih Satuan..."
                                                    allow-create
                                                    @create="handleUnitCreate"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail tambahan..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Analisis Kebutuhan Sarpras -->
                                        <template
                                            v-else-if="
                                                type === 'analisis-kebutuhan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lembaga</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Lembaga..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Uraian kebutuhan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.unit"
                                                    :options="unitOptions"
                                                    placeholder="Pilih Satuan..."
                                                    allow-create
                                                    @create="handleUnitCreate"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Pengadaan /
                                                    Penggantian</label
                                                >
                                                <select
                                                    v-model="
                                                        form.procurement_type
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="Pengadaan">
                                                        Pengadaan
                                                    </option>
                                                    <option value="Penggantian">
                                                        Penggantian
                                                    </option>
                                                </select>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemilihan & Evaluasi Supplier -->
                                        <template
                                            v-else-if="
                                                type === 'pemilihan-evaluasi'
                                            "
                                        >
                                            <div
                                                class="sm:col-span-2 space-y-4"
                                            >
                                                <div
                                                    class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border border-gray-100 dark:border-gray-700"
                                                >
                                                    <h4
                                                        class="text-[10px] font-black uppercase tracking-widest text-pail-gold mb-6"
                                                    >
                                                        Section 1: Informasi
                                                        Supplier
                                                    </h4>
                                                    <div
                                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                    >
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Lembaga Pemohon
                                                                / Terkait
                                                                <span
                                                                    class="text-red-500"
                                                                    >*Wajib</span
                                                                ></label
                                                            >
                                                            <SearchableSelect
                                                                v-model="
                                                                    form.institution_id
                                                                "
                                                                :options="
                                                                    institutions
                                                                "
                                                                placeholder="Pilih Lembaga..."
                                                            />
                                                            <div
                                                                v-if="
                                                                    form.errors
                                                                        .institution_id
                                                                "
                                                                class="text-red-500 text-[10px] font-bold mt-1 uppercase"
                                                            >
                                                                {{
                                                                    form.errors
                                                                        .institution_id
                                                                }}
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Kategori
                                                                Supplier</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.subcategory
                                                                "
                                                                type="text"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                placeholder="Contoh: Bengkel Las"
                                                            />
                                                        </div>
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Nama
                                                                Supplier</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.title
                                                                "
                                                                type="text"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                placeholder="Nama perusahaan/supplier"
                                                            />
                                                        </div>
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Alamat</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.supplier_address
                                                                "
                                                                type="text"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Kontak</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.supplier_contact
                                                                "
                                                                type="text"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Produk/Jasa</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.supplier_product
                                                                "
                                                                type="text"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            />
                                                        </div>
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Petugas Penilai
                                                                (Staff
                                                                URT)</label
                                                            >
                                                            <SearchableSelect
                                                                v-model="
                                                                    form.performed_by
                                                                "
                                                                :options="
                                                                    petugasOptions
                                                                "
                                                                placeholder="Pilih Petugas..."
                                                                allow-create
                                                                @create="
                                                                    handlePetugasCreate
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border border-gray-100 dark:border-gray-700"
                                                >
                                                    <h4
                                                        class="text-[10px] font-black uppercase tracking-widest text-pail-gold mb-6"
                                                    >
                                                        Section 2: Penilaian
                                                        (Skala 10-100)
                                                    </h4>
                                                    <div
                                                        class="grid grid-cols-2 gap-4"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Harga
                                                                (25%)</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.sc_price
                                                                "
                                                                type="number"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                min="0"
                                                                max="100"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Kualitas
                                                                (30%)</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.sc_quality
                                                                "
                                                                type="number"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                min="0"
                                                                max="100"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Ketepatan Waktu
                                                                (20%)</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.sc_delivery
                                                                "
                                                                type="number"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                min="0"
                                                                max="100"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Layanan
                                                                (15%)</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.sc_service
                                                                "
                                                                type="number"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                min="0"
                                                                max="100"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Legalitas
                                                                (10%)</label
                                                            >
                                                            <input
                                                                v-model="
                                                                    form.sc_legal
                                                                "
                                                                type="number"
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                min="0"
                                                                max="100"
                                                            />
                                                        </div>
                                                        <div
                                                            class="flex items-end"
                                                        >
                                                            <div
                                                                class="w-full p-4 bg-pail-gold/10 rounded-2xl border border-pail-gold/20"
                                                            >
                                                                <p
                                                                    class="text-[9px] font-black text-pail-gold uppercase tracking-widest mb-1"
                                                                >
                                                                    Skor Akhir
                                                                </p>
                                                                <p
                                                                    class="text-xl font-black text-gray-900 dark:text-white"
                                                                >
                                                                    {{
                                                                        form.sc_total
                                                                    }}%
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border border-gray-100 dark:border-gray-700"
                                                >
                                                    <h4
                                                        class="text-[10px] font-black uppercase tracking-widest text-pail-gold mb-6"
                                                    >
                                                        Section 3: Keputusan
                                                    </h4>
                                                    <div
                                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                    >
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Status
                                                                Pemilihan</label
                                                            >
                                                            <select
                                                                v-model="
                                                                    form.status
                                                                "
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            >
                                                                <option
                                                                    value="Terpilih"
                                                                >
                                                                    Terpilih
                                                                </option>
                                                                <option
                                                                    value="Cadangan"
                                                                >
                                                                    Cadangan
                                                                </option>
                                                                <option
                                                                    value="Tidak Terpilih"
                                                                >
                                                                    Tidak
                                                                    Terpilih
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div
                                                            class="sm:col-span-2"
                                                        >
                                                            <label
                                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                                >Catatan</label
                                                            >
                                                            <textarea
                                                                v-model="
                                                                    form.description
                                                                "
                                                                class="w-full bg-white dark:bg-gray-800 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                                rows="2"
                                                            ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pengajuan RAB -->
                                        <template
                                            v-else-if="type === 'pengajuan-rab'"
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kebutuhan Proyek</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Pembelian Semen Gresik"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Volume</label
                                                >
                                                <input
                                                    v-model="form.volume"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.unit"
                                                    :options="unitOptions"
                                                    placeholder="Pilih Satuan..."
                                                    allow-create
                                                    @create="handleUnitCreate"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Harga Satuan</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.unit_price)"
                                                    @input="(e) => handleRupiahInput(e, 'unit_price')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div class="flex items-end">
                                                <div
                                                    class="w-full p-4 bg-gray-900 rounded-2xl border border-gray-800"
                                                >
                                                    <p
                                                        class="text-[9px] font-black text-pail-gold uppercase tracking-widest mb-1"
                                                    >
                                                        Total (Jumlah)
                                                    </p>
                                                    <p
                                                        class="text-xl font-black text-white"
                                                    >
                                                        {{ formatRupiah(form.total_price) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Pemeliharaan Kebersihan (Timeline) -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'pemeliharaan-kebersihan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Pemeliharaan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Pembersihan AC Tahunan"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Output Pembiayaan
                                                    Kegiatan</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.cost)"
                                                    @input="(e) => handleRupiahInput(e, 'cost')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div
                                                class="sm:col-span-2 border-t border-gray-100 dark:border-gray-800 pt-6 mt-2"
                                            >
                                                <label
                                                    class="text-[9px] font-black text-pail-gold uppercase tracking-widest mb-4 block text-center"
                                                    >Timeline Kegiatan</label
                                                >
                                                <div
                                                    class="grid grid-cols-3 md:grid-cols-6 gap-4"
                                                >
                                                    <div
                                                        v-for="month in [
                                                            'jul',
                                                            'aug',
                                                            'sep',
                                                            'oct',
                                                            'nov',
                                                            'dec',
                                                            'jan',
                                                            'feb',
                                                            'mar',
                                                            'apr',
                                                            'may',
                                                            'jun',
                                                        ]"
                                                        :key="month"
                                                    >
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block text-center uppercase"
                                                            >{{ month }}</label
                                                        >
                                                        <select
                                                            v-model="
                                                                form[month]
                                                            "
                                                            class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-2 py-3 text-[10px] text-center focus:ring-2 focus:ring-pail-gold"
                                                        >
                                                            <option value="">
                                                                -
                                                            </option>
                                                            <option value="√">
                                                                √
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Jadwal Token -->
                                        <template
                                            v-else-if="type === 'jadwal-token'"
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Pengisian</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas (Staff URT)</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.performed_by"
                                                    :options="petugasOptions"
                                                    placeholder="Pilih Petugas..."
                                                    allow-create
                                                    @create="
                                                        handlePetugasCreate
                                                    "
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Meter / ID
                                                    Pelanggan</label
                                                >
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                >
                                                    <input
                                                        v-model="form.title"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="Nama Meter"
                                                    />
                                                    <input
                                                        v-model="
                                                            form.serial_number
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="ID Pelanggan (11-12 digit)"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nominal Token (Rp)</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.total_price)"
                                                    @input="(e) => handleRupiahInput(e, 'total_price')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nomor Token (20
                                                    Digit)</label
                                                >
                                                <input
                                                    v-model="form.action_taken"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm font-mono tracking-widest focus:ring-2 focus:ring-pail-gold"
                                                    maxlength="20"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Jadwal Kebersihan -->
                                        <template
                                            v-else-if="
                                                type === 'jadwal-kebersihan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:col-span-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Hari</label
                                                    >
                                                    <select
                                                        v-model="
                                                            form.subcategory
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option value="">
                                                            Pilih Hari...
                                                        </option>
                                                        <option
                                                            v-for="day in [
                                                                'Senin',
                                                                'Selasa',
                                                                'Rabu',
                                                                'Kamis',
                                                                'Jumat',
                                                                'Sabtu',
                                                                'Minggu',
                                                            ]"
                                                            :key="day"
                                                            :value="day"
                                                        >
                                                            {{ day }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Petugas</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="
                                                            form.performed_by
                                                        "
                                                        :options="
                                                            petugasOptions
                                                        "
                                                        placeholder="Pilih Petugas..."
                                                        allow-create
                                                        @create="
                                                            handlePetugasCreate
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:col-span-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Area Kerja /
                                                        Ruangan</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="form.room_id"
                                                        :options="rooms"
                                                        placeholder="Pilih Ruangan..."
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Waktu / Jam</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.scheduled_at
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="Contoh: 07:00 - 08:00"
                                                    />
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Pekerjaan</label
                                                >
                                                <textarea
                                                    v-model="form.title"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Monitoring Kebersihan -->
                                        <template
                                            v-else-if="
                                                type === 'monitoring-kebersihan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:col-span-2"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Petugas</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="
                                                            form.performed_by
                                                        "
                                                        :options="
                                                            petugasOptions
                                                        "
                                                        placeholder="Pilih Petugas..."
                                                        allow-create
                                                        @create="
                                                            handlePetugasCreate
                                                        "
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Area Kerja</label
                                                    >
                                                    <SearchableSelect
                                                        v-model="form.room_id"
                                                        :options="rooms"
                                                        placeholder="Pilih Ruangan..."
                                                    />
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Pekerjaan</label
                                                >
                                                <textarea
                                                    v-model="form.title"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="sm:col-span-2 border-t border-gray-100 dark:border-gray-800 pt-6 mt-2"
                                            >
                                                <label
                                                    class="text-[9px] font-black text-pail-gold uppercase tracking-widest mb-4 block text-center"
                                                    >Penilaian Harian (SUDAH /
                                                    BELUM)</label
                                                >
                                                <div
                                                    class="grid grid-cols-3 md:grid-cols-6 gap-4"
                                                >
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Senin</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.mon_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.mon_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Selasa</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.tue_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.tue_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Rabu</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.wed_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.wed_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Kamis</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.thu_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.thu_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Jumat</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.fri_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.fri_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Sabtu</label
                                                        >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                v-model="
                                                                    form.sat_status
                                                                "
                                                                class="w-5 h-5 text-pail-gold rounded focus:ring-pail-gold border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
                                                            />
                                                            <span
                                                                class="text-[10px] font-bold text-gray-500"
                                                                >{{
                                                                    form.sat_status
                                                                        ? "SUDAH"
                                                                        : "BELUM"
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Penyerahan Barang -->
                                        <template
                                            v-else-if="
                                                type === 'penyerahan-barang'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Divisi (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Divisi..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Barang</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk</label
                                                >
                                                <input
                                                    v-model="form.brand"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Ukuran</label
                                                >
                                                <input
                                                    v-model="form.size"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <select
                                                    v-model="form.unit"
                                                    @change="handleUnitChange"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="unit in unitOptions"
                                                        :key="unit"
                                                        :value="unit"
                                                    >
                                                        {{ unit }}
                                                    </option>
                                                </select>
                                                <!-- Custom Unit Input -->
                                                <div
                                                    v-if="showCustomUnit"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customUnitValue
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan satuan custom..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="applyCustomUnit"
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Barang</label
                                                >
                                                <select
                                                    v-model="
                                                        form.after_condition
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="Baik">
                                                        Baik
                                                    </option>
                                                    <option
                                                        value="Rusak Ringan"
                                                    >
                                                        Rusak Ringan
                                                    </option>
                                                    <option value="Rusak Berat">
                                                        Rusak Berat
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Penerimaan Barang -->
                                        <template
                                            v-else-if="
                                                type === 'penerimaan-barang'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Penerimaan</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Penerima (Staff URT)</label
                                                >
                                                <select
                                                    v-model="form.performed_by"
                                                    @change="
                                                        handlePetugasChange(
                                                            form.performed_by,
                                                        )
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option
                                                        v-for="petugas in petugasOptions"
                                                        :key="petugas"
                                                        :value="petugas"
                                                    >
                                                        {{ petugas }}
                                                    </option>
                                                    <option value="_custom_">
                                                        + Tambah Petugas Lain...
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="showCustomPetugas"
                                                    class="mt-2 flex gap-2"
                                                >
                                                    <input
                                                        v-model="
                                                            customPetugasValue
                                                        "
                                                        type="text"
                                                        placeholder="Nama petugas..."
                                                        class="flex-1 bg-white dark:bg-gray-800 border border-pail-gold/30 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                    <button
                                                        @click="
                                                            applyCustomPetugas
                                                        "
                                                        type="button"
                                                        class="px-4 py-2 bg-pail-gold text-white rounded-xl text-xs font-black uppercase hover:bg-pail-gold/90 transition-all"
                                                    >
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Barang</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Nama barang yang diterima"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Merk</label
                                                >
                                                <input
                                                    v-model="form.brand"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Ukuran</label
                                                >
                                                <input
                                                    v-model="form.size"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.unit"
                                                    :options="unitOptions"
                                                    placeholder="Pilih Satuan..."
                                                    allow-create
                                                    @create="handleUnitCreate"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Asal Barang</label
                                                >
                                                <input
                                                    v-model="form.source"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Toko, Supplier, atau Donatur"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail tambahan..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Berita Acara Pemeriksaan Aset -->
                                        <template
                                            v-else-if="
                                                type ===
                                                'berita-acara-pemeriksaan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Nama Aset</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.item_id"
                                                    :options="items"
                                                    placeholder="Pilih Aset..."
                                                    :customLabel="
                                                        (opt) =>
                                                            `${opt.name} (${opt.code})`
                                                    "
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Jumlah</label
                                                >
                                                <input
                                                    v-model="form.quantity"
                                                    type="number"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi</label
                                                >
                                                <select
                                                    v-model="
                                                        form.before_condition
                                                    "
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="Baik">
                                                        Baik
                                                    </option>
                                                    <option
                                                        value="Rusak Ringan"
                                                    >
                                                        Rusak Ringan
                                                    </option>
                                                    <option value="Rusak Berat">
                                                        Rusak Berat
                                                    </option>
                                                    <option value="Hilang">
                                                        Hilang
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail hasil pemeriksaan..."
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Hari / Tanggal</label
                                                >
                                                <input
                                                    v-model="form.completed_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div
                                                class="sm:col-span-2 mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800"
                                            >
                                                <h4
                                                    class="text-[10px] font-black text-pail-gold uppercase tracking-widest mb-4"
                                                >
                                                    Tim Pemeriksa
                                                </h4>
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Staff
                                                            Sarpras</label
                                                        >
                                                        <SearchableSelect
                                                            v-model="
                                                                form.performed_by
                                                            "
                                                            :options="users"
                                                            placeholder="Pilih Staff..."
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                            >Koordinator
                                                            Sarpras</label
                                                        >
                                                        <input
                                                            v-model="
                                                                form.responsible_person
                                                            "
                                                            type="text"
                                                            class="w-full bg-white dark:bg-gray-800 border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-pail-gold"
                                                            placeholder="Nama Koordinator"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Agenda Perbaikan Sarpras -->
                                        <template
                                            v-else-if="
                                                type === 'agenda-perbaikan'
                                            "
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Satuan Kerja
                                                    (Lembaga)</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Satuan Kerja..."
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Uraian Pekerjaan</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lokasi / Ruangan</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.room_id"
                                                    :options="rooms"
                                                    placeholder="Pilih Ruangan"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tanggal Agenda</label
                                                >
                                                <input
                                                    v-model="form.scheduled_at"
                                                    type="date"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Status</label
                                                >
                                                <select
                                                    v-model="form.status"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                >
                                                    <option value="pending">
                                                        Pending
                                                    </option>
                                                    <option value="ongoing">
                                                        Proses
                                                    </option>
                                                    <option value="completed">
                                                        Selesai
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pelaksana</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.performed_by"
                                                    :options="petugasOptions"
                                                    placeholder="Pilih Petugas..."
                                                    allow-create
                                                    @create="
                                                        handlePetugasCreate
                                                    "
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Keterangan</label
                                                >
                                                <textarea
                                                    v-model="form.description"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                    placeholder="Detail tambahan..."
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- Specialized fields for Parkir Area -->
                                        <template
                                            v-else-if="type === 'parkir-area'"
                                        >
                                            <div
                                                class="sm:col-span-2 grid grid-cols-2 gap-4"
                                            >
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Tipe Kendaraan</label
                                                    >
                                                    <select
                                                        v-model="
                                                            form.vehicle_type
                                                        "
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    >
                                                        <option value="Motor">
                                                            Motor
                                                        </option>
                                                        <option value="Mobil">
                                                            Mobil
                                                        </option>
                                                        <option value="Bus">
                                                            Bus
                                                        </option>
                                                        <option value="Lainnya">
                                                            Lainnya
                                                        </option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Nomor Plat
                                                        (TNKB)</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.plate_number
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="DR 1234 XY"
                                                    />
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Nama Pemilik /
                                                        Pengendara</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.owner_name
                                                        "
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Jam Masuk</label
                                                    >
                                                    <input
                                                        v-model="
                                                            form.entry_time
                                                        "
                                                        type="datetime-local"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Jam Keluar
                                                        (Opsional)</label
                                                    >
                                                    <input
                                                        v-model="form.exit_time"
                                                        type="datetime-local"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    />
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                        >Gerbang / Area
                                                        Parkir</label
                                                    >
                                                    <input
                                                        v-model="form.gate_name"
                                                        type="text"
                                                        class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                        placeholder="Contoh: Gerbang Depan / Parkir Guru"
                                                    />
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Standard Maintenance logic -->
                                        <template v-else>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Lembaga Terkait</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.institution_id
                                                    "
                                                    :options="institutions"
                                                    placeholder="Pilih Lembaga"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Judul Aktivitas</label
                                                >
                                                <input
                                                    v-model="form.title"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    placeholder="Contoh: Pembersihan AC Ruang Mudir"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Sebelum</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.before_condition
                                                    "
                                                    :options="
                                                        beforeConditionOptions
                                                    "
                                                    placeholder="Pilih Kondisi..."
                                                    allow-create
                                                    @create="
                                                        handleBeforeConditionCreate
                                                    "
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Kondisi Sesudah</label
                                                >
                                                <SearchableSelect
                                                    v-model="
                                                        form.after_condition
                                                    "
                                                    :options="
                                                        afterConditionOptions
                                                    "
                                                    placeholder="Pilih Kondisi..."
                                                    allow-create
                                                    @create="
                                                        handleAfterConditionCreate
                                                    "
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Tindakan Pemeliharaan /
                                                    Perbaikan</label
                                                >
                                                <textarea
                                                    v-model="form.action_taken"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Area / Lokasi Fisik</label
                                                >
                                                <input
                                                    v-model="form.location"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Biaya / Nilai</label
                                                >
                                                <input
                                                    :value="formatRupiah(form.cost)"
                                                    @input="(e) => handleRupiahInput(e, 'cost')"
                                                    type="text"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-pail-gold"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                                    >Petugas Pelaksana</label
                                                >
                                                <SearchableSelect
                                                    v-model="form.performed_by"
                                                    :options="petugasOptions"
                                                    placeholder="Pilih Petugas..."
                                                    allow-create
                                                    @create="
                                                        handlePetugasCreate
                                                    "
                                                />
                                            </div>
                                        </template>
                                    </template>
                                </template>

                                <!-- Common Selectors for Vehicles Only -->
                                <div
                                    v-if="
                                        type.includes('kendaraan') &&
                                        type.includes('pengajuan') &&
                                        type !== 'penggunaan-kendaraan'
                                    "
                                >
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block"
                                        >Pilih Kendaraan</label
                                    >
                                    <SearchableSelect
                                        v-model="form.vehicle_id"
                                        :options="vehicles"
                                        placeholder="Pilih Armada"
                                        :customLabel="
                                            (opt) =>
                                                `${opt.name} - ${opt.plate_number}`
                                        "
                                    />
                                </div>
                            </div>

                            <div
                                class="mt-8 md:mt-12 flex flex-col-reverse sm:flex-row justify-end gap-3 md:gap-4"
                            >
                                <button
                                    @click="showCreateModal = false"
                                    type="button"
                                    class="w-full sm:w-auto px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-gray-200 transition-all text-center"
                                >
                                    Batal
                                </button>
                                <button
                                    :disabled="form.processing"
                                    type="submit"
                                    class="w-full sm:w-auto px-12 py-4 bg-gray-900 dark:bg-pail-gold text-pail-gold dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl disabled:opacity-50"
                                >
                                    Simpan Dokumentasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>

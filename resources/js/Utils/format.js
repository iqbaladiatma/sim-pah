export const formatRupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};

export const parseRupiah = (string) => {
    return parseInt(string.replace(/[^0-9]/g, "")) || 0;
};

<?php

namespace App\Exports;

use App\Models\Request as GeneralRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $status;
    protected $startDate;
    protected $endDate;

    public function __construct($status = null, $startDate = null, $endDate = null)
    {
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = GeneralRequest::with(['user.institution', 'institution'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Filter by date range if provided
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL PENGAJUAN',
            'KODE LEMBAGA',
            'NAMA LEMBAGA',
            'TIPE PENGAJUAN',
            'JUDUL PENGAJUAN',
            'DESKRIPSI',
            'ESTIMASI BIAYA (Rp)',
            'STATUS',
            'CATATAN ADMIN',
            'PENGAJU',
            'EMAIL PENGAJU',
        ];
    }

    /**
     * @param mixed $request
     * @return array
     */
    public function map($request): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $request->created_at->format('d/m/Y H:i'),
            $request->institution->code ?? '-',
            $request->institution->name ?? '-',
            strtoupper($request->type),
            $request->title,
            $request->description,
            'Rp ' . number_format($request->estimated_cost, 0, ',', '.'),
            strtoupper($request->status),
            $request->admin_note ?? '-',
            $request->user->name ?? '-',
            $request->user->email ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C9A658'], // Pail Gold
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 18,  // TANGGAL
            'C' => 15,  // KODE LEMBAGA
            'D' => 30,  // NAMA LEMBAGA
            'E' => 18,  // TIPE
            'F' => 35,  // JUDUL
            'G' => 50,  // DESKRIPSI
            'H' => 20,  // BIAYA
            'I' => 12,  // STATUS
            'J' => 40,  // CATATAN
            'K' => 25,  // PENGAJU
            'L' => 30,  // EMAIL
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Pengajuan';
    }
}

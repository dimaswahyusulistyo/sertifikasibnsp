<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventarisExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle, 
    WithCustomStartCell,
    ShouldAutoSize
{
    protected $data;
    protected $search;

    public function __construct($data, $search = null)
    {
        $this->data = $data;
        $this->search = $search;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Produk',
            'Deskripsi',
            'Harga (Rp)',
            'Stok',
            'Dibuat',
            'Diperbarui'
        ];
    }

    public function map($produk): array
    {
        static $no = 1;
        return [
            $no++,
            $produk->nama_produk,
            $produk->deskripsi_produk,
            number_format($produk->harga, 0, ',', '.'),
            $produk->stok_barang,
            $produk->created_at->format('d/m/Y H:i'),
            $produk->updated_at->format('d/m/Y H:i')
        ];
    }

    public function startCell(): string
    {
        return 'A6'; // Mulai dari baris ke-6 untuk memberi ruang header
    }

    public function title(): string
    {
        return 'Data Inventaris';
    }

    public function styles(Worksheet $sheet)
    {
        // Header Laporan
        $sheet->setCellValue('A1', 'LAPORAN INVENTARIS BARANG');
        $sheet->setCellValue('A2', 'Dicetak: ' . now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'));
        
        if ($this->search) {
            $sheet->setCellValue('A3', 'Filter: "' . $this->search . '"');
        }

        // Merge cells untuk header
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        if ($this->search) {
            $sheet->mergeCells('A3:G3');
        }

        // Style untuk header laporan
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        if ($this->search) {
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['size' => 12, 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Style untuk header tabel
        $headerRange = 'A6:G6';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
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
        ]);

        // Style untuk data tabel
        $lastRow = $this->data->count() + 6;
        $dataRange = 'A6:G' . $lastRow;
        
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alignment khusus untuk kolom tertentu
        $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('D7:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Harga
        $sheet->getStyle('E7:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Stok
        $sheet->getStyle('F7:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Dibuat
        $sheet->getStyle('G7:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Diperbarui

        // Set row height
        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('6')->setRowHeight(20);

        // Set column widths manually untuk kontrol yang lebih baik
        $sheet->getColumnDimension('A')->setWidth(8);   // No
        $sheet->getColumnDimension('B')->setWidth(25);  // Nama Produk
        $sheet->getColumnDimension('C')->setWidth(40);  // Deskripsi
        $sheet->getColumnDimension('D')->setWidth(15);  // Harga
        $sheet->getColumnDimension('E')->setWidth(10);  // Stok
        $sheet->getColumnDimension('F')->setWidth(18);  // Dibuat
        $sheet->getColumnDimension('G')->setWidth(18);  // Diperbarui

        return [];
    }
}
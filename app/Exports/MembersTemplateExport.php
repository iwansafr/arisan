<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return empty array for template (or with sample data)
     */
    public function array(): array
    {
        // Return dengan contoh data
        return [
            [1, 'John Doe', 1, '081234567890'],
            [2, 'Jane Smith', 2, '082345678901'],
        ];
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'order',
            'name',
            'gender',
            'phone',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B5563'],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 12,
            'D' => 20,
        ];
    }
}
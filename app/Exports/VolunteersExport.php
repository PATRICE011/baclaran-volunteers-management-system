<?php

namespace App\Exports;

use App\Models\Volunteer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VolunteersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use RegistersEventListeners;

    public function collection()
    {
        return Volunteer::with(['detail.ministry'])
            ->where('is_archived', false) // Exclude archived volunteers
            ->join('volunteer_details', 'volunteers.id', '=', 'volunteer_details.volunteer_id')
            ->orderBy('volunteer_details.full_name', 'asc')
            ->select('volunteers.*')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Volunteer ID',
            'Full Name',
            'Nickname',
            'Ministry',
            'Status'
        ];
    }

    public function map($volunteer): array
    {
        $status = $volunteer->detail->volunteer_status ?? 'N/A';

        // Optionally add color coding based on status
        return [
            $volunteer->volunteer_id,
            $volunteer->detail->full_name ?? 'N/A',
            $volunteer->nickname,
            $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
            $status
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        // Get the sheet object
        $sheet = $event->sheet->getDelegate();

        // Style the header row (row 1)
        $headerRange = 'A1:E1';

        // Apply background color and font style to header
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3490DC'], // Blue color
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style the data rows
        $lastRow = $sheet->getHighestRow();
        $dataRange = 'A2:E' . $lastRow;

        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        // Add conditional formatting for status column
        $statusColumn = 'E';
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell($statusColumn . $row)->getValue();

            if ($status === 'Active') {
                $sheet->getStyle($statusColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C6EFCE'], // Light green
                    ],
                    'font' => [
                        'color' => ['rgb' => '006100'], // Dark green text
                    ],
                ]);
            } elseif ($status === 'Inactive') {
                $sheet->getStyle($statusColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFC7CE'], // Light red
                    ],
                    'font' => [
                        'color' => ['rgb' => '9C0006'], // Dark red text
                    ],
                ]);
            }
        }

        // Freeze the header row so it stays visible when scrolling
        $sheet->freezePane('A2');
    }
}

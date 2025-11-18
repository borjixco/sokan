<?php

namespace App\Exports;

use App\Models\Transfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransferRentExport implements  FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // رنگ فونت (مشکی)
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'], // رنگ پس زمینه (خاکستری روشن)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // وسط چین کردن عنوان‌ها
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    public function headings(): array
    {
        return [
            'شماره واحد',
            'شماره قرارداد',
            'طرف اول قرارداد',
            'طرف دوم قرارداد',
            'رهن',
            'اجاره',
            'تاریخ تنظیم',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Transfer::with(['unit','currentOwners.user', 'occupants.user'])->where('type', 'RENT')->whereBetween('doing_at',[$this->filters['start_date'], $this->filters['end_date']])->get();
    }

    public function map($transfer): array
    {
        return [
            $transfer->unit->unit_number,
            $transfer->contract_number,
            collect($transfer->currentOwners)->pluck('user')->map( fn ($item) => $item->name)->implode(', '),
            collect($transfer->occupants)->pluck('user')->map( fn ($item) => $item->name)->implode(', '),
            $transfer->mortgage_amount,
            $transfer->rental_amount,
            $transfer->doingAtObject['jalali'],
        ];
    }
}

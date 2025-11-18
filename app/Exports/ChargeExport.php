<?php

namespace App\Exports;

use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
use App\Models\Charge;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class ChargeExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
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
            'شناسه',
            'واحد',
            'متراژ',
            'پرداخت کننده',
            'مبلغ (ریال)',
            'تاریخ ایجاد',
            'دوره پرداخت',
            'مهلت پرداخت',
            'روش پرداخت',
            'وضعیت',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Charge::whereDate('period',$this->filters['period'])->get();
    }

    public function map($charge): array
    {
        return [
            $charge->id,
            $charge->unit->unit_number,
            $charge->unit->meterage,
            $charge->user->name,
            number_format($charge->amount),
            $charge->createdAtObject['label'],
            $charge->periodObject['label'],
            $charge->dueDateObject['label'],
            $charge->payment_method ? ChargePaymentMethodEnum::fromKey($charge->payment_method)->value : '-',
            $charge->status ? ChargeStatusEnum::fromKey($charge->status)->value : '-',
        ];
    }
}

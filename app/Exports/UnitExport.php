<?php

namespace App\Exports;

use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
use App\Enums\UnitBlockEnum;
use App\Enums\UnitRoofEnum;
use App\Enums\UnitStatusEnum;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitExport implements  FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
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
            'طبقه',
            'متراژ',
            'موقعیت',
            'نام مالک',
            'موبایل مالک',
            'وضعیت ملک',
            'وضعیت سقف',
            'پلمپ',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Unit::with(['owners' => function ($query) {
            $query->where('status', 'CURRENT')->with('user');
        }])->get();
    }

    public function map($unit): array
    {
        $owner = $unit->owners->first();
        return [
            $unit->unit_number,
            $unit->floor->label,
            $unit->meterage,
            $unit->position?->label,
            $owner->user->name,
            $owner->user->mobile,
            $unit->status ? UnitStatusEnum::fromKey($unit->status)->value : '-',
            $unit->roof ? UnitRoofEnum::fromKey($unit->roof)->value: '-',
            $unit->block ? UnitBlockEnum::fromKey($unit->block)->value: '-'
        ];
    }
}

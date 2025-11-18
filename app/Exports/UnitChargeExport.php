<?php

namespace App\Exports;

use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
use App\Enums\UnitBlockEnum;
use App\Enums\UnitRoofEnum;
use App\Enums\UnitStatusEnum;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Hekmatinasser\Verta\Verta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitChargeExport implements  FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
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
            'وضعیت',
            'نوع',
            'نام',
            'شماره',
            'متراژ',
            'ملبغ (ریال)',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $units = Unit::with([
            'owners' => fn ($query) => $query->where('status', 'CURRENT')->with('user'),
            'occupants' => fn ($query) => $query->where('status', 'CURRENT')->with('user')
        ])->get();
        $date = Verta::now()->startMonth()->toCarbon()->format('Y-m-d');
        request()->merge(['settingDate' => $date]);
        return collect(UnitResource::collection($units)->resolve());
    }

    public function map($unit): array
    {
        return [
            $unit['number'],
            $unit['status'] ? $unit['status']['label']->value : '-',
            $unit['occupant'] ? 'مستاجر' : 'مالک',
            $unit['occupant'] ? $unit['occupant']['user']['name'] : $unit['owner']['user']['name'],
            $unit['occupant'] ? $unit['occupant']['user']['mobile'] : $unit['owner']['user']['mobile'],
            $unit['meterage'],
            number_format($unit['charge_amount'] ?? $unit['formula_charge_amount'])
        ];
    }
}

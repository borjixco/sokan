<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OwnerExport implements  FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
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
            'واحدها',
            'نام',
            'کدملی',
            'شماره همراه',
            'شغل',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = User::with(['jobTypes','owners.unit', 'owners' => function ($q) {
            return $q->where('status','CURRENT');
        }])->whereHas('roles',function ($q){
            return $q->where('name','owner');
        })->get();
        return $users;
    }

    public function map($user): array
    {
        return [
            collect($user->owners)->pluck('unit')->map( fn ($item) => $item->unit_number)->implode(','),
            $user->name,
            $user->national_code,
            $user->mobile,
            $user->jobTypes->first()?->label
        ];
    }
}

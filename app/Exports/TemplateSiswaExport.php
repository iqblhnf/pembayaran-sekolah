<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateSiswaExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function headings(): array
    {
        return [
            'nama',
            'nis',
            'kelas',
            'angkatan',
            'alamat',
            'nama_ortu',
            'telp_ortu',
            'status',
        ];
    }

    public function array(): array
    {
        return [
            ['Agus Saputra', '12345', 'XII TKJ 1', '2022', 'Bandar Lampung', 'Budi Saputra', '08123456789', 'aktif'],
            ['Siti Aminah', '67890', 'XI RPL 2', '2023', 'Metro', 'Aminah', '0812341111', 'aktif'],
        ];
    }
}

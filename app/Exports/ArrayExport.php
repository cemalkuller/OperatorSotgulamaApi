<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        // Eğer satır varsa başlıkları ilk satırdan al, yoksa boş dizi dön
        return !empty($this->rows) ? array_keys($this->rows[0]) : [];
    }
}

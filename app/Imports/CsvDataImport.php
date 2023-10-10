<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CsvDataImport implements ToCollection,  WithHeadingRow
{

	public function collection(Collection $row)
    {


    }

    public function headingRow(): int
    {
        return 1;
    }
}

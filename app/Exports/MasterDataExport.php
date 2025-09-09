<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterDataExport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new TagsExport(),
            new TasksExport(),
            new OutcomesExport(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Models\Tag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TagsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Tag::with(['creator', 'approver'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name (JSON)',
            'Slug (JSON)',
            'Type',
            'Order Column',
            'Status',
            'Created By (User ID)',
            'Created By (Name)',
            'Approved By (User ID)',
            'Approved By (Name)',
            'Approved At',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param Tag $tag
     * @return array
     */
    public function map($tag): array
    {
        return [
            $tag->id,
            json_encode($tag->name),
            json_encode($tag->slug),
            $tag->type,
            $tag->order_column,
            $tag->status->value ?? $tag->status,
            $tag->created_by,
            $tag->creator?->name ?? 'N/A',
            $tag->approved_by,
            $tag->approver?->name ?? 'N/A',
            $tag->approved_at?->format('Y-m-d H:i:s'),
            $tag->created_at?->format('Y-m-d H:i:s'),
            $tag->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Tags';
    }
}

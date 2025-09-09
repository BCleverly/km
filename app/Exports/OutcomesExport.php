<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Tasks\Outcome;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class OutcomesExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Outcome::with(['author', 'tags', 'recommendedForTasks'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Difficulty Level',
            'Target User Type',
            'Author ID',
            'Author Name',
            'Status',
            'View Count',
            'Is Premium',
            'Intended Type',
            'Intended Type Label',
            'Tags (JSON)',
            'Tag Names (Comma Separated)',
            'Recommended For Tasks (JSON)',
            'Recommended For Task IDs (Comma Separated)',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param Outcome $outcome
     * @return array
     */
    public function map($outcome): array
    {
        return [
            $outcome->id,
            $outcome->title,
            $outcome->description,
            $outcome->difficulty_level,
            $outcome->target_user_type->value ?? $outcome->target_user_type,
            $outcome->user_id,
            $outcome->author?->name ?? 'N/A',
            $outcome->status->value ?? $outcome->status,
            $outcome->view_count,
            $outcome->is_premium ? 'Yes' : 'No',
            $outcome->intended_type,
            $outcome->intended_type_label,
            json_encode($outcome->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'type' => $tag->type,
            ])->toArray()),
            $outcome->tags->pluck('name')->join(', '),
            json_encode($outcome->recommendedForTasks->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'sort_order' => $task->pivot->sort_order ?? null,
            ])->toArray()),
            $outcome->recommendedForTasks->pluck('id')->join(', '),
            $outcome->created_at?->format('Y-m-d H:i:s'),
            $outcome->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Outcomes';
    }
}

<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Tasks\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Task::with(['author', 'tags', 'recommendedOutcomes'])->get();
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
            'Duration Time',
            'Duration Type',
            'Duration Display',
            'Target User Type',
            'Author ID',
            'Author Name',
            'Status',
            'View Count',
            'Is Premium',
            'Tags (JSON)',
            'Tag Names (Comma Separated)',
            'Recommended Outcomes (JSON)',
            'Recommended Outcome IDs (Comma Separated)',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param Task $task
     * @return array
     */
    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->description,
            $task->difficulty_level,
            $task->duration_time,
            $task->duration_type,
            $task->duration_display,
            $task->target_user_type->value ?? $task->target_user_type,
            $task->user_id,
            $task->author?->name ?? 'N/A',
            $task->status->value ?? $task->status,
            $task->view_count,
            $task->is_premium ? 'Yes' : 'No',
            json_encode($task->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'type' => $tag->type,
            ])->toArray()),
            $task->tags->pluck('name')->join(', '),
            json_encode($task->recommendedOutcomes->map(fn($outcome) => [
                'id' => $outcome->id,
                'title' => $outcome->title,
                'intended_type' => $outcome->intended_type,
                'sort_order' => $outcome->pivot->sort_order ?? null,
            ])->toArray()),
            $task->recommendedOutcomes->pluck('id')->join(', '),
            $task->created_at?->format('Y-m-d H:i:s'),
            $task->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Tasks';
    }
}

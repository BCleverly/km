<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Models\Tag;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Example import class showing how to restore relationships from exported data.
 * This is a demonstration - you would need to implement separate import classes
 * for each sheet (Tags, Tasks, Outcomes) and handle the relationships properly.
 */
class MasterDataImport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Tags' => new TagsImport(),
            'Tasks' => new TasksImport(),
            'Outcomes' => new OutcomesImport(),
        ];
    }
}

class TagsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Create or update tag
            $tag = Tag::updateOrCreate(
                ['id' => $row['id']],
                [
                    'name' => json_decode($row['name_json'], true),
                    'slug' => json_decode($row['slug_json'], true),
                    'type' => $row['type'],
                    'order_column' => $row['order_column'],
                    'status' => ContentStatus::from($row['status']),
                    'created_by' => $row['created_by_user_id'],
                    'approved_by' => $row['approved_by_user_id'],
                    'approved_at' => $row['approved_at'] ? \Carbon\Carbon::parse($row['approved_at']) : null,
                ]
            );
        }
    }
}

class TasksImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Create or update task
            $task = Task::updateOrCreate(
                ['id' => $row['id']],
                [
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'difficulty_level' => $row['difficulty_level'],
                    'duration_time' => $row['duration_time'],
                    'duration_type' => $row['duration_type'],
                    'target_user_type' => TargetUserType::from($row['target_user_type']),
                    'user_id' => $row['author_id'],
                    'status' => ContentStatus::from($row['status']),
                    'view_count' => $row['view_count'],
                    'is_premium' => $row['is_premium'] === 'Yes',
                ]
            );

            // Restore tag relationships
            if ($row['tags_json']) {
                $tagData = json_decode($row['tags_json'], true);
                $tagIds = collect($tagData)->pluck('id')->toArray();
                $task->tags()->sync($tagIds);
            }

            // Restore recommended outcomes relationships
            if ($row['recommended_outcomes_json']) {
                $outcomeData = json_decode($row['recommended_outcomes_json'], true);
                $outcomeIds = collect($outcomeData)->pluck('id')->toArray();
                $task->recommendedOutcomes()->sync($outcomeIds);
            }
        }
    }
}

class OutcomesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Create or update outcome
            $outcome = Outcome::updateOrCreate(
                ['id' => $row['id']],
                [
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'difficulty_level' => $row['difficulty_level'],
                    'target_user_type' => TargetUserType::from($row['target_user_type']),
                    'user_id' => $row['author_id'],
                    'status' => ContentStatus::from($row['status']),
                    'view_count' => $row['view_count'],
                    'is_premium' => $row['is_premium'] === 'Yes',
                    'intended_type' => $row['intended_type'],
                ]
            );

            // Restore tag relationships
            if ($row['tags_json']) {
                $tagData = json_decode($row['tags_json'], true);
                $tagIds = collect($tagData)->pluck('id')->toArray();
                $outcome->tags()->sync($tagIds);
            }

            // Restore recommended for tasks relationships
            if ($row['recommended_for_tasks_json']) {
                $taskData = json_decode($row['recommended_for_tasks_json'], true);
                $taskIds = collect($taskData)->pluck('id')->toArray();
                $outcome->recommendedForTasks()->sync($taskIds);
            }
        }
    }
}

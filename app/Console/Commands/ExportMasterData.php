<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exports\MasterDataExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:master-data {--filename=master-data-export.xlsx : The filename for the export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all tags, tasks, and outcomes to an Excel file with separate sheets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filename = $this->option('filename');
        
        $this->info('Starting master data export...');
        
        try {
            // Generate timestamp for unique filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $finalFilename = str_replace('.xlsx', "_{$timestamp}.xlsx", $filename);
            
            $this->info("Exporting to: {$finalFilename}");
            
            // Use raw PhpSpreadsheet since Laravel Excel is not working properly
            $this->warn('⚠️ Using raw PhpSpreadsheet method...');
            $this->createRawExport($finalFilename);
            
            $filePath = storage_path("app/exports/{$finalFilename}");
            if (file_exists($filePath)) {
                $this->info("📁 File saved to: storage/app/exports/{$finalFilename}");
                $this->info("📁 Full path: {$filePath}");
            } else {
                $this->error("❌ File was not created at: {$filePath}");
            }
            
            // Show summary
            $this->newLine();
            $this->info('📊 Export Summary:');
            $this->line('  • Tags sheet: All tags with creator/approver info');
            $this->line('  • Tasks sheet: All tasks with tags and recommended outcomes');
            $this->line('  • Outcomes sheet: All outcomes with tags and recommended tasks');
            $this->newLine();
            $this->info('💡 The JSON columns contain structured data for re-importing relationships.');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Export failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Create export using raw PhpSpreadsheet
     */
    private function createRawExport(string $filename): void
    {
        $spreadsheet = new Spreadsheet();
        
        // Remove default sheet
        $spreadsheet->removeSheetByIndex(0);
        
        // Create Tags sheet
        $this->createTagsSheet($spreadsheet);
        
        // Create Tasks sheet
        $this->createTasksSheet($spreadsheet);
        
        // Create Outcomes sheet
        $this->createOutcomesSheet($spreadsheet);
        
        // Save the file
        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path("app/exports/{$filename}");
        $writer->save($filePath);
        
        $this->info('✅ Raw PhpSpreadsheet export completed successfully!');
    }

    /**
     * Create Tags sheet
     */
    private function createTagsSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tags');
        
        // Headers
        $headers = [
            'ID', 'Name (JSON)', 'Slug (JSON)', 'Type', 'Order Column', 'Status',
            'Created By (User ID)', 'Created By (Name)', 'Approved By (User ID)', 
            'Approved By (Name)', 'Approved At', 'Created At', 'Updated At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle('A1:M1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Get data
        $tags = \App\Models\Models\Tag::with(['creator', 'approver'])->get();
        $row = 2;
        
        foreach ($tags as $tag) {
            $sheet->setCellValue("A{$row}", $tag->id);
            $sheet->setCellValue("B{$row}", json_encode($tag->name));
            $sheet->setCellValue("C{$row}", json_encode($tag->slug));
            $sheet->setCellValue("D{$row}", $tag->type);
            $sheet->setCellValue("E{$row}", $tag->order_column);
            $sheet->setCellValue("F{$row}", $tag->status->value ?? $tag->status);
            $sheet->setCellValue("G{$row}", $tag->created_by);
            $sheet->setCellValue("H{$row}", $tag->creator?->name ?? 'N/A');
            $sheet->setCellValue("I{$row}", $tag->approved_by);
            $sheet->setCellValue("J{$row}", $tag->approver?->name ?? 'N/A');
            $sheet->setCellValue("K{$row}", $tag->approved_at?->format('Y-m-d H:i:s'));
            $sheet->setCellValue("L{$row}", $tag->created_at?->format('Y-m-d H:i:s'));
            $sheet->setCellValue("M{$row}", $tag->updated_at?->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create Tasks sheet
     */
    private function createTasksSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tasks');
        
        // Headers
        $headers = [
            'ID', 'Title', 'Description', 'Difficulty Level', 'Duration Time', 'Duration Type',
            'Duration Display', 'Target User Type', 'Author ID', 'Author Name', 'Status',
            'View Count', 'Is Premium', 'Tags (JSON)', 'Tag Names (Comma Separated)',
            'Recommended Outcomes (JSON)', 'Recommended Outcome IDs (Comma Separated)',
            'Created At', 'Updated At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $sheet->getStyle('A1:S1')->getFont()->setBold(true);
        $sheet->getStyle('A1:S1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Get data
        $tasks = \App\Models\Tasks\Task::with(['author', 'tags', 'recommendedOutcomes'])->get();
        $row = 2;
        
        foreach ($tasks as $task) {
            $sheet->setCellValue("A{$row}", $task->id);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->description);
            $sheet->setCellValue("D{$row}", $task->difficulty_level);
            $sheet->setCellValue("E{$row}", $task->duration_time);
            $sheet->setCellValue("F{$row}", $task->duration_type);
            $sheet->setCellValue("G{$row}", $task->duration_display);
            $sheet->setCellValue("H{$row}", $task->target_user_type->value ?? $task->target_user_type);
            $sheet->setCellValue("I{$row}", $task->user_id);
            $sheet->setCellValue("J{$row}", $task->author?->name ?? 'N/A');
            $sheet->setCellValue("K{$row}", $task->status->value ?? $task->status);
            $sheet->setCellValue("L{$row}", $task->view_count);
            $sheet->setCellValue("M{$row}", $task->is_premium ? 'Yes' : 'No');
            $sheet->setCellValue("N{$row}", json_encode($task->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'type' => $tag->type,
            ])->toArray()));
            $sheet->setCellValue("O{$row}", $task->tags->pluck('name')->join(', '));
            $sheet->setCellValue("P{$row}", json_encode($task->recommendedOutcomes->map(fn($outcome) => [
                'id' => $outcome->id,
                'title' => $outcome->title,
                'intended_type' => $outcome->intended_type,
                'sort_order' => $outcome->pivot->sort_order ?? null,
            ])->toArray()));
            $sheet->setCellValue("Q{$row}", $task->recommendedOutcomes->pluck('id')->join(', '));
            $sheet->setCellValue("R{$row}", $task->created_at?->format('Y-m-d H:i:s'));
            $sheet->setCellValue("S{$row}", $task->updated_at?->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create Outcomes sheet
     */
    private function createOutcomesSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Outcomes');
        
        // Headers
        $headers = [
            'ID', 'Title', 'Description', 'Difficulty Level', 'Target User Type',
            'Author ID', 'Author Name', 'Status', 'View Count', 'Is Premium',
            'Intended Type', 'Intended Type Label', 'Tags (JSON)', 'Tag Names (Comma Separated)',
            'Recommended For Tasks (JSON)', 'Recommended For Task IDs (Comma Separated)',
            'Created At', 'Updated At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $sheet->getStyle('A1:R1')->getFont()->setBold(true);
        $sheet->getStyle('A1:R1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Get data
        $outcomes = \App\Models\Tasks\Outcome::with(['author', 'tags', 'recommendedForTasks'])->get();
        $row = 2;
        
        foreach ($outcomes as $outcome) {
            $sheet->setCellValue("A{$row}", $outcome->id);
            $sheet->setCellValue("B{$row}", $outcome->title);
            $sheet->setCellValue("C{$row}", $outcome->description);
            $sheet->setCellValue("D{$row}", $outcome->difficulty_level);
            $sheet->setCellValue("E{$row}", $outcome->target_user_type->value ?? $outcome->target_user_type);
            $sheet->setCellValue("F{$row}", $outcome->user_id);
            $sheet->setCellValue("G{$row}", $outcome->author?->name ?? 'N/A');
            $sheet->setCellValue("H{$row}", $outcome->status->value ?? $outcome->status);
            $sheet->setCellValue("I{$row}", $outcome->view_count);
            $sheet->setCellValue("J{$row}", $outcome->is_premium ? 'Yes' : 'No');
            $sheet->setCellValue("K{$row}", $outcome->intended_type);
            $sheet->setCellValue("L{$row}", $outcome->intended_type_label);
            $sheet->setCellValue("M{$row}", json_encode($outcome->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'type' => $tag->type,
            ])->toArray()));
            $sheet->setCellValue("N{$row}", $outcome->tags->pluck('name')->join(', '));
            $sheet->setCellValue("O{$row}", json_encode($outcome->recommendedForTasks->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'sort_order' => $task->pivot->sort_order ?? null,
            ])->toArray()));
            $sheet->setCellValue("P{$row}", $outcome->recommendedForTasks->pluck('id')->join(', '));
            $sheet->setCellValue("Q{$row}", $outcome->created_at?->format('Y-m-d H:i:s'));
            $sheet->setCellValue("R{$row}", $outcome->updated_at?->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}

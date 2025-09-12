<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SearchStrategyInterface
{
    /**
     * Search across all content types
     */
    public function search(string $query, string $type = 'all', bool $premium = false): LengthAwarePaginator;

    /**
     * Get result counts for each content type
     */
    public function getResultCounts(string $query, bool $premium = false): array;
}

<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class DatatableSort
{
    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  array<string, mixed>  $filters
     * @param  list<string>  $allowed
     * @return Builder<\Illuminate\Database\Eloquent\Model>
     */
    public static function apply(
        Builder $query,
        array $filters,
        array $allowed,
        string $defaultColumn = 'id',
        string $defaultDir = 'desc',
    ): Builder {
        $column = (string) ($filters['sort_by'] ?? $defaultColumn);
        $dir = strtolower((string) ($filters['sort_dir'] ?? $defaultDir)) === 'asc' ? 'asc' : 'desc';

        if (! in_array($column, $allowed, true)) {
            $column = $defaultColumn;
            $dir = $defaultDir === 'asc' ? 'asc' : 'desc';
        }

        $query->reorder();

        return $query->orderBy($column, $dir);
    }
}

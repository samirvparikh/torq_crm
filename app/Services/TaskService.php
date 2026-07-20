<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Task
    {
        $data['status'] = $data['status'] ?? TaskStatus::Pending->value;

        return Task::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Task $task, array $data): Task
    {
        if (($data['status'] ?? null) === TaskStatus::Completed->value) {
            $data['completed_at'] = $data['completed_at'] ?? now();
        }

        $task->update($data);

        return $task->fresh(['lead', 'customer', 'assignee']);
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Task::query()
            ->with(['lead', 'assignee']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        \App\Support\DatatableSort::apply($query, $filters, [
            'id', 'title', 'assigned_to', 'priority', 'status', 'due_date', 'created_at',
        ], 'id', 'desc');

        return $query->paginate($perPage);
    }
}

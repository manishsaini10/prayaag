<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * All data access flows through a repository. Services depend on
 * RepositoryInterface, never on Eloquent directly.
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $class = $this->model();
        $this->model = new $class();
    }

    /** @return class-string<Model> */
    abstract protected function model(): string;

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }

    public function find(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model->newInstance()->create($attributes);
    }

    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->findOrFail($id);
        $record->update($attributes);

        return $record->refresh();
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }
}

<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}

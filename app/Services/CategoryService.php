<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService extends BaseService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function getCategoryById(int $id): Category
    {
        return $this->categoryRepository->findById($id) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);
        return $this->categoryRepository->update($category, $data);
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->getCategoryById($id);
        $this->categoryRepository->delete($category);
    }
}

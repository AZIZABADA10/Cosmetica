<?php
namespace App\DAOs;

use App\Models\Category;
use App\DTOs\CategoryDTO;

class CategoryDAO
{
    public function getAll()
    {
        return Category::all();
    }

    public function getById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(CategoryDTO $dto): Category
    {
        return Category::create([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
    }

    public function update(Category $category, CategoryDTO $dto): Category
    {
        $category->update([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);

        return $category;
    }

     public function delete(Category $category): void
    {
        $category->delete();
    }
}
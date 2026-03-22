<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Services\CategoryService;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use JsonResponseTrait;

    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->successResponse($categories);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->successResponse($category, 'Catégorie créée avec succès', 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return $this->successResponse($category);
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());
        return $this->successResponse($category, 'Catégorie mise à jour avec succès');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return $this->successResponse(null, 'Catégorie supprimée avec succès');
    }
}

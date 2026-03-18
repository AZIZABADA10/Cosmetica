<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DAOs\CategoryDAO;
use App\DTOs\CategoryDTO;

class CategoryController extends Controller
{
    private CategoryDAO $categoryDAO;

     public function __construct(CategoryDAO $categoryDAO)
    {
        $this->categoryDAO = $categoryDAO;
    }

     public function index()
    {
        $categories = $this->categoryDAO->getAll();
        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string'
        ]);

        $dto = CategoryDTO::fromRequest($request);
        $category = $this->categoryDAO->create($dto);

        return response()->json([
        'message' => 'Catégorie créée avec succès.',
        'data' => $category
        ], 201);
    }

     public function show($id)
    {
        $category = $this->categoryDAO->getById($id);
        return response()->json($category, 200);
    }

     public function update(Request $request, $id)
    {
         $category = $this->categoryDAO->getById($id);

          $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

         $dto = CategoryDTO::fromRequest($request);
        $updatedCategory = $this->categoryDAO->update($category, $dto);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès.',
            'data' => $updatedCategory
        ], 200);
    }

     public function destroy($id)
    {
        $category = $this->categoryDAO->getById($id);
        $this->categoryDAO->delete($category);

        return response()->json([
            'message' => 'Catégorie supprimée avec succès.'
        ], 200);
    }
} 
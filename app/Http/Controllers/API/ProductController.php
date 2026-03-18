<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DAOs\ProductDAO;
use App\DTOs\ProductDTO;
use App\Models\Product;

class ProductController extends Controller
{
    private ProductDAO $productDAO;

    public function __construct(ProductDAO $productDAO)
    {
        $this->productDAO = $productDAO;
    }

    public function index()
    {
        return response()->json($this->productDAO->getAll(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productDAO->create($dto);

            return response()->json([
                'message' => 'Produit créé avec succès',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

 
    public function show($slug)
    {
        try {
            $product = $this->productDAO->getBySlug($slug);

            return response()->json($product, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Produit non trouvé'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('images')->findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            $dto = ProductDTO::fromRequest($request);
            $updated = $this->productDAO->update($product, $dto);

            return response()->json([
                'message' => 'Produit mis à jour avec succès',
                'data' => $updated
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        $this->productDAO->delete($product);

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ], 200);
    }
}
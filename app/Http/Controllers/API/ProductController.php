<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Services\ProductService;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use JsonResponseTrait;

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = \App\Models\Product::query();

        if ($request->has('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->has('max_price')) {
            $query->priceBelow($request->max_price);
        }

        $products = $query->with('category')->paginate(10);
        return $this->successResponse($products);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->validated(),
            $request->file('images') ?? []
        );

        return $this->successResponse($product, 'Produit créé avec succès', 201);
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);
        return $this->successResponse($product);
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateProduct(
            $id,
            $request->validated(),
            $request->file('images') ?? []
        );

        return $this->successResponse($product, 'Produit mis à jour avec succès');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return $this->successResponse(null, 'Produit supprimé avec succès');
    }
}
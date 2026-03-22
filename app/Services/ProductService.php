<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAllWithRelations();
    }

    public function getProductBySlug(string $slug): Product
    {
        return $this->productRepository->findBySlugWithRelations($slug) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
    }

    public function createProduct(array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($data, $images) {
            $product = $this->productRepository->create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price']
            ]);

            foreach ($images as $imageFile) {
                $path = $imageFile->store('products', 'public');
                $this->productRepository->addImage($product, $path);
            }

            return $product->load('images', 'category');
        });
    }

    public function updateProduct(int $id, array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($id, $data, $images) {
            $product = $this->productRepository->findById($id) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            
            $this->productRepository->update($product, [
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price']
            ]);

            if (!empty($images)) {
                $this->productRepository->deleteImages($product);
                foreach ($images as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $this->productRepository->addImage($product, $path);
                }
            }

            return $product->load('images', 'category');
        });
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->productRepository->findById($id) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        $this->productRepository->deleteImages($product);
        $this->productRepository->delete($product);
    }
}

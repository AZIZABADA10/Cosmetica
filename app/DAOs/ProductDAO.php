<?php

namespace App\DAOs;

use App\Models\Product;
use App\Models\Image;
use App\DTOs\ProductDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductDAO
{
    public function getAll()
    {
        return Product::with('images', 'category')->latest()->get();
    }

 
    public function getBySlug(string $slug): Product
    {
        return Product::with('images', 'category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

 
    public function create(ProductDTO $dto): Product
    {
        return DB::transaction(function () use ($dto) {

            if (count($dto->images) > 4) {
                throw new \Exception("Limite de 4 images par produit dépassée");
            }

            $product = Product::create([
                'category_id' => $dto->category_id,
                'name' => $dto->name,
                'description' => $dto->description,
                'price' => $dto->price
            ]);

            foreach ($dto->images as $imageFile) {
                $path = $imageFile->store('products', 'public');

                Image::create([
                    'product_id' => $product->id,
                    'image' => $path
                ]);
            }

            return $product->load('images', 'category');
        });
    }

 
    public function update(Product $product, ProductDTO $dto): Product
    {
        return DB::transaction(function () use ($product, $dto) {

            $product->update([
                'category_id' => $dto->category_id,
                'name' => $dto->name,
                'description' => $dto->description,
                'price' => $dto->price
            ]);

             if (!empty($dto->images)) {

                if (count($dto->images) > 4) {
                    throw new \Exception("Limite de 4 images par produit dépassée");
                }

 
                foreach ($product->images as $img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }

                foreach ($dto->images as $imageFile) {
                    $path = $imageFile->store('products', 'public');

                    Image::create([
                        'product_id' => $product->id,
                        'image' => $path
                    ]);
                }
            }

            return $product->load('images', 'category');
        });
    }

 
    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {

            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image);
                $img->delete();
            }

            $product->delete();
        });
    }
}
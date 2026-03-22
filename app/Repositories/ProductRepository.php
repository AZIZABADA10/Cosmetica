<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function getAllWithRelations(): Collection
    {
        return Product::with('images', 'category')->latest()->get();
    }

    public function findBySlugWithRelations(string $slug): ?Product
    {
        return Product::with('images', 'category')->where('slug', $slug)->first();
    }

    public function addImage(Product $product, string $path): Image
    {
        return Image::create([
            'product_id' => $product->id,
            'image' => $path
        ]);
    }

    public function deleteImages(Product $product): void
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image);
            $img->delete();
        }
    }
}

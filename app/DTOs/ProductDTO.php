<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ProductDTO
{
    public function __construct(
        public int $category_id,
        public string $name,
        public ?string $description,
        public float $price,
        public array $images = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            category_id: $request->category_id,
            name: $request->name,
            description: $request->description,
            price: $request->price,
            images: $request->file('images') ?? []
        );
    }
}
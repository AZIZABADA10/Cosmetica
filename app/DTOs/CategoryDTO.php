<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $description 
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('description')
        );
    }
}
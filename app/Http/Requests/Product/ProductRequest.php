<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images' => 'array|max:4',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'images.max' => 'La limite est de 4 images par produit.',
        ];
    }
}

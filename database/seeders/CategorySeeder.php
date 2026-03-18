<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Crèmes',
                'description' => 'Crèmes naturelles pour tous types de peau'
            ],
            [
                'name' => 'Huiles',
                'description' => 'Huiles végétales bio pour soins et massages'
            ],
            [
                'name' => 'Sérums',
                'description' => 'Sérums concentrés pour visage et cheveux'
            ],
            [
                'name' => 'Masques',
                'description' => 'Masques naturels pour soins du visage'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
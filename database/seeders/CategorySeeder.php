<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Politik',
            'Ekonomi',
            'Olahraga',
            'Teknologi',
            'Kesehatan',
            'Pendidikan',
            'Hiburan',
            'Lifestyle',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category
            ]);
        }
    }
} 
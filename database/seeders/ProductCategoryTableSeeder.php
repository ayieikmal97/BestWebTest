<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Fashion',
            'Home & Living',
            'Beauty & Health',
            'Sports',
            'Books',
            'Groceries',
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                ['name' => $category]
            );
        }
    }
}

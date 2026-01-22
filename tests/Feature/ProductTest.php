<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_create_product()
    {
        $category = ProductCategory::factory()->create();
        $user = User::factory()->create();

        
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/product', [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'description' => 'Sample description',
            'price' => 100,
            'stock' => 50,
            'status' => 1,
        ]);

        
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_product_creation_requires_fields()
    {
        $user = User::factory()->create();

        
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/product', []);
        
        $response->assertJsonValidationErrors(['name', 'category_id', 'price']);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::factory()->create([
            'name' => 'Old Name'
        ]);

        $user = User::factory()->create();

        
        $this->actingAs($user, 'sanctum');
        
        $response = $this->postJson("/api/product", [
            'name' => 'Updated Product',
            'category_id' => $product->category_id,
            'description' =>'Sample description',
            'price' => 99.99,                       
            'stock' => 10,
            'status' => 1,
            'id'=>$product->id
        ]);

        if ($response->status() !== 200) {
            dump($response->json()); 
        }

        // 4. Assertions
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id'   => $product->id, 
            'name' => 'Updated Product'
        ]);
    }

    public function test_admin_can_soft_delete_product()
    {
        $user = User::factory()->create();

        
        $this->actingAs($user, 'sanctum');

        $product = Product::factory()->create();
        
        $this->deleteJson("/api/product/{$product->id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_bulk_delete_products()
    {
        $user = User::factory()->create();

        
        $this->actingAs($user, 'sanctum');

        $products = Product::factory()->count(3)->create();
        $ids = $products->pluck('id')->toArray();

        $this->deleteJson('/api/product', ['ids' => $ids])
            ->assertStatus(200);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }


}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_guest_can_view_all_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    public function test_guest_can_view_product_by_slug()
    {
        $product = Product::factory()->create(['name' => 'Bio Cream']);

        $response = $this->getJson("/api/products/slug/{$product->slug}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Bio Cream');
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id
        ]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->postJson('/api/admin/products', [
            'category_id' => $category->id,
            'name' => 'New Product',
            'description' => 'A great description',
            'price' => 29.99
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_client_cannot_create_product()
    {
        $client = User::factory()->create([
            'role_id' => Role::where('name', 'client')->first()->id
        ]);
        $category = Category::factory()->create();

        $response = $this->actingAs($client, 'api')->postJson('/api/admin/products', [
            'category_id' => $category->id,
            'name' => 'Stealth Product',
            'price' => 10.00
        ]);

        $response->assertStatus(403);
    }
}

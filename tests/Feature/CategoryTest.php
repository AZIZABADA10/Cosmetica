<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_admin_can_crud_categories()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id
        ]);

        // Create
        $response = $this->actingAs($admin, 'api')->postJson('/api/admin/categories', [
            'name' => 'Essential Oils',
            'description' => 'Pure natural oils'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Essential Oils']);
        $categoryId = $response->json('data.id');

        // Update
        $response = $this->actingAs($admin, 'api')->putJson("/api/admin/categories/{$categoryId}", [
            'name' => 'Premium Oils'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['name' => 'Premium Oils']);

        // Delete
        $response = $this->actingAs($admin, 'api')->deleteJson("/api/admin/categories/{$categoryId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }
}

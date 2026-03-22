<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_user_can_create_order()
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'client')->first()->id
        ]);
        $product = Product::factory()->create(['price' => 100]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                ['slug' => $product->slug, 'quantity' => 2]
            ]
        ]);

        dd($response->json());
        $response->assertStatus(201)
                 ->assertJsonPath('data.total_price', '200.00');
        
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 200]);
    }

    public function test_employee_can_update_order_status()
    {
        $employee = User::factory()->create([
            'role_id' => Role::where('name', 'employe')->first()->id
        ]);
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'status' => 'pending',
            'total_price' => 100
        ]);

        $response = $this->actingAs($employee, 'api')->putJson("/api/orders/{$order->id}/status", [
            'status' => 'preparing'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);
    }

    public function test_user_can_cancel_pending_order()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 50
        ]);

        $response = $this->actingAs($user, 'api')->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
    }

    public function test_user_cannot_cancel_preparing_order()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'preparing',
            'total_price' => 50
        ]);

        $response = $this->actingAs($user, 'api')->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(400); // Bad request because logic prevents it
    }
}

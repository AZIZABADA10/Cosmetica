<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminService extends BaseService
{
    public function getSalesStats(): array
    {
        $totalSales = Order::where('status', '!=', 'cancelled')->sum('total_price');

        $popularProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'total_sales' => $totalSales,
            'popular_products' => $popularProducts,
            'sales_by_category' => $salesByCategory
        ];
    }
}

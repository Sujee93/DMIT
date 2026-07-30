<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'customerCount' => Customer::count(),
            'userCount' => User::count(),
            'recentProducts' => Product::latest()->take(5)->get(),
        ]);
    }
}

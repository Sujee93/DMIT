<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
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
            'invoiceCount' => Invoice::count(),
            'salesToday' => Invoice::whereDate('date_of_invoice', today())->sum('total_amount'),
            'recentProducts' => Product::latest()->take(5)->get(),
        ]);
    }
}

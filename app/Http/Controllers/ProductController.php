<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();
        $status = $request->query('status');

        $products = Product::query()
            ->search($search)
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();

        return view('products.index', compact('products', 'search', 'status'));
    }

    public function create(): View
    {
        return view('products.create', ['product' => new Product(['is_active' => true])]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('products.index')->with('success', "Product {$product->code} created.");
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')->with('success', "Product {$product->code} updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('admin');

        // Invoice lines keep a copy of the product details, so history is preserved.
        $product->delete();

        return redirect()->route('products.index')->with('success', "Product {$product->code} deleted.");
    }
}

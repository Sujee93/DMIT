<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Stock on hand, movement history and manual adjustments.
 */
class StockController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();
        $filter = (string) $request->query('filter', '');

        $products = Product::query()
            ->search($search)
            ->when($filter === 'in', fn ($q) => $q->where('stock', '>', 0))
            ->when($filter === 'out', fn ($q) => $q->where('stock', '<=', 0))
            ->when($filter === 'low', fn ($q) => $q->where('stock', '>', 0)->where('reorder_level', '>', 0)->whereColumn('stock', '<=', 'reorder_level'))
            ->orderBy('code')
            ->paginate(30)
            ->withQueryString();

        $totals = Product::query()->selectRaw(
            'SUM(CASE WHEN stock > 0 THEN stock ELSE 0 END) as units, '
            .'SUM(CASE WHEN stock > 0 THEN stock * cost ELSE 0 END) as value, '
            .'SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_count, '
            .'SUM(CASE WHEN stock > 0 AND reorder_level > 0 AND stock <= reorder_level THEN 1 ELSE 0 END) as low_count'
        )->first();

        return view('stock.index', compact('products', 'search', 'filter', 'totals'));
    }

    public function show(Product $product): View
    {
        $movements = $product->stockMovements()
            ->with('creator:id,name')
            ->latest('movement_date')
            ->latest('id')
            ->paginate(30);

        return view('stock.show', compact('product', 'movements'));
    }

    public function createAdjustment(Request $request): View
    {
        $this->authorize('admin');

        return view('stock.adjust', [
            'products' => Product::query()->orderBy('code')->get(['id', 'code', 'name', 'stock']),
            'selectedId' => $request->integer('product_id') ?: null,
        ]);
    }

    public function storeAdjustment(StockAdjustmentRequest $request, StockService $stock): RedirectResponse
    {
        $data = $request->validated();
        $product = Product::query()->findOrFail($data['product_id']);
        $quantity = $data['direction'] === 'add' ? (int) $data['quantity'] : -(int) $data['quantity'];

        DB::transaction(fn () => $stock->adjust($product, $quantity, $data['note'], $data['movement_date'], $request->user()));

        return redirect()->route('stock.show', $product)
            ->with('success', 'Stock '.($quantity > 0 ? 'added' : 'removed').": {$product->code} ".($quantity > 0 ? '+' : '').$quantity.'.');
    }
}

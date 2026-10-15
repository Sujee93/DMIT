<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrnRequest;
use App\Models\Contact;
use App\Models\Grn;
use App\Models\Product;
use App\Services\GrnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Goods Received Notes (stock in from suppliers).
 */
class GrnController extends Controller
{
    public function __construct(private readonly GrnService $grns) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();
        $supplierId = $request->integer('supplier_id') ?: null;

        $grns = Grn::query()
            ->with('supplier:id,name,company')
            ->withSum('items as total_quantity', 'quantity')
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $q->where(fn ($q) => $q->where('grn_no', 'like', $like)->orWhere('supplier_ref', 'like', $like));
            })
            ->latest('grn_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('grns.index', [
            'grns' => $grns,
            'search' => $search,
            'supplierId' => $supplierId,
            'suppliers' => Contact::query()->suppliers()->orderBy('name')->get(['id', 'name', 'company']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('grns.create', $this->formData(new Grn([
            'grn_date' => today(),
            'supplier_id' => $request->integer('supplier_id') ?: null,
        ])));
    }

    public function store(GrnRequest $request): RedirectResponse
    {
        $grn = $this->grns->create($request->validated(), $request->user());

        return redirect()->route('grns.show', $grn)->with('success', "{$grn->grn_no} saved and stock updated.");
    }

    public function show(Grn $grn): View
    {
        $grn->load(['supplier', 'items', 'creator:id,name']);

        return view('grns.show', compact('grn'));
    }

    public function edit(Grn $grn): View
    {
        $grn->load('items');

        return view('grns.edit', $this->formData($grn));
    }

    public function update(GrnRequest $request, Grn $grn): RedirectResponse
    {
        $grn = $this->grns->update($grn, $request->validated(), $request->user());

        return redirect()->route('grns.show', $grn)->with('success', "{$grn->grn_no} updated and stock adjusted.");
    }

    public function destroy(Grn $grn): RedirectResponse
    {
        $this->authorize('admin');

        $this->grns->delete($grn);

        return redirect()->route('grns.index')->with('success', "{$grn->grn_no} deleted and its stock removed.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Grn $grn): array
    {
        $columns = ['id', 'code', 'name', 'color', 'size', 'cost', 'stock'];
        $products = Product::query()->active()->orderBy('code')->get($columns);

        if ($grn->exists) {
            $missing = $grn->items->pluck('product_id')->diff($products->pluck('id'));
            if ($missing->isNotEmpty()) {
                $products = $products->concat(Product::query()->whereIn('id', $missing)->get($columns));
            }
        }

        return [
            'grn' => $grn,
            'suppliers' => Contact::query()->suppliers()->active()->orderBy('name')->get(['id', 'name', 'company']),
            'productOptions' => $products->map(fn (Product $p) => [
                'id' => $p->id,
                'code' => $p->code,
                'name' => $p->name,
                'description' => $p->variant(),
                'cost' => $p->cost,
                'stock' => $p->stock,
            ])->values(),
        ];
    }
}

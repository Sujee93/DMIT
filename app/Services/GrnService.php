<?php

namespace App\Services;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Goods Received Notes: add stock from a supplier and record what we owe them.
 */
class GrnService
{
    public function __construct(
        private readonly InvoiceNumberGenerator $numbers,
        private readonly StockService $stock,
    ) {}

    /**
     * @param  array<string, mixed>  $data  validated GrnRequest data
     */
    public function create(array $data, User $user): Grn
    {
        return DB::transaction(function () use ($data, $user) {
            $grn = new Grn($this->headerAttributes($data));
            $grn->grn_no = $this->numbers->nextGrn();
            $grn->created_by = $user->id;

            $this->fillItemsAndTotals($grn, $data);
            $this->addStock($grn, $user);

            return $grn;
        });
    }

    /**
     * @param  array<string, mixed>  $data  validated GrnRequest data
     */
    public function update(Grn $grn, array $data, User $user): Grn
    {
        return DB::transaction(function () use ($grn, $data, $user) {
            $grn = Grn::query()->lockForUpdate()->findOrFail($grn->id);
            $grn->fill($this->headerAttributes($data));
            $grn->items()->delete();

            $this->fillItemsAndTotals($grn, $data);
            $this->addStock($grn, $user);

            return $grn;
        });
    }

    public function delete(Grn $grn): void
    {
        DB::transaction(function () use ($grn) {
            $grn = Grn::query()->lockForUpdate()->findOrFail($grn->id);

            $shortages = $this->stock->sync(StockMovement::SOURCE_GRN, $grn->id, $grn->grn_no, $grn->grn_date, []);
            if ($shortages !== []) {
                throw ValidationException::withMessages([
                    'grn' => 'This GRN cannot be deleted because some of its stock has already been invoiced: '
                        .$this->describe($shortages).'.',
                ]);
            }

            $grn->items()->delete();
            $grn->delete();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function headerAttributes(array $data): array
    {
        return [
            'supplier_id' => $data['supplier_id'],
            'grn_date' => $data['grn_date'],
            'supplier_ref' => $data['supplier_ref'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillItemsAndTotals(Grn $grn, array $data): void
    {
        $lines = array_values($data['items']);
        $products = Product::query()->whereIn('id', array_column($lines, 'product_id'))->get()->keyBy('id');

        $subtotal = 0;
        $rows = [];
        foreach ($lines as $index => $line) {
            /** @var Product $product */
            $product = $products->get((int) $line['product_id']);
            $quantity = (int) $line['quantity'];
            $unitCost = Money::toCents($line['unit_cost']);
            $lineTotal = $quantity * $unitCost;
            $subtotal += $lineTotal;

            $rows[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_cost' => Money::fromCents($unitCost),
                'line_total' => Money::fromCents($lineTotal),
                'sort_order' => $index,
            ];
        }

        $discount = Money::toCents($data['discount'] ?? 0);
        if ($discount > $subtotal) {
            throw ValidationException::withMessages(['discount' => 'The discount cannot be more than the bill subtotal.']);
        }

        $grn->subtotal = Money::fromCents($subtotal);
        $grn->discount = Money::fromCents($discount);
        $grn->total = Money::fromCents($subtotal - $discount);
        $grn->save();

        $grn->items()->createMany($rows);

        // Keep each product's cost at its most recent purchase price, so invoice profit is current.
        foreach ($rows as $row) {
            $latestCost = GrnItem::query()
                ->join('grns', 'grns.id', '=', 'grn_items.grn_id')
                ->where('grn_items.product_id', $row['product_id'])
                ->orderByDesc('grns.grn_date')
                ->orderByDesc('grns.id')
                ->value('grn_items.unit_cost');

            Product::query()->whereKey($row['product_id'])->update(['cost' => $latestCost ?? $row['unit_cost']]);
        }
    }

    private function addStock(Grn $grn, User $user): void
    {
        $quantities = [];
        foreach ($grn->items()->get() as $item) {
            $quantities[$item->product_id] = ($quantities[$item->product_id] ?? 0) + $item->quantity;
        }

        $shortages = $this->stock->sync(StockMovement::SOURCE_GRN, $grn->id, $grn->grn_no, $grn->grn_date, $quantities, $user);
        if ($shortages !== []) {
            throw ValidationException::withMessages([
                'items' => 'Quantities cannot be reduced below what has already been invoiced: '.$this->describe($shortages).'.',
            ]);
        }
    }

    /**
     * @param  array<int, int>  $shortages
     */
    private function describe(array $shortages): string
    {
        $codes = Product::query()->whereIn('id', array_keys($shortages))->pluck('code', 'id');

        return collect($shortages)->map(fn ($short, $id) => "{$codes[$id]} ({$short} short)")->implode(', ');
    }
}

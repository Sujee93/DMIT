<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

/**
 * The only place product stock is changed. Every change is recorded as a
 * StockMovement and stock is never allowed to go below zero.
 *
 * All methods must run inside a database transaction (product rows are locked).
 */
class StockService
{
    /**
     * Make the stock effect of a document (GRN or invoice) equal to $quantities.
     *
     * Whatever the document moved before is reversed and the new quantities applied,
     * so creating, editing and deleting a document all go through here.
     *
     * @param  array<int, int>  $quantities  product_id => signed quantity (+ in, - out)
     * @return array<int, int> product_id => units short, for lines that would take stock below zero
     */
    public function sync(
        string $source,
        int $sourceId,
        ?string $reference,
        CarbonInterface|string $date,
        array $quantities,
        ?User $user = null,
    ): array {
        $previous = StockMovement::query()
            ->where('source', $source)
            ->where('source_id', $sourceId)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->pluck('qty', 'product_id')
            ->map(fn ($qty) => (int) $qty)
            ->all();

        $productIds = array_unique(array_merge(array_keys($previous), array_keys($quantities)));
        $products = $this->lockProducts($productIds);

        $shortages = [];
        foreach ($productIds as $id) {
            $change = ($quantities[$id] ?? 0) - ($previous[$id] ?? 0);
            $newStock = $products[$id]->stock + $change;
            if ($change < 0 && $newStock < 0) {
                $shortages[$id] = -$newStock;
            }
        }

        if ($shortages !== []) {
            return $shortages;
        }

        foreach ($productIds as $id) {
            $change = ($quantities[$id] ?? 0) - ($previous[$id] ?? 0);
            if ($change !== 0) {
                $products[$id]->forceFill(['stock' => $products[$id]->stock + $change])->save();
            }
        }

        StockMovement::query()->where('source', $source)->where('source_id', $sourceId)->delete();

        foreach ($quantities as $productId => $quantity) {
            if ($quantity === 0) {
                continue;
            }
            StockMovement::query()->create([
                'product_id' => $productId,
                'source' => $source,
                'source_id' => $sourceId,
                'reference' => $reference,
                'movement_date' => $date,
                'quantity' => $quantity,
                'created_by' => $user?->id,
            ]);
        }

        return [];
    }

    /**
     * Manual correction or opening stock (positive adds, negative removes).
     */
    public function adjust(Product $product, int $quantity, string $note, CarbonInterface|string $date, User $user): StockMovement
    {
        $product = $this->lockProducts([$product->id])[$product->id];

        if ($product->stock + $quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->stock} of {$product->code} in stock, so you cannot remove ".abs($quantity).'.',
            ]);
        }

        $product->forceFill(['stock' => $product->stock + $quantity])->save();

        return StockMovement::query()->create([
            'product_id' => $product->id,
            'source' => StockMovement::SOURCE_ADJUSTMENT,
            'movement_date' => $date,
            'quantity' => $quantity,
            'note' => $note,
            'created_by' => $user->id,
        ]);
    }

    /**
     * How many units a document has already taken out, per product (positive numbers).
     * Used to show "available" stock while editing an invoice.
     *
     * @return array<int, int>
     */
    public function takenBy(string $source, int $sourceId): array
    {
        return StockMovement::query()
            ->where('source', $source)
            ->where('source_id', $sourceId)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->pluck('qty', 'product_id')
            ->map(fn ($qty) => -(int) $qty)
            ->all();
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, Product> keyed by id
     */
    private function lockProducts(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        // Locked in id order so concurrent documents cannot deadlock each other.
        return Product::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id')
            ->all();
    }
}

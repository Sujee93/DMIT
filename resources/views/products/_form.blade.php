<div class="form-grid">
    <x-form.input name="code" label="Product code" :value="$product->code" required maxlength="50" placeholder="e.g. SN-1001" autofocus />
    <x-form.input name="name" label="Product name" :value="$product->name" required maxlength="255" placeholder="e.g. Urban Runner Sneaker" />
    <x-form.input name="color" label="Colour" :value="$product->color" maxlength="100" placeholder="Optional" />
    <x-form.input name="size" label="Size" :value="$product->size" maxlength="50" placeholder="Optional, e.g. 42 or 6-10" />
    <x-form.input name="cost" label="Cost price" type="number" step="0.01" min="0" :value="$product->cost" required inputmode="decimal" hint="What you pay the supplier" />
    <x-form.input name="price" label="Selling price" type="number" step="0.01" min="0" :value="$product->price" required inputmode="decimal" hint="Default wholesale price (can be changed per invoice)" />
    <x-form.textarea name="description" label="Description" :value="$product->description" wrapper-class="span-2" maxlength="2000" rows="3" />
    <x-form.checkbox name="is_active" label="Active (available for new invoices)" :checked="$product->is_active" />
</div>

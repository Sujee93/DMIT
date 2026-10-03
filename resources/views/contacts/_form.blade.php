<div class="form-grid">
    <x-form.select name="type" label="Type" :options="['customer' => 'Customer', 'supplier' => 'Supplier']" :value="$contact->type" required />
    <x-form.input name="code" label="Code" :value="$contact->code" maxlength="50" placeholder="Optional, e.g. WP HOR 267" hint="Printed on invoices as Customer Code." />
    <x-form.input name="name" label="Contact name" :value="$contact->name" required maxlength="255" autofocus />
    <x-form.input name="company" label="Company / Shop name" :value="$contact->company" maxlength="255" />
    <x-form.input name="phone" label="Phone" type="tel" :value="$contact->phone" maxlength="50" />
    <x-form.input name="email" label="Email" type="email" :value="$contact->email" maxlength="255" />
    <x-form.textarea name="address" label="Address" :value="$contact->address" maxlength="500" rows="2" />
    <x-form.textarea name="notes" label="Notes" :value="$contact->notes" wrapper-class="span-2" maxlength="2000" rows="3" />
    <x-form.checkbox name="is_active" label="Active" :checked="$contact->is_active" hint="Inactive contacts are hidden when creating invoices." />
</div>

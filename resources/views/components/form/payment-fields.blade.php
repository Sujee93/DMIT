@props(['methods', 'maxAmount' => null, 'defaultAmount' => null, 'stacked' => false])
{{-- Shared fields for customer receipts and supplier payments. --}}
<div @class(['form-grid', 'form-grid--1' => $stacked]) data-payment-form>
    <x-form.input name="payment_date" label="Payment date" type="date" :value="today()->toDateString()" required />
    <x-form.input name="amount" label="Amount" type="number" step="0.01" min="0.01" :max="$maxAmount" :value="$defaultAmount" required inputmode="decimal" data-amount />
    <x-form.select name="method" label="Payment method" :options="$methods" value="cash" required data-method />
    <x-form.input name="reference" label="Reference / Cheque no." maxlength="100" hint="Card slip, transfer or cheque number" data-reference />
    <x-form.input name="bank" label="Bank" maxlength="100" wrapper-class="cheque-only" />
    <x-form.input name="cheque_date" label="Cheque date" type="date" wrapper-class="cheque-only" />
    <x-form.textarea name="notes" label="Notes" wrapper-class="span-2" rows="2" maxlength="1000" />
</div>

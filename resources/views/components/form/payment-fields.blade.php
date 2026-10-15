@props(['methods', 'maxAmount' => null, 'defaultAmount' => null, 'stacked' => false, 'cheques' => null, 'defaultMethod' => 'cash'])
{{-- Shared fields for customer receipts and supplier payments.
     $cheques (supplier payments only): customer cheques that can be handed to the supplier. --}}
<div @class(['form-grid', 'form-grid--1' => $stacked]) data-payment-form>
    <x-form.input name="payment_date" label="Payment date" type="date" :value="today()->toDateString()" required />
    <x-form.select name="method" label="Payment method" :options="$methods" :value="$defaultMethod" required data-method />

    @if ($cheques !== null)
        <div class="field span-2 customer-cheque-only">
            <span class="label">Customer cheques to give the supplier</span>
            @if ($cheques->isEmpty())
                <div class="alert alert-info mb-0">No usable cheques in hand. Cheques appear here when customers pay by cheque, until they are used, deposited or expire.</div>
            @else
                @php $selectedCheques = array_map('intval', (array) old('cheque_ids', [])); @endphp
                <div class="cheque-picker" data-cheque-picker>
                    @foreach ($cheques as $cheque)
                        <label>
                            <input type="checkbox" name="cheque_ids[]" value="{{ $cheque->id }}" data-cheque-amount="{{ $cheque->amount }}" @checked(in_array($cheque->id, $selectedCheques, true))>
                            <span>
                                <span class="cell-title mono">{{ $cheque->cheque_no }}</span>
                                <span class="cell-sub">{{ $cheque->bank ?: 'Bank not recorded' }} · {{ $cheque->customer->displayName() }}</span>
                                <span class="cell-sub">Dated {{ $cheque->cheque_date->format('d M Y') }} ·
                                    <span @class(['text-warning fw-bold' => $cheque->isExpiringSoon()])>valid until {{ $cheque->expiresOn()->format('d M Y') }}</span></span>
                            </span>
                            <span class="amount">{{ money($cheque->amount) }}</span>
                        </label>
                    @endforeach
                    <div class="cheque-picker__total"><span>Payment amount</span><span>{{ $business->currency_symbol }} <span data-cheque-total>0.00</span></span></div>
                </div>
            @endif
            @error('cheque_ids')<span class="field__error">{{ $message }}</span>@enderror
        </div>
    @endif

    <x-form.input name="amount" label="Amount" type="number" step="0.01" min="0.01" :max="$maxAmount" :value="$defaultAmount" required inputmode="decimal" data-amount wrapper-class="not-customer-cheque" />
    <x-form.input name="reference" label="Reference / Cheque no." maxlength="100" hint="Card slip, transfer or cheque number" data-reference wrapper-class="not-customer-cheque" />
    <x-form.input name="bank" label="Bank" maxlength="100" wrapper-class="cheque-only" />
    <x-form.input name="cheque_date" label="Cheque date" type="date" wrapper-class="cheque-only" hint="The date written on the cheque" />
    <x-form.textarea name="notes" label="Notes" wrapper-class="span-2" rows="2" maxlength="1000" />
</div>

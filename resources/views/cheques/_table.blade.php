{{-- Cheque list. Vars: $cheques, $showCustomer (bool), $showActions (bool) --}}
<div class="table-wrap">
    <table class="table">
        <thead>
        <tr>
            <th>Cheque no.</th>
            @if ($showCustomer)<th>From customer</th>@endif
            <th>Bank</th>
            <th>Cheque date</th>
            <th>Valid until</th>
            <th>Status</th>
            <th class="num">Amount</th>
            @if ($showActions)<th></th>@endif
        </tr>
        </thead>
        <tbody>
        @foreach ($cheques as $cheque)
            <tr>
                <td>
                    <div class="cell-title mono">{{ $cheque->cheque_no }}</div>
                    @if ($cheque->invoice_id && $cheque->relationLoaded('invoice') && $cheque->invoice)
                        <div class="cell-sub">for <a href="{{ route('invoices.show', $cheque->invoice_id) }}">{{ $cheque->invoice->invoice_no }}</a></div>
                    @endif
                </td>
                @if ($showCustomer)
                    <td><a href="{{ route('contacts.show', $cheque->customer_id) }}">{{ $cheque->customer->displayName() }}</a></td>
                @endif
                <td>{{ $cheque->bank ?: '—' }}</td>
                <td class="nowrap">
                    {{ $cheque->cheque_date->format('d M Y') }}
                    @if ($cheque->cheque_date->isFuture())<div class="cell-sub">post-dated</div>@endif
                </td>
                <td class="nowrap">
                    <span @class(['text-danger fw-bold' => $cheque->isExpired(), 'text-warning fw-bold' => $cheque->isExpiringSoon()])>{{ $cheque->expiresOn()->format('d M Y') }}</span>
                </td>
                <td>
                    <x-badge :tone="$cheque->statusTone()">{{ $cheque->statusLabel() }}</x-badge>
                    @if ($cheque->status === \App\Enums\ChequeStatus::Endorsed && $cheque->relationLoaded('supplierPayment') && $cheque->supplierPayment)
                        <div class="cell-sub">to <a href="{{ route('contacts.show', $cheque->supplierPayment->supplier_id) }}">{{ $cheque->supplierPayment->supplier->name }}</a>
                            · {{ $cheque->supplierPayment->payment_date->format('d M Y') }}</div>
                    @elseif ($cheque->status === \App\Enums\ChequeStatus::Deposited && $cheque->deposited_on)
                        <div class="cell-sub">on {{ $cheque->deposited_on->format('d M Y') }}</div>
                    @elseif ($cheque->status === \App\Enums\ChequeStatus::Returned)
                        <div class="cell-sub">{{ $cheque->returned_on?->format('d M Y') }} {{ $cheque->notes ? '· '.$cheque->notes : '' }}</div>
                    @endif
                </td>
                <td class="num fw-bold">{{ money($cheque->amount) }}</td>
                @if ($showActions)
                    <td>
                        <div class="actions">
                            @if ($cheque->status === \App\Enums\ChequeStatus::InHand)
                                <form method="POST" action="{{ route('cheques.deposit', $cheque) }}" class="inline-form" data-confirm="Mark cheque {{ $cheque->cheque_no }} as deposited to the bank today?">
                                    @csrf
                                    <input type="hidden" name="deposited_on" value="{{ today()->toDateString() }}">
                                    <button type="submit" class="btn btn-soft btn-sm">Deposited</button>
                                </form>
                            @elseif ($cheque->status === \App\Enums\ChequeStatus::Deposited)
                                @can('admin')
                                    <form method="POST" action="{{ route('cheques.undo-deposit', $cheque) }}" class="inline-form" data-confirm="Move cheque {{ $cheque->cheque_no }} back to in hand?">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">Undo</button>
                                    </form>
                                @endcan
                            @endif

                            @can('admin')
                                @if (in_array($cheque->status, [\App\Enums\ChequeStatus::InHand, \App\Enums\ChequeStatus::Deposited], true))
                                    <details class="inline-details">
                                        <summary class="btn btn-danger btn-sm">Returned</summary>
                                        <form method="POST" action="{{ route('cheques.return', $cheque) }}" class="inline-details__panel"
                                              data-confirm="Mark cheque {{ $cheque->cheque_no }} as returned? The customer's payment will be reversed and the invoice will be owed again.">
                                            @csrf
                                            <label class="label" for="ret-{{ $cheque->id }}">Returned on</label>
                                            <input id="ret-{{ $cheque->id }}" type="date" name="returned_on" value="{{ today()->toDateString() }}" class="input input-sm" required>
                                            <label class="label" for="why-{{ $cheque->id }}">Reason</label>
                                            <input id="why-{{ $cheque->id }}" type="text" name="notes" maxlength="500" class="input input-sm" placeholder="e.g. Insufficient funds">
                                            <button type="submit" class="btn btn-danger btn-sm btn-block">Confirm returned</button>
                                        </form>
                                    </details>
                                @endif
                            @endcan
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

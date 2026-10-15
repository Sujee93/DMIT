<?php

return [
    // A cheque can be deposited or passed to a supplier for this many months after its date.
    'cheque_valid_months' => (int) env('CHEQUE_VALID_MONTHS', 6),

    // Cheques expiring within this many days are highlighted.
    'cheque_expiry_warning_days' => 14,

    'grn_prefix' => env('GRN_PREFIX', 'GRN-'),
];

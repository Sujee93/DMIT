<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Cheque = 'cheque';
    case BankTransfer = 'bank_transfer';
    // Supplier payments only: cheques received from customers, passed on to the supplier.
    case CustomerCheque = 'customer_cheque';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Card => 'Card',
            self::Cheque => 'Cheque',
            self::BankTransfer => 'Bank Transfer',
            self::CustomerCheque => 'Customer cheque',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function forCustomers(): array
    {
        return [self::Cash, self::Card, self::Cheque, self::BankTransfer];
    }

    /**
     * @return array<int, self>
     */
    public static function forSuppliers(): array
    {
        return [self::Cash, self::BankTransfer, self::CustomerCheque, self::Cheque];
    }

    /**
     * @return array<string, string> value => label, for the customer receipt form
     */
    public static function customerOptions(): array
    {
        $options = [];
        foreach (self::forCustomers() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return array<string, string> value => label, for the supplier payment form
     */
    public static function supplierOptions(): array
    {
        $options = [];
        foreach (self::forSuppliers() as $case) {
            $options[$case->value] = $case === self::Cheque ? 'Own cheque' : $case->label();
        }

        return $options;
    }
}

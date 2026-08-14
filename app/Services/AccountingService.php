<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Payslip;
use App\Models\Purchase;
use App\Models\Sale;

class AccountingService
{
    private array $accountCache = [];

    public function accountId(string $code): int
    {
        if (! isset($this->accountCache[$code])) {
            $this->accountCache[$code] = Account::where('code', $code)->value('id');
        }

        return $this->accountCache[$code];
    }

    public function paymentAccount(?string $method): string
    {
        $method = strtolower((string) $method);

        return match (true) {
            str_contains($method, 'cash') => '1000',
            default => '1010',
        };
    }

    public function postSale(Sale $sale): void
    {
        if ($this->entryExists(Sale::class, $sale->id, 'sale')) {
            return;
        }

        $lines = [];
        $revenue = $sale->subtotal - $sale->discount;

        if ($sale->amount_paid > 0) {
            $lines[] = ['account' => $this->paymentAccount($sale->payment_method), 'debit' => $sale->amount_paid];
        }
        if ($sale->balance_due > 0) {
            $lines[] = ['account' => '1100', 'debit' => $sale->balance_due];
        }
        $lines[] = ['account' => '4000', 'credit' => $revenue];
        if ($sale->tax_amount > 0) {
            $lines[] = ['account' => '2100', 'credit' => $sale->tax_amount];
        }

        $cogs = 0;
        foreach ($sale->items as $item) {
            $cogs += $item->quantity * $item->product->purchase_price;
        }
        if ($cogs > 0) {
            $lines[] = ['account' => '5000', 'debit' => $cogs];
            $lines[] = ['account' => '1200', 'credit' => $cogs];
        }

        $this->createEntry([
            'date' => $sale->invoice_date,
            'type' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'description' => 'Sale '.$sale->invoice_number,
        ], $lines);
    }

    public function postSalePayment(Sale $sale, float $amount, ?string $method): void
    {
        $this->createEntry([
            'date' => now(),
            'type' => 'payment',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'description' => 'Payment received for '.$sale->invoice_number,
        ], [
            ['account' => $this->paymentAccount($method), 'debit' => $amount],
            ['account' => '1100', 'credit' => $amount],
        ]);
    }

    public function postPurchase(Purchase $purchase): void
    {
        if ($this->entryExists(Purchase::class, $purchase->id, 'purchase')) {
            return;
        }

        $lines = [
            ['account' => '1200', 'debit' => $purchase->subtotal],
        ];
        if ($purchase->tax_amount > 0) {
            $lines[] = ['account' => '1210', 'debit' => $purchase->tax_amount];
        }
        if ($purchase->amount_paid > 0) {
            $lines[] = ['account' => $this->paymentAccount($purchase->payment_method), 'credit' => $purchase->amount_paid];
        }
        if ($purchase->balance_due > 0) {
            $lines[] = ['account' => '2000', 'credit' => $purchase->balance_due];
        }

        $this->createEntry([
            'date' => $purchase->purchase_date,
            'type' => 'purchase',
            'reference_type' => Purchase::class,
            'reference_id' => $purchase->id,
            'description' => 'Purchase '.$purchase->purchase_number,
        ], $lines);
    }

    public function postPurchasePayment(Purchase $purchase, float $amount, ?string $method): void
    {
        $this->createEntry([
            'date' => now(),
            'type' => 'payment',
            'reference_type' => Purchase::class,
            'reference_id' => $purchase->id,
            'description' => 'Payment made for '.$purchase->purchase_number,
        ], [
            ['account' => '2000', 'debit' => $amount],
            ['account' => $this->paymentAccount($method), 'credit' => $amount],
        ]);
    }

    public function postPayroll(Payslip $payslip): void
    {
        if ($this->entryExists(Payslip::class, $payslip->id, 'payroll')) {
            return;
        }

        $this->createEntry([
            'date' => $payslip->paid_date ?? now(),
            'type' => 'payroll',
            'reference_type' => Payslip::class,
            'reference_id' => $payslip->id,
            'description' => 'Salary for '.$payslip->employee->name.' ('.$payslip->month.')',
        ], [
            ['account' => '5100', 'debit' => $payslip->net_salary],
            ['account' => '1010', 'credit' => $payslip->net_salary],
        ]);
    }

    public function createEntry(array $data, array $lines): JournalEntry
    {
        $entry = JournalEntry::create([
            'date' => $data['date'],
            'type' => $data['type'] ?? 'journal',
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'description' => $data['description'],
            'user_id' => auth()->id() ?? $data['user_id'] ?? null,
        ]);

        foreach ($lines as $line) {
            $entry->lines()->create([
                'account_id' => $this->accountId($line['account']),
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
            ]);
        }

        return $entry;
    }

    private function entryExists(string $referenceType, int $referenceId, string $type): bool
    {
        return JournalEntry::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('type', $type)
            ->exists();
    }
}

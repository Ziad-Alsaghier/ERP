<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountType;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountsService
{
    public function getChartAccounts(array $miniTypes = null)
    {
        // Fetch parent accounts
        $chart_accounts = ChartOfAccount::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            'chart_of_accounts.type',
            'chart_of_accounts.parent'
        )
            ->where('parent', '=', 0)
            ->where('created_by', \Auth::user()->creatorId())
            ->when($miniTypes, function ($query) use ($miniTypes) {
                $query->whereHas('miniType', function ($query) use ($miniTypes) {
                    $query->whereIn('name', $miniTypes);
                });
            })
            ->orderBy('chart_of_accounts.code', 'asc')
            ->get()
            ->toArray();

        // Fetch sub-accounts
        $chart_subAccounts = ChartOfAccount::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            'chart_of_account_parents.account'
        )
            ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
            ->where('chart_of_accounts.parent', '!=', 0)
            ->where('chart_of_accounts.created_by', Auth::user()->creatorId())
            ->when($miniTypes, function ($query) use ($miniTypes) {
                $query->whereHas('miniType', function ($query) use ($miniTypes) {
                    $query->whereIn('name', $miniTypes);
                });
            })
            ->get()
            ->map(function ($subAccount) {
                // Modify the name field as per your requirement
                $subAccount->name = $subAccount->code . ' - ' . __($subAccount->name);
                return $subAccount;
            })
            ->toArray();

        return [
            'parent_accounts' => $chart_accounts,
            'sub_accounts'    => $chart_subAccounts,
        ];
    }

    /**
     * Public function to get accounts for a specific miniType category.
     *
     * @param array $miniTypes
     * @return array
     */
    public function getAccountsByMiniType(array $miniTypes = null)
    {
        return $this->getChartAccounts($miniTypes);
    }

    /**
     * Get all chart account types
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChartAccountTypes()
    {
        return ChartOfAccountType::select('id', 'name')
            ->where('created_by', Auth::user()->creatorId())
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getMiniTypesByCategories(array $categoryNames = []): array
    {
        $miniTypes = [
            // Non-current assets
            'money' => [
                'Bank Account',
                'Non-bank Cash and Equivalents',
            ],
            'non_current_assets' => [
                'Intangible Assets',
                'Other Fixed Assets',
                'Property, Plant, and Equipment',
                'Investing in Other Companies',
                'Work in Progress',
            ],
            'fixed_assets' => [
                'Property, Plant, and Equipment',
                'Other Fixed Assets',
                'Depreciation',
                'Intangible Assets',
                'Accumulated Depreciation',
            ],
            // Revenue
            'revenue' => [
                'Sales',
                'Other Revenue',
                'Gain/losses on sales of equipment',
                'Gain/losses on sales of intangible assets',
            ],
            // Expenses
            'expenses' => [
                'Amortization',
                'Depreciation',
                'Employee incentives and benefits',
                'General and Administrative',
                'Marketing',
                'Other Operational Cost',
                'Salaries',
                'Technical and Consulting Expenses',
                'Change in currency value gains or losses',
                'Research & Development',
                'Taxes',
                'Zakat',
                'Interest Expenses',
                'Cost of sales',
                'Other Direct Cost',
            ],
            // Current Assets
            'current_assets' => [
                'Accounts Receivable',
                'Bank Account',
                'Employees Advances',
                'Inventory',
                'Non-bank Cash and Equivalents',
                'Other Current Assets',
                'Petty Cash',
                'Prepaid Expenses and Others',
                'Assets Spare Parts Inventory',
            ],
            // Current Liability
            'current_liability' => [
                'Accounts Payable',
                'Accrued Expenses',
                'Accrued Salaries and Amounts Owed to Employees',
                'Accumulated Depreciation',
                'Accumulated Amortization',
                'Allowance for Doubtful Accounts',
                'Other Current Liability',
                'Provisions',
                'Short-term Borrowings',
                'Taxes Payable',
                'Unearned Revenues',
                'Zakat Payable',
                'VAT Payable',
                'Interest Payable',
                'Current Part of Long-term Borrowings',
            ],
            // Non-current Liability
            'non_current_liability' => [
                'Long-term Borrowings',
                'Other Non-current Liabilities',
                'End of Service Benefits',
                'Retention',
            ],
            // Issued Capital
            'issued_capital' => [
                'Additional paid-in capital',
                'Employees Equity',
                'Registered capital',
            ],
            // Other equity
            'other_equity' => [
                'Other equity',
                'Reserves',
            ],
            // Retained earnings or losses
            'retained_earnings' => [
                'Retained earnings',
                'Dividends',
            ],
        ];

        // If no category names are passed, return all mini types
        if (empty($categoryNames)) {
            return array_merge(...array_values($miniTypes));
        }

        $result = [];
        foreach ($categoryNames as $categoryName) {
            if (isset($miniTypes[$categoryName])) {
                $result = array_merge($result, $miniTypes[$categoryName]);
            }
        }

        return $result;
    }


    public function addTransaction($name, $type, $amount, $ref = 'test', $refId = 0, $refSubId = 0, $date = '')
    {

        $accountID = ChartOfAccount::getAnAcount($name);
        $data = [
            'account_id' => $accountID,
            'transaction_type' => $type,
            'transaction_amount' => $amount,
            'reference' => $ref,
            'reference_id' => $refId,
            'reference_sub_id' => $refSubId,
            'date' => $date,
        ];

        \Log::info($accountID, $data);
        Utility::addTransactionLines($data);
    }

    public function calcInvCost($invoice)
    {
        // الحصول على جميع المشتريات للمنتج في السنة المالية الحالية
        $currentYearStart = now()->startOfYear(); // بداية السنة المالية الحالية
        $currentYearEnd = now()->endOfYear(); // نهاية السنة المالية الحالية

        // استعلام لاسترجاع كافة المشتريات للمنتج في السنة المالية الحالية
        $products = $invoice->products; // للحصول على المنتجات في الفاتورة
        $totalCost = 0;

        foreach ($products as $product) {
            // استعلام لاسترجاع المشتريات السابقة لهذا المنتج في السنة المالية الحالية
            $purchases = $product->purchases()
                ->whereBetween('purchase_date', [$currentYearStart, $currentYearEnd])
                ->get();

            // تحديد طريقة الحساب (FIFO أو LIFO أو Average) التي اختارها المستخدم
            $method = $invoice->cost_method;  // يمكن أن يكون FIFO أو LIFO أو Average
            $quantity = $product->quantity; // كمية المنتج في الفاتورة

            switch ($method) {
                case 'FIFO':
                    // FIFO: استخدام أول سعر شراء لهذا المنتج
                    $unitCost = $this->getFIFOCost($purchases);
                    break;

                case 'LIFO':
                    // LIFO: استخدام آخر سعر شراء لهذا المنتج
                    $unitCost = $this->getLIFOCost($purchases);
                    break;

                case 'Average':
                    // الطريقة المتوسطة: حساب المتوسط المرجح لجميع المشتريات
                    $unitCost = $this->getAverageCost($purchases);
                    break;

                default:
                    // في حالة عدم تحديد الطريقة
                    throw new \Exception("طريقة حساب التكلفة غير صحيحة");
            }

            // إضافة التكلفة الإجمالية للمخزون
            $totalCost += $unitCost * $quantity;
        }

        return $totalCost;
    }

    private function getFIFOCost($purchases)
    {
        // في FIFO نستخدم أول سعر شراء
        $firstPurchase = $purchases->sortBy('purchase_date')->first();  // أول شراء بناءً على التاريخ
        return $firstPurchase->unit_cost;
    }

    private function getLIFOCost($purchases)
    {
        // في LIFO نستخدم آخر سعر شراء
        $lastPurchase = $purchases->sortByDesc('purchase_date')->first();  // آخر شراء بناءً على التاريخ
        return $lastPurchase->unit_cost;
    }

    private function getAverageCost($purchases)
    {
        // في الطريقة المتوسطة نحتاج إلى حساب المتوسط المرجح
        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($purchases as $purchase) {
            $totalCost += $purchase->unit_cost * $purchase->quantity;
            $totalQuantity += $purchase->quantity;
        }

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0; // إذا كانت الكمية أكبر من 0
    }
}

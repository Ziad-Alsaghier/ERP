<?php

namespace App\Services;

use App\Models\ChartOfAccount;
// use Illuminate\Support\Facades\Auth;

class ReportServices
{
    public function getEpit($startDate, $endDate)
    {
        $revenueSubTypes = [
            'Operational Revenue',
            'Non-operating revenues',
        ];
        $costSubTypes = [
            'Direct Cost',
            'Operational Cost',
        ];
        $operationalRevenueAccounts = ChartOfAccount::where('created_by', \Auth::user()->creatorId())
            ->whereHas('subType', function ($query) use ($revenueSubTypes) {
                $query->whereIn('name', $revenueSubTypes);
            })
            ->get();
        $operationalCostAccounts = ChartOfAccount::where('created_by', \Auth::user()->creatorId())
            ->whereHas('subType', function ($query) use ($costSubTypes) {
                $query->whereIn('name', $costSubTypes);
            })
            ->get();

        $totalRevenue = 0;
        foreach ($operationalRevenueAccounts as $account) {
            $accountBalance = $account->getBalanceAccount($account, $startDate, $endDate);
            $totalRevenue += $accountBalance;
            
        }
        $totalCost = 0;
        foreach ($operationalCostAccounts as $account) {
            $accountBalance = $account->getBalanceAccount($account, $startDate, $endDate);
            $totalCost += $accountBalance;
        }
        $netIncome = $totalRevenue - abs($totalCost);
        \Log::info('net :' . $netIncome .'Revenue : ' . $totalRevenue .' , Cost : ' . $totalCost);
        return $netIncome;
    }

    public function cashFlowReportByAccounts($groupedAccounts, $epit)
    {

        $report = [
            'ebit' => $epit ?? 0, // Placeholder for EBIT
            'non_cash_adjustments' => [],
            'net_non_cash_adjustments'=>[],
            'changes_in_working_capital' => [],
            'cashflow_from_investing_activities' => [],
            'cashflow_from_financing_activities' => [],
            'net_changes_in_working_capital' => 0,
            'net_cashflow_from_operating_activities' => 0,
            'net_cashflow_from_investing_activities' => 0,
            'net_cashflow_from_financing_activities' => 0,
            'net_increase_in_cash_and_equivalents' => 0,
        ];
        foreach ($groupedAccounts as $miniTypeName => $accounts) {
            switch ($miniTypeName) {
                case 'Depreciation':
                    foreach ($accounts as $account) {
                        $report['non_cash_adjustments'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Asset depreciation'
                        ];
                    }
                    break;
                case 'Accounts Receivable':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Decrease in AR'
                        ];
                    }
                    break;
                case 'Inventory':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase in inventory'
                        ];
                    }
                    break;
                case 'Petty Cash':
                case 'Prepaid Expenses and Others':
                case 'Employees Advances':

                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Decrease in prepaid expenses and other CA'
                        ];
                    }
                    break;
                case 'Accounts Payable':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase in AP'
                        ];
                    }
                    break;
                case 'Accrued Salaries and Amounts Owed to Employees':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase (Decrease) in Accrued salaries and amounts owed to employees'
                        ];
                    }
                    break;
                case 'VAT Payable':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase (Decrease) in VAT Payable'
                        ];
                    }
                    break;
                case 'Accrued Expenses':
                    foreach ($accounts as $account) {
                        $report['changes_in_working_capital'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase in acrued expenses and other CL'
                        ];
                    }
                    break;
                    //Cashflow from investing activities
                case 'Property, Plant, and Equipment':
                    foreach ($accounts as $account) {
                        $report['cashflow_from_investing_activities'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase in fixed assets'
                        ];
                    }
                    break;
                case 'Other Fixed Assets':
                    foreach ($accounts as $account) {
                        $report['cashflow_from_investing_activities'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Increase in fixed assets'
                        ];
                    }
                    break;
                // case 'Property, Plant, and Equipment':
                //     foreach ($accounts as $account) {
                //         $report['cashflow_from_investing_activities'][] = [
                            
                //             'name'=>$account->name,
                //             'description' => $miniTypeName,
                //             'amount' => $account->balance,
                //             'group_by' => 'Decrease in fixed assets'
                //         ];
                //     }
                //     break;
                // case 'Other Fixed Assets':
                //     foreach ($accounts as $account) {
                //         $report['cashflow_from_investing_activities'][] = [
                            
                //             'name'=>$account->name,
                //             'description' => $miniTypeName,
                //             'amount' => $account->balance,
                //             'group_by' => 'Decrease in fixed assets'
                //         ];
                //     }
                //     break;

                case 'Other equity':
                    foreach ($accounts as $account) {
                        $report['cashflow_from_financing_activities'][] = [
                            
                            'name'=>$account->name,
                            'code'=>$account->code,
                            'description' => $miniTypeName,
                            'amount' => $account->balance,
                            'group_by' => 'Related parties'
                        ];
                    }
                    break;
                    // Add more cases based on your miniType names and reporting needs

                default:
                    false;
                    break;
            }
        }

        
        $report['net_changes_in_working_capital'] = array_sum(array_column($report['changes_in_working_capital'], 'amount'));
        $report['net_non_cash_adjustments'] = array_sum(array_column($report['non_cash_adjustments'], 'amount'));
        $report['net_cashflow_from_investing_activities'] = array_sum(array_column($report['cashflow_from_investing_activities'], 'amount'));
        $report['net_cashflow_from_financing_activities'] = array_sum(array_column($report['cashflow_from_financing_activities'], 'amount'));
        $report['net_cashflow_from_operating_activities'] = $report['ebit'] + array_sum(array_column($report['non_cash_adjustments'], 'amount')) + $report['net_changes_in_working_capital'];
        $report['net_increase_in_cash_and_equivalents'] = $report['net_cashflow_from_operating_activities'] + $report['net_cashflow_from_investing_activities'] + $report['net_cashflow_from_financing_activities'];
        return $report;
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillProduct extends Model
{
    protected $fillable = [
        'product_id',
        'bill_id',
        'chart_account_id',
        'quantity',
        'tax',
        'discount',
        'total',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id');
    }

    public function chartAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccount', 'id', 'chart_account_id');
    }

    public function getTotalTax()
    {
        $taxData = Utility::getTaxData();
        $totalTax = 0;

            // $taxes = Utility::totalTaxRate($product->tax);

            $taxArr = explode(',', $this->tax);
            $taxes = 0;
            foreach ($taxArr as $tax) {
                // $tax = TaxRate::find($tax);
                $taxes += !empty($taxData[$tax]['rate']) ? $taxData[$tax]['rate'] : 0;
            }

            $totalTax += ($taxes / 100) * ($this->price * $this->quantity);


        return $totalTax;
    }
    public function getSubTotal()
    {
        $subTotal = 0;


            $subTotal += ($this->price * $this->quantity);


        $accountTotal = 0;


        return $subTotal ;
    }
    public function getTotalDiscount()
    {
        $totalDiscount = 0;

            $totalDiscount += $this->discount;


        return $totalDiscount;
    }



    public function getTotal()
    {
        return ($this->getSubTotal() - $this->getTotalDiscount()) + $this->getTotalTax();
    }

    public function updateProductCostRate()
    {
        $productId = $this->product_id;

        // Fetch all BillProduct entries for the product where is_sold is false
        $billProducts = self::where('product_id', $productId)
                             ->get();
        // $billProducts = self::where('product_id', $productId)
        //                      ->where('is_sold', false)
        //                      ->get();

        if ($billProducts->isNotEmpty()) {
            $totalCost = 0;
            $totalQuantity = 0;

            foreach ($billProducts as $billProduct) {
                // Calculate total cost based on price * quantity for each BillProduct
                $totalCost += $billProduct->price * $billProduct->quantity;
                $totalQuantity += $billProduct->quantity; // Accumulate total quantity
            }

            // Calculate average cost rate
            $averageCostRate = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

            // Update the product's cost rate in ProductService
            $product = ProductService::find($productId);
            if ($product) {
                $product->cost_rate = $averageCostRate;
                $product->save();
            }
        }
    }


    /**
     * Handle the "created" event.
     */
    protected static function booted()
    {
        // static::created(function ($billProduct) {
        //     $billProduct->updateProductCostRate();
        // });

        // static::updated(function ($billProduct) {
        //     $billProduct->updateProductCostRate();
        // });

        // static::deleted(function ($billProduct) {
        //     $billProduct->updateProductCostRate();
        // });
    }

}

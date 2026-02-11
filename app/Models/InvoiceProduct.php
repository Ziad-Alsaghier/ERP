<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'product_id',
        'invoice_id',
        'quantity',
        'tax',
        'discount',
        'total',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id');
    }


    // Added by muhammed 28/9

    public function getSubTotal()
    {
        return $this->price * $this->quantity;
    }


    // public function getTotalTax()
    // {
    //     $totalTax = 0;
    //     foreach($this->items as $product)
    //     {
    //         $taxes = Utility::totalTaxRate($product->tax);


    //         $totalTax += ($taxes / 100) * ($product->price * $product->quantity - $product->discount) ;
    //     }

    //     return $totalTax;
    // }

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
    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        $totalDiscount += $this->discount;
        return $totalDiscount;
    }
}

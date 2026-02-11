<?php

namespace App\Models;

use App\Models\Scopes\UserProductScope;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService extends Model
{
    protected $fillable = [
        'sku',
        'code',
        'sale_price',
        'purchase_price',
        'tax_id',
        'category_id',
        'unit_id',
        'attribute_id',
        'sale_chartaccount_id',
        'expense_chartaccount_id',
        'created_by',
    ];

    protected $append = [
         'type',
        'name'

    ];
// Change Attribute Type to is_service ❗
    public function getTypeAttribute(): string
    {
    return $this->is_service ? __('Service') : __('Product');
    }
    public function getManufacturableAttribute(): string
    {
        return $this->attributes['manufacturable'] == 1 ? __('Manufacturable') : __('Non-Manufacturable');
    }
    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id')->first();
    }
    public function lang()
    {
        $l = app()->getLocale();
        return ProductServiceLang::where('product_id', $this->id)->where('lang', $l)->first();
    }


    public function getNameAttribute(){

        return  $this->lang()->name ?? Null;
    }
    public function getDescriptionAttribute(){

        return  $this->lang()?->description;
    }
    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id');
    }

    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);

        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = Tax::find($tax);
        }

        return $taxes;
    }

    public function taxRate($taxes)
    {
        $taxArr  = explode(',', $taxes);
        $taxRate = 0;
        foreach($taxArr as $tax)
        {
            $tax     = Tax::find($tax);
            $taxRate += $tax->rate;
        }

        return $taxRate;
    }

    public static function taxData($taxes)
    {
        $taxArr = explode(',', $taxes);

        $taxes = [];
        foreach($taxArr as $tax)
        {
            $taxesData = Tax::find($tax);
            $taxes[]   = !empty($taxesData) ? $taxesData->name : '';
        }

        return implode(',', $taxes);
    }

    public static function getallproducts()
    {
        return ProductService::select('product_services.*', 'c.name as categoryname')
            ->where('product_services.type', '=', 'product')
            ->leftjoin('product_service_categories as c', 'c.id', '=', 'product_services.category_id')
            ->where('product_services.created_by', '=', Auth::user()->creatorId())
            ->orderBy('product_services.id', 'DESC');
    }

    public function getTotalProductQuantity()
    {
        $totalquantity = $purchasedquantity = $posquantity = 0;
        $authuser = Auth::user();
        $product_id = $this->id;
        $purchases = Purchase::where('created_by', $authuser->creatorId());

        if ($authuser->isUser())
        {
            $purchases = $purchases->where('warehouse_id', $authuser->warehouse_id);
        }

        foreach($purchases->get() as $purchase)
        {
            $purchaseditem = PurchaseProduct::select('quantity')->where('purchase_id', $purchase->id)->where('product_id', $product_id)->first();

            $purchasedquantity += $purchaseditem != null ? $purchaseditem->quantity : 0;

        }

        $poses = Pos::where('created_by', $authuser->creatorId());

        if ($authuser->isUser())
        {
            $pos = $poses->where('warehouse_id', $authuser->warehouse_id);
        }

        foreach($poses->get() as $pos)
        {
            $positem = PosProduct::select('quantity')->where('pos_id', $pos->id)->where('product_id', $product_id)->first();
            $posquantity += $positem != null ? $positem->quantity : 0;
        }

        $totalquantity = $purchasedquantity - $posquantity;


        return $totalquantity;
    }


    public function getQuantity()
    {
        $totalquantity = $purchasedquantity = $quotationquantity = 0;
        $authuser = Auth::user();
        $product_id = $this->id;
        $purchases = Purchase::where('created_by', $authuser->creatorId());

        if ($authuser->isUser())
        {
            $purchases = $purchases->where('warehouse_id', $authuser->warehouse_id);
        }

        foreach($purchases->get() as $purchase)
        {
            $purchaseditem = PurchaseProduct::select('quantity')->where('purchase_id', $purchase->id)->where('product_id', $product_id)->first();

            $purchasedquantity += $purchaseditem != null ? $purchaseditem->quantity : 0;

        }

        $quotations = Quotation::where('created_by', $authuser->creatorId());

        if ($authuser->isUser())
        {
            $quotation = $quotations->where('warehouse_id', $authuser->warehouse_id);
        }

        foreach($quotations->get() as $quotation)
        {
            $quotationitem = QuotationProduct::select('quantity')->where('quotation_id', $quotation->id)->where('product_id', $product_id)->first();
            $quotationquantity += $quotationitem != null ? $quotationitem->quantity : 0;
        }

        $totalquantity = $purchasedquantity - $quotationquantity;


        return $totalquantity;
    }

    public static function tax_id($product_id)
    {
        $tax = DB::table('product_services')
        ->where('id', $product_id)
        ->where('created_by', Auth::user()->creatorId())
        ->select('tax_id')
        ->first();

        return ($tax != null) ? $tax->tax_id : 0;
    }

    public function warehouseProduct($product_id,$warehouse_id)
    {

        $product=WarehouseProduct::where('warehouse_id',$warehouse_id)->where('product_id',$product_id)->first();

        return !empty($product)?$product->quantity:0;
    }


    public function parent()
    {
        return $this->belongsTo(ProductService::class, 'parent_id');
    }





  

    // Get all parent products that use this product as a raw material
    public function parentProducts()
    {
        return $this->belongsToMany(
            ProductService::class,
            'product_service_raws',
            'product_raw_id',
            'product_service_id'
        );
    }



    public function productCategoryLang()
    {
        return $this->hasMany(ProductServiceCategoryLang::class, 'category_id', 'category_id');
    }

    // Add new Feature : Wee Make Attributes for Products ⭐

    /*
            ===================================================
           | New Features ⭐ => Make The Creator Data Globally |
            ===================================================

    */
    protected static function booted()
    {
        static::addGlobalScope(new UserProductScope);


    }


    public static function boot(){
        parent::boot();

        static::creating(function ($product) {
            if (!$product->code) {
                $product->code = self::generateUniqueCode();
            }
        });
    }

    protected static function generateUniqueCode(): int
    {
        // Optional: lock the table for safe concurrency
        return DB::transaction(function () {
            $lastCode = ProductService::lockForUpdate()
                ->max('code');

            return $lastCode ? $lastCode + 1 : 1000;
        });
    }


            public function images()
        {
            return $this->hasMany(ProductServiceImage::class);
        }


        public function mainImage()
    {
        return $this->hasOne(ProductServiceImage::class, 'product_service_id')->where('status', '1');
    }

       public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_service_attributes', 'product_id', 'attr_id')
            ->withPivot('attr_value')
            ->distinct(); // this removes duplicates
    }

 


}

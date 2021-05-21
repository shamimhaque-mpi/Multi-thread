<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
	protected $fillable = ["product_variant_one", "product_variant_two", "product_variant_three", "price", "stock", "product_id"];
	
    // RELATION WITH PRODUCT VARIANT
    public function variant(){
    	return $this->hasOne(ProductVariant::class, 'product_id', 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];


    // RELATION WITH PRODUCT VARIANT price
    public function variant_info(){
    	return $this->hasMany(ProductVariantPrice::class, 'product_id', 'id')->with(['variant']);
    }

}

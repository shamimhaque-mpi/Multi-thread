<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request, DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $products = Product::with('variant_info');

        if($request->isMethod('post') && $request->search){
            foreach ($request->search as $key => $value) {
                if($value!=''){
                    session()->flash('value', $value);
                    if($key=='title'){
                        $products = $products->where('title', 'LIKE', "%{$value}%");
                    }
                    else if($key=='variant_id'){
                        $products = $products->whereIn('id', function($query){
                            $value = session()->get('value');
                            $query->select('product_id')->from('product_variants')->where('variant_id', $value);
                        });
                    }
                    else if($key=='price_from'){
                        $products = $products->whereIn('id', function($query){
                            $value = session()->get('value');
                            $query->select('product_id')->from('product_variant_prices')->where('price', '>=',  $value);
                        });
                    }
                    else if($key=='price_to'){
                        $products = $products->whereIn('id', function($query){
                            $value = session()->get('value');
                            $query->select('product_id')->from('product_variant_prices')->where('price', '<=',  $value);
                        });
                    }
                    else if($key=='date'){
                        $products = $products->whereDate('created_at', '=', $value);
                    }
                }
            }
        }

        $products = $products->get();

        $variants = Variant::all();
        return view('products.index', compact('products', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if(!Product::where(['title'=>$request->title])->exists()){
            // PRODUCT
            $product = [
                "title"         => $request->title,
                "sku"           => $request->sku,
                "description"   => $request->description
            ];
            $product = Product::create($product);
            // PRODUCT VARIANT
            if(is_array($request->product_variant)){
                //
                $variant_price  = $request->product_variant_prices;

                foreach ($variant_price as $key => $row) {
                    $productVariant = [
                        'product_id' => $product->id,
                        'variant_id' => $row['variant_id'],
                        'variant'    => $row['title']
                    ];
                    $product_variant = ProductVariant::create($productVariant);

                    // PRODUCT VARIANT PRICE
                    $product_variant_price = [
                        "product_variant_one"   => $product_variant->id,
                        "product_variant_two"   => $product_variant->id,
                        "product_variant_three" => $product_variant->id,

                        "product_id" => $product->id,
                        "price"      => $variant_price[$key]['price'],
                        "stock"      => $variant_price[$key]['stock'],
                    ];
                    ProductVariantPrice::create($product_variant_price);                
                }
            }
            return 1;
        }
        return 0;


        /*

        // PRODUCT IAMGES
        $productImages = [
            "product_id" =>
            "file_path"  =>
            "thumbnail"  =>
        ];
        ProductImage::create($productImages);*/


        return response()->json($request->all());
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, $id)
    {
        $variants = Variant::all();
        $product  = Product::where('id', $id)->with('variant_info')->first();

        return view('products.edit', compact('variants', 'product'));
    }


    public function variants(Request $request){
        if($request->isMethod('POST') && $request->product_id){
            $variant = DB::table('product_variants')
            ->join('product_variant_prices', 'product_variant_prices.product_id', '=', 'product_variants.product_id')
            ->where('product_variants.product_id', $request->product_id)
            ->select('product_variants.*', 'product_variant_prices.*')
            ->groupBy('product_variant_prices.id')
            ->get();

            return response()->json($variant);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // return response()->json($request->all());
        if($request->product_id && $request->product_id!=''){
            // PRODUCT
            $product = [
                "title"         => $request->title,
                "sku"           => $request->sku,
                "description"   => $request->description
            ];
            Product::where(['id'=>$request->product_id])->update($product);

            // DELETE OLD RECORD
            ProductVariant::where(['product_id'=>$request->product_id])->delete();
            ProductVariantPrice::where(['product_id'=>$request->product_id])->delete();

            // PRODUCT VARIANT
            if(is_array($request->product_variant)){
                //
                $variant_price  = $request->product_variant_prices;

                foreach ($variant_price as $key => $row) {
                    $productVariant = [
                        'product_id' => $request->product_id,
                        'variant_id' => $row['variant_id'],
                        'variant'    => $row['title']
                    ];
                    $product_variant = ProductVariant::create($productVariant);

                    // PRODUCT VARIANT PRICE
                    $product_variant_price = [
                        "product_variant_one"   => $product_variant->id,
                        "product_variant_two"   => $product_variant->id,
                        "product_variant_three" => $product_variant->id,

                        "product_id" => $request->product_id,
                        "price"      => $variant_price[$key]['price'],
                        "stock"      => $variant_price[$key]['stock'],
                    ];
                    ProductVariantPrice::create($product_variant_price);                
                }
            }

        }
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
